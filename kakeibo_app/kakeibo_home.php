<?php
include('./common/ChromePhp.php');
include_once('./user_db/user_db.php');

session_start();

if (!$_SESSION['login_state']) {
    header('Location: ./index.php');
    exit();
}

$userDB = new UserDB();

ChromePhp::log($_POST);
ChromePhp::log($_SESSION);

if ($_POST['action'] == 'logout') {
    $_SESSION['login_state'] = false;
    $_SESSION['user_id'] = null;
    header('Location: ./index.php');
    exit();
}

?>
<!DOCTYPE HTML>
<html>
    <head>
        <?php include('./header.php') ?>
        <title>家計簿アプリ ホーム</title>
    </head>
    <body class="kakeibo_home">
        <div class="content_area">
            <h1 class="app_name_small">家計簿アプリ</h1>
            <h2>家計簿ホーム</h2> 
            <form class='row_height_40px login_state_row' id="login_state" action="" enctype="multipart/form-data" method="post">
                <span class='login_state_text'><?php echo $userDB->getUserNameById($_SESSION['user_id']) ?>としてログイン中</span>
                <button class='app_green_button logout_button' type='submit' name='action' value='logout'>ログアウト</button>
            </form>     
        </div>
    </body>
</html>