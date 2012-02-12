<?php
	class Blog
	{
		public function __construct($core, $settings, $data)
		{
			require_once($core->getModulesPath().'blog/page/page.class.php');
			
			$this->pageModule = new Page($core, $settings, $data);
		}
		
		public static function install($database)
		{
			$success = true;
			
			// Create blog database
			$queryStr = "CREATE TABLE IF NOT EXISTS `blog_articles` (
				`id`  int UNSIGNED NOT NULL AUTO_INCREMENT ,
				`title`  text NULL ,
				`date`  int NULL ,
				`categories`  text NULL ,
				`content`  text NULL ,
				PRIMARY KEY (`id`)
				)
				;";
			
			$query = $database->prepare($queryStr);
			$success = $success && $query->execute();
			
			$content = 'Bienvenue sur votre nouveau site web !
			
			Pour commencer à le configurer ou écrire des articles, rendez vous dans l\'administration, en suivant le lien en bas de page.
			
			Vous pourrez également y supprimer cet article temporaire.
			
			Amusez vous bien :)';
			
			$query = $database->prepare('INSERT INTO blog_articles VALUES(\'\', :title, :date, :categories, :content)');
			$success = $success && $query->execute(array('title' => 'Bienvenue !', 'date' => time(), 'categories' => 'Bienvenue', 'content' => $content));
			
			return ($database->exec($query) == false);
		}
		
		public function execute($core)
		{
			$this->pageModule->execute($core, false);
			
		    $core->setPageTitle($core->getPageTitle().' - Blog');
			$core->setPageCanonicalLink($core->getBaseURL().'blog');
		    $core->addPagePathStep('Blog', $core->getBaseURL().'blog');
		}
		
		public function display()
		{
			$this->pageModule->display();
		}
	
		private $pageModule;
	}
?>