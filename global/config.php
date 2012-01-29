<?php
	// CMS data
	$internalBasePath			= '{{INTERNAL_BASE_PATH}}';
	$modulesBasePath			= $internalBasePath.'modules/';
	$urlBasePath				= '{{URL_BASE_PATH}}';
	$urlParameterName			= 'data';
	$urlParameterSeparator		= '/';
	$dataSeparator				= '-';
	$defaultModule				= '{{DEFAULT_MODULE}}';
	
	// Database data
	$sqlDriver					= '{{SQL_DRIVER}}';
	$sqlHost					= '{{SQL_SERVER}}';
	$sqlBase					= '{{SQL_BASE}}';
	$sqlDSN						= $sqlDriver.':host='.$sqlHost.';dbname='.$sqlBase;
	$sqlUsername				= '{{SQL_USERNAME}}';
	$sqlPassword				= '{{SQL_PASSWORD}}';

	// Administration data
	$adminUsername				= '{{ADMIN_USERNAME}}';
	$adminPassword				= '{{ADMIN_PASSWORD}}';
	
	// Website data
	$pageTitle					= '{{PAGE_TITLE}}';
	$pageCanonicalLink			= $urlBasePath;
	$pageDescription			= '{{PAGE_DESCRIPTION}}';
	$pageKeywords				= '{{PAGE_KEYWORDS}}';
	$pageLanguage				= '{{PAGE_LANGUAGE}}';
	$pageReadingDir				= '{{PAGE_READINGDIR}}';
	$pageEncoding				= 'utf-8';
	$pagePath					= array();
	
	// Website data before any modification by modules
	$pageTitleBase				= $pageTitle;
	$pageCanonicalLinkBase		= $pageCanonicalLink;
	$pageDescriptionBase		= $pageDescription;
	$pageKeywordsBase			= $pageKeywords;
	$pageLanguageBase			= $pageLanguage;
	$pageReadingDirBase			= $pageReadingDir;
	$pageEncodingBase			= $pageEncoding;
	$pagePathBase				= $pagePath;
?>