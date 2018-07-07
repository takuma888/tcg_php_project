<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/7/7
 * Time: 上午10:19
 */

use Offers\Utils\Ip2Country;
use Offers\Utils\ISO3166;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * get /offer
 */
route()->get('/offer', function (ServerRequestInterface $request, ResponseInterface $response) {
    // 先筛选出策略
    // ip => geo
    $ip = $request->getServerParams()['REMOTE_ADDR'];
    /** @var Ip2Country $ip2country */
    $ip2country = env()->get('offers.utils.ip2country');
    $countryId = $ip2country->getCountry($ip);
    /** @var ISO3166 $iso3166 */
    $iso3166 = env()->get('offers.utils.iso3166');
    $geo2 = $iso3166->getAlpha2($countryId);
    if ($geo2 == 'XX') {
        $geo2 = '';
    }
    $geo3 = $iso3166->getAlpha3($countryId);
    if ($geo3 == 'XXX') {
        $geo3 = '';
    }
    $gets = $request->getQueryParams();
    $client = $gets['ad-set'] ?? ''; // 客户端渠道 例如 pc 或者 apk

    $date = date('Y-m-d', time());
    /** @var \Offers\Service\StrategyService $strategyService */
    $strategyService = service(\Offers\Service\StrategyService::class);

    $condition = [];
    // geo
    $condition[] = '(`geo2` IS NULL OR `geo2` = :geo2) AND (`geo3` IS NULL OR `geo3` = :geo3)';
    // date
    $condition[] = '((`start_at` IS NULL OR `start_at` <= :date) AND (`end_at` IS NULL OR `end_at` >= :date))';
    // client
    $condition[] = '(`client` IS NULL OR `client` = :client)';
    // priority
    $condition[] = '(`priority` > 0)';
    $condition = implode(' AND ', $condition);
    $condition .= ' ORDER BY `priority` DESC';
    $strategies = $strategyService->baseSelectMany($condition, [
        ':geo2' => $geo2,
        ':geo3' => $geo3,
        ':date' => $date,
        ':client' => $client,
    ]);


    $countryExt = $sourceExt = $packageNameExt = $idExt = [
        'in' => [],
        'not_in' => [],
    ];
    $packageSizeExt = [
        'between' => [],
        'not_between' => [],
    ];
    if ($strategies) {
        $strategyIds = [];
        foreach ($strategies['data'] as $strategy) {
            $strategyIds[] = $strategy['id'];
        }
        /** @var \Offers\Service\StrategyCalcService $strategyCalcService */
        $strategyCalcService = service(\Offers\Service\StrategyCalcService::class);

        $countryExt = $strategyCalcService->calcExtCountry($strategyIds);
        $sourceExt = $strategyCalcService->calcExtSource($strategyIds);
        $packageNameExt = $strategyCalcService->calcExtPackageName($strategyIds);
        $idExt = $strategyCalcService->calcExtId($strategyIds);
        $packageSizeExt = $strategyCalcService->calcExtPackageSize($strategyIds);
    }

    // offer
    /** @var \Offers\Service\OfferService $offerService */
    $offerService = service(\Offers\Service\OfferService::class);
    $condition = [];
    $condition[] = '`status` > 0';
    $params = [];

    $array = [
        'country' => $countryExt,
        'source' => $sourceExt,
        'package_name' => $packageNameExt,
        'id' => $idExt,
    ];

    foreach ($array as $field => $ext) {
        if ($ext['in']) {
            foreach ($ext['in'] as $k => $v) {
                $ext['in'][$k] = "'{$v}'";
            }
            $condition[] = '(`' . $field . '` IN (' . implode(', ', $ext['in']) . '))';
        }
        if ($ext['not_in']) {
            foreach ($ext['not_in'] as $k => $v) {
                $ext['not_in'][$k] = "'{$v}'";
            }
            $condition[] = '(`' . $field . '` NOT IN (' . implode(', ', $ext['not_in']) . '))';
        }
    }
    // package size todo
    /*foreach ($packageSizeExt['between'] as $ext) {
    }*/

    $clauseExpr = '';
    $clauseExpr .= implode(' AND ', $condition);
    $clauseExpr .= ' LIMIT 100';


    $offers = $offerService->baseSelectMany($clauseExpr, $params);
    $offerIds = [];
    foreach ($offers['data'] as $offer) {
        $offerIds[] = $offer['id'];
    }
    $offerId = 0;
    if ($offerIds) {
        $offerId = array_rand($offerIds, min(1, count($offerIds)));
    }

    if (!$offerId) {
        $offers = $offerService->baseSelectMany('`status` > 0 ORDER BY RAND() LIMIT 1', $params);
        $offerIds = [];
        foreach ($offers['data'] as $offer) {
            $offerIds[] = $offer['id'];
        }
        $offerId = $offerIds[0];
    }

    $offerExtInfo = $offerService->getExtInfoById($offerId);
    if ($offerExtInfo['data']) {
        $info = json_decode($offerExtInfo['data']['info'], true);
        $source = $offerExtInfo['data']['source'];
        $id = $offerExtInfo['data']['id'];
        /** @var \Offers\Service\OfferApiService $offerApiService */
        $offerApiService = service(\Offers\Service\OfferApiService::class);
        $data = $offerApiService->export2api($id, $source, $info);
        return json($response, $data);
    }
});