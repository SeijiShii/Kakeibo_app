<?php
    if ($_POST['action'] == 'login') {
        echo 'Login!<br>';
        echo $_POST['user_id'];
        echo '<br>';
        echo $_POST['password'];
    } elseif ($_POST['action'] == 'create_account') {
        echo 'Create Account!';
    }
?>
