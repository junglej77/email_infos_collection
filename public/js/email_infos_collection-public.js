(function ($) {
	'use strict';
	// 询盘邮件发送
	$('.get-quote').on('click', function () {
		$('.dialog_popup_Email').removeClass('hidden')
		$(".dialog_content").css('animation-name', 'flipInY').css('visibility', 'visible');
	})
	$('.dialog_close').on('click', function () {
		$(".dialog_content").css('animation-name', 'flipOutY').css('visibility', 'hieden');
		setTimeout(function () {
			$('.dialog_popup_Email').addClass('hidden')
		}, 2000)
	})
	$('#sendEmail').click(function () {
		$.post('/wp-json/info/email/senda', {
			to_email: 'jiangjungle7@gmail.com',
			to_name: '爱你哟',
			// to_email: '553805001@qq.com',
			// to_name: '死妖精',
			subject: 'test',
			body: '我日你asshole',
		})
			.then(function (response) {
				console.log(response);
			})
			.catch(function (error) {
				console.log(error);
			});
	})

})(jQuery);
