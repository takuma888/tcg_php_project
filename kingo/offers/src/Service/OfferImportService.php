<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/6/28
 * Time: 下午1:37
 */



namespace Offers\Service;


use Offers\Utils\Curl;
use Symfony\Component\Yaml\Yaml;

class OfferImportService
{

    const SOURCE_MOBI_SUMMER = 'mobi_summer';
    const SOURCE_PUB_NATIVE = 'pub_native';
    const SOURCE_SOLO = 'solo';
    const SOURCE_MOBI_SMARTER = 'mobi_smarter';
    const SOURCE_INPLAYABLE = 'inplayable';

    /**
     * @param $source
     * @throws \Exception
     */
    public function import($source)
    {
        /** @var Curl $curl */
        $curl = env()->get('offers.utils.curl');
        // 加载配置
        $configFilePath = __DIR__ . '/../Resource/config/offer_source.yml';
        $config = Yaml::parse(file_get_contents($configFilePath));
        $config = $config[$source] ?? [];
        if (!$config) {
            return;
        }
        $url = $config['url'];
        $method = $config['method'];

        $parameters = $config['parameters'];
        $paging = $config['paging'] ?? [];
        $page = 1;
        if ($paging) {
            $parameters[$paging['page_key']] = $page;
            $parameters[$paging['size_key']] = $paging['size_value'];
        }

        // 开启事务
        $tables = [
            $this->getOfferBaseTable(),
            $this->getOfferExtTable(),
        ];
        $conn = query()::connectionForWrite($tables);
        $supportTransaction = query()::supportTransaction($tables);
        if ($supportTransaction) {
            $conn->beginTransaction();
        }

        try {
            $this->startImport($source);
            if (strtolower($method) == 'get') {
                $result = $curl->getRequest($url, $parameters);
            } else {
                $result = $curl->postRequest($url, $parameters);
            }
            if ($config['response']['type'] == 'json') {
                $result = json_decode($result, true);
            }
            $statusKey = $config['response']['status_key'] ?? null;
            $successValue = $config['response']['success_value'] ?? null;
            if ($statusKey) {
                if (!isset($statusKey)) {
                    return;
                } else {
                    if ($successValue && $result[$statusKey] != $successValue) {
                        return;
                    }
                }
            }
            $dataKey = $config['response']['data_key'] ?? null;
            if ($dataKey) {
                $offers = $result[$dataKey] ?? [];
            } else {
                $offers = $result;
            }
            $this->importOffers($offers, $source);

            $responsePaging = $config['response']['paging'] ?? [];
            $pageTotalKey = $responsePaging['page_total_key'] ?? null;
            $totalKey = $responsePaging['total_key'] ?? null;
            if ($pageTotalKey) {
                $totalPage = $result[$pageTotalKey];
            } elseif ($totalKey && isset($paging['size_value'])) {
                $total = $this->getArrayValue($result, $totalKey);
                $totalPage = ceil($total / $paging['size_value']);
            } else {
                $totalPage = 1;
            }
            while ($page < $totalPage) {
                $page += 1;
                $parameters[$paging['page_key']] = $page;
                if (strtolower($method) == 'get') {
                    $result = $curl->getRequest($url, $parameters);
                } else {
                    $result = $curl->postRequest($url, $parameters);
                }
                if ($config['response']['type'] == 'json') {
                    $result = json_decode($result, true);
                }
                if ($statusKey) {
                    if (!isset($statusKey)) {
                        return;
                    } else {
                        if ($successValue && $result[$statusKey] != $successValue) {
                            return;
                        }
                    }
                }
                $dataKey = $config['response']['data_key'] ?? null;
                if ($dataKey) {
                    $offers = $result[$dataKey] ?? [];
                } else {
                    $offers = $result;
                }
                $this->importOffers($offers, $source);
            }
            if ($supportTransaction) {
                $conn->commit();
            }
        } catch (\Exception $e) {
            if ($supportTransaction) {
                $conn->rollBack();
            }
            throw $e;
        }
        $this->endImport($source);
    }

