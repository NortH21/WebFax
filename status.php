<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ярнет.Факс</title>
<script src="links/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="links/jquery.tablesorter.js" type="text/javascript"></script>
<link href="links/styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">
<div class="content">
  <h1>Ярнет.Факс</h1>
  <br>
    <table id="Table1" width="100%" border="0" cellpadding="0" cellspacing="0" class="formLabel">
    <thead>
    <th>ИД</th><th>Дата</th><th>IP</th><th>IP маш</th><th>Телефон</th><th>Файл</th>
    </thead>
    <tbody>
<?php
include "config.php";
$connect = mysql_connect($db_location,$db_user,$db_pwd) or die('Хьюстон у нас проблема');
mysql_select_db($db_name);
mysql_query('SET names utf8');
$getlog_query = mysql_query("SELECT id,date,ip,ip_local,send_phone,send_file_name FROM `logs` ORDER BY logs.date DESC LIMIT 10");
while ($getlog_result = mysql_fetch_assoc($getlog_query))
{
    echo '<tr><td>'.$getlog_result['id'].'</td><td>'.date('d-m-Y H:i:s', $getlog_result['date']).'</td><td>'.$getlog_result['ip'].'</td><td>'.$getlog_result['ip_local'].'</td>';
    echo '<td>'.$getlog_result['send_phone'].'</td><td>'.$getlog_result['send_file_name'].'</td></tr>';
}
mysql_close($connect);
?>
    </tbody>
    </table>
    <br>
    <table id="Table" width="100%" border="0" cellpadding="0" cellspacing="0" class="formLabel">
    <thead>
    <th>Дата</th><th>Отправитель</th><th></th><th>Получатель</th><th>Статус</th><th>Сcылка</th>
    </thead>
    <tbody>
<?php
$connect = mysql_connect($db_location,$db_user,$db_pwd) or die('Хьюстон у нас проблема');
mysql_select_db($db_name);
mysql_query('SET names utf8');
$getlog_query = mysql_query("SELECT source,destination,time,status,error,file FROM `fax` ORDER BY fax.time DESC LIMIT 10");
while ($getlog_result = mysql_fetch_assoc($getlog_query))
{
    echo '<tr><td>'.date('d-m-Y H:i:s', $getlog_result['time']).'</td><td>'.$getlog_result['source'].'</td><td></td><td>'.$getlog_result['destination'].'</td>';
    echo '<td>'.($getlog_result['status'] == "SUCCESS" ? ($getlog_result['source'] == "593001" ? "<font color='green'>Отправлен</font>" : "<font color='blue'>Получен<font>") : "<font color='red'>Увы и ах</font>").'</td><td><a href="/files/'.$getlog_result['file'].'.pdf">Файл</a></td></tr>';
}
mysql_close($connect);
?>
    </tbody>
  </table>
</div>
<p class="footer-copyrights">Web Fax for Asterisk. Released under GPLv3. <a href="http://fax.yarnet.ru/changelog">v.1.0.2</a></p>
</div>
<script type="text/javascript">
$(document).ready(function()
    {
        $("#Table").tablesorter();
    }
);
$(document).ready(function()
    {
        $("#Table1").tablesorter();
    }
);
</script>
</body>
</html>
