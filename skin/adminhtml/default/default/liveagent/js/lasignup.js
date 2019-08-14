var apikey = "jx5imiBhB6K12zui03YJL0lumHOr7S5T";
var productid = "b229622b";
var languageCode = 'en-US';
var redirectAfterSignupToNewWindow = true;

var valid_sub = false;
var valid_email = false;
var valid_name = false;
var last_valid_sub = "";
var last_valid_email = "";
var token = false;
var timer;
var ccnotrequired = true;
var stripesAnim;
var button_text;
var sending = false;
var requestData = null;
var formKey = '';

jQuery(function($) {
	function stripesAnimate() {
		animating();
		stripesAnim = setInterval(animating, 2500);
	}
	
	function animating() {
		$('.progress-stripes').animate({
			marginLeft: "-=30px"
		}, 2500, "linear").append('/');
	} 

	var translateMe = function(phrase) {
	  if (typeof translations == "undefined") return phrase;
	  if(translations.hasOwnProperty(phrase)) return translations[phrase];
	  return phrase; 
	}

	var setProgress = function(progress) {
		if (progress == null) {
			progress = 0;
		}
		$('.progress-bar').width(progress + "%");
		if(progress <= 33) {
			$('.loader-label').text(translateMe("Installing..."));
		} else if(progress <= 66) {
			$('.loader-label').text(translateMe("Launching..."));
		} else {
			$('.loader-label').text(translateMe("Coffee time!"));
		}
		$('.percentage').text(progress + "%");
	}

	var loading = function(account_id) {
		var check_url = "https://signup.ladesk.com/api/accounts/" + account_id + "/installprogress?apikey=" + apikey;
		$.ajax({
		type: "GET",
		url: check_url,
		success: function(my_data, textStatus, request) {
			console.log(my_data.response);
				if (my_data.response.account_status != 'a') {
					setProgress(my_data.response.progress);
					setTimeout(function() {loading(account_id);}, 700);
				} else {
					$('.progress-bar').width("100%");
					$('.loader-label').text(translateMe("Redirecting..."));
					$('.percentage').text("100%");
					redirectForm = '<form method="POST" action="' + $('#continue').val() + 
					'"><input type="hidden" name="la-full-name" value="' + requestData.customer_name + 
					'"><input type="hidden" name="la-owner-email" value="' + requestData.customer_email + 
					'"><input type="hidden" name="la-url" value="' + requestData.subdomain + 
					'"><input type="hidden" name="apiKey" value="' + requestData.initial_api_key + 
					'"><input type="hidden" name="AuthToken" value="' + my_data.response.login_token +
					'"><input type="hidden" name="action" value="r"/><input type="hidden" name="form_key" value="' + formKey + '"/></form>';
					$(redirectForm).appendTo('body').submit();
				}
		},
		error: function(data, textStatus, errorThrown) {
			console.log(data.responseJSON.response.errormessage);
		},
		dataType: "json"
		});
	}

	var hideSingupForm = function(response) {
	  //ga('send', 'event', 'LA SignUp', $('#plan').val());
	  $('#signup').hide(300);
		if (response.cloud_type == "S") {
		  $('#loader').show(300);
			stripesAnimate(); 
			loading(response.account_id, requestData);
		} else {
			$('#completed').removeClass("invisible");
			$('#completed').addClass("visible");
			$('#completed').addClass("mainBox");
		}
	}

	var doSignUpRequest = function() {
		var check_url = "https://signup.ladesk.com/api/accounts?apikey=" + apikey;
		var extra = {};
		if (typeof language_custom != 'undefined') {
			languageCode = language_custom;
		}
		var data = {
			variation_id: $('#variation').val(),
			customer_email: $('#mailFieldinnerWidget').val(),
			customer_name: $('#nameFieldinnerWidget').val(),
			subdomain: $('#domainFieldinnerWidget').val(),
			location_id: "magento",
			language: languageCode,
			apikey: apikey,
			initial_api_key: randomString(),
			extra_params: JSON.stringify(extra)
		};
		$.ajax({
			type: "POST",
			url: check_url,
			data: data,
			success: function(my_data, textStatus, request) {
				console.log(my_data.response);
				requestData = data;
				formKey = $('#form_key').val();
				hideSingupForm(my_data.response);
			},
			error: function (data, textStatus, errorThrown) {
				console.log(data.responseJSON.response.errormessage);
				$('#createButtonmain').removeAttr("disabled");
				$('#createButtontextSpan').html(translateMe('Start now'));
			  sending = false;
			},
			dataType: "json"
		});
	}

	$.fn.setButton = function(buttonId) {
		var res = $("textarea#" + buttonId).val();
		$("textarea#la-config-button-code").val(res);
		$("#buttonId").val(buttonId);
		replacePlaceholder();
		$("#configForm").submit();
	};

	$.fn.getIframePreviewCode = function(id) {
		$('#iFramePreview' + id).contents().find("body").html($('#iFrame' + id).val());
	}

	$.fn.addError = function(message) {
		this.removeClass("g-FormField2-Error");
		this.removeClass("g-FormField2-Waiting");
		this.removeClass("g-FormField2-Valid");
		this.addClass("g-FormField2-Error");  
		$(this).find('.g-FormField2-Message').html(message);
	}

	$.fn.addValidating = function(message) {
		this.removeClass("g-FormField2-Error");
		this.removeClass("g-FormField2-Waiting");
		this.removeClass("g-FormField2-Valid");
		this.addClass("g-FormField2-Waiting");
		$(this).find('.g-FormField2-Message').html(message);
	}

	$.fn.addOk = function(message) {
		this.removeClass("g-FormField2-Error");
		this.removeClass("g-FormField2-Waiting");
		this.removeClass("g-FormField2-Valid");
		this.addClass("g-FormField2-Valid");
		$(this).find('.g-FormField2-Message').html(message);
	}

	$.fn.removeError = function() {
		this.removeClass("g-FormField2-Error");
		this.removeClass("g-FormField2-Waiting");
		this.removeClass("g-FormField2-Valid");
		$(this).find('.g-FormField2-Message').html("");
	}

	var sendForm = function() {
		if ((valid_sub) && (valid_email) && (valid_name) && (ccnotrequired||token)) {
			doSignUpRequest();
		} else {
			if(!$('#mailFieldmain input').val()) {
			  $('#mailFieldmain').addError(translateMe("Your email goes here"));
			}
			if(!$('#nameFieldmain input').val()) {
			  $('#nameFieldmain').addError(translateMe("Fill in your name"));
			}
			if(!$('#domainFieldmain input').val()) {
			  $('#domainFieldmain').addError(translateMe("Choose a unique name"));
			}
			$('#createButtonmain').removeAttr("disabled");
			$('#createButtontextSpan').html(translateMe(button_text));
			sending = false;
		}
	};

	$.fn.validateName = function() {
		if(!$('#nameFieldmain input').val()) {
			valid_name = false;
		  $('#nameFieldmain').addError(translateMe("Fill in your name"));
		} else {
		  valid_name = true;
		  $('#nameFieldmain').addOk("");
		}
	}

	$.fn.validateEmail = function() {
		var email= encodeURIComponent($('#mailFieldmain input').val());
		var check_url = "https://signup.ladesk.com/api/signupcheck/email?apikey=" + apikey + "&productid=" + productid + "&email=" + email;
		var my_data;
		valid_email = false;
		$('#mailFieldmain').addValidating(translateMe("Validating email..."));
		$.ajax({
		url: check_url,
		data: my_data,
		success: function(my_data, textStatus, request) {
			console.log(my_data.response);
		if (my_data.response.is_valid == "true") {
			$('#mailFieldmain').addOk(translateMe("Email OK."));
			valid_email = true;
			last_valid_email = email;
		} else {
			$('#mailFieldmain').addError(translateMe("Yikes! This email is already in use"));
		}
		},
		error: function (my_data, textStatus, errorThrown) {
			$('#mailFieldmain').addError(translateMe("Whoops, enter a valid email"));
			console.log(my_data.responseJSON.response.errormessage);
		},
		dataType: "json"
		});
	}

	$.fn.validateDomain = function() {
		var subdomain = $('#domainFieldmain input').val();
		if (subdomain == '') {return true;}
		var check_url = "https://signup.ladesk.com/api/signupcheck/domain?apikey=" + apikey + "&productid=" + productid + "&subdomain=" + subdomain;
		var my_data;
		valid_sub = false;
		$('#domainFieldmain').addValidating(translateMe("Validating domain..."));
		$.ajax({
		url: check_url,
		data: my_data,
		dataType: 'json',
		success: function(my_data) {
			console.log(my_data.response);
			$('#domainFieldmain').addError(translateMe(my_data.response.error));
			if (!my_data.response.error) {
				$('#domainFieldmain').addOk(translateMe("Domain is valid"));
				valid_sub = true;
				last_valid_sub = subdomain;
			}
		},
		error: function (my_data, textStatus, errorThrown) {
			$('#domainFieldmain').addError(translateMe("Problem with domain. Contact our support, please."));
			console.log(my_data.responseJSON.response.errormessage);
		},
		})
	}

	var randomString = function() {
		var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
		var string_length = 32;
		var randomstring = '';
		for (var i=0; i < string_length; i++) {
			var rnum = Math.floor(Math.random() * chars.length);
			randomstring += chars.substring(rnum, rnum + 1);
		}
		return randomstring;
	}
	
	var replacePlaceholder = function() {
		var widgetCode = $('textarea#' + $('#buttonId').val() ).val();
		var pos = widgetCode.indexOf("function(e){") + 13;
		var result = '';
		if ($('#configOptionName').is(':checked')) {
			result += "%%firstName%%%%lastName%%";
		}
		if ($('#configOptionEmail').is(':checked')) {
			result += "%%email%%";
		}
		if ($('#configOptionPhone').is(':checked')) {
			result += "%%phone%%";
		}
		/*if ($('#configOptionOrder').is(':checked')) {
			result += "%%order%%";
		}*/
		$('textarea#la-config-button-code').val([widgetCode.slice(0, pos), result, widgetCode.slice(pos)].join(''));	
		$('.SaveWidgetCode').show();
	}

	if (typeof productid == 'undefined') {
		productid = default_productid;
	}
	
	$('#domainFieldmain input').alphanum({
		allow              : '-0123456789',    	// Allow extra characters
		disallow           : '',    			// Disallow extra characters
		allowSpace         : false,  			// Allow the space character
		allowNumeric       : true,  			// Allow digits 0-9
		allowUpper         : true,  			// Allow upper case characters
		allowLower         : true,  			// Allow lower case characters
		allowCaseless      : false,  			// Allow characters that don't have both upper & lower variants - eg Arabic or Chinese
		allowLatin         : true,  			// a-z A-Z
		allowOtherCharSets : false,  			// eg Ã©, Ã, Arabic, Chinese etc
		forceLower         : true
	});
	
	$('#domainFieldmain').on('keyup', function() {
  	clearInterval(timer);
  	timer = setTimeout(function() {
     	$.fn.validateDomain();
  	}, 500);
	});
	
	$('textarea#la-config-button-code').on('change', function() {
		$('.SaveWidgetCode').show();
	});
	
	$('#domainFieldmain').on('change', function() {
		if(!$('#domainFieldmain input').val()) {
			$('#domainFieldmain').addError(translateMe("Choose a unique name"));
  	} else {
    	if($('#domainFieldmain input').val() != last_valid_sub) {
    		$.fn.validateDomain();
    	}
    }
	});
	
	$("#mailFieldmain").on('keyup', function() {
    $('#mailFieldmain').removeError();
  	clearInterval(timer);
  	timer = setTimeout(function() {
    	$.fn.validateEmail();
   	}, 500);
	});
	
	$('#mailFieldmain').focusout(function() {
    $('#mailFieldmain').removeError();
  	if(!$('#mailFieldmain input').val()) {
		$('#mailFieldmain').addError(translateMe("Your email goes here"));
    } else {
    	if($('#mailFieldmain input').val() != last_valid_email) {
    		$.fn.validateEmail();
    	}
    }
	});
	
	$('#mailFieldmain').on('change', function() {
    $('#mailFieldmain').removeError();
  	if(!$('#mailFieldmain input').val()) {
      $('#mailFieldmain').addError(translateMe("Your email goes here"));
    } else {
      $.fn.validateEmail();
    }
	});
	
	$('#nameFieldmain').focusout(function() {
		$.fn.validateName();
	});
	
	$('#nameFieldmain').on('change', function() {
		$.fn.validateName();
	});
	
	$('#createButtontextSpan').click(function() {
    if(!sending) {
		sending = true;
		$(this).attr("disabled", "disabled"); // Disable the submit button to prevent repeated clicks
		button_text = $('#createButtontextSpan').html();
		$('#createButtontextSpan').html(translateMe('Creating...'));
		sendForm();
    }
    return false;
	});
	
	$('.configOptions input').change(function () {
		replacePlaceholder();
	});
});

