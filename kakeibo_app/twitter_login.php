<?php
// ini_set('display_errors', "On");

include_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/common/ChromePhp.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/vendor/autoload.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/secret/twitter_login_secret.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/user_db/user_db.php';

use Abraham\TwitterOAuth\TwitterOAuth;
// use \Exception;

class TwitterLogin {

    public static function login() {

        $tmpConn = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET);

        //コールバックURLをここでセット
        $request_token = $tmpConn->oauth('oauth/request_token', array('oauth_callback' => 'http://' . $_SERVER['HTTP_HOST'] . '/kakeibo_app/twitter_login_callback.php'));
        
        // var_dump($request_token);

        //callback.phpで使うのでセッションに入れる
        $_SESSION['twitter_oauth_token'] = $request_token['oauth_token'];
        $_SESSION['twitter_oauth_token_secret'] = $request_token['oauth_token_secret'];

        //Twitter.com 上の認証画面のURLを取得( この行についてはコメント欄も参照 )
        $url = $tmpConn->url('oauth/authenticate', array('oauth_token' => $request_token['oauth_token']));

        // var_dump($url);

        //Twitter.com の認証画面へリダイレクト
        header( 'location: '. $url );
    }

    public static function onCallback() {

        session_start();

        //login.phpでセットしたセッション
        $request_token = []; 
        $request_token['oauth_token'] = $_SESSION['twitter_oauth_token'];
        $request_token['oauth_token_secret'] = $_SESSION['twitter_oauth_token_secret'];

        // var_dump($_REQUEST);

        //Twitterから返されたOAuthトークンと、あらかじめlogin.phpで入れておいたセッション上のものと一致するかをチェック
        if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
            die( 'Error!' );
        }

        //OAuth トークンも用いて TwitterOAuth をインスタンス化
        $secondConn = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);

        //アプリでは、access_token(配列になっています)をうまく使って、Twitter上のアカウントを操作していきます
        $_SESSION['twitter_access_token'] = $secondConn->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
        /*
        ちなみに、この変数の中に、OAuthトークンとトークンシークレットが配列となって入っています。
        */

        // var_dump($_SESSION);

        //セッションIDをリジェネレート
        session_regenerate_id();

        // ここで新たにTwitterOAuthをinstanciateしないと死ぬ
        //      半日死んだ。
        $thirdConn = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET, $_SESSION['twitter_access_token']['oauth_token'], $_SESSION['twitter_access_token']['oauth_token_secret']);


        // 連携解除されることがあるので
        try {
            //ユーザー情報をGET
            $user = $thirdConn->get("account/verify_credentials");
            //(ここらへんは、Twitter の API ドキュメントをうまく使ってください)

            //GETしたユーザー情報をvar_dump
            // var_dump( $user );

            $userDB = new UserDB;
            $result = $userDB->createUserWithOAuth2IfNeededThenLogin('twitter_id', $user->id, $user->name);

            // var_dump($result);

            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['login_state'] = $result['login_result'];

            // var_dump($_SESSION);

            $path = 'http://'.$_SERVER['HTTP_HOST'].'/kakeibo_app/kakeibo_home.php';
            // var_dump($path);

            header('Location: '.$path);
            exit();
        } catch(Exception $e) {
            $code = $e->getCode();
            pr($code);//89なら expired
            die;
        }
        
    }


}
?>