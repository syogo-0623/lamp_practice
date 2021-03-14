<?php
//定義ファイルの読み込み
require_once '../conf/const.php';
//関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザー情報ファイルの読み込み
require_once MODEL_PATH . 'user.php';
//商品情報の読み込み
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
//ユーザーのカート情報の取得
$carts = get_user_carts($db, $user['user_id']);
//トークンの取得
$token = get_post('token');

//トークンのチェック
if (is_valid_csrf_token($token) === true) {
  //購入カートがなかったら
  if(purchase_carts($db, $carts) === false){
  set_error('商品が購入できませんでした。');
  //カートページへ
  redirect_to(CART_URL);
  } 
} else {
  set_error ('不正な操作が行われました。');
}

//トータル金額の定義
$total_price = sum_carts($carts);

//finish_view.phpへ
include_once '../view/finish_view.php';