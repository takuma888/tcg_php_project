<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/7/7
 * Time: 上午11:53
 */

namespace Offers\Service;


class StrategyCalcService
{

    /**
     * @param array $strategyIds
     * @return array
     * @throws \Exception
     */
    public function calcExtCountry(array $strategyIds)
    {
        $countryIn = [];
        $countryNotIn = [];

        $condition = [];
        $params = [];
        $condition[] = "(`category` = :category_country1 AND `type` = :type_country1 AND `strategy_id` IN (" . implode(', ', $strategyIds) . "))";
        $params[':category_country1'] = 'country';
        $params[':type_country1'] = 'include';
        $clauseExpr = implode(' OR ', $condition);
        $includes = $this->getStrategyService()->extSelectMany($clauseExpr, $params);
        if ($includes['data']) {
            foreach ($includes['data'] as $strategy) {
                $countryIn[] = $strategy['value1'];
                $countryIn[] = $strategy['value2'];
            }
        }

        $condition = [];
        $params = [];
        $condition[] = "(`category` = :category_country1 AND `type` = :type_country1 AND `strategy_id` IN (" . implode(', ', $strategyIds) . "))";
        $params[':category_country1'] = 'country';
        $params[':type_country1'] = 'exclude';
        $clauseExpr = implode(' OR ', $condition);
        $excludes = $this->getStrategyService()->extSelectMany($clauseExpr, $params);
        if ($excludes['data']) {
            foreach ($excludes['data'] as $strategy) {
                $countryNotIn[] = $strategy['value1'];
                $countryNotIn[] = $strategy['value2'];
            }
        }

        $countryIn = array_unique($countryIn);
        $countryNotIn = array_unique($countryNotIn);

        return [
            'in' => $countryIn,
            'not_in' => $countryNotIn,
        ];
    }

    /**
     * @param array $strategyIds
     * @return array
     * @throws \Exception
     */
    public function calcExtSource(array $strategyIds)
    {
        $sourceIn = [];
        $sourceNotIn = [];

        $condition = [];
        $params = [];
        $condition[] = '(`category` = :category_source1 AND `type` = :type_source1 AND `strategy_id` IN (' . implode(', ', $strategyIds) . '))';
        $params[':category_source1'] = 'source';
        $params[':type_source1'] = 'include';
        $clauseExpr = implode(' OR ', $condition);
        $includes = $this->getStrategyService()->extSelectMany($clauseExpr, $params);
        if ($includes['data']) {
            foreach ($includes['data'] as $strategy) {
                $sourceIn[] = $strategy['value1'];
            }
        }

        $condition = [];
        $params = [];
        $condition[] = '(`category` = :category_source1 AND `type` = :type_source1 AND `strategy_id` IN (' . implode(', ', $strategyIds) . '))';
        $params[':category_source1'] = 'source';
        $params[':type_source1'] = 'exclude';
        $clauseExpr = implode(' OR ', $condition);
        $excludes = $this->getStrategyService()->extSelectMany($clauseExpr, $params);
        if ($excludes['data']) {
            foreach ($excludes['data'] as $strategy) {
                $sourceNotIn[] = $strategy['value1'];
            }
        }

        $sourceIn = array_unique($sourceIn);
        $sourceNotIn = array_unique($sourceNotIn);

        return [
            'in' => $sourceIn,
            'not_in' => $sourceNotIn,
        ];
    }

    /**
     * @param array $strategyIds
     * @return array
     * @throws \Exception
     */
    public function calcExtPackageName(array $strategyIds)
    {
        $packageNameIn = [];
        $packageNameNotIn = [];

        $condition = [];
        $params = [];
        $condition[] = '(`category` = :category_package_name1 AND `type` = :type_package_name1 AND `strategy_id` IN (' . implode(', ', $strategyIds) . '))';
        $params[':category_package_name1'] = 'package_name';
        $params[':type_package_name1'] = 'include';
        $clauseExpr = implode(' OR ', $condition);
        $includes = $this->getStrategyService()->extSelectMany($clauseExpr, $params);
        if ($includes['data']) {
            foreach ($includes['data'] as $strategy) {
                $packageNameIn[] = $strategy['value1'];
            }
        }

        $condition = [];
        $params = [];
        $condition[] = '(`category` = :category_package_name1 AND `type` = :type_package_name1 AND `strategy_id` IN (' . implode(', ', $strategyIds) . '))';
        $params[':category_package_name1'] = 'package_name';
        $params[':type_package_name1'] = 'exclude';
        $clauseExpr = implode(' OR ', $condition);
        $excludes = $this->getStrategyService()->extSelectMany($clauseExpr, $params);
        if ($excludes['data']) {
            foreach ($excludes['data'] as $strategy) {
                $packageNameNotIn[] = $strategy['value1'];
            }
        }

        $packageNameIn = array_unique($packageNameIn);
        $packageNameNotIn = array_unique($packageNameNotIn);

        return [
            'in' => $packageNameIn,
            'not_in' => $packageNameNotIn,
        ];
    }

