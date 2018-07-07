<?php

namespace Offers\Utils;


class Ip2Country
{
    /**
     * @param $ip
     * @return mixed
     * @throws \Exception
     */
    public function getCountry($ip)
    {
        /** @var Curl $curl */
        $curl = env(ENV_OFFERS)->get('offers.utils.curl');
        $json = $curl->getRequest('http://ip.taobao.com/service/getIpInfo.php', [
            'ip' => $ip,
        ]);
        $result = json_decode($json, true);
        return $result['data']['country_id'];
    }
}