<h3>収支を記録</h3>
<?php include $_SERVER['DOCUMENT_ROOT'].'/kakeibo_app/components/category_select.php' ?>
<form class='row_height_50px trafic_record_form' action='' enctype="multipart/form-data" method="post">
    <input type='number' class='text_input trafic_record_date' placeholder='日付'>
    <input type='text' class='text_input trafic_record_text' placeholder='項目'>
    <input type='number' class='text_input trafic_record_text' placeholder='金額'>
    <button class='app_ui_button trafic_record_button'>記録する</button>
</form>