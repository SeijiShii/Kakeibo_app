<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/twitter_login.php');

TwitterLogin::onCallback();
?>