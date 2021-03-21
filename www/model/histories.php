<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

//ユーザーの購入履歴
function get_history($db, $user_id) {
    $sql = "
        SELECT
            history.history_id,
            history.create_datetime,
            SUM(detail.price * detail.amount) AS total
        FROM
            history
        INNER JOIN
            detail
        ON
            history.history_id = detail.history_id
        WHERE
            user_id = ?
        GROUP BY
            history_id
        ORDER BY
            create_datetime desc
    ";
    return fetch_all_query($db, $sql, array($user_id));
}

//ユーザー毎の購入明細
function get_detail($db, $history_id) {
    $sql = "
        SELECT
            items.name,
            detail.price,
            detail.amount,
            SUM(detail.price * detail.amount) AS subtotal,
            history.create_datetime
        FROM
            detail
        INNER JOIN
            history
        ON
            history.history_id = detail.history_id  
        INNER JOIN
            items
        ON
            detail.item_id = items.item_id
        WHERE
            detail.history_id
        GROUP BY
            
    ";
    return fetch_all_query($db, $sql, array($history_id));
}