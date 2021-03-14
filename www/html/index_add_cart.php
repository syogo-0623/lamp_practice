<?php
header('X-FRAME-OPTIONS: DENY');
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
//ログインユーザー接続
$user = get_login_user($db);

//item_idのポスト取得
$item_id = get_post('item_id');
//トークンの取得
$token = get_post('token');

//トークンのチェック
if (is_valid_csrf_token($token) === true) {
  //カートに商品追加
  if(add_cart($db,$user['user_id'], $item_id)){
    set_message('カートに商品を追加しました。');
  } else {
    set_error('カートの更新に失敗しました。');
  }
} else {
  set_error('不正な操作が行われました。');
}

//ホームページへ
redirect_to(HOME_URL);