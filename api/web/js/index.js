$(function(){
	// 复制
	var clipboard = new Clipboard('.btn');
	clipboard.on('success', function(e) {
	    $.toast("复制成功", "text");
	});

	clipboard.on('error', function(e) {
	    $.toast("复制失败", "text");
	});

	var clipboard2 = new Clipboard('.btn2');
	clipboard2.on('success', function(e) {
		// var num = 3
		$.toast("复制成功", "text");
		setTimeout(function() {
			window.location.href = 'http://download.ugchain.com/ugcApp/'
		}, 1000)  

		// time = setInterval(function() {
		// 	$.toast("复制成功，" + num +"s后即将为您跳转", "text");
  	//        num--;
	 //        if(num == 0){          
	 //            clearInterval(time);
	 //        }
		// }, 1000);
		// setTimeout(function() {
		// 	window.location.href = 'http://download.ugchain.com/ugcApp/'
		// }, 3000)  
	})

	clipboard2.on('error', function(e) {
	    $.toast("复制失败", "text");
	})

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
			"background-size": '100% 100%',
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


		$('#packet-close').one('click', function() {
	    	$('#get-packet-btn').css({
				'background':'rgba(255,255,255,.3)'
			})
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
	                }else {
	                	$.toast(data.message, "text");
	                }
	            },
	            error: function() {
	            	$('#get-packet-btn').css({
						'background':'#fcd588'
					})
	            	$.toast("请求超时", "text");
	            }
	        })
	    });	

		// 获取口令 传入微信用户信息
		$('#get-packet-btn').one('click',function(){
			$('#get-packet-btn').css({
				'background':'rgba(255,255,255,.3)'
			})
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
	                }else {
	                	$.toast(data.message, "text");
	                }
	            },
	            error: function() {
	            	$('#get-packet-btn').css({
						'background':'#fcd588'
					})
	            	$.toast("请求超时", "text");
	            }
	        })
		})

	}else if(state == 1){
		// 已领取
		$('.received').show().css('opacity','1')
		// $('.top').css('background-size', '100% 70%');
		$('.finished').hide()
		$('#packet-open').hide()
			.siblings('.get-ugc').hide()
			.siblings('#packet-close').show()

		$('.state-info').show().css({
			'font-size':'21px',
			'color': '#fff'
		}).text('恭喜您抢到一个UGC红包').show()
	}else if(state == 2){
		// 已兑换
		$('.top').css('background-size','100% 102%')
		$('.exchanged').show()
		$('.received').hide()
		$('#packet-close').hide()
		// $('#packet-open').show().css('margin-top','-45px')
		 $('#packet-open').show()
		$('.state-info').show().css('margin-top','-40px')
		$('.get-ugc').show()
	}else if(state == 3){
		// 已领光
		$('.top').css('background-size','100% 101%')
		// $('.finished').show()
		$('.received').hide()
		$('#packet-open').hide()
			.siblings('.get-ugc').hide()
			.siblings('#packet-close').show()
		$('.state-info').show()
		$('.state-info').find('.state-tips')
			.show()
			.text('红包已领光')
			.siblings('').hide()
	}else if(state == 4){
		// 已结束
		// var str = '于'+ finish_time +'结束'
		$('.top').css('background-size', '100% 102%');
		// $('.finished').show()
		$('.received').hide()
		$('#packet-open').hide()
			.siblings('.get-ugc').hide()
			.siblings('#packet-close').show()
		$('.state-info').show().css({
				'padding-top': '10px'
			}).find('.state-time').siblings().hide()
			// })
		$('.state-info').find('.state-tips')
			.show()
			.text('红包已结束')
	}else {
		state = 0
		return false
	}


	// 模拟 加载领取详情
	var recond_height = $('.weui-panel__bd').height()
	if(recond_height > 780 || recond_height== 780){
		$('.weui-panel:after').css({
			'border-bottom': '1px solid #e5e5e5'
		})
		$('.weui-panel__bd').css({
			'height': '780px',
			'overflow': 'hidden'
		})
		$('.weui-loadmore').show().find('.weui-loading').hide()
		$('.weui-loadmore').on('click', function() {
			$('.weui-loadmore__tips').text("正在加载")
			$('.weui-loading').show()
			setTimeout(function() {
				$('.weui-panel__bd').css({
					'height': 'auto',
				})
				$('.weui-loadmore').hide()
			}, 1000)
		});
	}else{
		$('.weui-panel__bd').css({
			'height': 'auto',
			'overflow': 'visible'
		})
		$('.weui-loadmore').hide()
	}
	
	


})