<?php
	class Configure
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
			$configFile = $core->getModulesPath() . $this->moduleName . '/configuration.xml';

			if ($this->formSent)
			{
				$document = new DomDocument();
				$document -> load($configFile);

				foreach ($document->getElementsByTagName('setting') as $setting)
				{
					$id = $setting->getAttribute('id');
					$type = $setting->getAttribute('type');

					if ($type == 'boolean')
						$setting->nodeValue = isset($_POST['setting_' . $id]) ? 'true' : 'false';
					else
						$setting->nodeValue = $_POST['setting_' . $id];
				}

				$this->success = ($document->save($configFile) != false);
			}

			if (file_exists($configFile))
			{
				$xml = simplexml_load_file($configFile);

				foreach ($xml->children() as $child)
				{
					if ($child -> getName() == 'setting')
					{
						$setting = array('value' => (string)$child);
						foreach ($child->attributes() as $attribute => $value)
							$setting[$attribute] = (string)$value;
						$this->settings[] = $setting;
					}
				}
			}
			else
				$error = 1;
			// No config file

			$core->setPageTitle($core->getPageTitle().' - Configuration du module ' . $this->moduleName);
			$core->setPageCanonicalLink($core->getBaseURL().'admin/modules/configure-' . $this->moduleName);
			$core->addPagePathStep('Administration', $core->getBaseURL().'admin');
			$core->addPagePathStep('Modules', $core->getBaseURL().'admin/modules');
			$core->addPagePathStep('Configuration du module ' . $this->moduleName, $core->getBaseURL().'admin/modules/configure-' . $this->moduleName);
		}

		public function display()
		{
			$inputTypes = array
			(
				'integer' => 'number',
				'string' => 'text',
				'email'  => 'email',
				'boolean' => 'checkbox',
				'color' => 'color',
				'tel' => 'tel'
			);	
			
			?>
			
			<h1>Configuration du module <?php echo $this->moduleName; ?></h1>
			
			<div class="actionBar">
				<a class="button disabled" <?php /* href="<?php echo $this->baseURL; ?>admin/modules/edit-<?php echo $this->moduleName; ?>" */ ?>>Éditer le module</a>
				<a class="button disabled" <?php /* href="<?php echo $this->baseURL; ?>admin/modules/export-<?php echo $this->moduleName; ?>" */ ?>>Exporter le module</a>
				<a class="button" href="<?php echo $this->baseURL; ?>admin/modules/delete-<?php echo $this->moduleName; ?>">Supprimer le module</a>
			</div>
			
			<?php
			if(!empty($this->settings))
			{				
				if($this->formSent)
				{
					if($this->success)
					{
						?><div class="message success">La configuration à été enregistrée avec succès.</div><?php
					}
					else
					{
						?><div class="message error">La configuration n'a pas pu être enregistrée.</div><?php
					}
				}
				
				?>
		
				<form method="POST" action="<?php echo $this->baseURL ?>admin/modules/configure-<?php echo $this->moduleName; ?>" class="configForm">
					<?php
					foreach($this->settings as $setting)
					{
						if(isset($inputTypes[$setting['type']]))
							$inputType = $inputTypes[$setting['type']];
						else
							$inputType = 'text';
						
						?>
						<div class="setting">
						<label for="setting_<?php echo $setting['id']; ?>"><?php echo $setting['name']; ?></label>
						<?php
							if($setting['type'] == 'boolean')
							{
								?><input type="<?php echo $inputType; ?>" name="setting_<?php echo $setting['id']; ?>" id="setting_<?php echo $setting['id']; ?>" <?php echo $setting['value'] == 'true' ? 'checked="checked" ' : ''; ?>/><?php
							}
							else
							{
								?><input type="<?php echo $inputType; ?>" name="setting_<?php echo $setting['id']; ?>" id="setting_<?php echo $setting['id']; ?>" value="<?php echo $setting['value']; ?>" /><?php
							}	
						?>
						</div>
						<?php
					}
					?>
					<div class="configActions">
						<input type="submit" value="Enregistrer la configuration" name="submit" />
						<input type="reset" value="Effacer les changements" name="reset" />
					</div>
				</form>
				<?php
			}
		}

		private $baseURL;
		private $moduleName;
		private $formSent;
		private $settings = array();
		private $success;
	}
?>
