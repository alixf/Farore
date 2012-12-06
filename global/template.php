<!doctype html>
<html>
	<head>
		<title><?php echo $core->getPageTitle(); ?></title>
		<?php echo $core->getPageCanonicalLink() != '' ? '<link rel="canonical" href="'.$core->getPageCanonicalLink().'" />' : '' ; ?>
		
		<link rel="stylesheet" type="text/css" href="<?php echo $core->getBaseURL(); ?>themes/default/style.css" />
		<link rel="icon" type="image/png" href="<?php echo $core->getBaseURL(); ?>themes/default/favicon.png" />
		
		<?php echo $core->getPageKeywords() != '' || true ? '<meta name="keywords" content="'.implode(', ', array_unique($core->getPageKeywords())).'" />' : '' ; ?>
		<?php echo $core->getPageDescription() != '' || true ? '<meta name="description" content="'.$core->getPageDescription().'" />' : '' ; ?>
		
		<!--[if lt IE 9]>
			<script src="js_test/html5toie.js"></script>
		<![endif]-->
		<script type="text/javascript" src="<?php echo $core->getBaseURL(); ?>scripts/ajax.js"></script>
		<script type="text/javascript" src="<?php echo $core->getBaseURL(); ?>scripts/main.js"></script>
	</head>
	<body>
		<div id="page" class="<?php echo $core->getModuleName(); ?>">
			<div id="content">
				<?php
					$core->getModule()->display();
				?>
			</div>
		</div>
	</body>
</html>