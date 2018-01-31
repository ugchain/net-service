<?php
/**
 * 微信红包分享、领取等渲染H5页面的控制器
 * @auther: <gengxiankun@ugchain.com>
 * @time: 2018年1月3号 下午8:20
 */

namespace api\modules\redpacket\controllers;

use Yii;
use yii\web\Controller;
use yii\base\InvalidParamException;
use api\modules\redpacket\models\RedPacket;

class WeChatRedPacketController extends Controller
{
    /**
     * @var string
     * 通过code换取的是一个特殊的网页授权access_token,与基础支持中的access_token（该access_token用于调用其他接口）不同
     * 尤其注意：由于公众号的secret和获取到的access_token安全级别都非常高，必须只保存在服务器，不允许传给客户端。后续刷新access_token、通过access_token获取用户信息等步骤，也必须从服务器发起。
     * 获取code后，请求以下链接获取access_token：  https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code
     */
    const WECHAT_ACCESS_TOKE_URL = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code";

    /**
     * @var string
     * 如果网页授权作用域为snsapi_userinfo，则此时开发者可以通过access_token和openid拉取用户信息了。
     * http：GET（请使用https协议） https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN
     */
    const WECHAT_USER_INFO_URL = "https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN";

    public $enableCsrfValidation = false;

    /**
     * @var string
     * 微信公众号的唯一标识
     */
    static public $_appid;

    /**
     * @var string
     * 微信公众号的appsecret
     */
    static public $_secret;

    public $layout = false;

    public function actionShare() {
        //微信授权认证返回code码
        $code = Yii::$app->request->get("code", "011RsZ8l0zZsOk1u0s7l037I8l0RsZ8w");
        $redpacketId = Yii::$app->request->get("redpacket_id", "1");

        if (empty($code)) {
            //TODO 授权失败！未获得code码
        }

        if (empty($redpacketId)) {
            //TODO 未获取到redpacketId
        }

        $wechatAccessTokenUrl = sprintf(self::WECHAT_ACCESS_TOKE_URL, $this->appid, $this->secret, $code);
        $accessTokenData = $this->useGetRequestUrl($wechatAccessTokenUrl);
        if (empty($accessTokenData->openid)) {
            //TODO 获取openid失败
        }
        $redpacketInfo = RedPacket::getRedPacketInfoWithRecordList($redpacketId);
echo "<pre>";var_dump($redpacketInfo);exit;
        return $this->render('share', [
            'redpacketInfo' => $redpacketInfo
        ]);
    }

    /**
     * 使用GET方式请求URL，默认20s超时。私有方法，只支持在类内调用。
     *
     * @param $url 请求路由
     * @param string $responseFormat 响应资源格式(根据响应格式转换响应数据，入json格式则转换成object)
     * @param string $timeOut 超时时间，单位秒，默认20s
     * @return mixed 返回转换响应格式的响应资源
     */
    private function useGetRequestUrl($url, $responseFormat = 'json', $timeOut = '20') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        $response = curl_exec($ch);
        curl_close($ch);

        if (empty($response)) {
            return null;
        }

        switch ($responseFormat) {
            case 'json':
                $response = json_decode($response);
                break;
            default:
                //TODO 暂不支持此格式
                break;
        }
        return $response;
    }

    /**
     * [getter]获取微信公众号的唯一标识
     *
     * @return string
     */
    public function getAppid() {
        if (empty(self::$_appid)) {
            if (($appid = Yii::$app->params['wecat_redpacket_config']['appid']) == false) throw new InvalidParamException("wechat appid does not exist.");
            self::$_appid = $appid;
        }

        return self::$_appid;
    }

    /**
     * [getter]获取微信公众号的appsecret
     *
     * @return string
     */
    public function getSecret() {
        if (empty(self::$_secret)) {
            if (($secret = Yii::$app->params['wecat_redpacket_config']['secret']) == false) throw new InvalidParamException("wechat secret does not exist.");
            self::$_secret = $secret;
        }

        return self::$_secret;
    }
}
/* end of file for WeChatRedPacketController */