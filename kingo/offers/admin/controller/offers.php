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

    $page = $gets['page'] ?? 0;
    $size = $gets['size'] ?? 25;

    $filter = $gets['filter'] ?? [];
    $filterCondition = [];
    $filterParams = [];
    foreach ($filter as $item) {
        $item = json_decode($item, true);
        $key = $item['id'];
        $value = $item['value'];
        $filterCondition[] = "`{$key}` = :{$key}";
        $filterParams[":{$key}"] = $value;
    }

    $filterCondition = implode(' AND ', $filterCondition);
    if (!strlen($filterCondition)) {
        $filterCondition = 'WHERE 1';
    }

    $sort = $gets['sort'] ?? [];
    $sortCondition = [];
    foreach ($sort as $item) {
        $item = json_decode($item, true);
        $key = $item['id'];
        $desc = $item['desc'];
        if ($desc) {
            $sortCondition[] = "`{$key}` DESC";
        } else {
            $sortCondition[] = "`{$key}` ASC";
        }
    }
    if ($sortCondition) {
        $filterCondition .= " ORDER BY " . implode(', ', $sortCondition);
    }

    $limit = max(0, $size);
    $offset = (max(1, $page) - 1) * $limit;
    $filterCondition .= " LIMIT {$offset}, {$limit}";



    /** @var \Offers\Service\OfferService $offerService */
    $offerService = service(\Offers\Service\OfferService::class);
    $result = $offerService->selectMany($filterCondition, $filterParams);

    return json($response, $result);
});