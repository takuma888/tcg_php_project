<?php

namespace Offers\Utils;

class Curl
{

    public $url;

    /**
     * @param $url
     * @param array $params
     * @param int $timeout
     * @return bool|string
     * @throws \Exception
     */
    public function postRequest($url, array $params = [], $timeout = 0)
    {
        $this->url = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_REFERER, 'http://XXX');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

        if ($timeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, intval($timeout));
        }

        $res = curl_exec($ch);
        $err = curl_errno($ch);
        if ($err) {
            throw new \Exception(curl_error($ch), $err);
        }
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($res, $header_size);
        curl_close($ch);
        return $body;
    }

    /**
     * @param $url
     * @param array $params
     * @param int $timeout
     * @return bool|string
     * @throws \Exception
     */
    public function getRequest($url, array $params = [], $timeout = 0)
    {
        if ($params) {
            $url .= (strpos($url, '?') === false) ? '?' : '&';
            $url .= http_build_query($params);
        }
        $this->url = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_REFERER, 'http://XXX');

        if ($timeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT, intval($timeout));
        }

        $res = curl_exec($ch);
        $err = curl_errno($ch);

        if ($err) {
            throw new \Exception(curl_error($ch), $err);
        }
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($res, $header_size);
        curl_close($ch);
        return $body;
    }
}