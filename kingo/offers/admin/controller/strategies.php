<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/23
 * Time: 下午4:53
 */

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * get /strategies/countries
 * 获取所有国家列表
 */
route()->get('/strategies/countries', function (ServerRequestInterface $request, ResponseInterface $response) {
    /** @var \Offers\Utils\ISO3166 $iso3166 */
    $iso3166 = env()->get('offers.utils.iso3166');
    return json($response, $iso3166->getAlpha3ToCNs());
});

/**
 * get /strategies/sources
 * 获取所有来源列表
 */
route()->get('/strategies/sources', function (ServerRequestInterface $request, ResponseInterface $response) {
    $sourcesConfigFilePath = __DIR__ . '/../../src/Resource/config/offer_source.yml';
    $content = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($sourcesConfigFilePath));
    $sources = array_keys($content);
    return json($response, $sources);
});

/**
 * get /strategies/package-names
 * 模糊搜索包名
 */
route()->get('/strategies/package-names', function (ServerRequestInterface $request, ResponseInterface $response) {
    $gets = $request->getQueryParams();
    $packageName = $gets['package_name'] ?? '';

    $filterCondition = [];
    $filterParams = [];

    $filterCondition[] = '`package_name` LIKE :package_name';
    $filterParams[':package_name'] = "%{$packageName}%";

    $filterCondition = 'WHERE ' . implode(' AND ', $filterCondition);
    /** @var \Offers\Service\OfferService $offerService */
    $offerService = service(\Offers\Service\OfferService::class);
    $result = $offerService->selectMany($filterCondition, $filterParams);
    return json($response, $result);
});

/**
 * get /strategies/ids
 * 模糊搜索ID
 */
route()->get('/strategies/ids', function (ServerRequestInterface $request, ResponseInterface $response) {
    $gets = $request->getQueryParams();
    $id = $gets['id'] ?? '';

    $filterCondition = [];
    $filterParams = [];

    $filterCondition[] = '`id` LIKE :id';
    $filterParams[':id'] = "%{$id}%";

    $filterCondition = 'WHERE ' . implode(' AND ', $filterCondition);
    /** @var \Offers\Service\OfferService $offerService */
    $offerService = service(\Offers\Service\OfferService::class);
    $result = $offerService->selectMany($filterCondition, $filterParams);
    return json($response, $result);
});


/**
 * get /strategies
 * 获取策略列表
 */
route()->get('/strategies', function (ServerRequestInterface $request, ResponseInterface $response) {
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
    /** @var \Offers\Service\StrategyService $strategyService */
    $strategyService = service(\Offers\Service\StrategyService::class);
    $baseStrategies = $strategyService->baseSelectMany($filterCondition, $filterParams);

    return json($response, $baseStrategies);
});

/**
 * post /strategies/delete
 * 批量删除策略
 */
route()->post('/strategies/delete', function (ServerRequestInterface $request, ResponseInterface $response) {});