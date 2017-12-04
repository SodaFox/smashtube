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

	init: function()
	{
		this.initEvents();
		this.initLogin();
		this.initLogout();
	},

	initEvents: function()
	{
		$(".smashtube-nav-control").off(this.CLICK).on(this.CLICK, function()
		{
			var type = $(this).attr("data-type");

			SmashTube.toggleView(type);
		});
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
			url: SmashTube.Url.createUrl("/login")
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
			var username = $("[name='username']").val();
			var password = $("[name='password']").val();

			if (username == "")
			{
				usernameFilledIn = false;
				$("[name='username']").parent().effect("shake");
			}

			if (password == "")
			{
				passwordFilledIn = false;
				$("[name='password']").parent().effect("shake");
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
			url: SmashTube.Url.createUrl("/login")
		}).done(function (data, textStatus, jqXHR)
		{
			cw(data);
			if ($(data).find("[data-has-error]").attr("data-has-error") == "1")
			{
				$("[name='username']").parent().effect("shake");
				$("[name='password']").parent().effect("shake");

				$("[name='username']").val("");
				$("[name='password']").val("");

				SmashTube.Splash.hide();
			}
			else
			{
				 //login worked, reload page with logged in user;
				if ($(".smashtube-login-standalone-content").length > 0)
				{
					window.location = SmashTube.Url.createUrl("/");
				}
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

			window.location = SmashTube.Url.createUrl("/logout");
		});
	},
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