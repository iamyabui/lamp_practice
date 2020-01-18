order.php
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
    $order_id = $db->lastInsertId();
    $params = array(':user_id' => $user_id);
    return execute_query($db, $sql, $params, $order_id);
}

// 履歴画面、明細画面にデータをテーブルに挿入
function regist_order_transaction($db, $user_id, $carts){
  $db->beginTransaction();
  if(insert_order($db, $user_id) === false){
      $db->rollback();
      return false;
  }
  $order_id = $db->lastInsertId();
  if(insert_details($db, $carts, $order_id) === false){
      $db->rollback();
      return false;
  }
  $db->commit();
  return true;
}
