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

	// 发送邮件
	var file;
	$('#jungle_email_attachment').on('change', function () {
		// 获取上传的文件
		if (this.files[0].size > 1 * 1024 * 1024) {
			alert('File size exceeds 1MB limit');
		} else {
			file = this.files[0];
		}
	});
	function email_success() {
		alert('邮件发送成功')
		$(".dialog_content").css('animation-name', 'flipOutY').css('visibility', 'hieden');
		setTimeout(function () {
			$('.dialog_popup_Email').addClass('hidden')
		}, 2000)
		$('#jungle_email_name').val('')
		$('#jungle_email_account').val('')
		$('#jungle_email_phone').val('')
		$('#jungle_email_subject').val('')
		$('#jungle_email_message').val('')
		$('#jungle_email_attachment').val('')

	}
	function email_error(e) {
		var obj = e.responseJSON.data.params
		var str = Object.keys(obj).map(key => {
			return obj[key]
		}).join(',')
		alert(str);
	}
	$('#sendEmail').click(function () {
		var name = $('#jungle_email_name').val(),
			account = $('#jungle_email_account').val(),
			phone = $('#jungle_email_phone').val(),
			message = $('#jungle_email_message').val(),
			subject = $('#jungle_email_subject').val();


		if ($.trim(file) != '') {
			const formData = new FormData();
			formData.append('action', 'upload_file');  // 对应后端的 'wp_ajax_upload_file'
			formData.append('file', file);
			jQuery.ajax({
				url: '/wp-admin/admin-ajax.php',  // WordPress Ajax 处理 URL
				type: 'POST',
				data: formData,
				contentType: false,
				processData: false,
				success: function (response) {
					$.post('/wp-json/info/email/senda', {
						to_email: account,
						to_name: name,
						subject: subject,
						message: message,
						phone: phone,
						attachment: response.data,
					})
						.then(function (response) {
							email_success()
						})
						.catch(function (error) {
							email_error(error);
						});
				}
			});
		} else {
			$.post('/wp-json/info/email/senda', {
				to_email: account,
				to_name: name,
				subject: subject,
				message: message,
				phone: phone,
			})
				.then(function (response) {
					email_success()
				})
				.catch(function (error) {
					email_error(error);
				});
		}
	})

})(jQuery);
