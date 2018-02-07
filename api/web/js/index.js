$(function(){
	// 复制
	var clipboard = new Clipboard('.btn');
	clipboard.on('success', function(e) {
	    $.toast("复制成功", "text");
	    console.log(e)
	});

	clipboard.on('error', function(e) {
	    $.toast("复制失败", "text");
	    console.log(e)
	});

	console.log(state)

	// mask
	function mask(){
		$('.mask').show()
		.find('.close').on('click', function(){
			$('.mask').hide()
			window.location.reload()
		})
	}

	if(state == 0){
		// 未领取
		$('.tie').css({
			'margin-top': '-30px'
		})
		$('.top').css({
			"background": "url('/img/unreceived.png') no-repeat",
			"background-size": '100%',
		})
		$('.unreceived').show()
		$('.received').css({
			'height': '0',
			"padding-bottom": '0',
			"padding-top": '0'
		})

		$('.packet-open').hide()
			.siblings('.get-ugc').hide()
			.siblings('.packet-close').show()
		$('.state-info').hide()

		// 获取口令 传入微信用户信息
		$('#get-packet-btn').on('click',function(){
	        $.ajax({
	            url: 'grad-a-redpacket',
	            type: 'post',
	            dataType: 'json',
	            data: {
	                rid: rid,
	                openid: openid,
	                nickname: nickname,
	                headimgurl: headimgurl,
	                expire_time: expire_time
	            },
	            success: function(data){
	                if(data.code == 0){
	                	mask()
	                	$('#mask-copy').val(data.data.code)
	                }
	            },
	            error: function() {
	            	$.toast("您的网络有问题", "text");
	            }
	        })
		})
	}else if(state == 1){
		// 已领取
		$('.received').show().css('opacity','1')
		$('#packet-open').hide()
			.siblings('.get-ugc').hide()
			.siblings('#packet-close').show()

		$('.state-info').show().css({
			'font-size':'21px',
			'color': '#fff'
		}).text('恭喜您抢到一个UGC红包').show()
	}else if(state == 2){
		// 已兑换
		$('.exchanged').show()
		$('.received').hide()
		$('#packet-close').hide()
		$('#packet-open').show().css('margin-top','-35px')
		$('.state-info').show().css('margin-top','-40px')
		$('.get-ugc').show()
	}else if(state == 3){
		// 已领光
		$('.finished').show()
		$('.received').hide()
		$('#packet-open').hide()
			.siblings('.get-ugc').hide()
			.siblings('#packet-close').show()
			.css({
				'margin-top': '-80px',
			})
		$('.state-info').show()
		$('.state-info').find('.state-tips')
			.show()
			.text('红包已领光')
			.siblings('').hide()
	}else if(state == 4){
		// 已结束
		var str = '于'+ finish_time +'结束'
		$('.finished').show()
		$('.received').hide()
		$('#packet-open').hide()
			.siblings('.get-ugc').hide()
			.siblings('#packet-close').show()
		$('.state-info').show().css({
				'padding-top': '10px'
			}).find('.state-time')
				.text(str)
				.siblings().hide()
		$('.state-info').find('.state-tips')
			.show()
			.text('红包已结束')
	}else {
		state = 0
		return false
	}

	// 模拟 加载领取详情
	// setTimeout(function() {
	// 	$('.weui-loadmore').on('click', function() {
	// 		$('.weui-loading').show()
	// 		$('.weui-loadmore__tips').text("正在加载")
	// 		$('weui-panel__bd').css({
	// 			'height': 'auto'
	// 		})
	// 	});
	// }, 1500); 

	// 配置微信分享
	// wx.ready(function () {
	// 	wx.onMenuShareTimeline({
	// 	    title: '', 
	// 	    link: '', 
	// 	    imgUrl: '', 
	// 	    success: function () {
		   
	// 		},
	// 		cancel: function () {
			    
	// 		}
	// 	});

	// 	wx.onMenuShareAppMessage({
	// 		title: '', 
	// 		desc: '', 
	// 		link: '', 
	// 		imgUrl: '', 
	// 		type: '', 
	// 		dataUrl: '',
	// 		success: function () {
			
	// 		},
	// 		cancel: function () {
			
	// 		}
	// 	})
	// })

})