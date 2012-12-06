/**
 * Return the relative base url of the website using this script
 *
 * @return the relative base url of the website using this script
 *
 */
var getBaseURL = function()
{
	var scripts = document.getElementsByTagName('script');
	var myScript = scripts[scripts.length - 1];
	return function()
	{
		var url = myScript.src.split("scripts/")[0];
		return url.substring(url.search("[^:/]/") + 1);
	};
}();

/**
 * Inspect the document and set the links and forms' submits to load dynamically what they are linking
 */
function scanPage()
{
	var externLinkRegex = new RegExp("^https?://");
	var staticLinkRegex = new RegExp("static");

	// Loop through document's links
	var links = document.getElementsByTagName("a");
	for (var i = 0; i < links.length; ++i)
	{
		var link = links[i].getAttribute("href");

		// Check if the link is internal and dynamic
		if (!externLinkRegex.test(link) && !staticLinkRegex.test(links[i].className))
		{
			// Set the onclick callback of each link
			links.item(i).onclick = function(link)
			{
				return function()
				{
					// Add a new history state
					window.history.pushState(null, "Loading...", link);

					// Load the page
					loadPage(link, document.getElementById("content"), "loading");

					// Return false so that the browser will not try to load the page itself
					return false;
				};
			}(link);
		}
	}

	// Loop through document's inputs
	var inputs = document.getElementsByTagName("input");
	for (var i = 0; i < inputs.length; ++i)
	{
		if (inputs[i].type == "submit")
		{
			// Reach the form parent of the input element
			var form = inputs[i].parentNode;
			while (!(form instanceof HTMLFormElement) && form != undefined)
				form = form.parentNode;

			if (form != undefined)
			{
				// Set the onclick callback of each submit input
				inputs[i].onclick = function(form)
				{
					return function()
					{
						// Add a new history state
						window.history.pushState(null, "Loading...", form.getAttribute("action"));

						// Retrieve data and load the page
						formData = new FormData(form);
						loadPage(form.getAttribute("action"), document.getElementById("content"), "loading", formData);

						// Return false so that the browser will not try to load the page itself
						return false;
					};
				}(form);
			}
		}
	}
}

/**
 * Load a page in a given element, using XmlHttpRequest
 *
 * @param url		Required, the url to the page to be loaded
 * @param element	Required, the element which will contain the loaded page
 * @param className	Optional, a class name which will be added to the element during loading
 * @param data		Optional, the data to be sent in case of a POST request
 */
function loadPage(url, element, className, data)
{
	// Create XHR Object
	var xhr = new XMLHttpRequest();

	// Set callback
	xhr.onreadystatechange = function(url, element, className)
	{
		return function()
		{
			if (xhr.readyState == 4)
			{
				var html = xhr.responseText;

				// Check if the page is a full HTML page
				// If that's the case, we went through an HTTP redirection
				// To fix that and avoid the inclusion of an full HTML page in the HTML code
				// We get the redirected page url from the canonical link and load it as an ajax-compliant page
				var fullPageRegex = new RegExp("^<!doctype html>");
				if (fullPageRegex.test(html))
				{
					var requestLink = '/';
					var canonicalLinkRegex = new RegExp('<link rel="canonical" href="([a-zA-Z0-9-_/]+)"/>');

					if (canonicalLinkRegex.test(html))
						requestLink = canonicalLinkRegex.exec(html)[1];

					window.history.replaceState(null, "Loading...", requestLink);
					loadPage(requestLink, document.getElementById("content"), "loading", data);
				}
				else
				{
					// Parse special elements to load dynamic content outside of the ajax frame and remove it from the content
					html = parseTitleTag(html);
					html = parseCanonicalLinkTag(html);
					html = parseKeywordsTag(html);
					html = parseDescriptionTag(html);
					html = parsePathTag(html);

					// Set the remaining content in the ajax frame
					element.innerHTML = html;

					// Remove class from the element
					if (className != undefined && className != null && className != "")
					{
						if (element.className == className)
							element.removeAttribute("class");
						else
							element.className = element.className.substring(0, element.className.length - className.length - 1);
					}

					// Set the class attribute of the page element to the moduleName
					urlParts = url.split("/");
					var moduleName = urlParts[urlParts.length-1].split("-")[0];
					if (moduleName == "")
						moduleName = "home";
					document.getElementById("page").className = moduleName;

					// Rescan page (needed the dynamically loaded content may contains internal links)
					scanPage();

					// Call the onLoadPage callback
					if (window.onLoadPageFinish)
						window.onLoadPageFinish(url);
				}
			}
		};
	}(url, element, className);

	// Define the url to be loaded
	var rawURL = getBaseURL() + "raw/" + url.substring(getBaseURL().length);

	// If no data is provided, send a GET request, else, send a POST request
	if (data == undefined || data == null)
	{
		xhr.open("GET", rawURL, true);
		xhr.send(null);
	}
	else
	{
		xhr.open("POST", rawURL, true);
		xhr.send(data);
	}

	// Add class to the element
	if (className != undefined && className != null && className != "")
		element.className += className;

	// Call the onLoadPage callback
	if (window.onLoadPage)
		window.onLoadPage(url);
}

