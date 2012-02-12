<?php
	class Admin
	{
		public function __construct($core, $settings, $data)
		{
			header('Location: '.$core->getBaseURL().'blog/admin/articles');
			exit(0);
		}
		
		public function execute($core)
		{
		}
		
		public function display()
		{
		}
	}
?>