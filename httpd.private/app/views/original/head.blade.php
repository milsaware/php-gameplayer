<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo $meta['title'];?></title>
	<meta name="title" content="<?php echo $meta['title'];?>"/>
	<meta name="description" content="<?php echo $meta['description'];?>"/>
	<meta name="author" content="ozboware">
	<meta property="og:url" content="<?php echo BASEURL;?>" />
	<meta property="og:title" content="<?php echo $meta['title'];?>" />
	<meta property="og:description" content="<?php echo $meta['description'];?>" />
	<meta property="og:site_name" content="<?php echo SITENAME;?>" />

	<link href="<?php echo '/assets/'.SKIN.'/css/style.css';?>" rel="stylesheet" type="text/css">
	<link id="shorticon" rel="shortcut icon" href="/assets/images/favicon.ico" type="image/x-icon">
	<link id="favicon" rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">

    <title><?php echo $meta['title'];?></title>

	<script src="/assets/js/jquery.min.js"></script>
	<script src="<?php echo '/assets/'.SKIN.'/js/main.js';?>"></script>
</head>

<body>
<noscript id="no_script"></noscript>
<div class="navHead">
	<img class="logo" src="/assets/images/logo.png">
	<div id="nav_menu">
		<?php if(!isset($_SESSION['auth'])){?>
		<span class="nav_menu_signIn" id="navSignIn">Sign in</span>
		<?php }else{?>
		<img id="avatarMenu" src="<?php echo $meta['img'];?>avatar.png">
		<?php }?>
	</div>
</div>
<script>navIni();</script>
<div id="container">