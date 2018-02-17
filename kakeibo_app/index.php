<?php
include('./common/ChromePhp.php');
ChromePhp::log($_POST);
session_start();

// $_POSTが空なら新規読み込み
if (!empty($_POST)) {

    // エラー項目の確認
    if ($_POST['user_id'] == '') {
      $input_error['user_id'] = 'blank';
    }

    if (strlen($_POST['password']) < 8) {
      $input_error['password'] = 'length';
    }
    if ($_POST['password'] == '') {
      $input_error['password'] = 'blank';
    }

    if (empty($input_error)) {
      if ($_POST['action'] == 'login') {
        // include './login.php';

      } elseif ($_POST['action'] == 'create_account') {
        $_SESSION['create_account'] = $_POST;
        header('Location: create_account_confirm.php');
        exit();
      }
    }
}
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>家計簿アプリ</title>
        <!-- <link rel="stylesheet" type="text/css" href="style.css"> -->
        <link type="text/css" href="style.css?date=<?php echo date('YmdHis', filemtime('style.css')); ?>" rel="stylesheet">
        <body>
    </head>
        <div class="content_area">
            <h1 class="app_name_large">家計簿アプリ</h1>
            <h2>ログイン</h2>
            <div class="login_input_area">
                <form id="login_form" action="" enctype="multipart/form-data" method="post">
                    <?php if($input_error['user_id'] == 'blank'): ?>
                        <p id="waring_empty_user_name" class="input_warning">ユーザ名が空白です。</p>
                    <?php endif; ?>
                    <!-- <p id="waring_dupulicate_user_name" class="input_warning">そのユーザ名はすでに登録されています。</p> -->
                    <input type="text" name="user_id" size="40" class="text_input input_item" placeholder="ユーザ名" value="<?php echo htmlspecialchars($_POST["user_id"], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if($input_error['password'] == 'blank'): ?>
                        <p id="waring_empty_password" class="input_warning">パスワードが空白です。</p>
                    <?php endif; ?>
                    <?php if($input_error['password'] == 'length'): ?>
                        <p id="waring_short_password" class="input_warning">パスワードが短すぎます。(英数8文字以上)</p>
                    <?php endif; ?>
                    <!-- <p id="waring_wrong_password" class="input_warning">パスワードが一致しません。</p> -->
                    <input type="password" name="password" size="40" class="text_input input_item" placeholder="パスワード" value="<?php echo htmlspecialchars($_POST["password"], ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" name="action" value="login" class="app_ui_button input_item">ログイン</button>
                    <button type="submit" name="action" value="create_account" class="app_ui_button input_item">アカウント新規作成</button>
                </form>
                <form id="oauth2_login" action="" enctype="multipart/form-data" method="post">
                    <p>以下でもログインできます。</p>
                    <ul>
                        <li><button id="facebook_button" type="submit" name="action" value="fasebook_login"><img class="oauth2_logo" src="images/facebook.png"></button></li>
                        <li><button id="google_button" type="submit" name="action" value="google_login"><img class="oauth2_logo" src="images/google.png"></button></li>
                        <li><button id="twitter_button" type="submit" name="action" value="twitter_login"><img class="oauth2_logo" src="images/twitter.png"></button></li>
                        <li><button id="line_button" type="submit" name="action" value="line_login"><img class="oauth2_logo" src="images/line.png"></button></li>
                    </ul>
                </form>
            </div>
        </div>
    </body>
</html>
