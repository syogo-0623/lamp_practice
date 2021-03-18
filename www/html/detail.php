<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'datail.php';

session_start();

if(is_logined() === false) {
    redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);
$history = get_history($db, $user['user_id']);
$history_id = get_post('history_id');
$datail = get_detail($db, $history_id);

include_once VIEW_PATH . 'detail_view.php';