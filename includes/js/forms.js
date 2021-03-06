onecrmFormsInit = function() {

	var $ = jQuery;

	$('.ocrmf-form').each(function(i,f) {
		var form = this;
		var $form = $(form);
		var id = form.id.split('-').join('_');

		var captchaId;
		var invisible;


		var callback = function(value) {
			if (typeof captchaId === 'undefined' || !invisible)
				return;
			performSubmit();
		}

		var performSubmit = function() {
			$form.find('input.ocrmf-captcha').val(
				(typeof captchaId !== 'undefined') ? grecaptcha.getResponse(captchaId) : ''
			);
			$(form).ajaxSubmit({
				beforeSubmit: function(arr, $form, options) {
					$form.find('.ocrmf-input.submit').each(function(j, b) {
						$(this).attr('revert_value', this.value);
						if ($(this).attr('wait_value'))
							this.value = $(this).attr('wait_value');
						this.disabled = "disabled";
					});
				},
				success: function(data, status, xhr, $form) {
					if (typeof captchaId !== 'undefined')
						grecaptcha.reset(captchaId);
					var id = $form.find('[name="_ocrmf_id"]').val();
					var messages = eval('_ocrmf_messages_' + id);
					$(".ocrmf-form-status").empty().removeAttr('role').hide();
					$form.find('.ocrmf-invalid-field').remove();
					var have_errors = false;
					if (data.errors) {
						have_errors = true;
						for (var i in data.errors) {
							var elt = $form.find('[name="' + i + '"]')
							var err = data.errors[i];
							var span = document.createElement('span');
							span.setAttribute('role', 'alert');
							span.setAttribute('class', 'ocrmf-invalid-field');
							if (messages[err])
								$(span).append(messages[err]);
							else
								$(span).append(err);
							elt.after(span);
						}
					}
					if (typeof(data.http_result) == 'object' && data.http_result) {
						if (data.http_result.error) {
							$(".ocrmf-form-status").empty().attr('role', 'alert').append(data.http_result.error).show();
							have_errors = true;
						}
					}
					if (!have_errors) {
						if (typeof captchaId !== 'undefined')
							grecaptcha.reset(captchaId);
						var redirect = null;
						var message = messages.success;
						$form.resetForm().clearForm();
						if (data.redirect)
							redirect = data.redirect;
						if (typeof(data.http_result) == 'object' && data.http_result) {
							if (data.http_result.redirect)
								redirect = data.http_result.redirect;
							if (data.http_result.message)
								message = data.http_result.message;
						}
						$(".ocrmf-form-status").append(message).show();
						if (redirect)
							window.location.href = redirect;
					}

					$form.find('.ocrmf-input.submit').each(function(j, b) {
						this.value = $(this).attr('revert_value');
						this.disabled = "";
					});
				},
				data : {
					_ocrmf_ajax: '1',
				},
				dataType: 'json',
				error: function(xhr, status, error, $form) {
					if (typeof captchaId !== 'undefined')
						grecaptcha.reset(captchaId);
					var id = $form.find('[name="_ocrmf_id"]').val();
					var messages = eval('_ocrmf_messages_' + id);
					$(".ocrmf-form-status").empty().attr('role', 'alert').append(messages.error_posting).show();
					$form.find('.ocrmf-input.submit').each(function(j, b) {
						this.value = $(this).attr('revert_value');
						this.disabled = "";
					});
				}
			});

		}

		$(form).find('.recaptcha').first().each(function(i, e) {
				captchaId = grecaptcha.render(e,{
					callback: callback
				});
				invisible = this.getAttribute('data-size') === 'invisible';
		});


		$(form).on('submit', function() {
			if (typeof captchaId !== 'undefined' && invisible) {
				grecaptcha.execute(captchaId);
			} else {
				performSubmit();
			}
			return false;
		});
	});


};

var po = document.createElement('script');
po.type = 'text/javascript';
po.async = true;
po.src = 'https://www.google.com/recaptcha/api.js?onload=onecrmFormsInit&render=explicit';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(po, s);

