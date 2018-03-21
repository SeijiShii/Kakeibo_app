<?php
// ini_set('display_errors', "On");

require_once($_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/secret/db_define.php');

class UserDB {
    
    private $db;

    public function __construct() {
        $this->db = new mysqli('localhost', DB_USER_NAME, DB_USER_PASS, 'user_db');
        if ($this->db->connect_error) {
            ChromePhp::log($this->db->connect_error);
            exit();
        } else {
            // ChromePhp::log('DB Connected!');
            $this->db->set_charset("utf8");
        }
    }

    // Methods for Name and Password
    private function _createUserWithNameAndPass($userName, $password) {

        $sql = 'INSERT INTO user_table(user_id, user_name, password) VALUES(?, ?, ?);';
        if ($statement = $this->db->prepare($sql)) {
            $userId = $this->_generateUserId();
            $statement->bind_param('sss', $userId, $userName, sha1($password));
            $statement->execute();
            $success = $statement->affected_rows == 1;
            $statement->close();

            return $success;
        }
    }

    private function _getUserIdByNameAndPass($userName, $password) 
    {
        $sql = "SELECT user_id FROM user_table WHERE user_name = ? AND password = ?;";
       
        if ($statement = $this->db->prepare($sql)) {
            $statement->bind_param('ss', $userName, sha1($password));
            $statement->execute();
            $statement->bind_result($id);
            $statement->fetch();
            $statement->close();
            ChromePhp::log($id);
            return $id;
        }
    }

    private function _generateUserId() {
        $dateTime = date('YmdHis');
        $id = 'user_' . $dateTime . sprintf('%04d', mt_rand(1, 1000)); 
        // ChromePhp::log($id);
        return $id;
    }

    public function loginWithNameAndPass($userName, $password) {

        $loginResult = array();

        if ($this->checkUserNameExists($userName)) {
            $id = $this->_getUserIdByNameAndPass($userName, $password);
            if (!empty($id)) {
                $loginResult['login_result'] = true;
                $loginResult['user_id'] = $id;    
            } else {
                $loginResult['login_result'] = false;
                $loginResult['error'] = 'wrong_password';
            }
        } else {
            $loginResult['login_result'] = false;
            $loginResult['error'] = 'user_name_not_found';
        }

        // ChromePhp::log($loginResult);
        return $loginResult;
    }

    private function checkUserNameExists($userName) {

        $sql = "SELECT user_id FROM user_table WHERE user_name = ?;";
       
        if ($statement = $this->db->prepare($sql)) {
 
            $statement->bind_param('s', $userName);
            $statement->execute();
            $statement->store_result();
            $numRows = $statement->num_rows();
            $statement->free_result();
            $statement->close();

            // ChromePhp::log('In checkUserNameExists: before return');
            return $numRows > 0;
        }
    }

    public function createUserWithNameAndPassThenLogin($userName, $password) {

        $userDBResult = array();

        if ($this->checkUserNameExists($userName)) {
            $userDBResult['create_error'] = 'duplicate_user_name';
            return $userDBResult;
        }

        if ($this->_createUserWithNameAndPass($userName, $password)) {
            
            // ChromePhp::log('Create user success.');

            $userDBResult['create_result'] = true;

            $loginResult = $this->loginWithNameAndPass($userName, $password);

            ChromePhp::log($loginResult);

            if (empty($loginResult['error'])) {
                $userDBResult['error'] = $loginResult['error'];
            }

            $userDBResult['login_result'] = $loginResult['login_result'];
            $userDBResult['user_id'] = $loginResult['user_id'];

        } else {
            $userDBResult['create_result'] = false;
            $userDBResult['error'] = 'unknown_error';
        }

        return $userDBResult;
    }

    public function checkDuplicatedUserName($userName) {
        // ChromePhp::log('In checkDuplicatedUserName');
        return $this->checkUserNameExists($userName);
    }

    private function getUserDataById($userId) {

        // ChromePhp::log('In getUserDataById:' . $userId);

        $sql = 'SELECT * FROM user_table WHERE user_id = ?';
        if ($statement = $this->db->prepare($sql)) {
            $statement->bind_param('s', $userId);
            $statement->execute();
            $result = $statement->get_result();
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $statement->close();
            return $row;

        } else {
            ChromePhp::log('Error in getUserDataById');
        }
    }

    public function getUserNameById($userId) {

        $data = $this->getUserDataById($userId);
        if (!empty($data['user_name'])) {
            return $data['user_name'];
        } else {

        }
    }

    // OAuth2 login in methods
    private function _createUserWithOAuth2($dbFieldName, $oauthUserId, $userName) {
        $sql = 'INSERT INTO user_table(user_id, user_name, '.$dbFieldName.') VALUES(?, ?, ?);';
        
        ChromePhp::log($sql);

        if ($statement = $this->db->prepare($sql)) {
            $userId = $this->_generateUserId();
            $statement->bind_param('sss', $userId, $userName, $oauthUserId);
            $statement->execute();
            $success = $statement->affected_rows == 1;
            $statement->close();

            return $success;
        }
    }

    public function createUserWithOAuth2IfNeededThenLogin($dbFieldName, $oauthUserId, $userName) {

        // var_dump($dbFieldName);
        // var_dump($oauthUserId);
        // var_dump($userName);

        $userDBResult = array();

        $userData = $this->_getUserDataByOAuth2Id($dbFieldName, $oauthUserId);
        // var_dump(empty($userData));
        if (empty($userData)) {
            if ($this->_createUserWithOAuth2($dbFieldName, $oauthUserId, $userName)) {
                $userData = $this->_getUserDataByOAuth2Id($dbFieldName, $oauthUserId);
            } else {
                ChromePhp::log('Error in createUserWithOAuth2IfNeededThenLogin');
            }
        } 

        $userDBResult['login_result'] = true;
        $userDBResult['user_name'] = $userData['user_name'];
        $userDBResult['user_id'] = $userData['user_id'];

        return $userDBResult;
    }

    private function _getUserDataByOAuth2Id($dbFieldName, $oauthUserId) {

        $sql = 'SELECT * FROM user_table WHERE '.$dbFieldName.' = ?';
        
        ChromePhp::log($sql);

        if ($statement = $this->db->prepare($sql)) {
            $statement->bind_param('s', $oauthUserId);
            $statement->execute();
            // ChromePhp::log('sql executed.');
            $result = $statement->get_result();
            $row = $result->fetch_array(MYSQLI_ASSOC);
            // ChromePhp::log($row);
            $statement->close();
            return $row;

        } else {
            ChromePhp::log('Error in _getUserDataByOAuth2Id');
        }
    }
}
?>