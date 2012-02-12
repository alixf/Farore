<?php
	class Import
	{
		public function __construct($core, $settings, $data)
		{
			if (!$core->isAdmin())
			{
				header('Location: '.$core->getBaseURL().'admin/connection');
				exit(0);
			}
			
			$this->baseURL = $core->getBaseURL();
			$this->success = false;
			$this->formSent = isset($_POST['submit']);
			$this->moduleName = '';
			$this->error = 0;
		}
		
		public function execute($core)
		{
			if($this->formSent)
			{
				$this->moduleName = pathinfo($_FILES['moduleFile']['name'], PATHINFO_FILENAME);
				$extractPath = $core->getModulesPath().$this->moduleName.'/';
				
				if(!is_dir($extractPath))
				{
					$zip = new ZipArchive;
					$this->success = $zip->open($_FILES['moduleFile']['tmp_name']);
					if ($this->success === TRUE)
					{
					    $this->success = $zip->extractTo($extractPath);
					    $zip->close();
					}
				}
				else
					$this->error = 1; // Module already exists
			}
			
		    $core->setPageTitle($core->getPageTitle().' - Importer un module');
			$core->setPageCanonicalLink($core->getBaseURL().'admin/modules/import');
		    $core->addPagePathStep('Administration', $core->getBaseURL().'admin');
		    $core->addPagePathStep('Modules', $core->getBaseURL().'admin/modules');
		    $core->addPagePathStep('Importer un module', $core->getBaseURL().'admin/modules/import');
		}
		
		public function display()
		{
			if($this->formSent)
			{
				if($this->success)
				{
					?><div class="message success">Le module <?php echo $this->moduleName; ?> a été importé avec succès.</div><?php
				}
				else if($this->error == 1)
				{
					?><div class="message error">Le module <?php echo $this->moduleName; ?> existe déjà.</div><?php
				}
				else
				{
					?><div class="message error">L'importation du module <?php echo $this->moduleName; ?> a échoué.</div><?php
				}
			}
			else
			{
				?>
				<form method="POST" action="<?php echo $this->baseURL; ?>admin/modules/import" enctype="multipart/form-data">
					<input name="moduleFile" type="file" />
					<input type="submit" value="Upload File" name="submit" />
				</form>
				<?php
			}
		}

		private $baseURL;
		private $success;
		private $formSent;
		private $moduleName;
		private $error;
	}
?>
