<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

//ユーザー毎の購入明細
function get_datail($db, $history_id) {
    $sql = "
        SELECT
            items.name,
            detail.price,
            detail.amount,
            SUM(detail.price * detail.amount) AS subtotal,
            detail.create_datetime,
        FROM
            detail
        INNER JOIN
            items
        ON
            detail.item_id = items.item_id
        WHERE
            history_id = ?
    ";
    return fetch_all_query($db, $sql, array($history_id));
}