<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>家計簿アプリ</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div class="content-area">
            <h1 class="app-name">家計簿アプリ</h1>
            <div class="login-input-area">
                <form id="login-form" action="./login.php" method="post">
                    <input type="text" name="user-id" size="40" class="text-input input-item" placeholder="ユーザ名">
                    <input type="password" name="password" size="40" class="text-input input-item" placeholder="パスワード">
                    <button type="submit" name="action" value="login" class="input-item">ログイン</button>
                    <button type="submit" name="action" value="create-account" class="input-item">家計簿新規作成</button>
                </form>
                <form id="oauth2-login" action="oauth2-login.php" method="post">
                    <p>以下でもログインできます。</p>
                    <ul>
                        <li><button id="facebook-button" type="submit" name="action" value="fasebook-login"><img class="oauth2-logo" src="images/facebook.png"></button></li>
                        <li><button id="google-button" type="submit" name="action" value="google-login"><img class="oauth2-logo" src="images/google.png"></button></li>
                        <li><button id="twitter-button" type="submit" name="action" value="twitter-login"><img class="oauth2-logo" src="images/twitter.png"></button></li>
                        <li><button id="line-button" type="submit" name="action" value="line-login"><img class="oauth2-logo" src="images/line.png"></button></li>
                    </ul>
                </form>
            </div>
        </div>
    </body>
</html>
