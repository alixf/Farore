<?php
	class Disable
	{
		public function __construct($core, $settings, $data)
		{
			if (!$core->isAdmin())
			{
				header('Location: '.$core->getBaseURL().'admin/connection');
				exit(0);
			}
			
			$this->moduleName = isset($data[0]) ? $data[0] : '';
			$this->success = false;
			$this->baseURL = $core->getBaseURL();
		}
		
		public function execute($core)
		{
			if(!empty($this->moduleName))
				$this->success = rename($core->getModulesPath().$this->moduleName, $core->getModulesPath().'.'.$this->moduleName);
			
		    $core->setPageTitle($core->getPageTitle().' - Désactiver un module : '.$this->moduleName);
			$core->setPageCanonicalLink('/admin/modules/disable-'.$this->moduleName);
		    $core->addPagePathStep('Administration', $core->getBaseURL().'admin');
		    $core->addPagePathStep('Modules', $core->getBaseURL().'admin/modules');
		    $core->addPagePathStep('Désactiver un module : '.$this->moduleName, $core->getBaseURL().'admin/modules/disable-'.$this->moduleName);
		}
		
		public function display()
		{
			if(empty($this->moduleName))
			{
		        ?><div class="message error">Aucun module n'a été sélectionné.<a href="<?php echo $this->baseURL ?>admin/modules">Aller à la liste des modules</a></div><?php
			}
			else if(!$this->success)
			{
		        ?><div class="message error">Une erreur est survenue lors de la désactivation du module <?php echo $this->moduleName; ?>.<a href="<?php echo $this->baseURL ?>admin/modules">Aller à la liste des modules</a></div><?php
			}
			else
			{
		        ?><div class="message success">Le module <?php echo $this->moduleName; ?> a été désactivé.<a href="<?php echo $this->baseURL ?>admin/modules">Aller à la liste des modules</a></div><?php
			}
		}

		private $moduleName;
		private $success;
		private $baseURL;
	}
?>
