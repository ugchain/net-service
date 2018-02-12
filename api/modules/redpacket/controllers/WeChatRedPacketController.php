<?php
/**
 * 微信红包分享、领取等渲染H5页面的控制器
 * @auther: <gengxiankun@ugchain.com>
 * @time: 2018年1月3号 下午8:20
 */

namespace api\modules\redpacket\controllers;

use common\helpers\RewardData;
use Yii;
use yii\db\Exception;
use yii\web\Controller;
use yii\base\InvalidParamException;
use common\helpers\OutputHelper;
use api\modules\redpacket\models\RedPacket;
use api\modules\redpacket\models\RedPacketRecord;

class WeChatRedPacketController extends Controller
{
    /**
     * @var string
     * 在确保微信公众账号拥有授权作用域（scope参数）的权限的前提下（服务号获得高级接口后，默认拥有scope参数中的snsapi_base和snsapi_userinfo），引导关注者打开如下页面：
     * https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect 若提示“该链接无法访问”，请检查参数是否填写错误，是否拥有scope参数对应的授权作用域权限。
     */
    const WECHAT_REDIRECT_URL = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";

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

    /**
     * @var string
     * 微信引导关注调转回的路由
     */
    static public $_redirect_uri;

    /**
     * @var bool
     * 取代页面原来的头部和尾部
     */
    public $layout = false;

    /**
     * @var bool
     * 关闭csrf验证
     */
    public $enableCsrfValidation = false;

    public $maxRepeatTimes = 2;

    /**
     * 分享微信红包的url
     * @return string
     */
    public function actionRedirectUrl()
    {
        $redpacketId = Yii::$app->request->get("redpacket_id", "");
        $redirect_uri = urlencode("$this->redirect_uri?redpacket_id=$redpacketId");
        $wechatRedirectUrl = sprintf(self::WECHAT_REDIRECT_URL, $this->appid, $redirect_uri);
        header("Location: $wechatRedirectUrl");
    }

    /**
     * 微信红包分享
     */
    public function actionShare()
    {
        //微信授权认证返回code码
        $code = Yii::$app->request->get("code", "");
        $redpacketId = Yii::$app->request->get("redpacket_id", "");

        if (!$redpacketId) {
            outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
        }

        //重新跳转用户权限页面
        if (!$code) {
            return $this->redirect("redirect-url?redpacket_id=$redpacketId");
        }

        //获取当前微信用户的access token和openid
        $wechatAccessTokenUrl = sprintf(self::WECHAT_ACCESS_TOKE_URL, $this->appid, $this->secret, $code);
        $accessTokenData = $this->useGetRequestUrl($wechatAccessTokenUrl);
        if (empty($accessTokenData->openid)) {
            if ($accessTokenData->errcode) {
                return $this->redirect("redirect-url?redpacket_id=$redpacketId");
            }
        }

        //获取当前微信用户的头像、昵称
        $wechatUserInfoUrl = sprintf(self::WECHAT_USER_INFO_URL, $accessTokenData->access_token, $accessTokenData->openid);
        $userInfoData = $this->useGetRequestUrl($wechatUserInfoUrl);

        //获取当前红包的详细信息
        $redpacketInfo = RedPacket::getRedPacketInfoWithRecordList($redpacketId, false);
        if (!$redpacketInfo)  return $this->render('does-not-exist');


        //获取当前用户的红包状态、红包口令
        $recordInfo = RedPacketRecord::getRedPacketRecordInfo($redpacketInfo['id'], $userInfoData->openid);

        //渲染页面
        return $this->render('share', [
            'redpacketInfo' => $redpacketInfo,
            'state' => !empty($recordInfo['state']) ? $recordInfo['state'] : 0,
            'record_code' => $recordInfo['code'],
            'record_amount' => $recordInfo['amount'],
            'openid' => $userInfoData->openid,
            'nickname' => $userInfoData->nickname,
            'headimgurl' => $userInfoData->headimgurl,
            //'jsApiConfig' => @Yii::$app->wechat->jsApiConfig(['jsApiList' => ['onMenuShareTimeline']])
        ]);
    }

    /**
     * 领取一个红包
     */
    public function actionGradARedpacket()
    {
        //开启事务
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model = new RedPacketRecord();
            $model->openid = Yii::$app->request->post('openid', '');
            $model->rid = Yii::$app->request->post('rid', '');
            $model->wx_name = Yii::$app->request->post('nickname', '');
            $model->wx_avatar = Yii::$app->request->post('headimgurl', '');

            //验证参数
            if(!($model->openid && $model->rid && $model->wx_name && $model->wx_avatar)){
                throw new Exception(\common\helpers\ErrorCodes::PARAM_NOT_EXIST);
            }

            //redis锁
            $redis = \Yii::$app->redis;
            $lock = "redpacket:lock:$model->rid";
            $repeatTimes = 0;
            //`gote repeat;`，重复执行抢红包
            repeat:
            //抢占当前红包的redis锁
            if ($redis->set($lock, 1, "nx", "ex", 5)) {
                $model->setInfoWithRedpacket();
                $model->grenerateRedpacketCode();
                $model->addtime = time();
                $model->wx_name = $this->filterEmoji($model->wx_name);
                $model->save();
                $transaction->commit();
                //解开当前红包的redis锁
                $redis->del($lock);
            }else {
                //超过最大重复数则提示信息给用户
                if ($repeatTimes >= $this->maxRepeatTimes) {
                    throw new Exception(\common\helpers\ErrorCodes::RED_PACKET_REPEAT_MAX);
                }
                //等待两秒重复抢当前红包
                sleep(2);
                $repeatTimes += 1;
                goto repeat;
            }
        } catch (Exception $e){
            //解开当前红包的redis锁
            $redis->del($lock);
            $transaction->rollback();
            outputHelper::ouputErrorcodeJson($e->getMessage());
        }

        outputHelper::ouputErrorcodeJson(\common\helpers\ErrorCodes::SUCCESS, ['code' => $model->code]);
    }

    /**
     * 使用GET方式请求URL，默认20s超时。私有方法，只支持在类内调用。
     *
     * @param $url 请求路由
     * @param string $responseFormat 响应资源格式(根据响应格式转换响应数据，入json格式则转换成object)
     * @param string $timeOut 超时时间，单位秒，默认20s
     * @return mixed 返回转换响应格式的响应资源
     */
    private function useGetRequestUrl($url, $responseFormat = 'json', $timeOut = '20')
    {
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
        if (empty(self::$_appid))
        {
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
    public function getSecret()
    {
        if (empty(self::$_secret)) {
            if (($secret = Yii::$app->params['wecat_redpacket_config']['secret']) == false) throw new InvalidParamException("wechat secret does not exist.");
            self::$_secret = $secret;
        }

        return self::$_secret;
    }

    /**
     * [getter]获取微信引导关注页面回跳的uri
     *
     * @return string
     */
    public function getRedirect_uri()
    {
        if (empty(self::$_redirect_uri)) {
            if (($redirect_uri = Yii::$app->params['wecat_redpacket_config']['redirect_uri']) == false) throw new InvalidParamException("wechat redirect_uri does not exist.");
            self::$_redirect_uri = $redirect_uri;
        }

        return self::$_redirect_uri;
    }

    /**
     * 过滤emoji和颜文字
     *
     * @param $str
     * @return mixed
     */
    private function filterEmoji($str)
    {
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);
        return $str;
    }
}
/* end of file for WeChatRedPacketController */