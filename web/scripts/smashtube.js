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
	FORGOT_PASSWORD_TEMPLATE: undefined,
	SEARCH_XHR: undefined,

	init: function()
	{
		this.initLogin();
		this.initLogout();
		this.initForgotPassword();
		this.initSearchEvents();
	},

	toggleView: function(type)
	{
		this.hideAllSections();

		if (type == "search-section")
		{
			var searchString = $("#smashtube-nav-search").val();
			var appendString = " Keine Suchkriterien angegeben";

			if (searchString != "")
				appendString = " Suche nach <b>" + searchString + "</b>";

			$("#smashtube-search-section").html("Hier sind die Suchergebnisse." + appendString);
		}
		
		$("#smashtube-" + type).attr("data-shown", "1")
	},

	hideAllSections: function()
	{
		$(".smashtube-content-group").attr("data-shown", "0");
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
				SmashTube.bindCheckLoginFormEvent();
				SmashTube.initForgotPasswordEvent();
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

			$("#smashtube-reset-securequestion-select").selectpicker();

			$("#smashtube-reset").modal("show");


			$("#smashtube-reset").off("shown.bs.modal").on("shown.bs.modal", function()
			{
				$("#smashtube-reset-submit").off(SmashTube.CLICK).on(SmashTube.CLICK, function()
				{
					var usernameFilledIn = SmashTube.checkResetFilledIn("#smashtube-reset-username");
					var answerFilledIn = SmashTube.checkResetFilledIn("#smashtube-reset-securequestion-answer");
					var newPasswordFilledIn = SmashTube.checkResetFilledIn("#smashtube-reset-new-password");
					var newPasswordRepeatFilledIn = SmashTube.checkResetFilledIn("#smashtube-reset-new-password-repeat");

					var questionPicked = $("#smashtube-reset-securequestion-select").val() != "" ? true : false;
					
					var newPasswordsMatch = true;

					if (!questionPicked)
						$("[data-id='smashtube-reset-securequestion-select']").effect("shake");

					if ($('#smashtube-reset-new-password').val() != $('#smashtube-reset-new-password-repeat').val())
					{
						newPasswordsMatch = false;

						$('#smashtube-reset-new-password').effect("shake");
						$('#smashtube-reset-new-password-repeat').effect("shake");
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

								alert("Ihr Passwort wurde erfolgreich zurückgesetzt.");

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

	initSearchEvents: function()
	{
		$("#smashtube-nav-search").off("keyup").on("keyup", function(event)
		{
			var key = String.fromCharCode(event.keyCode);

			if (/[a-zA-Z0-9-_ ]/.test(key))
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
					cw("ERROR! BETTER CHECK YOSELF!");
				});	
			}
		});
	},

	checkResetFilledIn: function(selector)
	{
		var retVal = true;

		if ($(selector).val() == "")
		{
			retVal = false;
			$(selector).effect("shake");	
		}

		return retVal;
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
});