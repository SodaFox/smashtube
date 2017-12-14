//-------------------------------------------------
//Author: mortner
//Date: 2017-11-23
//Descr.: This is the main js-File for smashtube
//-------------------------------------------------
//Changes:
//-------------------------------------------------
//	- 2017-11-23
//		- MOR: Created file
//		- MOR: Added main namespace
//		- MOR: Added output helper functions
//		- MOR: 
//-------------------------------------------------

SmashTube =
{
	/*GLOBALS:*/
	CLICK: "click touchend",
	MAIN_CONTAINER_SELECTOR: "#smashtube-content",
	LOGIN_POPOVER_TEMPLATE: undefined,
	REGISTER_TEMPLATE: undefined,
	FORGOT_PASSWORD_TEMPLATE: undefined,
	SEARCH_XHR: undefined,
	CONTACT_TEMPLATE: undefined,

	CURRENT_MEDIAELEMENT: undefined,

	__DEBUG_MODE__: true,

	init: function()
	{
		this.initExplore();
		this.initLogin();
		this.initLogout();
		this.initRegister();
		this.initForgotPassword();
		this.initSearchEvents();
		this.initContact();

		if (this.__DEBUG_MODE__)
			this.__initDebugEvents__();
	},

	initExplore: function()
	{
		$(".smashtube-nav-control[data-type='explore']").off(SmashTube.CLICK).on(SmashTube.CLICK, function()
		{
			SmashTube.Splash.show();
			window.location = SmashTube.Url.createUrl("/");	
		})
	},

	initLogin: function()
	{
		$.ajax(
		{
			type: 'GET',
			url: SmashTube.Url.createUrl("/user/security/login")
		}).done(function (data, textStatus, jqXHR)
		{
			SmashTube.LOGIN_POPOVER_TEMPLATE = $(data).find("#loginform");

			$("#smashtube-nav-sign-in").popover(
			{
				title: "Anmelden",
				placement: "bottom",
				html: true,
				container: 'body',
				content: SmashTube.LOGIN_POPOVER_TEMPLATE
			});

			$("#smashtube-nav-sign-in").off("shown.bs.popover").on("shown.bs.popover", function()
			{
				$("[name='_username']").focus();

				SmashTube.bindCheckLoginFormEvent();
				SmashTube.initForgotPasswordEvent();
				SmashTube.initRegisterEvent();
			});
		})
		.fail(function( jqXHR, textStatus, errorThrown )
		{
			ce(errorThrown);
		});
	},

	bindCheckLoginFormEvent: function()
	{
		$("#loginform-submit").off(SmashTube.CLICK).on(SmashTube.CLICK, function()
		{
			var usernameFilledIn = true;
			var passwordFilledIn = true;
			var username = $("[name='_username']").val();
			var password = $("[name='_password']").val();

			if (username == "")
			{
				usernameFilledIn = false;
				$("[name='_username']").parent().effect("shake");
			}

			if (password == "")
			{
				passwordFilledIn = false;
				$("[name='_password']").parent().effect("shake");
			}

			if (usernameFilledIn && passwordFilledIn)
			{
				SmashTube.submitLoginForm();
			}
			else
			{
				SmashTube.Notify.warning("Anmeldung fehlgeschlagen!");
			}
		});
	},

	submitLoginForm: function()
	{
		SmashTube.Splash.show();

		$.ajax(
		{
			type: "POST",
			data: $("#loginform").serialize(),
			url: SmashTube.Url.createUrl("/user/security/login")
		}).done(function (data, textStatus, jqXHR)
		{
			if ($(data).find("[data-has-error]").attr("data-has-error") == "1")
			{
				$("[name='_username']").parent().effect("shake");
				$("[name='_password']").parent().effect("shake");

				$("[name='_username']").val("");
				$("[name='_password']").val("");

				SmashTube.Splash.hide();

				SmashTube.Notify.warning("Anmeldung fehlgeschlagen!");
			}
			else
			{
				//login worked, reload page with logged in user;
				if ($(".smashtube-login-standalone-content").length > 0)
					window.location = SmashTube.Url.createUrl("/");
				else
					window.location.reload();
			}

		})
		.fail(function( jqXHR, textStatus, errorThrown )
		{
			SmashTube.Splash.hide();

			alert(textStatus + ": " + errorThrown);
			ce(errorThrown);
		});
	},

	initLogout: function()
	{
		$("#smashtube-nav-sign-out").off(SmashTube.CLICK).on(SmashTube.CLICK, function()
		{
			SmashTube.Splash.show();

			window.location = SmashTube.Url.createUrl("/user/security/logout");
		});
	},

	initRegister: function()
	{
		$.ajax({
			type: 'GET',
			url: SmashTube.Url.createUrl("/user/security/register")
		}).done(function (data, textStatus, jqXHR)
		{
			SmashTube.REGISTER_TEMPLATE = data;

		}).fail(function( jqXHR, textStatus, errorThrown )
		{
			//TODO: proper error handling
		}); 

		SmashTube.initRegisterEvent();
	},

	initRegisterEvent: function()
	{
		$("#loginform-register").off(SmashTube.CLICK).on(SmashTube.CLICK, function()
		{
			$("body").append(SmashTube.REGISTER_TEMPLATE);

			$("#form_question").selectpicker();

			$("#smashtube-register").modal("show");

			$("#smashtube-register").off("shown.bs.modal").on("shown.bs.modal", function()
			{
				$("#smashtube-register-submit").off(SmashTube.CLICK).on(SmashTube.CLICK, function()
				{
					var usernameFilledIn = SmashTube.checkInputFilledIn("#smashtube-register-username")
					var passwordFilledIn = SmashTube.checkInputFilledIn("#smashtube-register-password")
					var passwordRepeatFilledIn = SmashTube.checkInputFilledIn("#smashtube-register-password-repeat")
					var answerFilledIn = SmashTube.checkInputFilledIn("#smashtube-register-securequestion-answer")

					var questionPicked = $("#form_question").val() != "" ? true : false;

					var newPasswordsMatch = true;

					if ($("#smashtube-register-password").val() != $("#smashtube-register-password-repeat").val())
					{
						newPasswordsMatch = false;

						$("#smashtube-register-password").parent().effect("shake")
						$("#smashtube-register-password-repeat").parent().effect("shake")
					}

					if (!questionPicked) 
						$("[data-id='form_question']").parent().parent().effect("shake");

					if (usernameFilledIn && newPasswordsMatch && passwordFilledIn && passwordRepeatFilledIn && answerFilledIn && questionPicked)
					{
						var registerformData = $("#registerform").serialize();

						SmashTube.Splash.show()

						$.ajax({
							type: 'POST',
							url: SmashTube.Url.createUrl("/user/security/register"),
							data: registerformData
						}).done(function (data, textStatus, jqXHR)
						{
							SmashTube.Splash.hide();

							$("#smashtube-register").modal("hide");


						}).fail(function( jqXHR, textStatus, errorThrown )
						{
							SmashTube.Notify.warning("Das Registrierungsformular ist fehlerhaft ausgefüllt!");
							SmashTube.Splash.hide()
							//TODO: proper error handling
						});
					}		
					else
					{
						SmashTube.Notify.warning("Das Registrierungsformular ist fehlerhaft ausgefüllt!");
					}
				});	
			});

			$("#smashtube-register").off("hidden.bs.modal").on("hidden.bs.modal", function()
			{
				$("#smashtube-register").remove();
			});
		});
	},

	initForgotPassword: function()
	{
		$.ajax({
			type: 'GET',
			url: SmashTube.Url.createUrl("/user/security/reset"),
		}).done(function (data, textStatus, jqXHR)
		{
			SmashTube.FORGOT_PASSWORD_TEMPLATE = data;

		}).fail(function( jqXHR, textStatus, errorThrown )
		{
			//TODO: proper error handling
		});

		SmashTube.initForgotPasswordEvent();
	},

	initForgotPasswordEvent: function()
	{
		$("#loginform-forgot-password").off(SmashTube.CLICK).on(SmashTube.CLICK, function()
		{
			$("body").append(SmashTube.FORGOT_PASSWORD_TEMPLATE);

			$("#form_question").selectpicker();

			$("#smashtube-reset").modal("show");


			$("#smashtube-reset").off("shown.bs.modal").on("shown.bs.modal", function()
			{
				$("#smashtube-reset-submit").off(SmashTube.CLICK).on(SmashTube.CLICK, function()
				{
					var usernameFilledIn = SmashTube.checkInputFilledIn("#smashtube-reset-username");
					var answerFilledIn = SmashTube.checkInputFilledIn("#smashtube-reset-securequestion-answer");
					var newPasswordFilledIn = SmashTube.checkInputFilledIn("#smashtube-reset-new-password");
					var newPasswordRepeatFilledIn = SmashTube.checkInputFilledIn("#smashtube-reset-new-password-repeat");

					var questionPicked = $("#smashtube-reset-securequestion-select").val() != "" ? true : false;
					
					var newPasswordsMatch = true;

					if (!questionPicked)
						$("[data-id='smashtube-reset-securequestion-select']").parent().parent().effect("shake");

					if ($('#smashtube-reset-new-password').val() != $('#smashtube-reset-new-password-repeat').val())
					{
						newPasswordsMatch = false;

						$('#smashtube-reset-new-password').parent().effect("shake");
						$('#smashtube-reset-new-password-repeat').parent().effect("shake");
					}

					if (usernameFilledIn && questionPicked && answerFilledIn && newPasswordFilledIn && newPasswordRepeatFilledIn && newPasswordsMatch)
					{
						var resetFormData = $("#smashtube-reset-password").serialize();

						$.ajax({
							type: 'POST',
							data: resetFormData,
							url: SmashTube.Url.createUrl("/user/security/reset"),
						}).done(function (data, textStatus, jqXHR)
						{
							/*
								I hob reagiert

								Reset hot funktioniert
								*/
								$("#smashtube-reset").modal("hide");

								SmashTube.Notify.success("Ihr Passwort wurde erfolgreich zurückgesetzt.");

							}).fail(function( jqXHR, textStatus, errorThrown )
							{
								//TODO: proper error handling
							});
						}
					});
			});

			$("#smashtube-reset").off("hidden.bs.modal").on("hidden.bs.modal", function()
			{
				$("#smashtube-reset").remove();
			});
		});
	},

	initContact: function()
	{
		$.ajax({
			type: 'GET',
			url: SmashTube.Url.createUrl("/contact"),
		}).done(function (data, textStatus, jqXHR)
		{
			SmashTube.CONTACT_TEMPLATE = data;

		}).fail(function( jqXHR, textStatus, errorThrown )
		{
			//TODO: proper error handling
		});

		SmashTube.initContactEvents();
	},

	initContactEvents: function()
	{
		$("#smashtube-nav-contact").off(SmashTube.CLICK).on(SmashTube.CLICK, function()
		{
			$("body").append(SmashTube.CONTACT_TEMPLATE);

			$("#smashtube-contact").modal("show");


			$("#smashtube-contact").off("shown.bs.modal").on("shown.bs.modal", function()
			{
				$("#smashtube-contact-submit").off(SmashTube.CLICK).on(SmashTube.CLICK, function()
				{
					SmashTube.Splash.show();

					$.ajax({
						type: 'POST',
						url: SmashTube.Url.createUrl("/contact"),
						data: $("#contactform").serialize()
					}).done(function (data, textStatus, jqXHR)
					{
						$("#smashtube-contact").modal("hide");

						SmashTube.Splash.hide();

					}).fail(function( jqXHR, textStatus, errorThrown )
					{
						SmashTube.Notify.warning("Kontaktformular konnte nicht bearbeitet werden!");
						SmashTube.Splash.hide();
						//TODO: proper error handling
					});
				});
			});

			$("#smashtube-contact").off("hidden.bs.modal").on("hidden.bs.modal", function()
			{
				$("#smashtube-contact").remove();
			});

		});
	},

	initSearchEvents: function()
	{
		$("#smashtube-nav-search").off("keyup").on("keyup", function(event)
		{
			var key = String.fromCharCode(event.keyCode);

			if (/[a-zA-Z0-9-_ ]/.test(key) && $("#smashtube-nav-search").val() != "")
			{
				if (SmashTube.SEARCH_XHR)
					SmashTube.SEARCH_XHR.abort();

				var searchString = $("#smashtube-nav-search").val();
				var searchUrl = SmashTube.Url.createUrl("/search") + "?SearchString=" + searchString;

				SmashTube.SEARCH_XHR = $.ajax({
					type: 'GET',
					url: searchUrl,
				}).done(function (data, textStatus, jqXHR)
				{
					cl(data);
				}).fail(function( jqXHR, textStatus, errorThrown )
				{
					if (textStatus != "abort")
						ce("ERROR! BETTER CHECK YOSELF!");
				});	
			}
		});
	},

	checkInputFilledIn: function(selector)
	{
		var retVal = true;

		if ($(selector).val() == "")
		{
			retVal = false;
			$(selector).parent().effect("shake");	
		}

		return retVal;
	},

	__initDebugEvents__: function()
	{
		$(document).off("keydown.toggledemomode").on("keydown.toggledemomode", function(event)
		{
			if (event.originalEvent.shiftKey && event.originalEvent.ctrlKey && event.keyCode == 220)
			{
				$("#smashtube-demo-button").toggle();
				SmashTube.Notify.info("Debugmodus")
			}
		});

		$("#smashtube-demo-button").off(SmashTube.CLICK).on(SmashTube.CLICK, function()
		{
			$.ajax({
				type: 'GET',
				url: SmashTube.Url.createUrl("/debug/webplayer"),
			}).done(function (data, textStatus, jqXHR)
			{
				$("body").append(data);

				SmashTube.Splash.show();

				$("#smashtube-webplayer").modal("show");

				$("#smashtube-webplayer").off("shown.bs.modal").on("shown.bs.modal", function()
				{
					$('#smashtube-player-video').mediaelementplayer(
					{
						success: function(mediaElement, originalNode, instance)
						{
							SmashTube.CURRENT_MEDIAELEMENT = mediaElement;
							SmashTube.Splash.hide();

							$('#smashtube-player-video').bind('playing', function(e)
							{ 
								//Hat schauen angefangen; falls user angemeldet in history speichern
							});

							$('#smashtube-player-video').bind('pause', function(e)
							{ 
								//hat pausiert; falls user angemeldet zeitstempel speichern

								cw("Video paused @ " +SmashTube.CURRENT_MEDIAELEMENT.currentTime + " s");
							});

							$('#smashtube-player-video').bind('ended', function(e)
							{ 
								//FIXME: triggert net
								//Hat fertig; falls user angemeldet al
							});
						}
					});
				});

				$("#smashtube-webplayer").off("hidden.bs.modal").on("hidden.bs.modal", function()
				{
					$(this).remove();	
				});

			}).fail(function( jqXHR, textStatus, errorThrown )
			{
				cw("ERROR! BETTER CHECK YOSELF!");
			});	
		});
	},

	configNotify: function()
	{
		SmashTube.Notify.options.progressBar = true;
		SmashTube.Notify.options.positionClass = "toast-bottom-right";

	}
}

SmashTube.Url = 
{
	getBaseUrl: function()
	{
		var getUrl = window.location;
		return getUrl.protocol + "//" + getUrl.host;
	},

	createUrl: function(url)
	{
		return this.getBaseUrl() + url;
	}
}

SmashTube.Splash = {
	ANIMATION_SPEED: 150,

	show: function()
	{
		$("#smashtube-loader").fadeIn(this.ANIMATION_SPEED);
	},

	hide: function()
	{
		$("#smashtube-loader").fadeOut(this.ANIMATION_SPEED);
	},

	toggle: function()
	{
		$("#smashtube-loader").fadeToggle(this.ANIMATION_SPEED);
	},
}

SmashTube.Notify = toastr;

function cw(text)
{
	console.warn(text);
}

function cl(text)
{
	console.log(text);
}

function ce(text)
{
	console.error(text);
}

function isDefined(object)
{
	return typeof object !== 'undefined'
}

$(document).ready(function(){
	SmashTube.init();
	SmashTube.configNotify();
});