<?php

$a = new Item;
$b = new Cart;
$cart = isset($_COOKIE['cart']) ? @unserialize($_COOKIE['cart'],['allowed_classes'=>['Cart']]):(new Cart);
if(isset($_GET['confirm'])) {
	if($cart->getId()) {
		$cart->save();
	} else {
		$cart->create();
	}
	setcookie('cart',serialize($cart));
	$pageContent .= <<<CONTENT
<p>Items have been reserved.</p><p>Thanks for your participation.</p><br>
CONTENT;
} else if(isset($_GET['delete'])) {
	setcookie('cart','',time()-3600);
	header("Location: index.php?page=mycart");
	exit;
}
$items = empty($cart->items) ? [] : explode(',',$cart->items);

if(count($items) > 0) {
	$pageContent .= <<<CONTENT
<p>Here are the items you have in your cart : </p><br>
CONTENT;
	foreach($items as $item) {
		$item_db = $a->get($item)->__toArray();
		$pageContent .= <<<CONTENT
	<div class="item"><p><a href="index.php?page=catalog&id={$item}"><img width="200px" height="200px" src="img/{$item_db['img']}"><p>{$item_db['name']}</p><p>cost : {$item_db['price']}$</p></a></div>
CONTENT;
	}
	$pageContent .= <<<CONTENT
<p><a href="index.php?page=mycart&confirm=1">Confirm my order</a> | <a href="index.php?page=mycart&delete=1">Delete my cart</a></p>
CONTENT;
} else {
	$pageContent .= <<<CONTENT
<p>Your cart is empty.</p>
CONTENT;
}