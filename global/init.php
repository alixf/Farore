<?php
    require_once 'global/config.php';
	
	// HTTP Header initialisation
    header('Content-type:text/html; charset='.$pageEncoding);

	// Language locale initialisation
    setlocale(LC_ALL, $pageLanguage);

	// Session initialisation
    session_start();

	// Administration rights initialisation
	$isAdmin = false;
	if(isset($_SESSION['connected']) && $_SESSION['connected'] == true)
		$isAdmin = true;

	// Database initialisation
    $db = new PDO($sqlDSN, $sqlUsername, $sqlPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
?>