    /**
     * @param array $offers
     * @param $source
     * @throws \Exception
     */
    private function importOffers(array $offers, $source)
    {
        $baseTable = $this->getOfferBaseTable();
        $extTable = $this->getOfferExtTable();
        $tables = [
            $baseTable, $extTable,
        ];
        $conn = query()::connectionForWrite($tables);
        foreach ($offers as $offer) {
            $records = $this->getOfferRecords($source, $offer);
            foreach ($records as $record) {
                $baseRecord = $record['base'];
                $extRecord = $records['ext'];
                // base
                $fields = [];
                $placeholders = [];
                $values = [];
                $updates = [];
                foreach ($baseRecord as $k => $v) {
                    $k = trim($k, '`');
                    $fields[] = "`{$k}`";
                    $placeholders[] = ":{$k}";
                    $values[":{$k}"] = $v;
                    $updates[] = "`{$k}` = VALUES(`{$k}`)";
                }
                $sql = 'INSERT INTO {@table} ({@fields}) VALUES (@values) ON DUPLICATE KEY UPDATE {@updates}';
                $sql = strtr($sql, [
                    '{@fields}' => implode(', ', $fields),
                    '{@values}' => implode(', ', $placeholders),
                    '{@updates}' => implode(', ', $updates),
                ]);
                $query = query($sql);
                $query->table('{@table}', $baseTable)
                    ->setParameters($values);
                $sql = $query->getSQLForWrite([
                    '{@table}' => $baseRecord,
                ]);
                $stmt = $conn->prepare($sql);
                $stmt->execute($query->getParameters());
                // ext
                $fields = [];
                $placeholders = [];
                $values = [];
                $updates = [];
                foreach ($extRecord as $k => $v) {
                    $k = trim($k, '`');
                    $fields[] = "`{$k}`";
                    $placeholders[] = ":{$k}";
                    $values[":{$k}"] = $v;
                    $updates[] = "`{$k}` = VALUES(`{$k}`)";
                }
                $sql = 'INSERT INTO {@table} ({@fields}) VALUES (@values) ON DUPLICATE KEY UPDATE {@updates}';
                $sql = strtr($sql, [
                    '{@fields}' => implode(', ', $fields),
                    '{@values}' => implode(', ', $placeholders),
                    '{@updates}' => implode(', ', $updates),
                ]);
                $query = query($sql);
                $query->table('{@table}', $extTable)
                    ->setParameters($values);
                $sql = $query->getSQLForWrite([
                    '{@table}' => $baseRecord,
                ]);
                $stmt = $conn->prepare($sql);
                $stmt->execute($query->getParameters());
            }
        }
    }

    /**
     * @param $source
     * @throws \Exception
     */
    private function startImport($source)
    {
        // 重置offer的status为0
        $table = $this->getOfferBaseTable();
        $sql = 'UPDATE {@table} SET `status` = :status WHERE `source` = :source';
        $query = query($sql);
        $query->table('{@table}', $table)
            ->setParameters([
                ':status' => 0,
                ':source' => $source,
            ]);
        $conn = $query->getConnectionForWrite([
            '{@table}' => [
                'source' => $source,
            ],
        ]);
        $sql = $query->getSQLForWrite([
            '{@table}' => [
                'source' => $source,
            ],
        ]);
        $parameters = $query->getParameters();
        $stmt = $conn->prepare($sql);
        $stmt->execute($parameters);
    }


    private function endImport($source)
    {
        // 清空缓存 todo
    }

