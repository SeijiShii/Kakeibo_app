<?php
ini_set('display_errors', "On");
include_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/facebook_login.php';
session_start();
FacebookLogin::onCallback();
?>