<?php
session_start();
$username = (isset($_SESSION['logged_in']))? $_SESSION['uname'] : false;
$_GET['route'] = (isset($_GET['route']))? $_GET['route'] : 'home';
define("username", $username);
define("SKIN", "original");
define("SITENAME", "GameBud");
define("BASEURL", "/");
define("IMGURL", BASEURL."assets/images/");
define("DS", DIRECTORY_SEPARATOR);
define("RT", getcwd() . DS);
$root = (strpos(RT, DS.'httpd.www'.DS) !== false)? substr(RT, 0, strpos(RT, DS.'httpd.www')) : RT;
define("ROOT", $root.DS);
define("PUROOT", ROOT."www".DS);
define("PROOT", ROOT."httpd.private".DS);
define("APP", PROOT."app".DS);
define("VIEWS", APP."views".DS);
define("SYS", PROOT."system".DS);
define("CONTROLLER", APP."controllers".DS);
define("MODEL", APP."models".DS);
define("SYSCONT", SYS."controllers".DS);

require(SYSCONT.'appController.php');
require(SYSCONT.'routesController.php');
require(SYSCONT.'viewController.php');

include_once(SYS.'functions.php');
include_once PROOT.DS.'/routes/web.php';
