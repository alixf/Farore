<?php
	class Add
	{
		public function __construct($core, $settings, $data)
		{
			if (!$core->isAdmin())
			{
				header('Location: '.$core->getBaseURL().'admin/connection');
				exit(0);
			}
			
			$this->baseURL = $core->getBaseURL();
			$this->formSent = isset($_POST['submit']);
			$this->moduleName = isset($_POST['moduleName']) ? $_POST['moduleName'] : '';
			$this->success = false;
			$this->error = 0;
		}

		public function execute($core)
		{
			//TODO : Make checks on given moduleName

			if ($this->formSent && !empty($this->moduleName))
			{
				if (file_exists($core->getModulesPath() . $this->moduleName))
					$this->error = 1;
				else
					$this->success = mkdir($core->getModulesPath() . $this->moduleName, 0777, true);
			}

			$core->setPageTitle($core->getPageTitle().' - Créer un module');
			$core->setPageCanonicalLink($core->getBaseURL().'admin/modules/add');
			$core->addPagePathStep('Administration', $core->getBaseURL().'admin');
			$core->addPagePathStep('Modules', $core->getBaseURL().'admin/modules');
			$core->addPagePathStep('Créer un module', $core->getBaseURL().'admin/modules/add');
		}

		public function display()
		{
			if($this->formSent)
			{
				if($this->success)
				{
			        ?><div class="message success">Le module <?php echo $this->moduleName; ?> a été créé.<a href="<?php echo $this->baseURL ?>admin/modules">Aller à la liste des modules</a></div><?php
				}
				else
				{
					switch($this->error)
					{
					case 1 :
				        ?><div class="message error">Le module <?php echo $this->moduleName; ?> existe déjà.<a href="<?php echo $this->baseURL ?>admin/modules">Aller à la liste des modules</a></div><?php
						break;
					default :
				        ?><div class="message error">Une erreur est survenue lors de la création du module <?php echo $this->moduleName; ?>.<a href="<?php echo $this->baseURL ?>admin/modules">Aller à la liste des modules</a></div><?php
						break;
					}
				}
			}
			else
			{
				?>
				<form method="POST" action="<?php echo $this->baseURL ?>admin/modules/add">
					<label for="moduleName">Nom du module : </label>
					<input id="moduleName" type="text" name="moduleName" required />
					<input type="submit" name="submit" value="Créer le module" name="submit" />
				</form>
				<?php
			}
		}

		private $baseURL;
		private $formSent;
		private $success;
		private $error;
	}
?>
