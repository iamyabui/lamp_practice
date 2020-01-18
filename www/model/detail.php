<?php
// detailsテーブルに新しい購入明細を追加
function insert_details($db, $carts, $order_id){
 foreach($carts as $cart){
    $item_id = $cart['item_id'];
    $amount = $cart['amount'];
    $price_bought = $cart['price'];
    // テーブルにcartのデータを追加
    if(insert_detail($db, $item_id, $amount, $price_bought, $order_id) === false){
        return false;
    }
 }
 return true;
}

function insert_detail($db, $item_id, $amount, $price_bought, $order_id){
    $sql = "
      INSERT INTO
        details(
            order_id,
            item_id,
            amount,
            price_bought
        )
      VALUES(:order_id, :item_id, :amount, :price_bougt)
      ";
  
      $params = array(':order_id' => $order_id, ':item_id' => $item_id, ':amount' => $amount, ':price_bougt' => $price_bought);
      return execute_query($db, $sql, $params);
  }
  