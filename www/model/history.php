<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

//ユーザーの購入履歴
function get_history($db, $user_id) {
    $sql = "
        SELECT
            history.history_id,
            history.create_datetime,
            SUM(datail.price * datail.amount) AS total
        FROM
            history
        join
            datail
        ON
            history.history_id = datail.history_id
        WHERE
            user_id = ?
        ORDER BY
            create_datetime desc
    ";
    return fetch_all_query($db, $sql, array($user_id));
}