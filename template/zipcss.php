<?php
ob_start("ob_gzhandler");
header("Content-type: text/css; charset: ISO-8859-1");
header("Cache-Control: must-revalidate");
$offset = 60 * 60 ;
$ExpStr = "Expires: " .
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);
echo (file_get_contents ($_GET['file']));
?>