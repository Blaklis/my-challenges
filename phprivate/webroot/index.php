<?php

// initiate db connect
$db =  mysqli_connect('127.0.0.1','web','web','phprivate');
session_start();
// load every classes
ob_start();

// load header of the website
include('header.php');
include('classes/user.class.php');
include('classes/private.class.php');
// authorized pages
$pages = ['home','login', 'private'];
$pageContent = "";

// Check if the user try to load an authorized page
if(isset($_GET['page']) && in_array($_GET['page'], $pages))
	include $_GET['page'].'.php';
else
	include 'home.php';

$content = str_replace('{{content}}',$pageContent,ob_get_contents());
ob_end_clean();
echo $content;
