<?php
    switch ($_POST['action']) {
      case 'fasebook_login':
        echo "Facebook Login!";
        break;

      case 'google_login':
        echo "Google Login!";
        break;

      case 'twitter_login':
        echo "Twitter Login!";
        break;

      case 'line_login':
        echo "LINE Login!";
        break;

      default:
        # code...
        break;
    }
?>
