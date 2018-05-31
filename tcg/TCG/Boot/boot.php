<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/5/31
 * Time: 上午11:50
 */

/**
 * @param string $sRoot
 */
function bootEnv($sRoot)
{
    $sFileName = 'env.php';
    $sFilePath = $sRoot . '/' . $sFileName;
    if (realpath($sFilePath)) {
        require $sFilePath;
    }
}


function bootRoute($sRoot)
{
    $sFileName = 'route.php';
    $sFilePath = $sRoot . '/' . $sFileName;
    if (realpath($sFilePath)) {
        require $sFilePath;
    }
}

