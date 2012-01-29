<?php
	include_once 'global/init.php';

	function readSettings($modulePath)
	{
		$settings = array();
		$settingsFile = $modulePath.'configuration.xml';
		
		if(file_exists($settingsFile))
		{
			$document = new DomDocument();
			$document->load($settingsFile);
			
			foreach($document->getElementsByTagName('setting') as $setting)
			{
				switch($setting->getAttribute('type'))
				{
				case 'integer':
					$settings[$setting->getAttribute('id')] = intval($setting->nodeValue);
					break;
					
				case 'boolean':
					$settings[$setting->getAttribute('id')] = $setting->nodeValue == 'true';
					break;
					
				default :
					$settings[$setting->getAttribute('id')] = $setting->nodeValue;
					break;
				}
			}
		}
		
		return $settings;
	}

	// Declare variables to be used in the process
	$data = array();
	$module = '';
	$modulePath = $modulesBasePath;
	$moduleSettings = array();
	
	if (empty($_GET[$urlParameterName]))
		$_GET[$urlParameterName] = $defaultModule;

	// Parse the url parameter
	$modules = explode($urlParameterSeparator, $_GET[$urlParameterName]);
	
	// loop through modules specified in the url parameter
	$moduleExists = true;
	for ($i = 0; $i < count($modules) && $moduleExists; $i++)
	{
		// Define the exact module name
		$moduleName = $modules[$i];
		if($i == count($modules)-1)
		{
			// This is the last module, extract module's name and data
			$moduleAndData = explode($dataSeparator, $modules[$i]);
			$moduleName = $moduleAndData[0];
			$data = array_slice($moduleAndData, 1);
		}
		
		if (is_dir($modulePath . $moduleName))
		{
			// If module exists, add the module to module's path
			$module = $moduleName;
			$modulePath .= $module . '/';
			$modules[$i] = '';
			
			if($i == 0) // Top level module, read configuration
				$moduleSettings = readSettings($modulePath);
		}
		else
		{
			// If module doesn't exist, redirect to the 404 error module
			$module = '404';
			$modulePath = 'modules/error/404/';
			$moduleExists = false;
			
			// Read configuration
			$moduleSettings = readSettings('modules/error/');
		}
	}
	
	// Include Model and View
	include_once($modulePath.'model.php');
	include_once($internalBasePath.'global/template.php');
?>