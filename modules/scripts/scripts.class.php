<?php
	require_once 'global/module.class.php';
	
	class Scripts extends Module
	{
		public function __construct($core)
		{
			parent::__construct($core);
			
			$this->types = array
			(
				'js' =>	array(Minify::JS,	'application/javascript')
			);
		}
		
		public function execute()
		{
			// Retrieve complete filename from URL
			$this->fileName = $this->core->getBasePath().implode('/', $this->core->getModuleArray());
			$moduleData = $this->core->getModuleData();
			if(!empty($moduleData))
				$this->fileName .= '-'.implode('-', $moduleData);
			
			// Security check : prevent going to parent folders
			if(strpos($this->fileName, '..') !== false)
			{
				$this->core->parseURL('error/403');
				$this->success = false;
				return false;
			}

			// Catch a raw (without minify) request		
			$noMinify = substr($this->fileName, -4) == '-raw';
			if($noMinify)
				$this->fileName = substr($this->fileName, 0, -4);
			
			// Check file existence
			$this->success = file_exists($this->fileName);
			
			if($this->success)
			{
				$this->core->setIncludeTemplate(false);
				$this->core->setIncludeAjaxTags(false);
				
				$this->core->setMinifyType(Minify::NONE);
				$extension = pathinfo($this->fileName, PATHINFO_EXTENSION);
				if(isset($this->types[$extension]))
				{
					$typeData = $this->types[$extension];
					if(!$noMinify)
						$this->core->setMinifyType($typeData[0]);
					header('Content-Type:'.$typeData[1]);
				}
				
				$duration = strtotime("+1 year");
				header('Last-Modified: '.date('r',filemtime($this->fileName)));
				header('Cache-Control: public, max-age='.$duration);
				header('Expires: '.date('r',$duration));
			}
			else
			{
				$this->core->parseURL('error/404');
				return true;
			}
			
			// Return false to stop executing further modules
			return false;
		}
		
		public function display()
		{
			if($this->success)
				echo file_get_contents($this->fileName);
		}
		
		private $fileName;
		private $types;
	}
?>