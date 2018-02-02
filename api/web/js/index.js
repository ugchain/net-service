// 复制
var clipboard = new Clipboard('.btn');
clipboard.on('success', function(e) {
    $.toast("复制成功", "text");
});

clipboard.on('error', function(e) {
    $.toast("复制失败", "text");
});

// mask
function mask(){
	$('.mask').show()
		.find('.close')
		.on('click', function() {
			$('.mask').hide()
		})
}

// 未领取
if(state == 0){
	$('.tie').css({
		'margin-top': '-30px'
	})
	$('.top').css({
		"background": "url('/img/unreceived.png') no-repeat",
		"background-size": '100%',
	})
	$('.unreceived').show()

	$('.packet-pic-open').hide()
		.siblings('.get-ugc').hide()
		.siblings('.packet-pic').show()
	$('.state-info').hide()


	$('#get-packet-btn').on('click',function(){
        $.ajax({
            url: 'grad-a-redpacket',
            type: 'post',
            dataType: 'json',
            data: {
                rid: rid,
                openid: openid,
                nickname: nickname,
                headimgurl: headimgurl
            },
            success: function(data){
                if(data.code == 0){
                	mask()
					$('.received').show().css('opacity','0')
					$('#kl-txt').val(data.data.code)
					$('.btn').click()
                }
            },
            error: function() {
            	$.toast("您的网络有问题", "text");
            }
        })
	})

}

// 已领取
if(state == 1){
	$('.received').show()
	$('.packet-pic-open').hide()
		.siblings('.get-ugc').hide()
		.siblings('.packet-pic').show()

	$('.state-info').css({
		'font-size':'21px',
		'color': '#fff'
	}).text('恭喜您抢到一个UGC红包').show()
}


// 已兑换
if(state == 2){
	$('.exchanged').show()
}

// 已领光
if(state == 3){
	$('.finished').show()
	$('.packet-pic-open').hide()
		.siblings('.get-ugc').hide()
		.siblings('.packet-pic').show()
	$('.state-info').show()
	$('.state-info').find('.state-tips')
		.show()
		.text('红包已领光')
		.siblings('').hide()
}

// 已结束
if(state == 4){
	$('.finished').show()
	$('.packet-pic-open').hide()
		.siblings('.get-ugc').hide()
		.siblings('.packet-pic').show()
	$('.state-info').css({
			'margin-top': '10px'
		}).find('.state-time')
			.text('于1-25 16:32 结束')
			.siblings().hide()
	$('.state-info').find('.state-tips')
		.show()
		.text('红包已结束')
}

// 滚动加载
var loading = false;  
var str =  '<a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg">'
	str += '<div class="weui-media-box__hd">'
	str += '<img class="weui-media-box__thumb" src="">'
	str += '</div>' 
	str +=	' <div class="weui-media-box__bd">'      
	str +=	' <h4 class="weui-media-box__title">标题</h4>'        
	str +=	' <p class="weui-media-box__desc">'        
	str +=  ' 我是加载出来的。'
	str +=  '</p>'
	str +=	'</div>'     
	str +=	'</a>'    

// $('body').infinite().on("infinite", function() {
// 	if(loading) return;
// 	loading = true;

// 	// $.ajax({
// 	// 	url: '/path/to/file',
// 	// 	type: 'post',
// 	// 	dataType: 'json',
// 	// 	data: {
// 	// 		param1: 'value1',
// 	// 		param1: 'value1',
// 	// 	},
// 	// 	success: function(){
// 	// 		$(".weui-panel__bd").append(str);
// 	// 		loading = false;
// 	// 	},
// 	// 	error(function() {
// 	// 		loading = false;
// 	// 		$('.weui-loadmore__tips').text('加载失败')
// 	// 	})
// 	// })

// 	// 模拟
// 	setTimeout(function() {
// 	$(".weui-panel__bd").append(str);
// 	loading = false;
// 	}, 1500); 
// });