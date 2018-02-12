<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>家計簿アプリ</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div class="content_area">
            <h1 class="app_name">家計簿アプリ</h1>
            <div class="login_input_area">
                <form id="login_form" action="./login/login.php" method="post">
                    <input type="text" name="user_id" size="40" class="text_input input_item" placeholder="ユーザ名">
                    <input type="password" name="password" size="40" class="text_input input_item" placeholder="パスワード">
                    <button type="submit" name="action" value="login" class="input_item">ログイン</button>
                    <button type="submit" name="action" value="create_account" class="input_item">家計簿新規作成</button>
                </form>
                <form id="oauth2_login" action="./oauth2_login/oauth2_login.php" method="post">
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
