<?php
	require_once 'global/module.class.php';

	class Error extends Module
	{
		public function __construct($core)
		{
			parent::__construct($core);
		}

		public function execute()
		{	
			header('HTTP/1.0 500 Internal Server Error');
			
		    $this->core->setPage('Erreur', $this->core->getBaseURL().'error');
			
			return true;
		}
		
		public function display()
		{
			?>
			<h1>Erreur</h1>
			<div class="message error">
				Une erreur indeterminée est survenue. 
				<a href="<?php echo $this->core->getBaseURL(); ?>">Aller à l'accueil</a>
			</div>
			<?php
		}
	}
?>
