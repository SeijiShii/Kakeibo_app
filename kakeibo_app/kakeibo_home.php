<?php
include('./common/ChromePhp.php');
include_once('./user_db/user_db.php');

session_start();

if (!$_SESSION['login_state']) {
    header('Location: ./index.php');
    exit();
}

$userDB = new UserDB();

ChromePhp::log($_GET);
ChromePhp::log($_POST);
ChromePhp::log($_SESSION);

if ($_POST['action'] == 'logout') {
    $_SESSION['login_state'] = false;
    $_SESSION['user_id'] = null;
    $_SESSION['google_access_token'] = null;
    header('Location: ./index.php');
    exit();
}

?>
<!DOCTYPE HTML>
<html>
    <head>
        <?php include('./components/header.php') ?>
        <title>家計簿アプリ ホーム</title>
        <link type="text/css" href="./css/kakeibo_home_style.css?date=<?php echo date('YmdHis', filemtime('./css/kakeibo_home_style.css')); ?>" rel="stylesheet">
    </head>
    <body class="kakeibo_home">
        <div class="content_area">
            <h1 class="app_name_small">家計簿アプリ</h1>
            <h2>家計簿ホーム</h2> 
            <form class='row_height_50px login_state_row' id="login_state" action="" enctype="multipart/form-data" method="post">
                <span class='login_state_text'><?php echo $userDB->getUserNameById($_SESSION['user_id']) ?>としてログイン中</span>
                <button class='app_green_button logout_button' type='submit' name='action' value='logout'>ログアウト</button>
            </form>
            <form class='budget_select_form' action="" enctype="multipart/form-data" method="post">
                <select class='budget_selctor'></select>
                <span>
                    <button class='app_ui_button budget_name_change_button' type="submit" name="budget_select_action" value="change_name">表示中のバジェット名を変更</button>
                    <input type='text' class='text_input budget_name_change_text' placeholder='変更後の名前'>
                    <button class='app_ui_button budget_name_change_button' type="submit" name="budget_select_action" value="change_name_save">変更後の名前で保存</button>
                </span>
                <span>
                    <button class='app_ui_button create_budget_button' type="submit" name="budget_select_action" value="create_budget">バジェット新規作成</button>
                    <input type='text' class='text_input budget_name_create_text' placeholder='新規バジェット名'>
                    <button class='app_ui_button do_create_budget_button' type="submit" name="budget_select_action" value="do_create_budget">作成</button>
                </span>
            </form>
            <?php include('./components/func_tab.php') ?>
            <div class='tab_content_frame'>
                <?php 
                switch ($_GET['action']) {
                    case 'budget_status':
                    include './tab_contents/budget_status_tab.php';
                    break;

                    case 'estimate':
                    include './tab_contents/estimate_tab.php';
                    break;

                    case 'record_trafic':
                    include './tab_contents/record_trafic_tab.php';
                    break;
                }
                ?>
            </div>
        </div>
    </body>
</html>