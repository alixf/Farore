// The following piece of code check if a page is likely to be loaded immediatly and hide the content in that case
var contentHidden = false;
if(window.location.hash.length > 0 || window.location.toString().charAt(window.location.toString().length-1) == '#')
{
	document.getElementById("content").style.display = "none";
	contentHidden = true;
	document.getElementById("ribbon").innerHTML = "&#187; <a>Chargement ...</a> ";
}

/**
 * Inspect the document and set the a element (links) to load dynamically what they are referencing if it is an internal resource
 */
var scanPage = function()
{
	var internalLinkRegex = new RegExp("^https?://(www.)?eolhing.me/");
	var staticLinkRegex = new RegExp("static");
	var requestPrefix = "raw.php?data=";
	
	var links = document.getElementsByTagName("a");
	
	for(var i = 0; i < links.length; i++) // Loop through page's links
	{
		var link = links[i].href;
		
		if(internalLinkRegex.test(link) && !staticLinkRegex.test(links[i].className)) // Check if the link is internal and allowed to be dynamic
		{
			var relLink = link.substring(internalLinkRegex.exec(link)[0].length); 
		    var request = requestPrefix+relLink;
		    
		    links.item(i).onclick = function(pageRequest, relativeLink) // This function enable the closure of the pageRequest var
		    {
		    	return function() // This function will be called when the item is clicked
		    	{
		    		LoadPage(pageRequest, document.getElementById("content"), "loading");
		    		document.location.hash = relativeLink;
		    		
					// Cancel check-hash loading
					window.previousHash = window.location.hash;
					
		    		return false;
		    	};
		    }(request, relLink);
	    }
	}
};

/**
 * Load a page in a given element, using XHR
 * 
 * @param pageURL	Required, the url to the page to be loaded
 * @param element	Required, the element which will contain the loaded page
 * @param className	Optional, a class name which will be added to the element during loading
 */
