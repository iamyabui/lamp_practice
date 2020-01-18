<?php
require_once MODEL_PATH . 'detail.php';
// ordersテーブルに新しい購入明細を追加
function insert_order($db, $user_id){
  $sql = "
    INSERT INTO
      orders(
          user_id
      )
    VALUES(:user_id)
    ";
    $params = array(':user_id' => $user_id);
    return execute_query($db, $sql, $params);
}

// 履歴画面、明細画面にデータをテーブルに挿入
function regist_order_transaction($db, $user_id, $carts){
  if(insert_order($db, $user_id) === false){
      return false;
  }
  $order_id = $db->lastInsertId();
  if(insert_details($db, $carts, $order_id) === false){
      return false;
  }
  return true;
}
