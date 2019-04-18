<?php
$a = new PrivateEnv;
if(!$_SESSION['logged_in_as']){ header("Location: index.php"); exit; }
$username = $_SESSION['logged_in_as'];
$files = $a->list($username);


if(!isset($_GET['action']) || $_GET['action'] === 'home') {
	$fileList="";
	foreach($files as $file) {
		$fileList.="<p><a href='index.php?page=private&action=retrieve&name=".$file['name']."'>".$file['name']."</a></p>";
	}
	$pageContent = <<<CONTENT
<p>You can upload your files here.</p>
<form method="POST" enctype="multipart/form-data" action="index.php?page=private&action=upload ">
	<input name="file" type="file" placeholder="Upload your file">
	<input type="submit" name="submit" value="upload">
</form>
<br><br>
<p>Here are your files :</p>
{$fileList}
CONTENT;
} else if(isset($_GET['action']) && $_GET['action'] === 'upload' && isset($_FILES['file'])) {
	$a->upload($username,file_get_contents($_FILES['file']['tmp_name']));
	header('Location: index.php?page=private');
	exit;
} else if(isset($_GET['action']) && $_GET['action'] === 'retrieve' && isset($_GET['name'])) {
	if($file = $a->retrieve($_GET['name'])){
		ob_end_clean();
		header("Content-Disposition: attachment;filename=\"".$file['name']."\"");
		print $a->download($file['name']);
		exit;
	} else {
		header("Location: index.php");
		exit;
	}
}