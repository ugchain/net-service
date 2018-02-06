<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>红包领取</title>
    <link rel="stylesheet" href="/css/weui.min.css">
    <link rel="stylesheet" href="/css/jquery-weui.min.css">
    <link rel="stylesheet" type="text/css" href="/css/index.css">
    <script src="/js/vconsole.min.js"></script>
    <script src="/js/spine-widget.js"></script>
    <script>
        var vConsole = new VConsole();
    </script>
</head>
<body>
    <!-- 头部红包 -->
    <div class="top">
        <h1 class="packet-title">"<?= $redpacketInfo['title'];?>"</h1>
        <div class="red-img" >
            <div class="get-ugc" style="display: none;">
                <p id="getugc-num"><?= $record_amount;?></p>
                <p>UGC</p>
            </div>
            <div id="packet-close"> </div>
            <div id="packet-open"></div>

            <script>
                var theme_id = '<?= $redpacketInfo['theme_id'];?>'
                console.log(theme_id);
                var packet_name 
                if(theme_id ==1 ){
                    packet_name = 'putong'
                }else if(theme_id == 2 ){
                    packet_name = 'xinnian'
                }else if(theme_id == 3 ){
                    packet_name = 'yuhaihai'
                }else if(theme_id == 4 ){
                    packet_name = 'jiucai'
                }else if(theme_id == 5 ){
                    packet_name = 'baoerye'
                }else if(theme_id == 6 ){
                    packet_name = 'yifeichongtian'
                }
                spineWidget = new spine.SpineWidget("packet-close", {
                    json: "/resource/" + packet_name +'/'+ packet_name +".json",
                    atlas: "/resource/"+ packet_name +'/'+ packet_name  +".atlas",
                    animation: 'Close',
                    backgroundColor: "#00000000",
                    // debug: true,
                });
               
                spineWidget = new spine.SpineWidget("packet-open", {
                    json: "/resource/" + packet_name +'/'+ packet_name +".json",
                    atlas: "/resource/"+ packet_name +'/'+ packet_name  +".atlas",
                    animation: 'Open',
                    backgroundColor: "#00000000",
                    // debug: true,
                });
            </script>
           
            <!-- 提示 -->
            <p class="state-info">
				<span class="state-tips">
					恭喜您抢到
				</span>
                <span class="state-getugc">
    				<?= $record_amount;?>
    			</span>
                <span class="ugc-unit">
    					个UGC
    			</span>
                <span class="state-time">

    			</span>
            </p>


        </div>
        <!-- 未领取 -->
        <div class="unreceived">
            <a href="javascript:;" class="weui-btn weui-btn_default" id="get-packet-btn">
                抢红包
            </a>
            <p class="runtime">
                有效期剩余：
                <span><?=$redpacketInfo['last_time'] ?></span>
            </p>
        </div>

        <!-- 已领取 -->
        <div class="received">
            <p class="kl-title">
                红包口令
            </p>
            <img src="/img/kl-bg.png" class="kl-bg">
            <img src="/img/kl-title.png" class="kl-title-bg">
            <div class="kl">
                <input id="kl-txt" type="text" value="<?=$record_code; ?>" readonly="true">
                <button class="btn" data-clipboard-action="copy" data-clipboard-target="#kl-txt">点击复制</button>
            </div>
            <div class="kl-tips">
                请在 <?=$redpacketInfo['last_time'] ?> 内使用“UGC”应用进入“红包”输入红包口令拆开红包，获得UGC
            </div>
        </div>

        <!-- 已兑换 -->
        <div class="exchanged">

        </div>

        <!-- 已结束 已领光 -->
        <div class="finished">
            <img src="/img/kl-bg.png" class="kl-bg">
            <p>
                发出红包
                <span><?=$redpacketInfo['already_received_quantity'] ?></span>
                <span>/</span>
                <span><?=$redpacketInfo['quantity'] ?></span>
                个
            </p>
            <p>
                发出UGC
                <span><?=$redpacketInfo['already_received_amount'] ?></span>
                <span>/</span>
                <span><?=$redpacketInfo['amount'] ?></span>
            </p>
        </div>
    </div>
    <!-- 领带背景 -->
    <img src="/img/tie.png" class="tie">
    <!-- 领取记录 -->
    <div class="content">
        <div class="weui-panel weui-panel_access">
            <div class="weui-panel__hd">
                <span>已领取</span>
                <span><?=$redpacketInfo['already_received_quantity'] ?>/<?=$redpacketInfo['quantity'] ?></span>
                <span>共</span>
                <span><?=$redpacketInfo['already_received_amount'] ?>/<?=$redpacketInfo['amount'] ?> UGC</span>
            </div>

            <?php if($redpacketInfo['already_received_quantity'] == 0){ ?>
                <p class="no-one">
                    还没有人抢到过这个红包
                </p>
            <?php }else{ ?>
                <!-- 领取记录 -->
                <div class="weui-panel__bd">
                    <?php foreach ($redpacketInfo['redPacketRecordList'] as $redPacketRecord){ ?>
                        <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">
                        <div class="weui-media-box__hd">
                            <img class="weui-media-box__thumb" src="<?=$redPacketRecord['wx_avatar'] ?>">
                        </div>
                        <div class="weui-media-box__bd">
                            <div class="info-l">
                                <p id="username"><?=$redPacketRecord['wx_name'] ?></p>
                                <p class="time">
                                    <?=$redPacketRecord['time'] ?>
                                </p>
                            </div>
                            <div class="info-r" >
                                <p class="get-ugc">
                                    <?php if ($redPacketRecord['status'] == \api\modules\redpacket\models\RedPacketRecord::REDPACKET_RECORD_STATUS_EXCHANGESUCCESS){ ?>
                                        得到
                                        <span><?=$redPacketRecord['amount'] ?></span>
                                        个ugc
                                    <?php }else{ ?>
                                        领取了一个UGC红包
                                    <?php } ?>
                                </p>
                            </div>
                        </div>
                    </a>
                    <?php } ?>

                </div>
                <!-- <div class="weui-panel__ft"></div> -->
            <?php } ?>
        </div>
        <!-- 滚动加载 -->
        <div class="weui-loadmore">
            <i class="weui-loading"></i> 
            <span class="weui-loadmore__tips">加载更多</span>
        </div>
    </div>
    <!-- 底部二维码 -->
    <div class="foot">
        <div class="qr-code">
            <img src="/img/qr-code.png">
        </div>
        <a href="javascript:;" class="download-btn weui-btn weui-btn_default">
            下载“UGC”应用
        </a>
        <p class="download-info">
            进入“红包”输入兑换码拆开红包获得UGC
        </p>
    </div>
    <!-- 弹层 -->
    <div class="mask">
        <div>
            <img src="/img/mask.png" class="mask-img">
            <div class="download-app">
                <img src="/img/logo.png">
                <a href="#">点击下载“UGC”</a>
            </div>
            <button class="btn" data-clipboard-action="copy" data-clipboard-target="#mask-copy">
            </button>
            <input id="mask-copy" type="text" value="test" readonly="true">     
        </div>
        <p><img src="/img/close.png" class="close"></p>
    </div>
</body>
<script src="/js/jquery-3.3.1.min.js"></script>
<script src="/js/jquery-weui.min.js"></script>
<script src="/js/clipboard.min.js"></script>
<script src="/js/index.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script>
    // var state = <?= $state ?>;
    var state = 2;
    var rid = "<?=$redpacketInfo['id'] ?>";
    var openid = "<?=$openid ?>";
    var nickname = "<?=$nickname ?>";
    var headimgurl = "<?=$headimgurl ?>";
    var expire_time = "<?=$redpacketInfo['expire_time']?>"
    var finish_time = '<?=$redpacketInfo['finish_time']?>'
    // 接入wx_sdk
    // wx.config({
    //     debug: true, 
    //     appId: '', 
    //     timestamp: ,
    //     nonceStr: '', 
    //     signature: '',
    //     jsApiList: [
    //         'onMenuShareTimeline',
    //         'onMenuShareAppMessage',
    //     ] 
    // });
</script>
</html>