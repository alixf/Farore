<?php
	// Create blog database
	$query = "CREATE TABLE IF NOT EXISTS `blog_articles` (
		`id`  int UNSIGNED NOT NULL AUTO_INCREMENT ,
		`title`  text NULL ,
		`date`  int NULL ,
		`categories`  text NULL ,
		`content`  text NULL ,
		PRIMARY KEY (`id`)
		)
		;";
	
	if($db->exec($query) === false)
		$error['module_blog'] = true;
?>