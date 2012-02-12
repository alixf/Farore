<!doctype html>
<html>
	<head>
		<title><?php echo $core->getPageTitle(); ?></title>
		
		<?php echo $core->getPageCanonicalLink() != '' ? '<link rel="canonical" href="'.$core->getPageCanonicalLink().'" />' : '' ; ?>
		
		<link rel="stylesheet" type="text/css" href="<?php echo $core->getThemesURL(); ?>default/style.css" />
		
		<?php echo $core->getPageEncoding() != '' || true ? '<meta charset="'.$core->getPageEncoding().'">' : '' ; ?>
		<?php echo $core->getPageKeywords() != '' || true ? '<meta name="keywords" content="'.implode(', ', array_unique($core->getPageKeywords())).'" />' : '' ; ?>
		<?php echo $core->getPageDescription() != '' || true ? '<meta name="description" content="'.$core->getPageDescription().'" />' : '' ; ?>
		
		<!--[if lt IE 9]>
			<script src="scripts/html5toie.js"></script>
		<![endif]-->
	</head>
	<body>
		<header>
			<a href="<?php echo $core->getBaseURL(); ?>"><?php echo $core->getPageTitleBase(); ?></a>
		</header>
		<nav>
			<a id="home" href="<?php echo $core->getBaseURL(); ?>" rel="author">Blog</a>
		</nav>
		<div id="page" class="<?php echo $core->getModuleName() ?>">
			<div id="main">
				<div id="ribbon">
					<?php
						foreach ($core->getPagePath() as $pageStep)
							echo '<a href="' . $pageStep[1] . '">' . $pageStep[0] . '</a>';
					?>
				</div>
				<div id="content">
					<?php
						if($core->getModuleName() != '')
							$core->getModule()->display();
					?>
				</div>
			</div>
		</div>
		<footer>
			Propulsé par <a href="http://www.eolhing.me/Farore">Farore</a> - 
			<a class="button" rel="license" href="http://jeromechoain.wordpress.com/1970/01/01/licence-comlpete-bullshit/">Licence CB</a> - 
			<a class="button" href="<?php echo $core->getBaseURL(); ?>admin">Administration</a>
			<?php
				if ($core->isAdmin())
				{
					?> - <a class="button" href="<?php echo $core->getBaseURL(); ?>admin/connection-disconnect">Déconnexion</a><?php
				}
			?>
		</footer>
		<script src="<?php echo $core->getBaseURL(); ?>scripts/global.js"></script>
	</body>
</html>