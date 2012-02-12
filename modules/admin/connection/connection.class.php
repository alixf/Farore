<?php
	class Connection
	{
		public function __construct($core, $settings, $data)
		{
			$this->connection = !(isset($data[0]) && $data[0] == 'disconnect');
		}

		public function execute($core)
		{
			$this->baseURL = $core->getBaseURL();
			
			$currentlyLogged = (isset($_SESSION['connected']) && $_SESSION['connected'] == true); 
			$this->actionPermitted = ($this->connection != $currentlyLogged);
		    $this->formSent = isset($_POST['submit']);
			
			if($this->connection)
			{
				$this->formActionURL = $core->getBaseURL().'admin/connection';
				
				if ($this->actionPermitted)
				{
				    if($this->formSent)
				    {
				    	$this->result = $_POST['username'] == $core->getAdminUsername() && $_POST['password'] == $core->getAdminPassword();
						
				        if($this->result)
				        {
				            $_SESSION['connected'] = true;	
							header('Location: '.$core->getBaseURL().'admin');
				        }
				    }
				}
				
				// Update page data
			    $core->setPageTitle($core->getPageTitle().' - Connexion');
				$core->setPageCanonicalLink($core->getBaseURL().'admin/connection');
			    $core->addPagePathStep('Administration', $core->getBaseURL().'admin');
			    $core->addPagePathStep('Connexion', $core->getBaseURL().'admin/connection');
			}
			else
			{
				$this->formActionURL = $core->getBaseURL().'admin/connection-disconnect';
				
				if ($this->actionPermitted)
				{
					// Proceed to deconnection
					$this->result = true;
					$_SESSION['connected'] = false;
				}
				
				// Update page data
			    $core->setPageTitle($core->getPageTitle().' - Déconnexion');
				$core->setPageCanonicalLink($core->getBaseURL().'admin/connection-disconnect');
			    $core->addPagePathStep('Administration', $core->getBaseURL().'admin');
			    $core->addPagePathStep('Déconnexion', $core->getBaseURL().'admin/connection-disconnect');
			}
		}
		
		public function display()
		{
			if($this->connection)
			{
				if ($this->actionPermitted)
				{
				    if($this->formSent)
				    {
				        if($this->result)
				        {
							// This message will currently never show up as a redirection is set up in the model
					        ?>
					        <div class="message success">Ces identifiants sont corrects. <a href="<?php echo $this->baseURL ?>admin">Aller à l'administration</a></div>
					        <?php
				        }
						else
						{
					        ?>
					        <div class="message error">Ces identifiants sont incorrects.</div>
					        
						    <div class="center">
						        <form method="POST" action="<?php echo $this->formActionURL; ?>">
						            <label for="username">Identifiant</label><input type="text" name="username" id="username" required /><br />
						            <label for="password">Mot de Passe</label><input type="password" name="password" id="password" required /><br />
						            <input type="submit" name="submit" value="Connexion" />
						        </form>
						    </div>
					        <?php
						}
				    }
					else
					{
					    ?>
					    <div class="center">
					        <form method="POST" action="<?php echo $this->formActionURL; ?>">
					            <label for="username">Identifiant</label><input type="text" name="username" id="username" required /><br />
					            <label for="password">Mot de Passe</label><input type="password" name="password" id="password" required /><br />
					            <input type="submit" name="submit" value="Connexion" />
					        </form>
					    </div>
					    <?php	
					}
				}
				else
				{
			        ?>
			        <div class="message success">Vous êtes déjà connecté. <a href="<?php echo $this->baseURL ?>admin">Aller à l'administration</a></div>
			        <?php
				}
			}
			else
			{
				if ($this->actionPermitted)
				{
			        ?>
			        <div class="message success">Vous avez été déconnecté avec succès. <a href="<?php echo $this->baseURL ?>admin">Aller à l'accueil</a></div>
			        <?php
				}
				else
				{
			        ?>
			        <div class="message success">Vous êtes déjà déconnecté. <a href="<?php echo $this->baseURL ?>">Aller à l'accueil</a></div>
			        <?php
				}
			}
		}
	
		private $connection;
		private $formSent;
		private $actionPermitted;
		private $result;
		private $formActionURL;
		private $baseURL;
	}
?>
