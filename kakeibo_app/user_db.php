<?php
require('./db_define.php');

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

    private function _createUserWithNameAndPass($userName, $password) {

        $sql = 'INSERT INTO user_table(user_id, user_name, password) VALUES(?, ?, ?);';
        if ($statement = $this->db->prepare($sql)) {
            $userId = $this->_generateUserId($userName);
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

    // public function getUserIdByNameAndPass($userName, $password) 
    // {
    //     // ChromePhp::log($userName);
    //     // ChromePhp::log(sha1($password));
    //     $sql = "SELECT * FROM user_table WHERE user_name = ? AND password = ?;";
       
    //     if ($statement = $this->db->prepare($sql)) {
    //         $statement->bind_param('ss', $userName, sha1($password));
    //         $statement->execute();
    //         // ChromePhp::log($statement->error);
    //         $result = $statement->get_result();
    //         // $statement->bind_result($result);
    //         // $statement->fetch();
    //         while ($row = $result->fetch_row()){
    //             ChromePhp::log($row);
    //         }
    //         $result->close();
    //         $statement->close();
    //         // return $result;
    //     }
    // }

    private function _generateUserId($prefix) {
        $dateTime = date('YmdHis');
        $id = $prefix . '_' . $dateTime . sprintf('%04d', mt_rand(1, 1000)); 
        // ChromePhp::log($id);
        return $id;
    }

    public function loginWithNameAndPass($userName, $password) {

        $loginResult = array();

        if ($this->checkUserNameExists($userName)) {
            $id = $this->_getUserIdByNameAndPass($userName, $password);
            if (!empty($id)) {
                $loginResult['result'] = true;
                $loginResult['user_id'] = $id;    
            } else {
                $loginResult['result'] = false;
                $loginResult['error'] = 'wrong_password';
            }
        } else {
            $loginResult['result'] = false;
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

            ChromePhp::log('In checkUserNameExists: before return');
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
            
            // ユーザ作成に成功した直後にこれが返ってきたら意味不明
            if ($loginResult == 'user_name_not_found') {
                $userDBResult['error'] = 'unknown_error';
                $userDBResult['login_result'] = false;
            } else {
                $userDBResult['login_result'] = true;
                $userDBResult['user_id'] = $loginResult['user_id'];
            }
        } else {
            $userDBResult['create_result'] = false;
            $userDBResult['error'] = 'unknown_error';
        }

        return $userDBResult;
    }

    public function checkDuplicatedUserName($userName) {
        ChromePhp::log('In checkDuplicatedUserName');
        return $this->checkUserNameExists($userName);
    }
}
?>