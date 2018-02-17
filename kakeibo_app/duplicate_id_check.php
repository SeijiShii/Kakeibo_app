<?php
function checkDuplicateID($userDB, $userId) 
{
    ChromePhp::log($userId);

    $sql = 'SELECT * FROM user_table WHERE user_id=?';
    if ($statement = $userDB->prepare($sql)) {
        $statement->bind_param('s', $userId);
        $statement->execute();
        var_dump($GLOBALS);
    }
}
// ChromePhp::log('checkDuplicateID included.');

?>