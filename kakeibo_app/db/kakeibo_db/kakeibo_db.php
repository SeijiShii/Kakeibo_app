<?php

// ini_set('display_errors', "On");

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

        $createBudgetResult = array();
        if ($this->_checkBudgetNameExists($userId, $budgetName)) {
            $createBudgetResult['error'] = 'duplicated_budget_name';
            $createBudgetResult['success'] = false;
        } else {
            $sql = 'INSERT INTO budget_table(user_id, budget_name, budget_id) VALUES(?, ?, ?);';
        
            // ChromePhp::log($sql);
    
            if ($statement = $this->db->prepare($sql)) {
                $budgetId = $this->_generateId($userId . '_budget');
                $statement->bind_param('sss', $userId, $budgetName, $budgetId);
                $statement->execute();
                $createBudgetResult['success'] = $statement->affected_rows == 1;
                if ($createBudgetResult['success']) {
                    $createBudgetResult['create_budget_id'] = $budgetId;
                }
                $statement->close();    
            }
        }
        return $createBudgetResult;
    }

    private function _checkBudgetNameExists($userId, $budgetName) {

        $sql = "SELECT budget_name FROM budget_table WHERE user_id = ? AND budget_name = ?;";
       
        if ($statement = $this->db->prepare($sql)) {
 
            $statement->bind_param('ss', $userId, $budgetName);
            $statement->execute();
            $statement->store_result();
            $numRows = $statement->num_rows();
            $statement->free_result();
            $statement->close();

            return $numRows > 0;
        }
    }

    public function getBudgetsByUserId($userId) {

        $sql = "SELECT * FROM budget_table WHERE user_id = ?;";
       
        if ($statement = $this->db->prepare($sql)) {
 
            $statement->bind_param('s', $userId);
            $statement->execute();

            $result = $statement->get_result();

            $budgets = [];
            while ($row = $result->fetch_assoc()) {
                array_push($budgets, $row);
            }
            $statement->close();
            return $budgets;

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