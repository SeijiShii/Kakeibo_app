<?php

include('./user_db.php');
include('./common/ChromePhp.php');
$userDB = new UserDB;

session_start();

// if (!isset($_SESSION['create_account'])) {
//     header('Location: ./index.php');
//     exit();
// }

// $_POST = $_SESSION['create_account'];
// var_dump($_POST);
if (!empty($_POST)) {
    // ChromePhp::log($_POST);
    switch($_POST['action']) {
        case 'cancel':
            // ChromePhp::log('Cancel clicked!');
            $_SESSION['rewrite'] = $_SESSION['create_account'];
            $_SESSION['rewrite']['action'] = 'rewrite';

            header('Location: ./index.php');
            exit();
            break;

        case 'create_account_ok':
            ChromePhp::log('OK clicked!');
            $userDB->createUserWithNameAndPass($_SESSION['create_account']['user_name'], $_SESSION['create_account']['password']);
            break;
    }
}

?>
<!DOCTYPE HTML>
<html>
    <head>
        <?php include('./header.php') ?>
        <title>家計簿アプリ アカウント作成</title>
    </head>
    <body class="create_account_confirm_page">
        <div class="content_area">
            <h1 class="app_name_small">家計簿アプリ</h1>
            <h2>アカウント新規作成</h2>
            
            <form id="confirm_form" action="" enctype="multipart/form-data" method="post">
                <p class="message">新規アカウント名:</p>
                <p class="new_account_name"><?php echo $_SESSION['create_account']['user_name']; ?></p>
                <button class="app_ui_button button cancel_button" type="submit" name="action" value="cancel"  >戻る</button>
                <button class="app_ui_button  button ok_button" type="submit" name="action" value="create_account_ok">アカウント作成</button>
            </form>
        </div>
    </body>
</html>