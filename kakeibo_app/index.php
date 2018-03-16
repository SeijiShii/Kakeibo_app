<?php
// ini_set('display_errors', "On");

require_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/vendor/autoload.php';
include($_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/common/ChromePhp.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/user_db/user_db.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/google_signin.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/facebook_login.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/twitter_login.php');

session_start();
// session_destroy()

$userDB = new UserDB();
// ChromePhp::log($userDB);

// 確認画面からの戻りなら$_POSTにその前のセッションを書き戻す。
ChromePhp::log($_POST);
ChromePhp::log($_GET);

if (isset($_SESSION['rewrite']) && $_SESSION['rewrite']['action'] == 'rewrite') {
    $_POST['user_name'] = $_SESSION['rewrite']['user_name'];
    $_POST['password'] = $_SESSION['rewrite']['password'];
    $_SESSION['rewrite'] = null;
}
// ChromePhp::log($_SESSION);

// $_POSTが空なら新規読み込み
if (!empty($_POST)) {

    switch($_POST['action']) {
        case 'facebook_login':
            ChromePhp::log('Facebook Login!');
            FacebookLogin::login();
            break;

        case 'google_login':
            ChromePhp::log('Google Login!');
            GoogleSignIn::signIn();
            exit();
            break;

        case 'twitter_login':
            ChromePhp::log('Twitter Login!');
            TwitterLogin::login();
            exit();
            break;

        case 'line_login':
            ChromePhp::log('LINE Login!');    
            break;

        case 'login':
        case 'create_account':

            // エラー項目の確認
            if ($_POST['user_name'] == '') {
                $input_error['user_name'] = 'blank';
            }
        
            if (strlen($_POST['password']) < 8) {
                $input_error['password'] = 'length';
            }
            if ($_POST['password'] == '') {
                $input_error['password'] = 'blank';
            }
        
            ChromePhp::Log($input_error);
            if (empty($input_error)) {
                if ($_POST['action'] == 'login') {
                // include './login.php';
                $loginResult = $userDB->loginWithNameAndPass($_POST['user_name'], $_POST['password']);
                ChromePhp::log($loginResult);
                if (!empty($loginResult['error'])){
                    if ($loginResult['error'] == 'user_name_not_found') {
                        $input_error['user_name'] = $loginResult['error'];
                    } elseif ($loginResult['error'] == 'wrong_password') {
                        $input_error['password'] = $loginResult['error'];
                    }
                } else {
                    $_SESSION['user_id'] = $loginResult['user_id'];
                    $_SESSION['login_state'] = $loginResult['login_result'];
                    header('Location: ./kakeibo_home.php');
                }
        
                } elseif ($_POST['action'] == 'create_account') {
                // TODO: Check duplicate account name.
        
                    if ($userDB->checkDuplicatedUserName($_POST['user_name'])){
                        $input_error['user_name'] = 'duplicated_user_name';
                    }
        
                    if (empty($input_error)) {
                        $_SESSION['create_account'] = $_POST;
                        ChromePhp::log($_SESSION);
                        header('Location: ./create_account_confirm.php');
                        exit();
                    }
                } 
            }
            break;
    }
}
?>

<!DOCTYPE HTML>
<html>
    <head>
        <?php include('./header.php') ?>
        <title>家計簿アプリ</title>
    </head>
    <body>
        <div class="content_area">
            <h1 class="app_name_large">家計簿アプリ</h1>
            <h2>ログイン</h2>
            <div class="login_input_area">
                <form id="login_form" action="" enctype="multipart/form-data" method="post">
                    <?php if($input_error['user_name'] == 'blank'): ?>
                        <p id="waring_empty_user_name" class="input_warning">ユーザ名が空白です。</p>
                    <?php endif; ?>
                    <?php if($input_error['user_name'] == 'duplicated_user_name'): ?>
                        <p id="waring_dupulicate_user_name" class="input_warning">そのユーザ名はすでに登録されています。</p>
                    <?php endif; ?>
                    <?php if($input_error['user_name'] == 'user_name_not_found'): ?>
                        <p id="waring_user_name_not_found" class="input_warning">そのユーザ名は存在しません。</p>
                    <?php endif; ?>
                    <input type="text" name="user_name" size="40" class="text_input input_item" placeholder="ユーザ名" value="<?php echo htmlspecialchars($_POST["user_name"], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if($input_error['password'] == 'blank'): ?>
                        <p id="waring_empty_password" class="input_warning">パスワードが空白です。</p>
                    <?php endif; ?>
                    <?php if($input_error['password'] == 'length'): ?>
                        <p id="waring_short_password" class="input_warning">パスワードが短すぎます。(英数8文字以上)</p>
                    <?php endif; ?>
                    <?php if($input_error['password'] == 'wrong_password'): ?>
                        <p id="waring_wrong_password" class="input_warning">パスワードが一致しません。</p> 
                    <?php endif; ?>
                    <input type="password" name="password" size="40" class="text_input input_item" placeholder="パスワード" value="<?php echo htmlspecialchars($_POST["password"], ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" name="action" value="login" class="app_ui_button input_item">ログイン</button>
                    <button type="submit" name="action" value="create_account" class="app_ui_button input_item">アカウント新規作成</button>
                </form>
                <form id="oauth2_login" action="" enctype="multipart/form-data" method="post">
                    <p>以下でもログインできます。</p>
                    <ul>
                        <li><button id="facebook_button" type="submit" name="action" value="facebook_login"><img class="oauth2_logo" src="images/facebook.png"></button></li>
                        <li><button id="google_button" type="submit" name="action" value="google_login" ><img class="oauth2_logo" src="images/google.png"></button></li>
                        <li><button id="twitter_button" type="submit" name="action" value="twitter_login"><img class="oauth2_logo" src="images/twitter.png"></button></li>
                        <li><button id="line_button" type="submit" name="action" value="line_login"><img class="oauth2_logo" src="images/line.png"></button></li>
                    </ul>
                </form>
            </div>
        </div>
    </body>
</html>
