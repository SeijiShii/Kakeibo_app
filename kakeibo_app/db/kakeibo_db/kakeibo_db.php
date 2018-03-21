<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/secret/db_define.php';

class BudgetDB {

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

    }
}

?>