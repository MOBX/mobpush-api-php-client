<?php
/**
 *推送接口
 *  发送推送
 *  查询推送（根据batchId）
 *  查询推送（根据workno）
 * 推送统计接口
 *  查询推送统计（根据batchId）
 *  查询推送统计（根据workno）
 * 别名操作接口
 *  查询别名
 *  设置别名
 * 标签操作接口
 *  查询标签
 *  设置标签
 * 公共接口
 *  地理位置信息接口
 */

class Push
{
    const url = 'http://api.push.mob.com/';

    protected $appkey;
    protected $appsecret;
    protected $header;
    protected $url;

    public function __construct($appkey, $appsecret)
    {
        $this->appkey = $appkey;
        $this->appsecret = $appsecret;
        $header[] = 'key:' . $appkey;
    }

    /**
     * 查询推送
     * type 1（根据batchId） 2（根据workno）3查询推送统计（根据batchId）4查询推送统计（根据workno）5查询别名 6查询标签 7地理位置信息
     */
    public function getPush($val, $cate)
    {
        try {
            switch ($cate) {
                case 1:
                    $path = 'push/id/';
                    break;
                case 2:
                    $path = 'push/workno/';
                    break;
                case 3:
                    $path = 'stats/id/';
                    break;
                case 4:
                    $path = 'stats/workno/';
                    break;
                case 5:
                    $path = 'alias/';
                    break;
                case 6:
                    $path = 'tags/';
                    break;
                case 7:
                    $path = 'area/';
                    break;

                default:
                    $path = 'push/id/';
                    break;
            }
            $url = self::url . $path . $val;
            $header[] = 'key:' . $this->appkey;
            $header[] = 'sign:' . md5($this->appsecret);

            $resp = self::_curlGet($url, $header);
            return $resp;
        } catch (Exception $e) {
            $resp['status'] = $e->getCode();
            $resp['msg'] = $e->getMessage();
            echo json_encode($res);
        }
    }

    /**
     * 查询推送
     * type 1发送推送 2设置别名 3设置标签
     */
    public function postPush($request, $cate)
    {
        try {
            switch ($cate) {
                case 1:
                    $path = 'v2/push';
                    $params = self::_push($request);
                    break;
                case 2:
                    $path = 'alias';
                    $params = self::_alias($request);
                    break;
                case 3:
                    $path = 'tags';
                    $params = self::_tags($request);
                    break;

                default:
                    $path = 'v2/push';
                    $params = self::_push($request);
                    break;
            }
            $url = self::url . $path;
            $header[] = 'key:' . $this->appkey;
            $json = json_encode($params);
            $header[] = 'sign:' . md5($json . $this->appsecret);

            /* 调用服务端接口获取数据 */
            $resp = self::_curlPost($json, $url, $header);
            return $resp;
        } catch (Exception $e) {
            $resp['status'] = $e->getCode();
            $resp['msg'] = $e->getMessage();
            echo json_encode($resp);
        }
    }