jQuery(function($) {
    $.fn.ghostInput = function(options) {
        var o = $.extend({
            ghostText: ".domain.com",
            ghostPlaceholder: "Add subdomain",
            ghostTextClass: "domain"
        }, options);
        return this.each(function(i, element) {
            var $element = $(element);
            if($element.ghostInputValidate)
                return true;
            $element.ghostInputValidate = true;
            var r = $element.attr("id") || "";
            o.ghostText = $element.attr("data-ghost-text") || o.ghostText;
            o.ghosttextspan = $("<label />").text("");
            o.ghostHider = $("<label />").css({"visibility":"hidden"});
            o.ghostBox = $("<label />").attr("for", r).addClass(o.ghostTextClass).append(o.ghostHider).append(o.ghosttextspan);
            $element.parent().prepend(o.ghostBox);
            $element.bind("keyup keydown keypress change",function() {
                setTimeout(function() {
                    var t = "" == $.trim($element.val()) ? "": o.ghostText;
                    o.ghostHider.text($element.val());
                    o.ghosttextspan.text(t)
                }, 0)
            });
            o.ghostBox.bind("click",function() {
                $element.focus();
            });
            return true;
        });
    };
    $("#domainFieldinnerWidget").ghostInput();
	// Placeholdem( document.querySelectorAll( '.Placeholdem' ) );
});