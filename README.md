# [MobPush API for PHP](http://wiki.mob.com/mobpush-rest-api-接口文档/)

![image](https://github.com/MOBX/MOB-SMS-WEBAPI/blob/master/doc/images/logo.png)

**[MobPush API for PHP](http://wiki.mob.com/mobpush-rest-api-接口文档/)** 
为了帮助开发者更方便接入MobPush免费推送SDK，提供完整的API接口的PHP实现，包含设备操作相关接口、推送操作相关接口以及公共接口。

了解更多 [MobPush 免费推送SDK.](http://mobpush.mob.com)


## 优势

**免费使用**、**自定义UI**、**稳定服务**、**流程体验**、**数据同步**、**专业技术团队服务**

## 接口
* 推送接口
	* 发送推送
	* 查询推送（根据batchId）
	* 查询推送（根据workno）
* 推送统计接口
	* 查询推送统计（根据batchId）
	* 查询推送统计（根据workno）
* 别名操作接口
	* 查询别名
	* 设置别名
* 标签操作接口
	* 查询标签
	* 设置标签
* 公共接口
	* 地理位置信息接口	

 
## 使用注意事项
* 初始化appkey、appSecret , 在实例化类时传入: new Push('moba6b6c6d6', '********************************');
* 错误码请参考 
  [MobPush Api 错误码](http://wiki.mob.com/mobpush-rest-api-接口文档/#map-6)

## 使用DEMO 

如下为调用示例

```xml 

/**
 * 调用示例
 * GET Push::getPush($val, $cate)
 * cate：1（根据batchId） 2（根据workno）3查询推送统计（根据batchId）
 * 4查询推送统计（根据workno）5查询别名 6查询标签 7地理位置信息
 *
 * POST Push::postPush($params, $cate)
 * cate：1发送推送 2设置别名 3设置标签
 */
function test($cate = 'get-1')
{
    # 实例化
    $push = new Push('moba6b6c6d6', '********************************');
    switch ($cate) {
        case 'get-1':
            # 查询推送（根据batchId）
            $res = $push->getPush('5a7d35723dde6fd72383a96d ', 1);
            break;
        case 'get-2':
            # 查询推送（根据workno）
            $res = $push->getPush(10011, 2);
            break;
        case 'get-3':
            # 查询推送统计（根据batchId）
            $res = $push->getPush('5a7c5a293dde6fd72383a962', 3);
            break;
        case 'get-4':
            # 查询推送统计（根据workno）
            $res = $push->getPush(10011, 4);
            break;
        case 'get-5':
            # 查询别名（根据registrationId）
            $res = $push->getPush('59e6cf592fb6e6ffa300a8b2', 5);
            break;
        case 'get-6':
            # 查询标签（根据registrationId）
            $res = $push->getPush('59e6cf592fb6e6ffa300a8b2', 6);
            break;
        case 'get-7':
            # 地理位置信息接口（根据parentId）
            $res = $push->getPush(3, 7);
            break;
        case 'post-1':
            # 发送推送
            $params = array(
                'plats' => array(1, 2),
                # 设置推送范围
                'target' => 3, 'content' => '推送咯', 'type' => 1, 'tags' => array('tag1', 'tag2'), 'alias' => array(), 'registrationIds' => array(),
                # 设置Android定制信息
                'androidTitle' => '通知栏标题栏', 'androidstyle' => 0, 'androidVoice' => 1, 'androidShake' => 1, 'androidLight' => 1,
                # 设置iOS定制信息
                'iosProduction' => 1, 'title' => 'iOS Title', 'subtitle' => 'SubTitle', 'sound' => 'default', 'badge' => 1, 'slientPush' => 1, 'contentAvailable' => 1, 'mutableContent' => 1,
                # 设置推送扩展信息
                'unlineTime' => 1, 'extras' => array('key1' => 'val-1', 'key2' => 'val-2'),
            );
            $res = $push->postPush($params, 1);
            break;
        case 'post-2':# 设置别名
            $params = array(
                'registrationId' => '59e6cf592fb6e6ffa300a8b2',
                'alias' => '150210xxxxx',
            );
            $res = $push->postPush($params, 2);
            break;
        case 'post-3':# 设置标签
            $params = array(
                'registrationId' => '59e6cf592fb6e6ffa300a8b2',
                'opType' => 1,
                'tags' => array('tag1', 'tag2')
            );
            $res = $push->postPush($params, 3);
            break;

        default:

            break;
    }
    print_r($res);
}

# 获取参数
$cate = isset($_GET['cate']) ? $_GET['cate'] : 'get-1';

# 测试
test($cate);
```