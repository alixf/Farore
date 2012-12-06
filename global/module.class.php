<?php
	class Module
	{
		public function __construct($core)
		{
			$this->core = $core;
			$this->accessRights = self::$ALL;
		}
		
		public static function install($database)
		{
			return true;
		}
		
		public function getRights()
		{
			return $this->accessRights;
		}
		
		public function execute()
		{
			return true;
		}
		
		public function display()
		{
		}
		
		public static $NONE 	= 0;
		public static $OTHERS 	= 1;
		public static $USER	 	= 2;
		public static $ADMIN 	= 4;
		public static $ALL		= 7;
	
		protected $core;
		protected $accessRights;
	}
?>