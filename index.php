<?php
	if (file_exists('global/configuration.xml'))
		require_once 'home.php';
	else
		require_once 'install.php';
?>