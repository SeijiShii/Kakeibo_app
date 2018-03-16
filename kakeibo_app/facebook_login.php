
<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/common/ChromePhp.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/vendor/autoload.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/secret/facebook_login_secret.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/user_db/user_db.php';

class FacebookLogin {
    private static $fb, $helper;

    private static function initFB() {
        self::$fb = new Facebook\Facebook([
            'app_id' => FACEBOOK_APP_ID,
            'app_secret' => FACEBOOK_APP_SECRET,
            'default_graph_version' => 'v2.10'
        ]);

        // var_dump(self::$fb);

        self::$helper = self::$fb->getRedirectLoginHelper();

        
    }

    public static function login() {
        FacebookLogin::initFB();

        $loginUrl = self::$helper->getLoginUrl('http://'.$_SERVER['HTTP_HOST'].'/kakeibo_app/facebook_login_callback.php');

        // var_dump($loginUrl);

        header('Location: '.$loginUrl);
        exit();
    }

    public static function onCallback() {
        FacebookLogin::initFB();

        try {
            $accessToken = self::$helper->getAccessToken('http://'.$_SERVER['HTTP_HOST'].'/kakeibo_app/facebook_login_callback.php');
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (! isset($accessToken)) {
            if (self::$helper->getError()) {
              header('HTTP/1.0 401 Unauthorized');
              echo "Error: " . self::$helper->getError() . "\n";
              echo "Error Code: " . self::$helper->getErrorCode() . "\n";
              echo "Error Reason: " . self::$helper->getErrorReason() . "\n";
              echo "Error Description: " . self::$helper->getErrorDescription() . "\n";
            } else {
              header('HTTP/1.0 400 Bad Request');
              echo 'Bad request';
            }
            exit;
        }

        // Logged in
        // echo '<h3>Access Token</h3>';
        // var_dump($accessToken->getValue());

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = self::$fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        // echo '<h3>Metadata</h3>';
        // var_dump($tokenMetadata);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId(FACEBOOK_APP_ID);
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
              $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
              echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
              exit;
            }
          
            // echo '<h3>Long-lived</h3>';
            // var_dump($accessToken->getValue());
        }

        $_SESSION['fb_access_token'] = (string) $accessToken;

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = self::$fb->get('/me?fields=id,name', $accessToken);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
          
        $user = $response->getGraphUser();
        
        // var_dump($user);

        $userDB = new UserDB;
        $result = $userDB->createUserWithOAuth2IfNeededThenLogin('facebook_id', $user['id'], $user['name']);

        // var_dump($result);

        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['login_state'] = $result['login_result'];

        // var_dump($_SESSION);

        $path = 'http://'.$_SERVER['HTTP_HOST'].'/kakeibo_app/kakeibo_home.php';
        // var_dump($path);

        header('Location: '.$path);
        exit();
    }
}

?>