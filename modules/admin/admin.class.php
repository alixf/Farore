<?php
	class Admin
	{
		public function __construct($core, $settings, $data)
		{
			if (!$core->isAdmin())
			{
				header('Location: '.$core->getBaseURL().'admin/connection');
				exit(0);
			}
			$this->baseURL = $core->getBaseURL();
		}
		
		public static function install($database)
		{
			return true;
		}
		
		public function execute($core)
		{
			foreach(scandir($core->getModulesPath()) as $module)
			{
				if ($module != "." && $module != ".." && file_exists($core->getModulesPath().$module.'/admin') && is_dir($core->getModulesPath().$module.'/admin'))
					$this->modules[] = $module;
			}
			
		    $core->setPageTitle($core->getPageTitle().' - Administration');
			$core->setPageCanonicalLink($core->getBaseURL().'admin');
		    $core->addPagePathStep('Administration', $core->getBaseURL().'admin');
		}
		
		public function display()
		{
			?>
			<h1>Général</h1>
			<a href="<?php echo $this->baseURL; ?>admin/modules">Modules</a>
			<!-- a href="<?php echo $this->baseURL; ?>admin/configuration">Configuration</a-->
			<h1>Modules</h1>
			<?php
			foreach($this->modules as $module)
			{
				?><a href="<?php echo $this->baseURL.$module; ?>/admin"><?php echo ucfirst($module); ?></a><?php
			}
		}
		
		private $modules = array();
		private $baseURL;
	}
?>
