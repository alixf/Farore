<?php
	require_once 'global/core.class.php';	
	require_once 'libs/minifyHTML.php';
	
	$core = new Core();
	$core->parseURL();
	
	// Create module
	$core->createModule();
	
	// Execute module
	$core->getModule()->execute($core);
	
	ob_start('Minify_HTML::minify'); 
	if(!isset($_GET['raw']))
	{
		// Include template or view
		if(file_exists($core->getBasePath().'global/template.php'))
			include_once($core->getBasePath().'global/template.php');
		else
			$core->getModule()->display();
	}
	else
	{
		// Print special element that will be parsed to update dynamic content outside of the ajax inclusion frame
		echo '<ajax:title>'.$core->getPageTitle().'</ajax:title>';
		echo '<ajax:canonical>'.$core->getPageCanonicalLink().'</ajax:canonical>';
		echo '<ajax:description>'.$core->getPageDescription().'</ajax:description>';
		echo '<ajax:keywords>'.implode(', ', $core->getPageKeywords()).'</ajax:keywords>';
		
		$tmp = array();
		foreach($core->getPagePath() as $pageStep)
			$tmp[] = implode('|', $pageStep);
		echo '<ajax:path>'.implode(';', $tmp).'</ajax:path>';
		
		// Display module
		$core->getModule()->display();
	}
	ob_end_flush();
?>