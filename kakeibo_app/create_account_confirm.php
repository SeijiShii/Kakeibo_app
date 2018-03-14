<?php

include('./user_db/user_db.php');
include('./common/ChromePhp.php');
$userDB = new UserDB;

session_start();

if (!isset($_SESSION['create_account'])) {
    header('Location: ./index.php');
    exit();
}

// $_POST = $_SESSION['create_account'];
ChromePhp::log($_SESSION);

if (!empty($_POST)) {
    // ChromePhp::log($_POST);
    switch($_POST['action']) {
        case 'cancel':
            // ChromePhp::log('Cancel clicked!');
            $_SESSION['rewrite'] = $_SESSION['create_account'];
            $_SESSION['rewrite']['action'] = 'rewrite';

            $_SESSION['create_account'] = null;

            header('Location: ./index.php');
            exit();
            break;

        case 'create_account_ok':
            ChromePhp::log('OK clicked!');
            $result = $userDB->createUserWithNameAndPassThenLogin($_SESSION['create_account']['user_name'], $_SESSION['create_account']['password']);
            // ChromePhp::log($result);
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['login_state'] = $result['login_result'];
            $_SESSION['create_account'] = null;
            ChromePhp::log($_SESSION);
            if ($_SESSION['login_state']) {
                header('Location: ./kakeibo_home.php');
            } else {
                header('Location: ./index.php');
            }
            exit();
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
