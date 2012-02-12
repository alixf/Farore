<?php
	class Delete
	{
		public function __construct($core, $settings, $data)
		{
			if (!$core->isAdmin())
			{
				header('Location: '.$core->getBaseURL().'admin/connection');
				exit(0);
			}
			
			$this->baseURL = $core->getBaseURL();
			$this->moduleName = isset($data[0]) ? $data[0] : '';
			$this->formSent = isset($_POST['submit']);
			$this->success = false;
		}
		
		public function execute($core)
		{
			function rrmdir($dir)
			{
				$success = is_dir($dir);
				
				if($success)
				{
			    	$objects = scandir($dir); 
			    	foreach ($objects as $object)
			    	{ 
				      	if ($object != '.' && $object != '..')
						{
			 				if (filetype($dir.'/'.$object) == 'dir')
								$success = $success && rrmdir($dir.'/'.$object); 
			 				else
			 					$success = $success && unlink($dir.'/'.$object);
						}
					}
					reset($objects);
					$success = $success && rmdir($dir);
				}
				return $success;
			}
			
			if($this->formSent)
			{
				if(file_exists($core->getModulesPath().$this->moduleName))
					$this->success = rrmdir($core->getModulesPath().$this->moduleName);
				else if(file_exists($core->getModulesPath().'.'.$this->moduleName))
					$this->success = rrmdir($core->getModulesPath().'.'.$this->moduleName);
				else
					$this->success = false;
			}
			
		    $core->setPageTitle($core->getPageTitle().' - Supprimer un module : '.$this->moduleName);
			$core->setPageCanonicalLink('/admin/modules/delete-'.$this->moduleName);
		    $core->addPagePathStep('Administration', $core->getBaseURL().'admin');
		    $core->addPagePathStep('Modules', $core->getBaseURL().'admin/modules');
		    $core->addPagePathStep('Supprimer un module : '.$this->moduleName, $core->getBaseURL().'admin/modules/delete-'.$this->moduleName);
		}
		
		public function display()
		{
			if($this->formSent)
			{
				if($this->success)
				{
			        ?><div class="message success">Le module <?php echo $this->moduleName; ?> a été supprimé.<a href="<?php echo $this->baseURL ?>admin/modules">Aller à la liste des modules</a></div><?php
				}
				else
				{
			        ?><div class="message error">Une erreur est survenue lors de la suppression du module <?php echo $this->moduleName; ?>.<a href="<?php echo $this->baseURL ?>admin/modules">Aller à la liste des modules</a></div><?php
				}
			}
			else
			{
				?>
				<form method="POST" action="<?php echo $this->baseURL ?>admin/modules/delete-<?php echo $this->moduleName; ?>">
					Êtes vous sûr de vouloir supprimer définitivement ce module ?
					<input type="submit" name="submit" value="Supprimer le module" name="submit" />
				</form>
				<?php
			}
		}

		private $baseURL;
		private $moduleName;
		private $formSent;
		private $success;
	}
?>
