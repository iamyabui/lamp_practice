<!DOCTYPE html>
<html lang="ja">
<head>
  <!-- view/templatesフォルダ内のhead.phpを読み込み -->
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>カート</title>
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'cart.css')); ?>">
</head>
<body>
  <!-- view/templatesフォルダ内のheader_logined.phpを読み込み -->
  <!-- header_logined.php：ログイン後のheader部分で使用するフォーマット -->
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>カート</h1>
  <div class="container">

    <!-- view/templatesフォルダ内のmessages.phpを読み込み -->
    <!-- cart.phpで取得したerrorメッセージの表示 -->
    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <!-- ログインユーザのカートにitemが一つ以上ある場合 -->
    <?php if(count($carts) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品画像</th>
            <th>商品名</th>
            <th>価格</th>
            <th>購入数</th>
            <th>小計</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <!-- ログインユーザのカート内全てのitemについて実施 -->
          <?php foreach($carts as $cart){ ?>
          <tr>
            <!-- h関数でXSS対策 -->
            <!-- item毎の画像、商品名、値段、購入予定数、小計をテーブルで出力 -->
            <td><img src="<?php print(h(IMAGE_PATH . ($cart['image'])));?>" class="item_image"></td>
            <td><?php print(h($cart['name'])); ?></td>
            <td><?php print(h(number_format($cart['price']))); ?>円</td>
            <td>
              <!-- cart_change_amount.phpに、
              cart.phpで取得した以下ログインユーザのカート内情報を渡す   
              ・amout：購入予定数
              ・cart_id：カートID
              ・token：トークン
               -->
              <form method="post" action="cart_change_amount.php">
                <input type="number" name="amount" value="<?php print(h($cart['amount'])); ?>">
                個
                <input type="submit" value="変更" class="btn btn-secondary">
                <input type="hidden" name="cart_id" value="<?php print(h($cart['cart_id'])); ?>">
                <input type="hidden" name="token" value="<?php print(h($token)); ?>">
              </form>
            </td>
            <td><?php print(h(number_format($cart['price'] * $cart['amount']))); ?>円</td>
            <td>
              <!-- cart_delete_cart.phpに、
              cart.phpで取得した以下ログインユーザのカート内情報を渡す   
              ・cart_id：購入予定数
              ・token：トークン
               -->
              <form method="post" action="cart_delete_cart.php">
                <input type="submit" value="削除" class="btn btn-danger delete">
                <input type="hidden" name="cart_id" value="<?php print(h($cart['cart_id'])); ?>">
                <input type="hidden" name="token" value="<?php print(h($token)); ?>">
              </form>

            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <p class="text-right">合計金額: <?php print h(number_format($total_price)); ?>円</p>
      
      <!-- finish.phpに、
              cart.phpで取得した以下ログインユーザのカート内情報を渡す   
              ・token：トークン
               -->
      <form method="post" action="finish.php">
        <input class="btn btn-block btn-primary" type="submit" value="購入する">
        <input type="hidden" name="token" value="<?php print(h($token)); ?>">
      </form>
    <?php } else { ?>
      <p>カートに商品はありません。</p>
    <?php } ?> 
  </div>
  <script>

    // deleteクラスを持つ要素に対して、ボタンをクリックすると、削除OKもしくはキャンセルを選択するポップが出力される。
    $('.delete').on('click', () => confirm('本当に削除しますか？'))
  </script>
</body>
</html>