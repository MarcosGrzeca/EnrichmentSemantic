<?php

set_time_limit (0);
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
require_once("functions/functions.php");
require_once("functions/Connection.php");
require_once("libs/ChromePhp.php");
session_start();
ob_start();
ini_set('memory_limit', '2048M');
?>