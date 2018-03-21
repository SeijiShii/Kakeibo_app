<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/secret/db_define.php';

class KakeiboDB {

    private $db;

    public function __construct() {
        $this->db = new mysqli('localhost', DB_USER_NAME, DB_USER_PASS, 'kakeibo_db');
        if ($this->db->connect_error) {
            ChromePhp::log($this->db->connect_error);
            exit();
        } else {
            // ChromePhp::log('DB Connected!');
            $this->db->set_charset("utf8");
        }
    }

    public function createBudget($userId, $budgetName) {
        $sql = 'INSERT INTO budget_table(user_id, budget_name, budget_id) VALUES(?, ?, ?);';
        
        ChromePhp::log($sql);

        if ($statement = $this->db->prepare($sql)) {
            $budgetId = $this->_generateId('budget');
            $statement->bind_param('sss', $userId, $budgetName, $budgetId);
            $statement->execute();
            $success = $statement->affected_rows == 1;
            $statement->close();

            return $success;
        }
    }

    private function _generateId($idHeader) {
        $dateTime = date('YmdHis');
        $id = $idHeader . '_' . $dateTime . sprintf('%04d', mt_rand(1, 1000)); 
        // ChromePhp::log($id);
        return $id;
    }
}

?>