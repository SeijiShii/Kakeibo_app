<?php
// ini_set('display_errors', "On");

include_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/common/ChromePhp.php';
// require_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/vendor/autoload.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/secret/line_login_secret.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/user_db/user_db.php';

class LINELogin {

    public static function login() {

        // var_dump(LINE_API_KEY);

        $url = 'https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=' . LINE_API_KEY . '&redirect_uri=http://localhost:8080/kakeibo_app/line_login_callback.php&scope=profile&state=hogehoge';
        
        header('Location: ' . $url);
     
    }

    public static function onCallback() {

        session_start();

        $accessToken = LINELogin::fromCodeToToken();

        // var_dump($accessToken);

        $user = LINELogin::getUserFromToken($accessToken);

        $userDB = new UserDB;
        $result = $userDB->createUserWithOAuth2IfNeededThenLogin('line_id', $user->userId, $user->displayName);

        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['login_state'] = $result['login_result'];

        // var_dump($_SESSION);

        $path = 'http://'.$_SERVER['HTTP_HOST'].'/kakeibo_app/kakeibo_home.php';
        // var_dump($path);

        header('Location: '.$path);
        exit();


    }

    private static function fromCodeToToken() {

        // 認可コードの有効期間は10分間
        // var_dump($_REQUEST);

        $ch = curl_init();
 
        // 必要に応じてPOSTパラメータを設定
        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => LINE_API_KEY,
            'client_secret' => LINE_API_SECRET,
            'redirect_uri' => 'http://localhost:8080/kakeibo_app/line_login_callback.php',
            'code' => $_REQUEST['code']
        ];
       
        mb_convert_variables( 'SJIS-win', 'UTF-8',  $data );
       
        $options = [
          CURLOPT_URL => 'https://api.line.me/oauth2/v2.1/token',
          CURLOPT_HTTPHEADER => array('Content-Type : application/x-www-form-urlencoded'),
          CURLOPT_TIMEOUT => 60,
          CURLOPT_POST => true,
          CURLOPT_RETURNTRANSFER  => true,
          CURLOPT_POSTFIELDS  => http_build_query( $data ),
        ];
       
        curl_setopt_array( $ch,  $options );

        $response = curl_exec( $ch );
       
        curl_close( $ch );

        // var_dump($response);

        $resArray = array();
        $resArray = json_decode($response);

        // var_dump($resArray);

        return $resArray->access_token;
    }

    private static function getUserFromToken($accessToken) {

        $ch = curl_init();
       
        $options = [
          CURLOPT_URL => 'https://api.line.me/v2/profile',
          CURLOPT_HTTPHEADER => array('Authorization: Bearer ' . $accessToken ),
          CURLOPT_TIMEOUT => 60,
          CURLOPT_RETURNTRANSFER  => true,
        ];
       
        curl_setopt_array( $ch,  $options );

        $response = curl_exec( $ch );
       
        curl_close( $ch );

        // var_dump($response);

        $resArray = array();
        $resArray = json_decode($response);

        // var_dump($resArray);

        return $resArray;

    }

}



?>