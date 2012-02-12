<?php
	class Export
	{
		public function __construct($core, $settings, $data)
		{
			if (!$core->isAdmin())
			{
				header('Location: '.$core->getBaseURL().'admin/connection');
				exit(0);
			}
			// TODO : Implement module export
		}
		
		public function execute($core)
		{
		}
		
		public function display()
		{
		}
	}
?>
