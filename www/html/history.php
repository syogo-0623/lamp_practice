<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'histories.php';

session_start();

if(is_logined() === false) {
    redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

//管理者でログインした場合(管理ユーザー:type=1,一般ユーザー:type=2)
if($user['type'] === 1) {
    $histories = get_admin_history($db);
} else {
    //user_id毎に購入履歴情報取得
    $histories = get_history($db, $user['user_id']);
}

$history_id = get_post('history_id');

include_once VIEW_PATH . 'history_view.php';