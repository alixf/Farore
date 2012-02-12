<?php
	class Modules
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
		
		public function execute($core)
		{
		    $directory = dir($core->getModulesPath());
		    while(false !== ($entry = $directory->read()))
		    {
		    	if($entry != '.' && $entry != '..')
				{
					$enabled = ($entry[0] != '.');
					$moduleName = ($enabled ? $entry : substr($entry, 1));
					$this->modules[$moduleName] = array('version' => 1.0, 'enabled' => $enabled);
				}
			}
		    $directory->close();
			
			ksort($this->modules, SORT_STRING);
			
		    $core->setPageTitle($core->getPageTitle().' - Modules');
			$core->setPageCanonicalLink($core->getBaseURL().'admin/modules');
		    $core->addPagePathStep('Administration', $core->getBaseURL().'admin');
		    $core->addPagePathStep('Modules', $core->getBaseURL().'admin/modules');
		}
		
		public function display()
		{
		    ?>
		    <h1>Liste des modules</h1>
		    
		    <div class="actionBar">
			    <a class="button" href="<?php echo $this->baseURL ?>admin/modules/add">Créer un module</a>
			    <a class="button" href="<?php echo $this->baseURL ?>admin/modules/import">Importer un module</a>
			</div>
			    
		    <table class="moduleTable">
		        <tr>
		            <th class="name">Module</th>
		            <th class="version">Version</th>
		            <th class="configure">Configurer</th>
		            <th class="toggle">Activation</th>
		        </tr>
		
		        <?php
		        if(!empty($this->modules))
				{
			        foreach ($this->modules as $name => $module)
			        {
			            ?>
			            <tr>
			                <td class="name"><?php echo $name; ?></td>
			                <td class="version"><?php echo $module['version']; ?></td>
			                <td class="configure"><a class="button" href="<?php echo $this->baseURL ?>admin/modules/configure-<?php echo $name; ?>">Configurer</a></td>
			                
			                <?php
			                if($module['enabled'])
							{
			                	?>
								<td class="toggle">
									<a class="button" href="<?php echo $this->baseURL ?>admin/modules/disable-<?php echo $name; ?>">Désactiver</a>
								</td>
								<?php
			                }
			                else
			                {
			                	?>
			                	<td class="toggle">
			                		<a class="button" href="<?php echo $this->baseURL ?>admin/modules/enable-<?php echo $name; ?>">Activer</a>
			                	</td>
			                	<?php
			                }
			                ?>
			            </tr>
			            <?php
			        }
				}
				else
				{
					?><tr><td colspan="5"><div class="message error">Aucun module n'est installé</div></td></tr><?php
				}
		        ?>
		    </table>
		    <?php
		}

		private $baseURL;
		private $modules = array();
	}
?>