    /**
     * 推送参数处理
     * type 1通知 2自定义
     * target 推送范围:1广播；2别名；3标签；4regid；5地理位置;6用户分群
     * plat 1 android 2 ios 如包含ios和android则为[1,2]
     * 数组 plats tags alias registrationIds androidContent
     * taskTime 格式 Y-m-d H:i:s
     */
    public static function _push($request)
    {
        if (!isset($request['plats']) || !isset($request['target']) || !isset($request['content']) || !isset($request['type'])) {
            throw new Exception('params is not required', 5001);
        }
        /* 必传参数 */
        $requireArr = array('content', 'plats', 'target', 'type');
        $params = self::_valid($request, $requireArr);
        /* 需传数组 */
        $arr = array('plats', 'tags', 'alias', 'registrationIds', 'androidContent');
        foreach ($arr as $item) {
            if (isset($request[$item]) && !is_array($request[$item])) {
                throw new Exception($item . ' is not array', 5002);
            }
        }

        /* 选传参数 */
        # 设置推送范围
        if (isset($request['tags'])) {
            $params['tags'] = $request['tags'];
        }
        if (isset($request['alias'])) {
            $params['alias'] = $request['alias'];
        }
        if (isset($request['registrationIds'])) {
            $params['registrationIds'] = $request['registrationIds'];
        }
        if (isset($request['city'])) {
            $params['city'] = $request['city'];
        }
        if (isset($request['block'])) {
            $params['block'] = $request['block'];
        }
        # 设置Android定制信息
        if (isset($request['androidTitle'])) {
            $params['androidTitle'] = $request['androidTitle'];
        }
        if (isset($request['androidstyle'])) {
            $params['androidstyle'] = $request['androidstyle'];
        }
        if (isset($request['androidContent'])) {
            $params['androidContent'] = $request['androidContent'];
        }
        if (in_array(1, $params['plats'])) {
            $params['androidVoice'] = $request['androidVoice'];
            $params['androidShake'] = $request['androidShake'];
            $params['androidLight'] = $request['androidLight'];
        }
        if (isset($request['androidstyle'])) {
            $params['androidstyle'] = $request['androidstyle'];
        }
        # 设置iOS 定制信息
        if (isset($request['iosProduction'])) {
            $params['iosProduction'] = $request['iosProduction'];
        }
        if (isset($request['title'])) {
            $params['iosTitle'] = $request['title'];
        }
        if (isset($request['subtitle'])) {
            $params['iosSubtitle'] = $request['subtitle'];
        }
        if (isset($request['sound'])) {
            $params['iosSound'] = $request['sound'];
        }
        if (isset($request['badge'])) {
            $params['iosBadge'] = $request['badge'];
        }
        if (isset($request['category'])) {
            $params['iosCategory'] = $request['category'];
        }
        if (isset($request['slientPush'])) {
            $params['iosSlientPush'] = $request['slientPush'];
        }
        if (isset($request['contentAvailable'])) {
            $params['iosContentAvailable'] = $request['contentAvailable'];
        }
        if (isset($request['mutableContent'])) {
            $params['iosMutableContent'] = $request['mutableContent'];
        }
        # 设置推送扩展信息
        if (isset($request['unlineTime'])) {
            $params['unlineTime'] = $request['unlineTime'];
        }
        if (isset($request['extras'])) {
            $params['extras'] = json_encode($request['extras']);
        }
        return $params;
    }

    /**
     * 设备别名参数处理
     */
    public static function _alias($request)
    {
        $requireArr = array('alias', 'registrationId');
        return self::_valid($request, $requireArr);
    }

    /**
     * 设备标签参数处理
     * opType 1新增标签；2删除标签;3清除所有标签
     */
    public static function _tags($request)
    {
        # 必传参数
        $requireArr = array('opType', 'registrationId');
        $params = self::_valid($request, $requireArr);

        # 选传参数
        if (isset($request['tags'])) {
            if (is_array($request['tags'])) {
                $params['tags'] = $request['tags'];
            } else {
                throw new Exception('tags is not array', -1);
            }
        }
        return $params;
    }

    /**
     * CURL 请求
     * @param $jsonStr
     * @param $url
     * @param $header
     * @return mixed
     */
    public static function _curlPost($jsonStr, $url, $header)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        $httpHeader = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($jsonStr)
        );
        if (count($header) > 0) {
            $header = array_merge($header, $httpHeader);
        } else {
            $header = $httpHeader;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * 校验必传参数
     */
    public static function _valid($request, $arr) {
        foreach ($arr as $item) {
            if (!isset($request[$item])) {
                throw new Exception($item . ' is null', -1);
            } else {
                $params[$item] = $request[$item];
            }
        }
        return $params;
    }

    /**
     * 发送curlGet请求
     * @param $url
     * @param $header
     * @return mixed
     */
    public static function _curlGet($url, $header = array())
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $httpHeader = array('Content-Type: application/json; charset=utf-8');
        $header = count($header) > 0 ? array_merge($header, $httpHeader) : $httpHeader;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $response = substr($response, $header_size);

        curl_close($curl);
        return $response;
    }
}
