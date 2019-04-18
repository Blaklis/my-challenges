<?php

$a = new Item;
if(isset($_GET['id']) && $a->get($_GET['id'])) {
	$cart = isset($_COOKIE['cart']) ? @unserialize($_COOKIE['cart'],['allowed_classes'=>['Cart']]):(new Cart);
	if($cart and is_object($cart) and get_class($cart) == 'Cart') {
		$cart->items = empty($cart->items) ? $_GET['id']:$cart->items.','.$_GET['id'];
		setcookie('cart',serialize($cart));
	}
	$pageContent = <<<CONTENT
		<p>The item you selected is now added to your cart.</p>
CONTENT;
}

$pageContent .= <<<CONTENT
<p>Here are the items you can reserve from our shop. Click to add in your cart.</p>
CONTENT;

$items = $a->all();
foreach($items as $item) {
$pageContent .= <<<CONTENT
<div class="item"><p><a href="index.php?page=catalog&id={$item['id']}"><img src="img/{$item['img']}"><p>{$item['name']}</p><p>cost : {$item['price']}$</p></a></div>
CONTENT;
}