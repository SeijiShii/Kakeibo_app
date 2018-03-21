<?php

// ini_set('display_errors', "On");

include './common/ChromePhp.php';
include_once './db/user_db/user_db.php';
include_once './db/kakeibo_db/kakeibo_db.php';

function setBudgetSelect($budgets) {
    if (count($budgets) > 0) {
        $_POST['budget_select_visible'] = true;
        if (count($budgets) == 1) {
            $_SESSION['budget_select'] = $budgets[0];
        } else {
            if(isset($_POST['budget_id_select'])) {
                foreach($budgets as $budget) {
                    if ($budget['budget_id'] == $_POST['budget_id_select']) {
                        $_SESSION['budget_select'] = $budget;
                    }
                }
            }
        }
    }
    // var_dump($_SESSION['budget_select']);
}

session_start();

if (!$_SESSION['login_state']) {
    header('Location: ./index.php');
    exit();
}

// ChromePhp::log($_GET);
ChromePhp::log($_SESSION);

if ($_POST['action'] == 'logout') {

    session_destroy();

    header('Location: ./index.php');
    exit();
}

$userDB = new UserDB();
$kakeiboDB = new KakeiboDB();

$_SESSION['user_name'] = $userDB->getUserNameById($_SESSION['user_id']);
$budgets = $kakeiboDB->getBudgetsByUserId($_SESSION['user_id']);
// var_dump($budgets);
setBudgetSelect($budgets);


switch ($_POST['budget_select_action']) {
    case 'create_budget':
    break;

    case 'do_create_budget':
        if ($_POST['budget_create_name'] == '') {
            $select_budget_error['create_budget_name'] = 'blank';
            $_POST['budget_select_action'] = 'create_budget';
        } else {
            $createBudgetResult = $kakeiboDB->createBudget($_SESSION['user_id'], $_POST['budget_create_name']);
            if (isset($createBudgetResult['error']) && $createBudgetResult['error'] == 'duplicated_budget_name') {
                $select_budget_error['create_budget_name'] = 'duplicated_budget_name';
                $_POST['budget_select_action'] = 'create_budget';
            } else {
                if (isset($createBudgetResult['success'])) {
                    if ($createBudgetResult['success']) {
                        // errorがなく、successの場合
                        $budgets = $kakeiboDB->getBudgetsByUserId($_SESSION['user_id']);
                        $_POST['budget_id_select'] = $createBudgetResult['create_budget_id'];
                        setBudgetSelect($budgets);

                    } else {

                    }
                }
            }
        }
    break;

    case 'cancel_create_budget':
        $_POST['budget_select_action'] = null;
    break;
}

ChromePhp::log($_POST);

?>
<!DOCTYPE HTML>
<html>
    <head>
        <?php include('./components/header.php') ?>
        <title>家計簿アプリ ホーム</title>
        <link type="text/css" href="./css/kakeibo_home_style.css?date=<?php echo date('YmdHis', filemtime('./css/kakeibo_home_style.css')); ?>" rel="stylesheet">
    </head>
    <body class="kakeibo_home">
        <div class="content_area">
            <h1 class="app_name_small">家計簿アプリ</h1>
            <h2>家計簿ホーム</h2> 
            <form class='row_height_50px login_state_row' id="login_state" action="" enctype="multipart/form-data" method="post">
                <span class='login_state_text'><?php echo $_SESSION['user_name'] ?>としてログイン中</span>
                <button class='app_green_button logout_button' type='submit' name='action' value='logout'>ログアウト</button>
            </form>
            <form class='budget_select_form' action="" enctype="multipart/form-data" method="post" name='budget_select_form'>
                <?php
                if (count($budgets) > 0) {
                    echo "<select name='budget_id_select' id='budget_id_select' onChange='onChangeBudgetSelect()'>";
                    foreach($budgets as $budget) {
                        $isSelected = '';
                        if ($budget['budget_id'] == $_SESSION['budget_select']['budget_id']) {
                            $isSelected = " selected='selected' ";
                        }
                        echo '<option value=' . $budget['budget_id'] . $isSelected . ' >' . $budget['budget_name'] . '</option>';
                    }
                    echo "</select>";
                }
                ?>
                <?php if (isset($_POST['budget_select_visible']) && $_POST['budget_select_visible']): ?>
                <span>
                    <button class='app_ui_button budget_name_change_button' type="submit" name="budget_select_action" value="change_name">表示中のバジェット名を変更</button>
                    <?php if (false): ?>
                    <input type='text' class='text_input budget_name_change_text' placeholder='変更後の名前' name='budget_changed_name'>
                    <button class='app_ui_button budget_name_change_button' type="submit" name="budget_select_action" value="change_name_save">変更後の名前で保存</button>
                    <?php endif; ?>
                </span>
                <?php endif; ?>
                <br>
                <span>
                    <button class='app_ui_button create_budget_button' type="submit" name="budget_select_action" value="create_budget">バジェット新規作成</button>
                    <?php if ($_POST['budget_select_action'] === 'create_budget'): ?>
                    <input type='text' class='text_input budget_create_name_text' placeholder='新規バジェット名', name='budget_create_name' value='<?php if ($select_budget_error['create_budget_name']){
                        echo htmlspecialchars($_POST['budget_create_name'], ENT_QUOTES, 'utf-8');
                    }?>'>
                    <button class='app_ui_button do_create_budget_button' type="submit" name="budget_select_action" value="do_create_budget">作成</button>
                    <button class='app_green_button create_budget_cancel_button' type="submit" name="budget_select_action" value="cancel_create_budget">キャンセル</button>
                    <?php endif; ?>
                </span>
                <?php if ($select_budget_error['create_budget_name'] == 'blank'): ?>
                <p class="input_warning">新規バジェット名が空です。</p>
                <?php endif; ?>
                <?php if ($select_budget_error['create_budget_name'] == 'duplicated_budget_name'): ?>
                <p class="input_warning"><?php echo $_SESSION['user_name'] ?>はすでに <?php echo $_POST['budget_create_name'] ?>というバジェットを持っています。</p>
                <?php endif; ?>
            </form>
            <?php if (isset($_SESSION['budget_select'])): ?>
            <?php include('./components/func_tab.php') ?>
            <div class='tab_content_frame'>
                <?php 
                switch ($_GET['action']) {

                    case 'estimate':
                    include './tab_contents/estimate_tab.php';
                    break;

                    case 'record_trafic':
                    include './tab_contents/record_trafic_tab.php';
                    break;

                    default:
                    case 'budget_status':
                    include './tab_contents/budget_status_tab.php';
                    break;
                }
                ?>
            </div>
            <?php endif; ?>
        </div>
        <script type='text/javascript' src='./kakeibo_home.js'></script>
    </body>
</html>