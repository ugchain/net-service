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
		$('.received').css({
			'height': '0',
			"padding-bottom": '0',
			"padding-top": '0'
		})

		$('.packet-pic-open').hide()
			.siblings('.get-ugc').hide()
			.siblings('.packet-pic').show()
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

	}

	// 已领取
	if(state == 1){
		$('.received').show().css('opacity','1')
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
		$('.received').hide()
	}

	// 已领光
	if(state == 3){
		$('.finished').show()
		$('.received').hide()
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
		$('.received').hide()
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

	// 模拟 加载领取详情
	setTimeout(function() {
		
	}, 1500); 


	// 配置sdk
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