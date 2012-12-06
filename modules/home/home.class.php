<?php
	require_once 'global/module.class.php';
	
	class Home extends Module
	{
		public function __construct($core)
		{
			parent::__construct($core);
		}
		
		public function execute()
		{
		    $this->core->setPage('Accueil', $this->core->getBaseURL());

			return true;
		}
		
		public function display()
		{
			?>
			<?php
		}
	}
?>