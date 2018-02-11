<?php
    if ($_POST['action'] == 'login') {
        echo 'Login!<br>';
        echo $_POST['user-id'];
        echo '<br>';
        echo $_POST['password'];
    } elseif ($_POST['action'] == 'create-account') {
        echo 'Create Account!';
    }
?>
