<html>
	<head>
		<style>
			* { margin:0; padding:0; border:0; }
			body,html { width:100%; height:100%; background-image:url('img/skull.png'); background-size:cover; }
			#menu { width:100%;background-color:rgba(173,1,1,0.98);}
			#menu li { width:12.5%; display:inline-block; text-align:center; padding-bottom:25px;padding-top:25px;}
			#menu li a { color:white; text-decoration:none; }
			#content { margin-top:50px; background-color:rgba(255,255,255,0.98); margin:0 auto; width:75%; min-height:80%;}
			#contentData { padding:50px; }
			img { max-width:200px; max-height:200px; }
			.item { margin-top:50px; }
			input { display:block; border:1px solid red; margin:10px 0; }
			input[type="submit"] { border: 3px solid green; padding:3px; }
		</style>
	</head>
	<body>
		<div id="menu">
			<ul>
				<li><a href="index.php?page=home">Home</a></li><!--
				--><li><?php if(!$_SESSION['logged_in_as']){ echo '<a href="index.php?page=login">Login</a>'; } else { echo '<a href="index.php?page=login&action=logout">Logout</a>'; }?></li><!--
				--><li><?php if($_SESSION['logged_in_as']){ echo '<a href="index.php?page=private">Private</a>'; }?></li><!--
			-->
			</ul>
		</div>
		<div id="content">
			<div id="contentData">
				{{content}}
			</div>
		</div>
	</body>
</html>