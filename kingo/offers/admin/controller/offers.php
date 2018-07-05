<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/23
 * Time: 下午4:51
 */

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * get /offers
 * 获取 offer 列表
 */
route()->get('/offers', function (ServerRequestInterface $request, ResponseInterface $response) {
    $gets = $request->getQueryParams();
    // 分页
    $page = $gets['page'] ?? 1;
    $size = $gets['size'] ?? 25;

    // 其他查询条件
    $id = $gets['id'] ?? '';
    $id = trim($id);
    $source = $gets['source'] ?? '';
    $source = trim($source);
    $offerName = $gets['name'] ?? '';
    $offerName = trim($offerName);
    $packageName = $gets['package'] ?? '';
    $packageName = trim($packageName);
    $country = $gets['country'] ?? '';
    $country = trim(strtoupper($country));


    $filterCondition = [];
    $filterParams = [];

    if ($id) {
        $filterCondition[] = '`id` = :id';
        $filterParams[':id'] = $id;
    }

    if ($source) {
        $filterCondition[] = '`source` = :source';
        $filterParams[':source'] = $id;
    }

    if ($offerName) {
        $filterCondition[] = '`offer_name` LIKE :offer_name';
        $filterParams[':offer_name'] = "%{$offerName}%";
    }

    if ($packageName) {
        $filterCondition[] = '`package_name` LIKE :package_name';
        $filterParams[':package_name'] = "%{$packageName}%";
    }

    if ($country) {
        $filterCondition[] = '`country` LIKE :country';
        $filterParams[':country'] = "%{$country}%";
    }

    $filterCondition = implode(' AND ', $filterCondition);
    if (!strlen($filterCondition)) {
        $filterCondition = 'WHERE 1';
    }

    $limit = max(0, $size);
    $offset = (max(1, $page) - 1) * $limit;
    $filterCondition .= " LIMIT {$offset}, {$limit}";


    /** @var \Offers\Service\OfferService $offerService */
    $offerService = service(\Offers\Service\OfferService::class);
    $result = $offerService->selectMany($filterCondition, $filterParams);

    return json($response, $result);
});