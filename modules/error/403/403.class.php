<?php
	require_once 'global/module.class.php';

	class Module403 extends Module
	{
		public function __construct($core)
		{
			parent::__construct($core);
		}
		
		public function execute()
		{
			header('HTTP/1.0 403 Forbidden');
			
		    $this->core->setPage('Erreur 403', $this->core->getBaseURL().'error/403');
			
			return true;
		}
		
		public function display()
		{
			?>
			<h1>Erreur 403</h1>
			<div class="message error">
				Vous n'avez pas accès à cette page. 
				<a href="<?php echo $this->core->getBaseURL(); ?>">Aller à l'accueil</a>
			</div>
			<?php
		}
	}
?>
