<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴</title>
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'admin.css')); ?>">
</head>
<body>
  <?php 
  include VIEW_PATH . 'templates/header_logined.php'; 
  ?>

  <div class="container">
    <h1>購入明細画面</h1>
    <div>
        <p>注文番号：<?php print(h($order_id));?></p>
        <p>購入日時：<?php print($orders['created']);?></p>
        <p>合計金額：<?php print(h($total_price));?></p>
    </div>

    <?php include VIEW_PATH . 'templates/messages.php'; ?>
      <?php if(count($details) > 0){ ?>
      <table class="table table-bordered text-center">
        <thead class="thead-light">
            <tr>
            <th>商品名</th>
            <th>価格</th>
            <th>購入数</th>
            <th>小計</th>
            </tr>
        </thead>
        <tbody>
          <?php foreach($details as $detail){ ?>
            <tr>
            <td><?php print(h($detail['name']));?></td>
            <td><?php print(h($detail['price_bought']));?></td>
            <td><?php print(h($detail['amount'])); ?></td>
            <td><?php print(h(number_format($detail['price_bought'] * $detail['amount']))); ?>円</td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>購入履歴無</p>
    <?php } ?> 
  </div>
</body>
</html>