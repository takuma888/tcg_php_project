<?php
/**
 * Created by PhpStorm.
 * User: liuliwu
 * Date: 2018/7/6
 * Time: 下午5:51
 */

namespace Offers\Service;

class OfferApiService
{

    public function export2api($id, $source, $info)
    {
        switch ($source) {
            case OFFERS_SOURCE_MOBI_SUMMER:
                return $this->apiMobiSummer($id, $source, $info);
                break;
            case OFFERS_SOURCE_PUB_NATIVE:
                return $this->apiPubNative($id, $source, $info);
                break;
            case OFFERS_SOURCE_SOLO:
                return $this->apiSolo($id, $source, $info);
                break;
            case OFFERS_SOURCE_MOBI_SMARTER:
                return $this->apiMobiSmarter($id, $source, $info);
                break;
            case OFFERS_SOURCE_INPLAYABLE:
                return $this->apiInplayable($id, $source, $info);
                break;
            default:
                throw new \Exception("unknown offer source!");
                break;
        }
    }


    private function apiMobiSummer($id, $source, array $info)
    {
        $return = [
            'id' => $id,
            'ad_name' => $source,
            'config' => [],
        ];
        // 转化 url
        $trackingLink = $info['tracking_link'];
        $result = parse_url($trackingLink);
        $query = [];
        if (isset($result['query'])) {
            parse_str($result['query'], $query);
        }
        $query['aff_sub'] = $id;
        $query['aff_sub2'] = $id . '-' . time(); // click id
        if (isset($query['device_id'])) {
            unset($query['device_id']);
        }
        $url = $result['scheme'] . '://' . $result['host'] . (isset($result['port']) ? ':' . $result['port'] : '') . $result['path'] . '?';
        $gets = [];
        foreach ($query as $key => $value) {
            $gets[] = "{$key}={$value}";
        }
        $url .= implode('&', $gets);
        $url = str_replace('{gaid}', 'x-x-x', $url);
        $return['config']['tracking_url'] = $url;
        return $return;
    }


    private function apiPubNative($id, $source, array $info)
    {
        $return = [
            'id' => $id,
            'ad_name' => $source,
            'config' => [],
        ];
        // 转化 url
        $clickUrl = $info['campaigns'][0]['click_url'];
        $result = parse_url($clickUrl);
        $query = [];
        if (isset($result['query'])) {
            parse_str($result['query'], $query);
        }
        $query['gid'] = 'x-x-x';
        $query['aff_sub'] = $id;
        $query['aff_sub2'] = $id . '-' . time(); // click id
        $url = $result['scheme'] . '://' . $result['host'] . (isset($result['port']) ? ':' . $result['port'] : '') . $result['path'] . '?';
        $gets = [];
        foreach ($query as $key => $value) {
            $gets[] = "{$key}={$value}";
        }
        $url .= implode('&', $gets);
        $url = str_replace('{gaid}', 'x-x-x', $url);
        $return['config']['tracking_url'] = $url;
        return $return;
    }


    private function apiSolo($id, $source, array $info)
    {
        $return = [
            'id' => $id,
            'ad_name' => $source,
            'config' => [],
        ];
        $trackingLink = $info['tracking_link'];
        $result = parse_url($trackingLink);
        $query = [];
        if (isset($result['query'])) {
            parse_str($result['query'], $query);
        }
        $query['sub_1'] = $id . '-' . time(); // click id
        $query['sub_2'] = $id; // id
        $url = $result['scheme'] . '://' . $result['host'] . (isset($result['port']) ? ':' . $result['port'] : '') . $result['path'] . '?';
        $gets = [];
        foreach ($query as $key => $value) {
            $gets[] = "{$key}={$value}";
        }
        $url .= implode('&', $gets);
        $url = str_replace('{gaid}', 'x-x-x', $url);
        $return['config']['tracking_url'] = $url;
        return $return;
    }


    private function apiMobiSmarter($id, $source, array $info)
    {
        $return = [
            'id' => $id,
            'ad_name' => $source,
            'config' => [],
        ];
        // 转化 url
        $clickUrl = $info['click_url'];
        $result = parse_url($clickUrl);
        $query = [];
        if (isset($result['query'])) {
            parse_str($result['query'], $query);
        }
        $query['aff_sub'] = $id . '-' . time(); // click id
        $query['aff_sub2'] = $id; // id
        if (isset($query['channel'])) {
            unset($query['channel']);
        }
        if (isset($query['idfa'])) {
            unset($query['idfa']);
        }
        if (isset($query['android'])) {
            unset($query['android']);
        }
        $url = $result['scheme'] . '://' . $result['host'] . (isset($result['port']) ? ':' . $result['port'] : '') . $result['path'] . '?';
        $gets = [];
        foreach ($query as $key => $value) {
            $gets[] = "{$key}={$value}";
        }
        $url .= implode('&', $gets);
        $url = str_replace('{gaid}', 'x-x-x', $url);
        $return['config']['tracking_url'] = $url;
        return $return;
    }


    /**
     * @param $id
     * @param $source
     * @param array $info
     * @return array
     */
    private function apiInplayable($id, $source, array $info)
    {
        $return = [
            'id' => $id,
            'ad_name' => $source,
            'config' => [],
        ];
        // 转化 url
        $clickUrl = $info['click_url'];
        $result = parse_url($clickUrl);
        $query = [];
        if (isset($result['query'])) {
            parse_str($result['query'], $query);
        }
        if (isset($query['idfa'])) {
            unset($query['idfa']);
        }
        if (isset($query['android'])) {
            unset($query['android']);
        }
        if (isset($query['channel'])) {
            unset($query['channel']);
        }
        $query['aff_sub'] = $id . '-' . time(); // click id
        $query['aff_sub2'] = $id; // id
        $url = $result['scheme'] . '://' . $result['host'] . (isset($result['port']) ? ':' . $result['port'] : '') . $result['path'] . '?';
        $gets = [];
        foreach ($query as $key => $value) {
            $gets[] = "{$key}={$value}";
        }
        $url .= implode('&', $gets);
        $url = str_replace('{gaid}', 'x-x-x', $url);
        $return['config']['tracking_url'] = $url;
        return $return;
    }
}