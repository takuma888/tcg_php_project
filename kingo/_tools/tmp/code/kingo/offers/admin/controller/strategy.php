<?php
/**
 * Created by PhpStorm.
 * User: tachigo
 * Date: 2018/6/30
 * Time: 下午12:01
 */

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * get /strategy
 * 获取策略详细数据
 */
route()->get('/strategy/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {
    if (!$id) {
        throw new \Exception("ID参数不能为空");
    }

    /** @var \Offers\Service\StrategyService $strategyService */
    $strategyService = service(\Offers\Service\StrategyService::class);
    $data = $strategyService->getInfoById($id, true);
    $extData = [];
    foreach ($data['ext'] as $extItem) {
        $category = $extItem['category'];
        $type = $extItem['type'];
        if (!isset($extData[$category])) {
            $extData[$category] = [];
        }
        if (!isset($extData[$category][$type])) {
            $extData[$category][$type] = [];
        }
        $extData[$category][$type][] = $extItem;
    }
    return json($response, [
        'base' => $data['base'],
        'ext' => $extData,
    ]);
});

/**
 * post /strategy/add
 * 添加策略
 */
route()->post('/strategy/add', function (ServerRequestInterface $request, ResponseInterface $response) {
    $posts = $request->getParsedBody();
    $name = $posts['name'] ?? '';
    $description = $posts['description'] ?? '';
    $priority = $posts['priority'] ?? 0;
    $client = $posts['client'] ?? null;
    $geo2 = $posts['geo2'] ?? null;
    $geo3 = $posts['geo3'] ?? null;
    $startAt = $posts['start_at'] ?? null;
    $endAt = $posts['end_at'] ?? null;
    $name = trim($name);
    if (!$name) {
        throw new \Exception("名称不能为空");
    }
    $description = trim($description);
    $priority = intval($priority);
    /** @var \Offers\Service\StrategyService $strategyService */
    $strategyService = service(\Offers\Service\StrategyService::class);
    $strategyService->baseCreate([
        'name' => $name,
        'description' => $description,
        'priority' => $priority,
        'client' => $client,
        'geo2' => $geo2,
        'geo3' => $geo3,
        'start_at' => $startAt,
        'end_at' => $endAt,
    ]);
    flash()->success("添加策略成功");
    return json($response, []);
});

/**
 * post /strategy/edit
 * 编辑策略
 */
route()->post('/strategy/edit/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {
    /** @var \Offers\Service\StrategyService $strategyService */
    $strategyService = service(\Offers\Service\StrategyService::class);
    $strategy = $strategyService->getBaseInfoById($id);
    if (!$strategy['data']) {
        throw new \Exception("策略不存在");
    }
    $posts = $request->getParsedBody();
    $name = $posts['name'] ?? '';
    $description = $posts['description'] ?? '';
    $priority = $posts['priority'] ?? 0;
    $client = $posts['client'] ?? null;
    $geo2 = $posts['geo2'] ?? null;
    $geo3 = $posts['geo3'] ?? null;
    $startAt = $posts['start_at'] ?? null;
    $endAt = $posts['end_at'] ?? null;
    $name = trim($name);
    if (!$name) {
        throw new \Exception("名称不能为空");
    }
    $description = trim($description);
    $priority = intval($priority);
    $strategyService->baseUpdate($id, [
        'name' => $name,
        'description' => $description,
        'priority' => $priority,
        'client' => $client,
        'geo2' => $geo2,
        'geo3' => $geo3,
        'start_at' => $startAt,
        'end_at' => $endAt,
    ]);
    flash()->success("更新策略成功");
    return json($response, []);
});

/**
 * post /strategy/delete
 * 删除策略
 */
route()->post('/strategy/delete/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {
    /** @var \Offers\Service\StrategyService $strategyService */
    $strategyService = service(\Offers\Service\StrategyService::class);
    $strategyService->removeStrategyById($id);
    flash()->success("删除策略成功");
    return json($response, []);
});


/**
 * post /strategy/add-ext
 * 添加策略配置
 */
route()->post('/strategy/add-ext', function (ServerRequestInterface $request, ResponseInterface $response) {
    /** @var \Offers\Service\StrategyService $strategyService */
    $strategyService = service(\Offers\Service\StrategyService::class);
    $posts = $request->getParsedBody();
    $strategyId = $posts['strategy_id'] ?? 0;
    $strategyId = trim($strategyId);
    $category = $posts['category'] ?? '';
    $category = trim($category);
    $type = $posts['type'] ?? '';
    $type = trim($type);
    $value1 = $posts['value1'] ?? '';
    $value1 = trim($value1);
    $value2 = $posts['value2'] ?? '';
    $value2 = trim($value2);

    if (!$strategyId) {
        throw new \Exception("策略ID不能为空");
    }
    if (!$category) {
        throw new \Exception("分类不能为空");
    }
    if (!$type) {
        throw new \Exception("类型");
    }
    if (!$value1) {
        throw new \Exception("值1不能为空");
    }
    if ($category == 'datetime' && !$value2) {
        throw new \Exception("值2不能为空");
    }
    $strategyService->extCreate([
        'strategy_id' => $strategyId,
        'category' => $category,
        'type' => $type,
        'value1' => $value1,
        'value2' => $value2,
    ]);
    flash()->success("添加策略配置成功");
    return json($response, []);
});

/**
 * post /strategy/delete-ext
 * 删除策略配置
 */
route()->post('/strategy/delete-ext/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {
    /** @var \Offers\Service\StrategyService $strategyService */
    $strategyService = service(\Offers\Service\StrategyService::class);
    $strategyService->removeStrategyExtById($id);
    flash()->success("删除策略配置成功");
    return json($response, []);
});