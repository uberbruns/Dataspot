<!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6"> <![endif]--> <!--[if IE 7 ]>    <html lang="en" class="ie7"> <![endif]--> <!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]--> <!--[if IE 9 ]>    <html lang="en" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->
<head>

	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Dataspot</title>
	<meta name="description" content="">
	<meta name="author" content="">

	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=NO">

	<link rel="shortcut icon" href="images/favicon.ico">
	<link rel="apple-touch-icon" href="images/apple-touch-icon.png">
	<link rel="stylesheet" href="css/styles.css?v=2">

	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

</head>
<body>

	<div id="app">
	<script type="text/x-handlebars">
		<header><h1>Dataspot</h1></header>
		<?php include('app/app_library_template.html'); ?>
		<?php include('app/app_index_template.html'); ?>
		<?php include('app/app_record_template.html'); ?>
		<?php include('app/app_inspector_template.html'); ?>
	</script>
	<div class="header-shadow"></div>
	<div class="toolbar-shadow"></div>
	</div>
	
	<div id="shadows"></div>
	<script src="js/scripts.min.js?v=124"></script>

</body>
</html>
