<?php
	class Error
	{
		public function __construct($core, $settings, $data)
		{
		}
		
		public static function install($database)
		{
			return true;
		}
		
		public function execute($core)
		{
			$this->baseURL = $core->getBaseURL();
			
		    $core->setPageTitle($core->getPageTitle().' - Erreur');
		    $core->setPageCanonicalLink('/error');
		    $core->addPagePathStep('Erreur', '/error');
		}
		
		public function display()
		{
			?>
			<div class="message error">Erreur : Une erreur indeterminée est survenue. <a href="<?php echo $this->baseURL; ?>">Aller à l'accueil</a></div>
			<?php
		}
		
		private $baseURL;
	}
?>
