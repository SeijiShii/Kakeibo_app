<?php
ini_set('display_errors', "On");
include('../common/ChromePhp.php');
include_once('./google_signin.php');

session_start();

GoogleSignIn::onCallback();

?>
Fuga FUga