    /**
     * @param array $strategyIds
     * @return array
     * @throws \Exception
     */
    public function calcExtId(array $strategyIds)
    {
        $idIn = [];
        $idNotIn = [];

        $condition = [];
        $params = [];
        $condition[] = '(`category` = :category_id1 AND `type` = :type_id1 AND `strategy_id` IN (' . implode(', ', $strategyIds) . '))';
        $params[':category_id1'] = 'package_name';
        $params[':type_id1'] = 'include';
        $clauseExpr = implode(' OR ', $condition);
        $includes = $this->getStrategyService()->extSelectMany($clauseExpr, $params);
        if ($includes['data']) {
            foreach ($includes['data'] as $strategy) {
                $idIn[] = $strategy['value1'];
            }
        }

        $condition = [];
        $params = [];
        $condition[] = '(`category` = :category_id1 AND `type` = :type_id1 AND `strategy_id` IN (' . implode(', ', $strategyIds) . '))';
        $params[':category_id1'] = 'package_name';
        $params[':type_id1'] = 'exclude';
        $clauseExpr = implode(' OR ', $condition);
        $excludes = $this->getStrategyService()->extSelectMany($clauseExpr, $params);
        if ($excludes['data']) {
            foreach ($excludes['data'] as $strategy) {
                $idNotIn[] = $strategy['value1'];
            }
        }

        $idIn = array_unique($idIn);
        $idNotIn = array_unique($idNotIn);

        return [
            'in' => $idIn,
            'not_in' => $idNotIn,
        ];
    }

    /**
     * @param array $strategyIds
     * @return array
     * @throws \Exception
     */
    public function calcExtPackageSize(array $strategyIds)
    {
        $packageSizeBetween = [];
        $packageSizeNotBetween = [];

        $condition = [];
        $params = [];
        $condition[] = '(`category` = :category_package_size1 AND `type` = :type_package_size1 AND `strategy_id` IN (' . implode(', ', $strategyIds) . '))';
        $params[':category_package_size1'] = 'package_size';
        $params[':type_package_size1'] = 'include';
        $clauseExpr = implode(' OR ', $condition);
        $includes = $this->getStrategyService()->extSelectMany($clauseExpr, $params);
        if ($includes['data']) {
            foreach ($includes['data'] as $strategy) {
                $packageSizeBetween[md5($strategy['value1'] . '-' . $strategy['value2'])] = [
                    $strategy['value1'], $strategy['value2'],
                ];
            }
        }
        $condition = [];
        $params = [];
        $condition[] = '(`category` = :category_package_size1 AND `type` = :type_package_size1 AND `strategy_id` IN (' . implode(', ', $strategyIds) . '))';
        $params[':category_package_size1'] = 'package_size';
        $params[':type_package_size1'] = 'exclude';
        $clauseExpr = implode(' OR ', $condition);
        $includes = $this->getStrategyService()->extSelectMany($clauseExpr, $params);
        if ($includes['data']) {
            foreach ($includes['data'] as $strategy) {
                $packageSizeNotBetween[md5($strategy['value1'] . '-' . $strategy['value2'])] = [
                    $strategy['value1'], $strategy['value2'],
                ];
            }
        }

        $packageSizeBetween = array_values($packageSizeBetween);
        $packageSizeNotBetween = array_values($packageSizeNotBetween);

        return [
            'between' => $packageSizeBetween,
            'not_between' => $packageSizeNotBetween,
        ];
    }


    /**
     * @return StrategyService
     */
    private function getStrategyService()
    {
        return service(StrategyService::class);
    }
}