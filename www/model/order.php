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

// 以下はmodel/order.phpに追加
function sum_orders($db, $order_id){
    $details =  get_user_details($db, $order_id);
    $total_price = 0;
    foreach($details as $detail){
        $total_price += $detail['price_bought'] * $detail['amount'];
    }
    return $total_price;
}

// ユーザの履歴情報を取得
function get_user_orders($db, $user){
    $sql = "
    SELECT
        orders.order_id,
        orders.created,
        SUM(details.price_bought * details.amount) AS price_sum
    FROM
        orders
     INNER JOIN
     	details
     ON orders.order_id = details.order_id
     ";
    
    $params = array();

     if (is_admin($user) === false){
        $sql .= "
        WHERE
        user_id = :user_id";
        $params[':user_id'] = $user['user_id'];
    }
    
    $sql .= "
    GROUP BY
    	orders.order_id
    ORDER BY
        orders.created DESC
    ";
    
    return fetch_all_query($db, $sql, $params);
}

function get_order($db, $order_id){
    $sql = "
    SELECT
      created,
      user_id
    FROM
      orders
    WHERE
      order_id = :order_id
  ";
    $params = array(':order_id' => $order_id);
    return  fetch_query($db, $sql, $params);
  }