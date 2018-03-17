<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/line_login.php');
LINELogin::onCallback();
?>