function LoadPage(pageURL, element, className)
{
	// Add class to the element
	if(className != undefined && className != null && className != "")
		element.className += (element.className.length > 0) ? " "+className : className;
	
	// Create XHR Object
    var xhr = new XMLHttpRequest();
    
    // Set callback
    xhr.onreadystatechange = function()
    {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
        {
        	var html = xhr.responseText;
        	
        	// Parse special elements to load dynamic content outside of the ajax frame and remove it from the content
        	// Title
        	var titleRegex = new RegExp("<ajax:title>(.*)</ajax:title>");
        	if(titleRegex.test(html))
        	{
	        	var titleRes = titleRegex.exec(html);
	        	document.title = titleRes[1];
	        	var titleTagPosition = html.search(titleRegex);
        		html = html.substring(0, titleTagPosition)+html.substring(titleTagPosition+titleRes[0].length);
        	}
        	// Canonical link
        	var canonicalLinkRegex = new RegExp("<ajax:canonical>(.*)</ajax:canonical>");
        	if(canonicalLinkRegex.test(html))
        	{
	        	var canonicalLinkRes = canonicalLinkRegex.exec(html);
	        	var linkList = document.getElementsByTagName("link");
	        	for(i = 0; i < linkList.length; i++)
	        	{
	        		if(linkList[i].hasAttribute("rel") && linkList[i].rel == "canonical")
	        			linkList[i].href = canonicalLinkRes[1];
	        	}
	        	var canonicalLinkTagPosition = html.search(canonicalLinkRegex);
        		html = html.substring(0, canonicalLinkTagPosition)+html.substring(canonicalLinkTagPosition+canonicalLinkRes[0].length);
        	}
        	// Keywords
        	var keywordsRegex = new RegExp("<ajax:keywords>(.*)</ajax:keywords>");
        	if(keywordsRegex.test(html))
        	{
	        	var keywordsRes = keywordsRegex.exec(html);
	        	var metaTagList = document.getElementsByTagName("meta");
	        	for(i = 0; i < metaTagList.length; i++)
	        	{
	        		if(metaTagList[i].hasAttribute("name") && metaTagList[i].name == "keywords")
	        			metaTagList[i].content = keywordsRes[1];
	        	}
	        	var keywordsTagPosition = html.search(keywordsRegex);
        		html = html.substring(0, keywordsTagPosition)+html.substring(keywordsTagPosition+keywordsRes[0].length);
        	}
        	// Description
        	var descriptionRegex = new RegExp("<ajax:description>(.*)</ajax:description>");
        	if(descriptionRegex.test(html))
        	{
	        	var descriptionRes = descriptionRegex.exec(html);
	        	var metaTagList = document.getElementsByTagName("meta");
	        	for(i = 0; i < metaTagList.length; i++)
	        	{
	        		if(metaTagList[i].hasAttribute("name") && metaTagList[i].name == "description")
	        			metaTagList[i].content = descriptionRes[1];
	        	}
	        	var descriptionTagPosition = html.search(descriptionRegex);
        		html = html.substring(0, descriptionTagPosition)+html.substring(descriptionTagPosition+descriptionRes[0].length);
        	}
        	// Path
        	var pathRegex = new RegExp("<ajax:path>(.*)</ajax:path>");
        	if(pathRegex.test(html))
        	{
	        	var pathRes = pathRegex.exec(html);
	        	var pathArray = pathRes[1].split(";");
	        	var pathHTMLRes = "";
	        	for(pathStep in pathArray) // Create an html link for each element
	        	{
	        		if(typeof(pathArray[pathStep]) == "string") //TODO bugfix for a weird bug in Chrome 16.*-
	        		{
		        		var pathStepArray = pathArray[pathStep].split("|");
						pathHTMLRes += "&#187; <a href=\""+pathStepArray[1]+"\">"+pathStepArray[0]+"</a> ";
					}
	        	}
	        	document.getElementById("ribbon").innerHTML = pathHTMLRes;
	        	var pathTagPosition = html.search(pathRegex);
        		html = html.substring(0, pathTagPosition)+html.substring(pathTagPosition+pathRes[0].length);
        	}
        	
        	// Set the remaining content in the ajax frame
        	element.innerHTML = html;
        	
			// Remove class from the element
			if(className != undefined && className != null && className != "")
			{
				if(element.className == className)
					element.removeAttribute("class");
				else
					element.className = element.className.substring(0, element.className.length-className.length-1);
			}
			
			// Rescan page (needed the dynamically loaded content may contains internal links)
		    scanPage();
		    
		    // Explicitly load and render Google +1 buttons
		    gapi.plusone.go("content");
		    
		    // if the content is hidden (typically happens when a page is loaded and being immediatly ajax-replaced by another)
		    if(contentHidden)
				document.getElementById("content").removeAttribute("style");
        }
    };
    
    // Launch the request
    xhr.open("GET", pageURL, true);
    xhr.send(null);
}

/**
 * This function check regularly if the hash in the url have been modified and load the new page if needed
 * Especially useful to check when the user pressed the back or forward key in his browser as these don't trigger any event
 */
function checkHash()
{
	if(window.previousHash != window.location.hash)
	{
		window.previousHash = window.location.hash;
		LoadPage("raw.php?data="+window.location.hash.substring(1), document.getElementById("content"), "loading");
	}
	setTimeout("checkHash()", 100);
}

window.onload = function()
{
	// If the url contains an anchor, treat it as an ajax marker and load the corresponding page, else just scan the page for dynamic links
	if(window.location.hash.length > 0 || window.location.toString().charAt(window.location.toString().length-1) == '#')
		LoadPage("raw.php?data="+window.location.hash.substring(1), document.getElementById("content"), "loading");
	else
		scanPage();
	
	// Set the previous hash to the current one and launch the hash checking
	window.previousHash = window.location.hash;
	checkHash();
}