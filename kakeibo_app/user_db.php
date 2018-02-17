<?php
class UserDB {
    
    private $db;

    public function __construct() {
        $this->db = new mysqli('localhost', 'kakeibo_user', 'kakeibo_pass', 'user_db');
        if ($this->db->connect_error) {
            ChromePhp::log($this->db->connect_error);
            exit();
        } else {
            // ChromePhp::log('DB Connected!');
            $this->db->set_charset("utf8");
            // ChromePhp::log($this->db);
        }
    }

    public function checkDuplicatedId($userId) {
        
        // ChromePhp::log($userId);
        // ChromePhp::log($this->db);

        $sql = 'SELECT * FROM user_table WHERE user_id=?';
        if ($statement = $this->db->prepare($sql)) {
            $statement->bind_param('s', $userId);
            $statement->execute();
            // ChromePhp::log($statement);
            $statement->store_result();
            $numRows = $statement->num_rows();
            // ChromePhp::log($numRows);
            if ($numRows > 0) {
                $GLOBALS['input_error']['user_id'] = 'duplicated_user_id';
            }
            ChromePhp::log('In checkDuplicatedId func.');
        }
    }
}
?>