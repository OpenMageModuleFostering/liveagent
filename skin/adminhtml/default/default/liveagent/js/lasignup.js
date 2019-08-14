jQuery(function($) {(function(e){var t=function(e){return e.replace(/([a-z])([a-z]+)/gi,function(e,t,n){return t+n.toLowerCase()}).replace(/_/g,"")},n=function(e){return e.replace(/^([a-z]+)_TO_([a-z]+)/i,function(e,t,n){return n+"_TO_"+t})},r=function(e){return e?e.ownerDocument.defaultView||e.ownerDocument.parentWindow:window},i=function(t,n){var r=e.Range.current(t).clone(),i=e.Range(t).select(t);if(!r.overlaps(i)){return null}if(r.compare("START_TO_START",i)<1){startPos=0;r.move("START_TO_START",i)}else{fromElementToCurrent=i.clone();fromElementToCurrent.move("END_TO_START",r);startPos=fromElementToCurrent.toString().length}if(r.compare("END_TO_END",i)>=0){endPos=i.toString().length}else{endPos=startPos+r.toString().length}return{start:startPos,end:endPos}},s=function(t){var n=r(t);if(t.selectionStart!==undefined){if(document.activeElement&&document.activeElement!=t&&t.selectionStart==t.selectionEnd&&t.selectionStart==0){return{start:t.value.length,end:t.value.length}}return{start:t.selectionStart,end:t.selectionEnd}}else if(n.getSelection){return i(t,n)}else{try{if(t.nodeName.toLowerCase()=="input"){var s=r(t).document.selection.createRange(),o=t.createTextRange();o.setEndPoint("EndToStart",s);var u=o.text.length;return{start:u,end:u+s.text.length}}else{var a=i(t,n);if(!a){return a}var f=e.Range.current().clone(),l=f.clone().collapse().range,c=f.clone().collapse(false).range;l.moveStart("character",-1);c.moveStart("character",-1);if(a.startPos!=0&&l.text==""){a.startPos+=2}if(a.endPos!=0&&c.text==""){a.endPos+=2}return a}}catch(h){return{start:t.value.length,end:t.value.length}}}},o=function(e,t,n){var i=r(e);if(e.setSelectionRange){if(n===undefined){e.focus();e.setSelectionRange(t,t)}else{e.select();e.selectionStart=t;e.selectionEnd=n}}else if(e.createTextRange){var s=e.createTextRange();s.moveStart("character",t);n=n||t;s.moveEnd("character",n-e.value.length);s.select()}else if(i.getSelection){var o=i.document,u=i.getSelection(),f=o.createRange(),l=[t,n!==undefined?n:t];a([e],l);f.setStart(l[0].el,l[0].count);f.setEnd(l[1].el,l[1].count);u.removeAllRanges();u.addRange(f)}else if(i.document.body.createTextRange){var f=document.body.createTextRange();f.moveToElementText(e);f.collapse();f.moveStart("character",t);f.moveEnd("character",n!==undefined?n:t);f.select()}},u=function(e,t,n,r){if(typeof n[0]==="number"&&n[0]<t){n[0]={el:r,count:n[0]-e}}if(typeof n[1]==="number"&&n[1]<=t){n[1]={el:r,count:n[1]-e};}},a=function(e,t,n){var r,i;n=n||0;for(var s=0;e[s];s++){r=e[s];if(r.nodeType===3||r.nodeType===4){i=n;n+=r.nodeValue.length;u(i,n,t,r)}else if(r.nodeType!==8){n=a(r.childNodes,t,n)}}return n};jQuery.fn.selection=function(e,t){if(e!==undefined){return this.each(function(){o(this,e,t)})}else{return s(this[0])}};e.fn.selection.getCharElement=a})(jQuery)
	});
	
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
					// no original redirect ...
					/*redirectForm = '<form method="POST" action="' + my_data.response.login_url + '"><input type="hidden" name="action" value="login"><input type="hidden" name="AuthToken" value="' + my_data.response.login_token + '"><input type="hidden" name="l" value="' + languageCode + '"></form>';
					$(redirectForm).appendTo('body').submit();*/
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
			location_id: "qu_signup",
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
				//$('<img height="1" width="1" src="//www.googleadservices.com/pagead/conversion/966671101/imp.gif?label=ER6zCKjv_1cQ_fX4zAM&amp;guid=ON&amp;script=0" />').appendTo('#signup');
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
		$("#configForm").submit();
	};

	$.fn.getIframePreviewCode = function(id) {
		/*var iframe = document.createElement('iframe');
		iframe.src = ;
		//alert (encodeURI($('#iFrame' + id).val()));
		iframe.appendTo($('#iFramePreview' + id).html());
		//$('#iFramePreview' + id).html(iframe.contentDocument);*/
		//var iframe = $('<iframe />').attr('src', 'data:text/html;charset=utf-8,' + encodeURI($('#iFrame' + id).val())).appendTo($('#iFramePreview' + id));
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
	
	/*	$('#domainFieldmain').focusout(function() {
  	if(!$('#domainFieldmain input').val()) {
      $('#domainFieldmain').addError("Choose a unique name");
  	}
	else {
    	$.fn.validateDomain()
  	}
	});*/
	
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