<?php
//定義ファイルの読み込み
require_once '../conf/const.php';
//関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザーファイルの読み込み
require_once MODEL_PATH . 'user.php';
//商品情報ファイルの読み込み
require_once MODEL_PATH . 'item.php';
//カート情報ファイルの読み込み
require_once MODEL_PATH . 'cart.php';

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
//カートIDの接続
$cart_id = get_post('cart_id');
//トークンの取得
$token = get_post('token');

//トークンのチェック
if (is_valid_csrf_token($token) === true) {
  //カート情報の削除
  if(delete_cart($db, $cart_id)){
    set_message('カートを削除しました。');
  } else {
    set_error('カートの削除に失敗しました。');
  }
} else {
  set_error('不正な操作が行われました');
}

//カート情報へ
redirect_to(CART_URL);