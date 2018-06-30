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
    $name = trim($name);
    if (!$name) {
        throw new \Exception("名称不能为空");
    }
    $description = trim($description);
    $priority = intval($priority);
    if ($priority < 0) {
        throw new \Exception("优先级权重不能为负数");
    }
    /** @var \Offers\Service\StrategyService $strategyService */
    $strategyService = service(\Offers\Service\StrategyService::class);
    $strategyService->baseCreate([
        'name' => $name,
        'description' => $description,
        'priority' => $priority
    ]);
    flash()->success("添加策略成功");
    return json($response, []);
});

/**
 * post /strategy/edit
 * 编辑策略
 */
route()->post('/strategy/edit/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {});

/**
 * post /strategy/delete
 * 删除策略
 */
route()->post('/strategy/delete/{id:\d+}', function (ServerRequestInterface $request, ResponseInterface $response, $id) {});