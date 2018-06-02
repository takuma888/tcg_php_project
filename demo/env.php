<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/5/31
 * Time: 下午3:32
 */

use Pimple\Container;

$container = container(ENV_DEFAULT);
if (!$container) {
    $container = new Container();
    app(ENV_DEFAULT, $container);
}

// twig
$container['twig.engine.config.cache'] = false;
$container['twig.engine.config.debug'] = true;
$container['twig.loader.filesystem_loader'] = function () {
    return new \Twig_Loader_Filesystem();
};
$container['twig.extension.string_loader'] = function () {
    return new \Twig_Extension_StringLoader();
};
$container['twig'] = function (Container $c) {
    $engine = new \Twig_Environment($c['twig.loader.filesystem_loader'], [
        'cache' => $c['twig.engine.config.cache'],
        'debug' => $c['twig.engine.config.debug']
    ]);
    $engine->addExtension($c['twig.extension.string_loader']);
    return $engine;
};