/**
 * Callback triggered from browser actions such as refresh or previous, load the destination page
 *
 * @param event		The popState event
 *
 * @return false if the page will be loaded using ajax
 *
 */
window.onpopstate = function(event)
{
	// A first popState is generated when accessing the page from browser actions
	if (!window.firstPop)
		window.firstPop = true;
	else
	{
		// This is not the first popState, we can load the new page
		var url = window.location.pathname + window.location.search;
		loadPage(url, document.getElementById("content"), "loading");
		return false;
	}
	return true;
};
/**
 * Parse the title ajax-tag from an html string
 *
 * @param html	The html string to parse
 *
 * @return 		The resulting html string without the title tag
 *
 */
function parseTitleTag(html)
{
	var titleRegex = new RegExp("<ajax:title>(.*)</ajax:title>");
	if (titleRegex.test(html))
	{
		var titleRes = titleRegex.exec(html);
		document.title = titleRes[1];
		var titleTagPosition = html.search(titleRegex);
		html = html.substring(0, titleTagPosition) + html.substring(titleTagPosition + titleRes[0].length);
	}
	return html;
}

/**
 * Parse the canonical link ajax-tag from an html string
 *
 * @param html	The html string to parse
 *
 * @return 		The resulting html string without the canonical link tag
 *
 */
function parseCanonicalLinkTag(html)
{
	var canonicalLinkRegex = new RegExp("<ajax:canonical>(.*)</ajax:canonical>");
	if (canonicalLinkRegex.test(html))
	{
		var canonicalLinkRes = canonicalLinkRegex.exec(html);
		var linkList = document.getElementsByTagName("link");
		for ( i = 0; i < linkList.length; i++)
		{
			if (linkList[i].hasAttribute("rel") && linkList[i].rel == "canonical")
				linkList[i].href = canonicalLinkRes[1];
		}
		var canonicalLinkTagPosition = html.search(canonicalLinkRegex);
		html = html.substring(0, canonicalLinkTagPosition) + html.substring(canonicalLinkTagPosition + canonicalLinkRes[0].length);
	}
	return html;
}

/**
 * Parse the keywords ajax-tag from an html string
 *
 * @param html	The html string to parse
 *
 * @return 		The resulting html string without the keywords tag
 *
 */
function parseKeywordsTag(html)
{
	var keywordsRegex = new RegExp("<ajax:keywords>(.*)</ajax:keywords>");
	if (keywordsRegex.test(html))
	{
		var keywordsRes = keywordsRegex.exec(html);
		var metaTagList = document.getElementsByTagName("meta");
		for ( i = 0; i < metaTagList.length; i++)
		{
			if (metaTagList[i].hasAttribute("name") && metaTagList[i].name == "keywords")
				metaTagList[i].content = keywordsRes[1];
		}
		var keywordsTagPosition = html.search(keywordsRegex);
		html = html.substring(0, keywordsTagPosition) + html.substring(keywordsTagPosition + keywordsRes[0].length);
	}
	return html;
}

/**
 * Parse the description ajax-tag from an html string
 *
 * @param html	The html string to parse
 *
 * @return 		The resulting html string without the description tag
 *
 */
function parseDescriptionTag(html)
{
	var descriptionRegex = new RegExp("<ajax:description>(.*)</ajax:description>");
	if (descriptionRegex.test(html))
	{
		var descriptionRes = descriptionRegex.exec(html);
		var metaTagList = document.getElementsByTagName("meta");
		for ( i = 0; i < metaTagList.length; i++)
		{
			if (metaTagList[i].hasAttribute("name") && metaTagList[i].name == "description")
				metaTagList[i].content = descriptionRes[1];
		}
		var descriptionTagPosition = html.search(descriptionRegex);
		html = html.substring(0, descriptionTagPosition) + html.substring(descriptionTagPosition + descriptionRes[0].length);
	}
	return html;
}

/**
 * Parse the path ajax-tag from an html string
 *
 * @param html	The html string to parse
 *
 * @return 		The resulting html string without the path tag
 *
 */
function parsePathTag(html)
{
	var pathRegex = new RegExp("<ajax:path>(.*)</ajax:path>");
	if (pathRegex.test(html))
	{
		var pathRes = pathRegex.exec(html);
		var pathArray = pathRes[1].split(";");
		var pathHTMLRes = "";
		for (pathStep in pathArray)// Create an html link for each element
		{
			if ( typeof (pathArray[pathStep]) == "string")//TODO bugfix for a weird bug in Chrome 16.*-
			{
				var pathStepArray = pathArray[pathStep].split("|");
				pathHTMLRes += "<a href=\"" + pathStepArray[1] + "\">" + pathStepArray[0] + "</a>";
			}
		}

		var ribbon = document.getElementById("ribbon");
		if (ribbon != null)
			document.getElementById("ribbon").innerHTML = pathHTMLRes;

		var pathTagPosition = html.search(pathRegex);
		html = html.substring(0, pathTagPosition) + html.substring(pathTagPosition + pathRes[0].length);
	}
	return html;

}
