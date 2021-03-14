<?php
header('X-FRAME-OPTIONS: DENY');
//定義ファイルの読み込み
require_once '../conf/const.php';
//関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザーファイル読み込み
require_once MODEL_PATH . 'user.php';
//商品ファイルの読み込み
require_once MODEL_PATH . 'item.php';

//セッションスタート
session_start();

//ログインされなければログインページへ
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//DB接続へ
$db = get_db_connect();

//ログインユーザー接続
$user = get_login_user($db);

//管理者ではなかった場合ログインページへ
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

//商品IDポストの取得
$item_id = get_post('item_id');
$token = get_post('token');

//トークンのチェック
if (is_valid_csrf_token($token) === true) {
  //商品情報の削除
  if(destroy_item($db, $item_id) === true){
    //メッセージ
    set_message('商品を削除しました。');
    //削除できなければ
  } else {
    //メッセージ
    set_error('商品削除に失敗しました。');
    }
} else {
  set_error('不正なリクエストです。');
}



//管理者ページへ
redirect_to(ADMIN_URL);