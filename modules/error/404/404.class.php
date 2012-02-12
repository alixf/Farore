<?php
	class Module404
	{
		public function __construct($core, $settings, $data)
		{
		}
		
		public function execute($core)
		{
			$this->baseURL = $core->getBaseURL();
			
		    $core->setPageTitle($core->getPageTitle().' - Erreur 404');
		    $core->setPageCanonicalLink('/error/404');
		    $core->addPagePathStep('Erreur 404', '/error/404');
		}
		
		public function display()
		{
			?>
			<div class="message error">Erreur 404 : La page que vous recherchez n'existe pas. <a href="<?php echo $this->baseURL; ?>">Aller à l'accueil</a></div>
			<?php
		}
		
		private $baseURL;
	}
?>
