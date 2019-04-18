<?php
$a = new User;
if(isset($_POST['login']) && isset($_POST['password']) && isset($_GET['action']) && $_GET['action'] === 'login') {
	$a->login($_POST['login'],$_POST['password']);
	header("Location: index.php");
	exit;
} else if(isset($_POST['login']) && isset($_GET['action']) && $_GET['action'] === 'ask-otp') {
	$a->askOneTimePassword($_POST['login']);
	header("Location: index.php");
	exit;
} else if(isset($_POST['login']) && isset($_POST['token']) && isset($_GET['action']) && $_GET['action'] === 'confirm-otp') {
	$a->oneTimeLogin($_POST['login'], $_POST['token']);
	header("Location: index.php");
	exit;
} else if(isset($_POST['login']) && isset($_POST['password']) && isset($_GET['action']) && $_GET['action'] === 'register') {
	$a->register($_POST['login'], $_POST['password']);
	header("Location: index.php");
	exit;
} else if(isset($_GET['action']) && $_GET['action'] === 'logout') {
	session_destroy();
	header("Location: index.php");
	exit;
}

if(!isset($_GET['action']) || $_GET['action'] === 'login') {
	$pageContent = <<<CONTENT
<p>Enter your username and your password to identify yourself</p>
<form method="POST" action="index.php?page=login&action=login">
	<input name="login" type="text" placeholder="login">
	<input name="password" type="password" placeholder="password">
	<input type="submit" name="submit" value="login">
</form>
<a href="index.php?page=login&action=register">Register</a>
CONTENT;
} else if(!isset($_GET['action']) || $_GET['action'] === 'register') {
	$pageContent = <<<CONTENT
<p>Enter your username and your password to register yourself</p>
<form method="POST" action="index.php?page=login&action=register">
	<input name="login" type="text" placeholder="login">
	<input name="password" type="password" placeholder="password">
	<input type="submit" name="submit" value="login">
</form>
<a href="index.php?page=login&action=login">Login</a>
CONTENT;
}

// TODO : add html for otp once we have a mailing system.