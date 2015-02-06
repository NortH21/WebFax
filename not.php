<?php
include "config.php";
$CHPY = $_SERVER['REQUEST_URI'];

@list(,$url_variable) = explode("?", $CHPY);
@list($source,$destination,$time,$status,$error,$file) = explode(",",$url_variable);

$connect = mysql_connect($db_location,$db_user,$db_pwd) or die('Хьюстон у нас проблема');
mysql_select_db($db_name);
mysql_query('SET names utf8');

$addlog = mysql_query("INSERT INTO `fax` (source,destination,time,status,error,file) VALUE ('".$source."','".$destination."','".$time."','".$status."','".$error."','".$file."')");

mysql_close($connect);
?>