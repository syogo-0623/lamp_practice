<?php
header('X-FRAME-OPTIONS: DENY');
//定義ファイルの読み込み
require_once '../conf/const.php';
//関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザーファイルの読み込み
require_once MODEL_PATH . 'user.php';
//商品情報の読み込み
require_once MODEL_PATH . 'item.php';

//セッションスタート
session_start();

//ログインされなかったらログインページへ
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//DB接続
$db = get_db_connect();

//ログインされたユーザーの接続
$user = get_login_user($db);

//管理者ではなかったらログインページへ
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

//ポストの取得
$name = get_post('name');
$price = get_post('price');
$status = get_post('status');
$stock = get_post('stock');
$token = get_post('token');

//ゲットの取得
$image = get_file('image');

//トークンのチェック
if (is_valid_csrf_token($token) === true) {
  //商品情報の追加登録
  if(regist_item($db, $name, $price, $stock, $status, $image)){
    //メッセージ
    set_message('商品を登録しました。');
  }else {
    //メッセージ
    set_error('商品の登録に失敗しました。');
    }
} else {
  set_erorr('不正なリクエストです。');
}

//管理者ページへ
redirect_to(ADMIN_URL);