$(function() {
	var getCookiesFilter = function (cookieNameRegEx) { // фильтрует куки по регулярному выражению
			var allCookies = document.cookie,
				arrayCookies = allCookies.split(/\s*;\s*/),
				cookies = {},
				i,
				max,
				keyValue;
			if (allCookies) 
			{
				for (i = 0, max = arrayCookies.length; i < max; i += 1) 
				{
					keyValue = arrayCookies[i].split("=");
					if (keyValue[0].match(cookieNameRegEx)) 
					{
						cookies[decodeURIComponent(keyValue[0])] = decodeURIComponent(keyValue[1]);
					}
				}
			}
			return cookies;
		},
		objCookies = getCookiesFilter(/mso-tabs_widget_[0-9a-z]+/);

	$.each(objCookies, function(key, value)
	{
		$("." + key + " .mso-tabs-elem").eq(value).addClass("mso-tabs-current").siblings().removeClass("mso-tabs-current")
			.parents("div.mso-tabs").find(".mso-tabs-box").hide().eq(value).show();
	});
	
	$(".mso-tabs-nav").on("click", ".mso-tabs-elem:not(.mso-tabs-current)", function() 
	{
		var cookieName = $(this).parents(".mso-tabs_widget").prop("class").match(/mso-tabs_widget_[0-9a-z]+/).join(),
			index = $(this).index();
		$(this).addClass("mso-tabs-current").siblings().removeClass("mso-tabs-current")
				.parents("div.mso-tabs").find(".mso-tabs-box").hide().eq(index).fadeIn(300);
		$.cookie(cookieName, index, {expires: 1, path: "/"});
	});
});
