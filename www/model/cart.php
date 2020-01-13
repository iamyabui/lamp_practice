<?php 
require_once 'functions.php';
require_once 'db.php';

// 該当ユーザのカートにおける各商品に対する以下情報を取得
// （商品ID、商品名、値段、在庫、ステータス、画像、カートID、ユーザID、購入予定数）
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
      carts.user_id = :user_id
  ";
  $params = array(':user_id' => $user_id);
  return fetch_all_query($db, $sql, $params);
}

// 該当のユーザカート内における該当商品の情報を取得
function get_user_cart($db, $item_id, $user_id){
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
      carts.user_id = :user_id
    AND
      items.item_id = :item_id
  ";

  $params = array(':user_id' => $user_id, ':item_id' => $item_id);
  return fetch_query($db, $sql, $params);

}

// カート内に選択した商品がない場合、カートテーブルに商品を追加、ある場合は該当商品の購入予定数のみ更新
function add_cart($db, $item_id, $user_id) {
  // 該当のユーザカート内における該当商品の情報を取得
  $cart = get_user_cart($db, $item_id, $user_id);
  // カート内に該当商品がない場合、カートテーブルに行を新規で追加
  if($cart === false){
    return insert_cart($db, $item_id, $user_id);
  }// カート内に該当商品がある場合、購入予定数を１増やす
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

// cartsテーブルに、item_id、user_id、amountに1を代入して、行を追加
function insert_cart($db, $item_id, $user_id, $amount = 1){
  //item_id,user_id,amountに、名前付きプレースホルダーを使用
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(:item_id, :user_id, :amount)
  ";

  //:item_idに$item_id、:user_idに$user_id, :amountに$amountを代入し、params配列で渡す
  $params = array(':item_id' => $item_id, ':user_id' => $user_id, ':amount' => $amount);
  return execute_query($db, $sql, $params);
}

// 該当のカートIDに対してcartsテーブルの購入予定数(amount)を更新している
function update_cart_amount($db, $cart_id, $amount){
  //名前付きプレースホルダを使用して、バインドしたい値（amout、cart_id）それぞれに:名前で指定。
  $sql = "
    UPDATE
      carts
    SET
      amount = :amount
    WHERE
      cart_id = :cart_id
    LIMIT 1
  ";
  //名前付きプレースホルダーで指定したものについて、
  //params配列で、$amount,$cart_idを代入
  $params = array(':amount' => $amount, ':cart_id' => $cart_id);
  //$sqlと$paramsを引数としてexecute_queryを実行
  return execute_query($db, $sql, $params);
}

// 該当のcart_idに対して行ごと削除
function delete_cart($db, $cart_id){
//cart_idに、名前付きプレースホルダーを使用
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = :cart_id
    LIMIT 1
  ";

//:cart_idに$cart_idを代入し、paramsに渡す
  $params = array(':cart_id' => $cart_id);
  return execute_query($db, $sql, $params);
}
// 在庫からカート内商品購入数を引いて、itemsテーブルの在庫数を更新
function purchase_carts($db, $carts){
  // カート内商品の有無、商品表示ステータスチェック、在庫数チェックを実施、一つでもNGの場合false
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  // カート内商品分繰り返す
  foreach($carts as $cart){
    // カート内商品について、在庫から購入数を引いた数を、itemsテーブルの在庫数に更新
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  
  delete_user_carts($db, $carts[0]['user_id']);
}

function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
    user_id = :user_id
  ";

  $params = array(':user_id' => $user_id);
  execute_query($db, $sql, $params);
}

// カート内の合計金額を計算
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}
// カート内商品のチェック
function validate_cart_purchase($carts){
  // カート内に商品がない場合はエラーメッセージを表示
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    // カート内商品の商品表示ステータスがcloseの場合、エラーメッセージを表示
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    // カート内商品の購入予定数を購入可能か在庫チェック、在庫が足りない場合はエラーメッセージを表示
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

