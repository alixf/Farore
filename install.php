<?php
	$formSent = isset($_POST['submit']);
	
	$error = array();
	
	$languages = array
	(
		'fr'=>'Français'
	);
	
	$dbDrivers = array
	(
		'mysql' => 'MySQL',
		'sqlite' => 'SQLite',
		'pgsql' => 'PostgreSQL',
		'odbc' => 'ODBC',
		'oci' => 'Oracle',
		'sybase' => 'Sybase',
		'sqlsrv' => 'MS SQL'
	);
	
	$modules = scandir('modules');
	
	// Default values
	$adminUsername = '';
	$adminPassword = '';
	$databaseDriver = 'mysql';
	$databaseServer = '';
	$databaseUsername = '';
	$databasePassword = '';
	$databaseBase = '';
	$pageTitle = '';
	$pageDescription = '';
	$pageKeywords = '';
	$pageLanguage = 'fr';
	$defaultModule = 'blog';
		
	if($formSent)
	{
		// Get values from post data
		$adminUsername = $_POST['adminUsername'];
		$adminPassword = $_POST['adminPassword'];
		$databaseDriver = $_POST['databaseDriver'];
		$databaseServer = $_POST['databaseServer'];
		$databaseUsername = $_POST['databaseUsername'];
		$databasePassword = $_POST['databasePassword'];
		$databaseBase = $_POST['databaseBase'];
		$pageTitle = $_POST['websiteName'];
		$pageDescription = $_POST['websiteDescription'];
		$pageKeywords = $_POST['websiteKeywords'];
		$pageLanguage = $_POST['websiteLanguage'];
		$defaultModule = empty($_POST['defaultModule']) ? '' : $_POST['defaultModule'];
		
		// Check
		if(strlen($adminUsername) < 6)
			$error['adminUsername'] = true;
		if(strlen($adminPassword) < 6)
			$error['adminPassword'] = true;
		
		if(!array_key_exists($databaseDriver, $dbDrivers))
			$error['databaseDriver'] = true;
		try
		{
			error_reporting(E_ALL ^ E_WARNING); // Don't show php errors concerning DB Connection
			$database = new PDO($databaseDriver.':host='.$databaseServer.';dbname='.$databaseBase, $databaseUsername, $databasePassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
			error_reporting(E_ALL ^ E_NOTICE); // Turn error reporting back to default
		}
		catch(PDOException $e)
		{
			switch($e->getCode())
			{
			case 2002 :
				$error['databaseServer'] = true;
				break;
			case 1045 :
				$error['databaseUsername'] = true;
				$error['databasePassword'] = true;
				$error['databaseBase'] = true;
				break;
			case 1044 :
				$error['databaseBase'] = true;
				break;
			default :
				break;
			}
			$error['database'] = true;
		}
		
		if($pageTitle != strip_tags($pageTitle) || empty($pageTitle))
			$error['websiteName'] = true;
		if(!is_dir('modules/'.$defaultModule) || empty($defaultModule))
			$error['defaultModule'] = true;
		if($pageDescription != strip_tags($pageDescription))
			$error['websiteDescription'] = true;
		if($pageKeywords != strip_tags($pageKeywords))
			$error['websiteKeywords'] = true;
		if(!array_key_exists($pageLanguage, $languages))
			$error['websiteLanguage'] = true;
			
		// If everything is okay, install
		if(empty($error))
		{
			// Install each modules
			foreach(scandir(dirname(__FILE__).'/modules') as $module)
			{
				if($module != '.' && $module != '..')
				{
					include_once dirname(__FILE__).'/modules/'.$module.'/'.$module.'.class.php';
					$res = call_user_func(ucfirst($module).'::install', $database);
					if(!$res)
						$error['module_'.$module] = true;
				}
			}
			
			// If everything is still okay, finish installation
			if(empty($error))
			{
				// Resolve url base path
				$basePath = realpath(dirname(__FILE__)).'/';
				$baseURL = $_SERVER['REQUEST_URI'][strlen($_SERVER['REQUEST_URI'])-1] == '/' ? $_SERVER['REQUEST_URI'] : dirname($_SERVER['REQUEST_URI']).'/';
				
				$htaccess = file_get_contents('.htaccess');
				
				$htaccessFile = fopen('.htaccess', 'w');
				fwrite($htaccessFile, preg_replace('/ROOT\//', $baseURL, $htaccess));
				
				// Parse keywords
				$pageKeywordsArray = explode(',', $pageKeywords);
				for($i = 0; $i < count($pageKeywordsArray); $i++)
					$pageKeywordsArray[$i] = trim($pageKeywordsArray[$i]);
				$pageKeywords = implode(', ', $pageKeywordsArray);
				
				// Fill the settings
				$settings['basePath'] = $basePath;
				$settings['libsPath'] = $basePath.'libs/';
				$settings['themesPath'] = $basePath.'themes/';
				$settings['imagesPath'] = $basePath.'images/';
				$settings['modulesPath'] = $basePath.'modules/';
				$settings['scriptsPath'] = $basePath.'scripts/';
				
				$settings['baseURL'] = $baseURL;
				$settings['libsURL'] = $baseURL.'libs/';
				$settings['themesURL'] = $baseURL.'themes/';
				$settings['imagesURL'] = $baseURL.'images/';
				$settings['modulesURL'] = $baseURL.'modules/';
				$settings['scriptsURL']	= $baseURL.'scripts/';
				
				$settings['urlDataSeparator'] = '-';
				$settings['urlParameterName'] = 'data';
				$settings['urlParameterSeparator'] = '/';
				$settings['databaseDriver'] = $databaseDriver;
				$settings['databaseHost'] = $databaseHost;
				$settings['databaseBase'] = $databaseBase;
				$settings['databaseUsername'] = $databaseUsername;
				$settings['databasePassword'] = $databasePassword;
				$settings['adminUsername'] = $adminUsername;
				$settings['adminPassword'] = $adminPassword;
				
				$settings['pageTitle'] = $pageTitle;
				$settings['pageDescription'] = $pageDescription;
				$settings['pageKeywords'] = $pageKeywords;
				$settings['pageLanguage'] = $pageLanguage;
				
				$settings['pageCanonicalLink'] = $baseURL;
				$settings['pageReadingDir'] = 'ltr';
				$settings['pageEncoding'] = 'utf-8';
				
				$settings['defaultModule'] = $defaultModule;
				
				// Create configuration document
				$document = new DOMDocument('1.0', 'utf-8');
				$document->formatOutput = true;
				$root = $document->createElement('settings');
				$root = $document->appendChild($root);
				foreach($settings as $setting => $value)
				{
					$settingElement = $document->createElement('setting', $value);
					$settingNode = $root->appendChild($settingElement);
					$settingNode->setAttribute('id', $setting);
				}
				$document->save('global/configuration.xml');
				
				// Installation finished
				header('Location: .');
				exit(0);
			}
		}
	}
?>
<!doctype html>
<html>
	<head>
		<title>Farore - Installation</title>
		<meta charset="utf-8" />
		<style type="text/css">
			body
			{
				margin : 0px;
				font-family : Helvetica, Arial, serif;
				background-color : #AAA;
			}
			.content
			{
				width : 800px;
				margin : auto;
			}
			
			h1
			{
				text-align:center;
				color:#FFF;
				text-shadow:0px 1px #000;
				border:1px solid #555;
				background:-moz-linear-gradient(top,  rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 100%);
				background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,255,0.3)), color-stop(100%,rgba(255,255,255,0)));
				background:-webkit-linear-gradient(top, rgba(255,255,255,0.3) 0%,rgba(255,255,255,0) 100%);
				background:-o-linear-gradient(top, rgba(255,255,255,0.3) 0%,rgba(255,255,255,0) 100%);
				background:-ms-linear-gradient(top, rgba(255,255,255,0.3) 0%,rgba(255,255,255,0) 100%);
				background:linear-gradient(top, rgba(255,255,255,0.3) 0%,rgba(255,255,255,0) 100%);
				background-color:#888;
				box-shadow:0px 1px 3px rgba(0,0,0,0.5);
			}
			h2
			{
				text-align : center;
				color : #FFF;
				text-shadow : 0px -1px 0px rgba(0,0,0,0.5);
			}
			.item
			{
				width : 90%;
				border : 1px solid rgba(255,255,255,0.5);
				margin : 5px auto;
				background:-moz-linear-gradient(top,  rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
				background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,255,0.1)), color-stop(100%,rgba(255,255,255,0)));
				background:-webkit-linear-gradient(top, rgba(255,255,255,0.1) 0%,rgba(255,255,255,0) 100%);
				background:-o-linear-gradient(top, rgba(255,255,255,0.1) 0%,rgba(255,255,255,0) 100%);
				background:-ms-linear-gradient(top, rgba(255,255,255,0.1) 0%,rgba(255,255,255,0) 100%);
				background:linear-gradient(top, rgba(255,255,255,0.1) 0%,rgba(255,255,255,0) 100%);
				background-color : #7A4;
				box-shadow : 0px 1px 3px rgba(0,0,0,0.5);
				clear : both;
				color : #FFF;
				text-shadow : 0px -1px 0px rgba(0,0,0,0.5);
			}
			.item.error
			{
				background-color : #C43;
			}
			.description
			{
				max-height : 0px;
				-moz-transition : all 0.25s linear;
				-webkit-transition : all 0.25s linear;
				transition : all 0.25s linear;
				overflow : hidden;
				text-align : center;
				opacity : 0.75;
				border-top : 0px dashed rgba(255,255,255,0.5);
				margin-top : 0px;
				padding-top : 0px;
			}
			input:focus + .description, select:focus + .description, textarea:focus + .description
			{
				max-height : 100px;
				-moz-transition : all 0.25s linear;
				-webkit-transition : all 0.25s linear;
				transition : all 0.25s linear;
				overflow : hidden;
				border-top : 1px dashed rgba(255,255,255,0.5);
				margin-top : 5px;
				padding-top : 5px;
			}
			label
			{
				display : inline-block;
				width : 33%;
				text-align : right;
				vertical-align : middle;
			}
			label:after
			{
				content : " : ";
			}
			input[type="text"], input[type="password"], textarea, select
			{
				display : inline-block;
				width : 64%;
				max-width : 64%;
				min-width : 64%;
				border : 0px;
				padding : 3px 7px;
				background-color : rgba(255,255,255,0.2);
				margin : 3px;
				vertical-align : middle;
				color : #FFF;
				text-shadow : 0px 0px 3px rgba(0,0,0,0.5);
			}
			select /* Fix for select element doesn't supporting rgba background-color */
			{
				background-color : #9AC075;
				width : 66%;
				min-width : 66%;
				max-width : 66%;
			}
			input[type="submit"]
			{
				cursor : pointer;
				dislay : block;
				width : 100%;
				background:-moz-linear-gradient(top,  rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 100%);
				background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,255,0.3)), color-stop(100%,rgba(255,255,255,0)));
				background:-webkit-linear-gradient(top, rgba(255,255,255,0.3) 0%,rgba(255,255,255,0) 100%);
				background:-o-linear-gradient(top, rgba(255,255,255,0.3) 0%,rgba(255,255,255,0) 100%);
				background:-ms-linear-gradient(top, rgba(255,255,255,0.3) 0%,rgba(255,255,255,0) 100%);
				background:linear-gradient(top, rgba(255,255,255,0.3) 0%,rgba(255,255,255,0) 100%);
				background-color : #6C4;
				border : 0px;
				margin : 0px;
				font-size : 2em;
				color : #FFF;
				text-shadow : 0px 1px #000;
				font-weight : bold;
				margin-top : 15px;
				border : 1px solid #3A2;
				box-shadow : 0px 1px 3px rgba(0,0,0,0.5);
			}
		</style>
	</head>
	<body>
		<header>
			
		</header>
		<div class="content">
			<form method="POST" action=".">
				
				<h1>Installation de Farore</h1>
				
				<h2>Administration</h2>
				<div class="item<?php if(isset($error['adminUsername'])) echo ' error'; ?>">
					<label>Identifiant</label><input type="text" name="adminUsername" value="<?php echo $adminUsername; ?>" />
					<div class="description">L'identifiant est le nom que vous utiliserez pour vous connecter à l'administration de votre site.<br />Il doit être composé d'au moins 6 caractères.</div>
				</div>
				<div class="item<?php if(isset($error['adminPassword'])) echo ' error'; ?>">
					<label>Mot de passe</label><input type="password" name="adminPassword" value="<?php echo $adminPassword; ?>" />
					<div class="description">Le mot de passe sera requis à chaque connexion à l'administration de votre site.<br />Il doit être supérieur à 6 caractères.</div>
				</div>
				
				<h2>Base de données</h2>
				
				<div class="item<?php if(isset($error['databaseDriver'])) echo ' error'; ?>">
					<label>Driver</label><select name="databaseDriver">
						<?php
						foreach($dbDrivers as $driver => $driverName)
							echo '<option value="'.$driver.'"'.($driver == $databaseDriver ? ' selected="selected"' : '').'>'.$driverName.'</option>';
						?>
					</select>
					<div class="description">Veuillez sélectionner le type de votre base de donnée.<br />La plupart du temps, il s'agit de MySQL</div>
				</div>
				
				<div class="item<?php if(isset($error['databaseServer'])) echo ' error'; ?>">
					<label>Serveur</label><input type="text" name="databaseServer" value="<?php echo $databaseServer; ?>" />
					<div class="description">Veuillez indiquer l'adresse du serveur où se trouve la base de donnée.<br />La plupart du temps, il s'agit de "localhost"</div>
				</div>
				<div class="item<?php if(isset($error['databaseUsername'])) echo ' error'; ?>">
					<label>Identifiant</label><input type="text" name="databaseUsername" value="<?php echo $databaseUsername; ?>" />
					<div class="description">Veuillez indiquer le nom d'utilisateur requis pour se connecter à la base de données.<br />Si vous n'êtes pas sûr, renseignez vous auprès de votre hébergeur.</div>
				</div>
				<div class="item<?php if(isset($error['databasePassword'])) echo ' error'; ?>">
					<label>Mot de passe</label><input type="password" name="databasePassword" value="<?php echo $databasePassword; ?>" />
					<div class="description">Veuillez indiquer le mot de passe requis pour se connecter à la base de données.<br />Si vous n'êtes pas sûr, renseignez vous auprès de votre hébergeur.</div>
				</div>
				<div class="item<?php if(isset($error['databaseBase'])) echo ' error'; ?>">
					<label>Base</label><input type="text" name="databaseBase" value="<?php echo $databaseBase; ?>" />
					<div class="description">Veuillez indiquer le nom de la base de données où seront stockées les données du site.<br />Si vous n'êtes pas sûr, renseignez vous auprès de votre hébergeur.</div>
				</div>
					
				<h2>Mon site</h2>
				<div class="item<?php if(isset($error['websiteName'])) echo ' error'; ?>">
					<label>Nom du site</label><input type="text" name="websiteName" value="<?php echo $pageTitle; ?>" />
					<div class="description">Le nom de votre site s'affichera dans le titre de chaque page.</div>
				</div>
				<div class="item<?php if(isset($error['defaultModule'])) echo ' error'; ?>">
					<label>Module par défaut</label><select name="defaultModule">
						<?php
						foreach($modules as $module)
						{
							if($module != '.' && $module != '..' && is_dir('modules/'.$module))
								echo '<option value="'.$module.'"'.($module ==  $defaultModule ? ' selected="selected"' : '').'>'.$module.'</option>';
						}
						?>
					</select>
					<div class="description">Le module par défaut est la page qui sera affiché sur votre site.<br />Si vous n'êtes pas sûr, laissez la valeur par défaut : blog.</div>
				</div>
				<div class="item<?php if(isset($error['websiteDescription'])) echo ' error'; ?>">
					<label>Description du site</label><textarea name="websiteDescription"><?php echo $pageDescription; ?></textarea>
					<div class="description">Veuillez écrire une courte description de votre site.<br />Cette description sera utile pour les moteurs de recherche.</div>
				</div>
				<div class="item<?php if(isset($error['websiteKeywords'])) echo ' error'; ?>">
					<label>Mots clés du site</label><input type="text" name="websiteKeywords"  value="<?php echo $pageKeywords; ?>"/>
					<div class="description">Veuillez écrire une liste de mots clés séparés par des virgules.<br />Cette liste sera utile pour les moteurs de recherche.</div>
				</div>
				<div class="item<?php if(isset($error['websiteLanguage'])) echo ' error'; ?>">
					<label>Langage du site</label><select name="websiteLanguage">
						<?php
						foreach($languages as $language => $languageName)
							echo '<option value="'.$language.'"'.($language == $pageLanguage ? ' selected="selected"' : '').'>'.$languageName.'</option>';
						?>
					</select>
					<div class="description">Veuillez sélectionner la langue dans laquelle vous redigerez les pages de votre site.<br />Cette information sera utile pour les moteurs de recherche.</div>
				</div>
				
				<input type="submit" name="submit" value="Installer !" />
				
			</form>
		</div>
		
	</body>
</html>