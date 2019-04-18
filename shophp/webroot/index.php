<?php

// initiate db connect
$db = new mysqli('127.0.0.1','web','web','shophp');

// load every classes
include('classes/entity.class.php');
include('classes/item.class.php');
include('classes/cart.class.php');
ob_start();

// load header of the website
include('header.php');
// authorized pages
$pages = ['home','about','catalog','mycart'];
$pageContent = "";

// Check if the user try to load an authorized page
if(isset($_GET['page']) && in_array($_GET['page'], $pages))
	include $_GET['page'].'.php';
else
	include 'home.php';

$content = str_replace('{{content}}',$pageContent,ob_get_contents());
ob_end_clean();
echo $content;