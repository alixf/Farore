<?php
	require_once 'global/core.class.php';
	require_once 'libs/minify.class.php';

	/*
	 * Initialize core and parse url
	 */
	$core = new Core();
	$core->setMinifyType(Minify::HTML);
	$core->parseURL(isset($_GET[$core->getUrlParameterName()]) ? $_GET[$core->getUrlParameterName()] : '');

	/*
	 * Execute modules
	 */
	$run = true;
	while ($run)
	{
		switch($core->createModule())
		{
		case Core::MODULENOTFOUND :
			$core->parseURL('/error/404');
			break;
		case Core::MODULEUNAUTHORIZED :
			$core->parseURL('/error/403');
			break;
		case Core::MODULESUCCESS :
			$core->getModule()->execute();
			break;
		default :
			$run = false;
			break;
		}
	}
	
	/*
	 * Display last module
	 */
	$minifyType = $core->getMinifyType();
	$obCallback = function($input) use ($minifyType)
	{
		return Minify::apply($input, $minifyType);
	};
	
	ob_start($obCallback);
	if ($core->getIncludeAjaxTags())
		echo $core->getAjaxTags();

	if ($core->getIncludeTemplate() && file_exists($core->getBasePath().'global/template.php'))
		include_once ($core->getBasePath().'global/template.php');
	else
		$core->getModule()->display();
	ob_end_flush();
?>