    /**
     * @param $source
     * @param $offer
     * @return array
     * @throws \Exception
     */
    private function getOfferRecords($source, $offer)
    {
        $return = [];
        switch ($source) {
            case self::SOURCE_MOBI_SUMMER:
                $countries = [''];
                if ($offer['country']) {
                    $countries = explode(',', $offer['country']);
                }
                $platforms = [''];
                if ($offer['platform']) {
                    $platforms = explode(',', $offer['platform']);
                }
                foreach ($countries as $country) {
                    foreach ($platforms as $platform) {
                        $id = md5(implode('|' , [
                            $source,
                            $offer['offer_id'],
                            $country,
                            strtolower($platform),
                        ]));
                        $base = [
                            'id' => $id,
                            'source' => $source,
                            'offer_id' => $offer['offer_id'],
                            'offer_name' => $offer['offer_name'],
                            'package_name' => isset($offer['package_name']) ? $offer['package_name'] : '',
                            'country' => $country,
                            'platform' => strtolower($platform),
                            'category' => isset($offer['category']) ? $offer['category'] : '',
                            'payout_type' => $offer['payout_type'],
                            'payout' => $offer['payout'],
                            'status' => 1,
                        ];
                        $ext = [
                            'id' => $id,
                            'info' => json_encode($offer)
                        ];
                        $return[] = [
                            'base' => $base,
                            'ext' => $ext,
                        ];
                    }
                }
                break;
            case self::SOURCE_PUB_NATIVE:
                $platforms = [''];
                if ($offer['app_details']['platform']) {
                    $platforms = explode(',', $offer['app_details']['platform']);
                }
                foreach ($offer['campaigns'] as $campaign) {
                    $countries = [''];
                    if (isset($campaign['countries'])) {
                        $countries = $campaign['countries'];
                    }
                    foreach ($countries as $country) {
                        foreach ($platforms as $platform) {
                            $id = md5(implode('|', [
                                $source,
                                $campaign['cref'],
                                $country,
                                strtolower($platform),
                            ]));
                            $base = [
                                'id' => $id,
                                'source' => 'Pub Native',
                                'offer_id' => $campaign['cref'],
                                'offer_name' => isset($offer['creatives']['title']) ? $offer['creatives']['title'] : '',
                                'package_name' => isset($offer['app_details']['bundle_id']) ? $offer['app_details']['bundle_id'] : '',
                                'country' => $country,
                                'platform' => strtolower($platform),
                                'category' => isset($offer['app_details']['category']) ? $offer['app_details']['category'] : '',
                                'payout_type' => 'CPI',
                                'payout' => $campaign['points'],
                                'status' => 1,
                            ];
                            $info = $campaign;
                            $info['app_details'] = $offer['app_details'];
                            $info['creatives'] = $offer['creatives'];
                            $ext = [
                                'id' => $id,
                                'info' => json_encode($info)
                            ];$return[] = [
                                'base' => $base,
                                'ext' => $ext,
                            ];
                        }
                    }
                }
                break;
            case self::SOURCE_SOLO:
                $countries = [''];
                $platforms = [''];
                $targeting = $offer['targeting'];
                if ($targeting['platform']) {
                    $platforms = [strtolower($targeting['platform'])];
                }
                if ($targeting['geo']) {
                    $countries = $targeting['geo'];
                }
                foreach ($countries as $country) {
                    foreach ($platforms as $platform) {
                        $id = md5(implode('|' , [
                            $source,
                            $offer['id'],
                            $country,
                            $platform,
                        ]));
                        $package_name = '';
                        $category = '';
                        if (isset($offer['product_info'])) {
                            if (isset($offer['product_info']['package_id'])) {
                                $package_name = $offer['product_info']['package_id'];
                            }
                            if (isset($offer['product_info']['category'])) {
                                $category = $offer['product_info']['category'];
                            }
                            if (isset($offer['product_info']['description'])) {
                                unset($offer['product_info']['description']);
                            }
                            if (isset($offer['product_info']['native_one_sentence_description'])) {
                                unset($offer['product_info']['native_one_sentence_description']);
                            }
                        }
                        if (isset($offer['note'])) {
                            unset($offer['note']);
                        }
                        $base = [
                            'id' => $id,
                            'source' => $source,
                            'offer_id' => $offer['id'],
                            'offer_name' => $offer['campaign_name'],
                            'package_name' => $package_name,
                            'country' => $country,
                            'platform' => strtolower($platform),
                            'category' => $category,
                            'payout_type' => $offer['payout_type'],
                            'payout' => $offer['payout'],
                            'status' => 1,
                        ];
                        $ext = [
                            'id' => $id,
                            'info' => json_encode($offer),
                        ];
                        $return[] = [
                            'base' => $base,
                            'ext' => $ext,
                        ];
                    }
                }
                break;
            case self::SOURCE_MOBI_SMARTER:
                $countries = [''];
                if ($offer['countries']) {
                    $countries = $offer['countries'];
                }
                $platforms = [''];
                if ($offer['platform']) {
                    $platforms = explode(',', $offer['platform']);
                }
                foreach ($countries as $country) {
                    foreach ($platforms as $platform) {
                        $id = md5(implode('|' , [
                            $source,
                            $offer['id'],
                            $country,
                            strtolower($platform),
                        ]));
                        $base = [
                            'id' => $id,
                            'source' => $source,
                            'offer_id' => $offer['id'],
                            'offer_name' => $offer['name'],
                            'package_name' => isset($offer['packet_name']) ? $offer['packet_name'] : '',
                            'country' => $country,
                            'platform' => strtolower($platform),
                            'category' => isset($offer['category']) && $offer['category'] ? $offer['category'] : '',
                            'payout_type' => 'CPA',
                            'payout' => $offer['price'],
                            'status' => 1,
                        ];
                        $ext = [
                            'id' => $id,
                            'info' => json_encode($offer),
                        ];
                        $return[] = [
                            'base' => $base,
                            'ext' => $ext,
                        ];
                    }
                }
                break;
            case self::SOURCE_INPLAYABLE:
                $countries = [''];
                if ($offer['countries']) {
                    $countries = $offer['countries'];
                }
                $platforms = [''];
                if ($offer['platform']) {
                    $platforms = explode(',', $offer['platform']);
                }
                foreach ($countries as $country) {
                    foreach ($platforms as $platform) {
                        $id = md5(implode('|' , [
                            $source,
                            $offer['id'],
                            $country,
                            strtolower($platform),
                        ]));
                        $base = [
                            'id' => $id,
                            'source' => $source,
                            'offer_id' => $offer['id'],
                            'offer_name' => $offer['app_name'],
                            'package_name' => isset($offer['app_pkg']) ? $offer['app_pkg'] : '',
                            'country' => $country,
                            'platform' => strtolower($platform),
                            'category' => isset($offer['category']) ? $offer['category'] : '',
                            'payout_type' => 'CPI',
                            'payout' => $offer['price'],
                            'status' => 1,
                        ];
                        $ext = [
                            'id' => $id,
                            'info' => json_encode($offer),
                        ];
                        $return[] = [
                            'base' => $base,
                            'ext' => $ext,
                        ];
                    }
                }
                break;
            default:
                throw new \Exception("Unknown source! {$source}");
                break;
        }
        return $return;
    }


    private function getArrayValue(array $array, $expr, $default = null)
    {
        $pieces = [];
        if (strpos($expr, '.') !== false) {
            $pieces = explode('.', $expr);
        } else {
            $pieces[] = $expr;
        }
        $return = [];
        while ($pieces) {
            $key = array_shift($pieces);
            $return = $array[$key] ?? [];
        }
        return $return ? $return : $default;
    }


    /**
     * @return \TCG\MySQL\Table
     */
    private function getOfferBaseTable()
    {
        return table('offer_base');
    }

    /**
     * @return \TCG\MySQL\Table
     */
    private function getOfferExtTable()
    {
        return table('offer_ext');
    }
}