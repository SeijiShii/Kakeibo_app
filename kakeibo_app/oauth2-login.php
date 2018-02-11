<?php
    switch ($_POST['action']) {
      case 'fasebook-login':
        echo "Facebook Login!";
        break;

      case 'google-login':
        echo "Google Login!";
        break;

      case 'twitter-login':
        echo "Twitter Login!";
        break;

      case 'line-login':
        echo "LINE Login!";
        break;

      default:
        # code...
        break;
    }
?>
