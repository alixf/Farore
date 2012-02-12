<?php
	if(!file_exists('global/configuration.xml'))
		require_once 'install.php';
	else
		require_once 'home.php';
?>