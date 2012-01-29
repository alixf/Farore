<!doctype html>
<html>
	<head>
		<title><?php echo($pageTitle); ?></title>
		
		<?php echo !empty($pageCanonicalLink) ? '<link rel="canonical" href="'.$pageCanonicalLink.'" />' : '' ; ?>
		
		<link rel="stylesheet" type="text/css" href="<?php echo $themesUrlPath; ?>default/style.css" />
		
		<?php echo !empty($pageEncoding) || true ? '<meta charset="'.$pageEncoding.'">' : '' ; ?>
		<?php echo !empty($pageKeywords) || true ? '<meta name="keywords" content="'.implode(', ', array_unique($pageKeywords)).'" />' : '' ; ?>
		<?php echo !empty($pageDescription) || true ? '<meta name="description" content="'.$pageDescription.'" />' : '' ; ?>
		
		<!--[if lt IE 9]>
			<script src="scripts/html5toie.js"></script>
		<![endif]-->
	</head>
	<body>
		<header>
			<a href="<?php echo $urlBasePath; ?>"><?php echo $pageTitleBase; ?></a>
		</header>
		<nav>
			<a id="home" href="<?php echo $urlBasePath; ?>" rel="author">Blog</a>
		</nav>
		<div id="page">
			<div id="main">
				<div id="ribbon">
					<?php
						foreach ($pagePath as $pageStep)
							echo '<a href="' . $urlBasePath.$pageStep[1] . '">' . $pageStep[0] . '</a> ';
					?>
				</div>
				<div id="content">
					<?php
					if(file_exists($modulePath . 'view.php'))
						include($modulePath . 'view.php');
					?>
				</div>
			</div><aside>
				lol stuff
			</aside>
		</div>
		<footer>
			Propulsé par <a href="http://www.eolhing.me/Farore">Farore</a>.
			<a class="button" rel="license" href="http://jeromechoain.wordpress.com/1970/01/01/licence-comlpete-bullshit/">Licence CB</a>
			<a class="button" href="<?php echo $urlBasePath; ?>admin">Administration</a>
			<?php if ($isAdmin){ ?><a class="button" href="<?php echo $urlBasePath; ?>admin/connection-disconnect">Déconnexion</a><?php } ?>
		</footer>
		<script src="scripts/global.js"></script>
	</body>
</html>