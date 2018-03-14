<?php
// ini_set('display_errors', "On");

include_once($_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/user_db/user_db.php');

class GoogleSignIn {

    private static $client;
    private static function initClient() {

        // var_dump($_SERVER['DOCUMENT_ROOT']);
        require_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/vendor/autoload.php';

        self::$client = new Google_Client();
        self::$client->setAuthConfig($_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/secret/client_secret_394041585919-h5gcc158t7ff0t921nngvh5mc99l31ic.apps.googleusercontent.com.json');
        self::$client->setAccessType("offline");        // offline access
        // self::$client->setApprovalPrompt ("force");
        self::$client->setIncludeGrantedScopes(true);   // incremental auth
        self::$client->setApplicationName('家計簿アプリ');
        self::$client->addScope(Google_Service_Plus::USERINFO_PROFILE);
        // self::$client->addScope(['email']);
        self::$client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].'/kakeibo_app/google_signin/google_signin_callback.php');

    }

    public static function signIn() {

        // include('./common/ChromePhp.php');
        
        // session_start();

        GoogleSignIn::initClient();

        // // $client->setAccessType("offline");という設定だからアクセストークンの格納とか要らない感じか？　
        // if (isset($_SESSION['google_access_token']) && $_SESSION['google_access_token']) {
        //     ChromePhp::log($_SESSION['google_access_token']);
        //     self::$client->setAccessToken($_SESSION['google_access_token']);
        
        //     GoogleSignIn::onSignedIn();

        // } else {
        //     $google_auth_url = self::$client->createAuthUrl();
        //     // ChromePhp::log($google_auth_url);
        //     header('Location: ' . filter_var($google_auth_url, FILTER_SANITIZE_URL));
        // }

        $google_auth_url = self::$client->createAuthUrl();
        header('Location: ' . filter_var($google_auth_url, FILTER_SANITIZE_URL));
    }

    public static function onSignedIn() {

        // NOT NEEDED IN CASE: $client->setAccessType("offline"); !?
        // var_dump(self::$client->isAccessTokenExpired());
        // if (self::$client->isAccessTokenExpired()) {
        //     $refreshToken = self::$client->getRefreshToken();
        //     var_dump($refreshToken);
        //     self::$client->setAccessToken($refreshToken);
        // }

        $googleOAuth2 = new Google_Service_Oauth2(self::$client);
        // var_dump($googleOAuth2->userinfo->get());
        $googleUser = $googleOAuth2->userinfo->get();
        // var_dump($googleUser);

        $userDB = new UserDB;
        // var_dump($userDB);
        
        $result = $userDB->createUserWithGoogleIfNeededThenLogin($googleUser);
        var_dump($result);

        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['login_state'] = $result['login_result'];

        var_dump($_SESSION);

        $path = 'http://'.$_SERVER['HTTP_HOST'].'/kakeibo_app/kakeibo_home.php';
        var_dump($path);

        header('Location: '.$path);
        exit();

    }

    public static function onCallback() {
        if (isset($_GET['code'])) {
            GoogleSignIn::initClient();
            self::$client->authenticate($_GET['code']);
        
            // この時点でaccessTokenがExpireしていることがある
            $accessToken = self::$client->getAccessToken();
            $_SESSION['google_access_token'] = $accessToken;
            // var_dump($_SESSION['google_access_token']);

            GoogleSignIn::onSignedIn();
        
        }
    }
}


?>
