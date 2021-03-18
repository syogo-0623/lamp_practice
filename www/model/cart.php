<?php 
//関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//DB接続ファイルの読み込み
require_once MODEL_PATH . 'db.php';

//ユーザーのカート情報取得
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  return fetch_all_query($db, $sql, array($user_id));
}

//ユーザーのカート情報取得
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";

  return fetch_query($db, $sql, array($user_id, $item_id));

}

//カートの追加処理
function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  //カートになかった場合
  if($cart === false){
    //追加処理へ
    return insert_cart($db, $user_id, $item_id);
  }
  //カートの商品情報追加
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

//商品追加処理
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, 1)
  ";

  return execute_query($db, $sql, array($item_id, $user_id));
}

//商品購入数更新処理
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  return execute_query($db, $sql, array($amount, $cart_id));
}

//商品削除処理
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  return execute_query($db, $sql, array($cart_id));
}

//購入カート
function purchase_carts($db, $carts){
  //有効な購入カートでなかった場合
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  //購入した後にカートの中身の削除と在庫更新処理を購入履歴・明細に挿入
  $db->beginTransaction();
  try {
    insert_history($db, $carts[0]['user_id']);
    $history_id = $db->lastInsertId();

    //購入後の購入明細処理
    foreach($carts as $cart){
      insert_datail(
        $db,
        $history_id,
        $cart['item_id'],
        $cart['price'],
        $cart['name'],
        $cart['amount']
      );
      //購入後の在庫更新処理
      if(update_item_stock(
          $db, 
          $cart['item_id'], 
          $cart['stock'] - $cart['amount']
        ) === false){
        set_error($cart['name'] . 'の購入に失敗しました。');
      }
    }
      //ユーザーのカート削除処理
      delete_user_carts($db, $carts[0]['user_id']);
      //コミット
      $db->commit();
  } catch(PDOException $e) {
    //ロールバック
    $db->rollback();
    throw $e;
  }
}

//ユーザーのカート削除処理
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  execute_query($db, $sql, array($user_id));
}

//購入合計価格の計算
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

//購入カートの数量確認
function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

//購入履歴へ追加
function insert_history($db, $user_id) {
  $sql = "
    INSERT INTO
      history (
        user_id
      )
    VALUES(?)
  ";
  return execute_query($db, $sql, array($user_id));
}

//購入明細に追加
function insert_datail($db, $history_id, $name, $item_id, $amount, $price) {
  $sql = "
    INSERT INTO
      datail(
        history_id,
        name,
        item_id,
        amount,
        price
      )
    VALUES(?, ?, ?, ?, ?)
  ";
  return execute_query($db, $sql, array($history_id, $name, $item_id, $amount, $price));
}