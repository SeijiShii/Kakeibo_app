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
            // ChromePhp::log($this->db);
        }
    }

    public function checkDuplicatedId($userName) {
        
        // ChromePhp::log($userName);
        // ChromePhp::log($this->db);

        $sql = 'SELECT * FROM user_table WHERE user_name=?;';
        if ($statement = $this->db->prepare($sql)) {
            $statement->bind_param('s', $userName);
            $statement->execute();
            // ChromePhp::log($statement);
            $statement->store_result();
            $numRows = $statement->num_rows();
            // ChromePhp::log($numRows);
            if ($numRows > 0) {
                $GLOBALS['input_error']['user_name'] = 'duplicated_user_name';
            }
            $statement->close();
            // ChromePhp::log('In checkDuplicatedId func.');
        }
    }

    public function createUserWithNameAndPass($userName, $password) {

        $sql = 'INSERT INTO user_table(user_id, user_name, password) VALUES(?, ?, ?);';
        if ($statement = $this->db->prepare($sql)) {
            $userId = $this->generateUserId($userName);
            ChromePhp::log($userId);
            $statement->bind_param('sss', $userId, $userName, sha1($password));
            $statement->execute();
            // ChromePhp::log($statement->affected_rows);
            // ChromePhp::log($statement->affected_rows == 1);
            if ($statement->affected_rows == 1) {
                ChromePhp::log('Successfully created account!');
            }
            $statement->close();
        }
    }

    private function generateUserId($prefix) {
        // ChromePhp::log('generateUserId called.');
        $dateTime = date('YmdHis');
        ChromePhp::log($dateTime);
        $id = $prefix . '_' . $dateTime . sprintf('%04d', mt_rand(1, 1000)); 
        ChromePhp::log($id);
        return $id;
    }
}
?>