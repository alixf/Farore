<?php
	class Core
	{
		const MODULENONELEFT = -1;
		const MODULESUCCESS = 0;
		const MODULENOTFOUND = 404;
		const MODULEUNAUTHORIZED = 403;
		
		public function __construct()
		{
			setlocale(LC_ALL, $this->pageLanguage);
			session_start();
			
			$this->basePath = substr(__DIR__, 0, -6);

			// Load Framework settings
			$this->readSettings('global/configuration.xml');

			// Create database connection
			if ($this->databaseDriver != null)
			{
				try
				{
					$driverOptions = NULL;
					if($this->databaseDriver == "mysql")
						 $driverOptions = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'');
					$this->database = new PDO($this->databaseDriver . ':host=' . $this->databaseHost . ';dbname=' . $this->databaseBase, $this->databaseUsername, $this->databasePassword,$driverOptions);
					if($this->databaseDriver == "mysql")
						$this->database->setAttribute(PDO::ERRMODE_SILENT, PDO::ERRMODE_EXCEPTION);
				}
				catch (PDOException $e)
				{
					echo 'Error connecting to the database ' . $e->getMessage();
				}
			}

			// Render
			$this->includeAjaxTags = false;
			$this->includeTemplate = true;
			$this->minifyType = 0;
			header('Content-type:text/html; charset='.$this->pageEncoding);
			
			// Rights
			$this->userRights = isset($_SESSION['rights']) ? $_SESSION['rights'] : 1;
			
			// Errors
			$this->errors = array();
		}

		public function readSettings($configurationFileName)
		{
			$document = new DOMDocument();
			$document->load($configurationFileName);
			$settingsList = $document->getElementsByTagName('setting');
			foreach ($settingsList as $setting)
			{
				$value = $setting->nodeValue;
				switch($setting->getAttribute('id'))
				{
				case 'baseURL' :
					$this->baseURL = $value;
					break;
				case 'urlDataSeparator' :
					$this->urlDataSeparator = $value;
					break;
				case 'urlParameterName' :
					$this->urlParameterName = $value;
					break;
				case 'urlParameterSeparator' :
					$this->urlParameterSeparator = $value;
					break;
				case 'databaseDriver' :
					$this->databaseDriver = $value;
					break;
				case 'databaseHost' :
					$this->databaseHost = $value;
					break;
				case 'databaseBase' :
					$this->databaseBase = $value;
					break;
				case 'databaseUsername' :
					$this->databaseUsername = $value;
					break;
				case 'databasePassword' :
					$this->databasePassword = $value;
					break;
				case 'adminUsername' :
					$this->adminUsername = $value;
					break;
				case 'adminPassword' :
					$this->adminPassword = $value;
					break;
				case 'pageTitle' :
					$this->pageTitle = $value;
					$this->pageBaseTitle = $value;
					break;
				case 'pageDescription' :
					$this->pageDescription = $value;
					break;
				case 'pageKeywords' :
					$this->pageKeywords = explode(', ', $value);
					break;
				case 'pageLanguage' :
					$this->pageLanguage = $value;
					break;
				case 'pageCanonicalLink' :
					$this->pageCanonicalLink = $value;
					break;
				case 'pageReadingDir' :
					$this->pageReadingDir = $value;
					break;
				case 'pageEncoding' :
					$this->pageEncoding = $value;
					break;
				case 'defaultModule' :
					$this->defaultModule = $value;
					break;
				case 'theme' :
					$this->theme = $value;
					break;
				default :
					break;
				}
			}

			$this->themeURL = $this->baseURL.'themes/'.$this->theme.'/';
		}

		public function readModuleSettings()
		{
			$this->moduleSettings = array();
			$settingsFile = $this->getModulePath().'configuration.xml';

			if (file_exists($settingsFile))
			{
				$document = new DomDocument();
				$document->load($settingsFile);

				foreach ($document->getElementsByTagName('setting') as $setting)
				{
					switch($setting->getAttribute('type'))
					{
					case 'integer' :
						$this->moduleSettings[$setting->getAttribute('id')] = intval($setting->nodeValue);
						break;

					case 'boolean' :
						$this->moduleSettings[$setting->getAttribute('id')] = ($setting->nodeValue == 'true');
						break;

					default :
						$this->moduleSettings[$setting->getAttribute('id')] = $setting->nodeValue;
						break;
					}
				}
			}
		}

		public function parseURL($url)
		{
			// Clear url
			$url = trim($url, "/");

			// If the url start with 'raw', set up specific render settings for raw pages
			if (substr($url, 0, 3) == 'raw')
			{
				$this->setIncludeAjaxTags(true);
				$this->setIncludeTemplate(false);
				$url = substr($url, 3);
				$url = trim($url, "/");
			}

			// If there's no module specified in the url, go to the default module
			$url = empty($url) ? $this->defaultModule : $url;

			// Extract modules and data
			$this->modules = explode($this->urlParameterSeparator, $url);

			$tmp = explode($this->urlDataSeparator, $this->modules[count($this->modules) - 1]);
			$this->modules[count($this->modules) - 1] = $tmp[0];
			$this->moduleData = array();
			for ($i = 1; $i < count($tmp); ++$i)
				$this->moduleData[] = $tmp[$i];

			$this->moduleIndex = -1;

			// Fix bugs when a module have the same name as a file at the root directory
			for ($i = 0; $i < count($this->modules); ++$i)
			{
				if (substr($this->modules[$i], -4) == '.php')
					$this->modules[$i] = substr($this->modules[$i], 0, -4);
			}
		}

		public function getAjaxTags()
		{
			$res = '';
			$res .= '<ajax:module>'.$this->getModuleName().'</ajax:module>';
			$res .= '<ajax:title>'.$this->getPageTitle().'</ajax:title>';
			$res .= '<ajax:canonical>'.$this->getPageCanonicalLink().'</ajax:canonical>';
			$res .= '<ajax:description>'.$this->getPageDescription().'</ajax:description>';
			$res .= '<ajax:keywords>'.implode(', ', $this->getPageKeywords()).'</ajax:keywords>';
			$tmp = array();
			foreach ($this->getPagePath() as $pageStep)
				$tmp[] = implode('|', $pageStep);
			$res .= '<ajax:path>'.implode(';', $tmp).'</ajax:path>';
			return $res;
		}

		public function createModule()
		{
			$this->nextModule();
			
			if($this->moduleIndex >= count($this->modules))
				return self::MODULENONELEFT;
			
			$moduleName = $this->getModuleName();

			// Include module
			if (file_exists($this->getModulePath().$moduleName.'.class.php'))
				require_once ($this->getModulePath().$moduleName.'.class.php');
			else
				return self::MODULENOTFOUND;

			$moduleClassName = ucfirst($moduleName);
			if (is_numeric(substr($moduleClassName, 0, 1)))
				$moduleClassName = 'Module'.$moduleClassName;
			
			$this->module = new $moduleClassName($this, $this->moduleIndex > count($this->modules) - 1);
			
			return $this->module->getRights() & $this->getUserRights() ? self::MODULESUCCESS : self::MODULEUNAUTHORIZED;
		}

		public function nextModule()
		{
			$this->moduleIndex = min($this->moduleIndex+1, count($this->modules));
		}

		public function getIncludeAjaxTags()
		{
			return $this->includeAjaxTags;
		}

		public function setIncludeAjaxTags($includeAjaxTags)
		{
			$this->includeAjaxTags = $includeAjaxTags;
		}

		public function getModuleArray()
		{
			return $this->modules;
		}

		public function getIncludeTemplate()
		{
			return $this->includeTemplate;
		}

		public function setIncludeTemplate($includeTemplate)
		{
			$this->includeTemplate = $includeTemplate;
		}

		public function getDatabase()
		{
			return $this->database;
		}

		public function getModule()
		{
			return $this->module;
		}

		public function getModuleName()
		{
			return $this->modules[min($this->moduleIndex, count($this->modules)-1)];
		}

		public function getModulePath()
		{
			$res = $this->getBasePath().'modules/';
			for ($i = 0; $i <= $this->moduleIndex; ++$i)
				$res .= $this->modules[$i].'/';
			return $res;
		}

		public function getModuleData()
		{
			return $this->moduleData;
		}
		
		public function setModuleData($moduleData)
		{
			$this->moduleData = $moduleData;
		}

		public function getModuleSettings()
		{
			return $this->moduleSettings;
		}

		public function getMinifyType()
		{
			return $this->minifyType;
		}

		public function setMinifyType($minifyType)
		{
			$this->minifyType = $minifyType;
		}

		public function getBasePath()
		{
			return $this->basePath;
		}

		public function getBaseURL()
		{
			return $this->baseURL;
		}

		public function getUrlDataSeparator()
		{
			return $this->urlDataSeparator;
		}

		public function getUrlParameterName()
		{
			return $this->urlParameterName;
		}

		public function getUrlParameterSeparator()
		{
			return $this->urlParameterSeparator;
		}

		public function getDatabaseDriver()
		{
			return $this->databaseDriver;
		}

		public function getDatabaseHost()
		{
			return $this->databaseHost;
		}

		public function getDatabaseBase()
		{
			return $this->databaseBase;
		}

		public function getDatabaseUsername()
		{
			return $this->databaseUsername;
		}

		public function getDatabasePassword()
		{
			return $this->databasePassword;
		}

		public function getAdminUsername()
		{
			return $this->adminUsername;
		}

		public function getAdminPassword()
		{
			return $this->adminPassword;
		}

		public function getPageTitle()
		{
			return $this->pageTitle;
		}

		public function getPageBaseTitle()
		{
			return $this->pageBaseTitle;
		}

		public function getPagePath()
		{
			return $this->pagePath;
		}

		public function getPageDescription()
		{
			return $this->pageDescription;
		}

		public function getPageKeywords()
		{
			return $this->pageKeywords;
		}

		public function getPageLanguage()
		{
			return $this->pageLanguage;
		}

		public function getPageCanonicalLink()
		{
			return $this->pageCanonicalLink;
		}

		public function getPageReadingDir()
		{
			return $this->pageReadingDir;
		}

		public function getPageEncoding()
		{
			return $this->pageEncoding;
		}

		public function getDefaultModule()
		{
			return $this->defaultModule;
		}

		public function getUserRights()
		{
			return $this->userRights;
		}

		public function setUserRights($rights)
		{
			$this->userRights = $rights;
		}

		public function setPageTitle($pageTitle)
		{
			$this->pageTitle = $pageTitle;
		}

		public function setPageCanonicalLink($pageCanonicalLink)
		{
			$this->pageCanonicalLink = $pageCanonicalLink;
		}

		public function addPagePathStep($pageName, $pageLink)
		{
			$this->pagePath[] = array($pageName, $pageLink);
		}

		public function setPageKeywords($pageKeywords)
		{
			$this->pageKeywords = $pageKeywords;
		}

		public function setPageDescription($pageDescription)
		{
			$this->pageDescription = $pageDescription;
		}

		public function setPage($title, $url)
		{
			$this->setPageTitle($this->getPageBaseTitle().' - '.$title);
			$this->setPageCanonicalLink($url);
			$this->addPagePathStep($title, $url);
		}
		
		public function setPageOverride($title, $url)
		{
			$this->setPageTitle($this->getPageBaseTitle().' - '.$title);
			$this->setPageCanonicalLink($url);
			$this->pagePath = array();
			$this->addPagePathStep($title, $url);
		}
		
		public function isModuleFinal()
		{
			return $this->moduleIndex >= count($this->modules)-1;
		}
		
		// Database
		private $database;

		// Module queue
		private $module;
		private $modules;
		private $moduleIndex;
		private $moduleData;
		private $moduleSettings;

		// Misc
		private $minifyType;
		private $includeAjaxTags;
		private $includeTemplate;
		
		//Rights
		private $userRights;
		
		// Errors
		private $errors;

		// Settings
		private $basePath;
		private $baseURL;
		private $theme;
		private $themeURL;
		private $urlDataSeparator;
		private $urlParameterName;
		private $urlParameterSeparator;
		private $databaseDriver;
		private $databaseHost;
		private $databaseBase;
		private $databaseUsername;
		private $databasePassword;
		private $adminUsername;
		private $adminPassword;
		private $pageTitle;
		private $pageBaseTitle;
		private $pagePath = array();
		private $pageDescription;
		private $pageKeywords;
		private $pageLanguage;
		private $pageCanonicalLink;
		private $pageReadingDir;
		private $pageEncoding;
		private $defaultModule;
	}
?>
