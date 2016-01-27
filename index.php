<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Факс</title>
<script src="links/val.js" type="text/javascript" ></script>
<script src="links/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="links/jquery.tablesorter.js" type="text/javascript"></script>

<link href="links/styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">
<div class="content">
  <h1>Факс</h1>
    <p>Пожалуйста, воспользуйтесь формой ниже для ввода необходимой информации и нажмите кнопку &quot;отправить факс&quot;.
    <form action="sendfax.php" method="post" enctype="multipart/form-data" name="form1" id="form1">
      <table width="892" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="188" class="formLabel">Заголовок факса</td>
          <td width="232"><label for="faxHeader"></label>
          <input name="faxHeader" type="text" id="faxHeader" value="Fax" /></td>
          <td width="458"  class="descriptionText">Введите заголовок, который должен отображаться в верхней части вашего факса.</td>
        </tr>
        <tr>
          <td class="formLabel">Номер назначения<span style="color: #CC0000">*</span></td>
          <td><label for="dest"></label>
            <span id="sprytextfield3">
            <input name="dest" type="text" id="dest" value="" /><br />
          <span class="textfieldRequiredMsg">Это поле является обязательным.</span><span class="textfieldInvalidFormatMsg">Неверный Формат.</span></span></td>
          <td class="descriptionText">Номер назначения факса. Пожалуйста, не вставляйте пробелы или специальные символы. Номер в формате <strong>593113</strong> или <strong>8XXXX593113</strong></td>
        </tr>
        <tr>
          <td width="188" class="formLabel">Внутренний номер факса</td>
          <td width="232"><label for="fax_disa_number"></label>
          <input name="fax_disa_number" type="text" id="fax_disa_number" value="" /></td>
          <td width="458"  class="descriptionText">Если вы знаете допольнительный/внутренний номер факса напишите его и после вызова абонента факс-сервер донаберёт на внутренний номер.</td>
        </tr>
        <tr>
          <td class="formLabel">Приложить файл<span style="color: #CC0000">*</span></td>
          <td><label for="faxFile"></label>
          <input type="file" name="faxFile" id="faxFile" /></td>
          <td class="descriptionText">Расширение файлов: <strong>PDF, DOC, JPG/JPEG, TXT, ODF, ODT, ODS</strong>.</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input type="submit" name="sendFax" id="sendFax" value="Отправить факс" /></td>
          <td class="descriptionText">Важно! Не получилось один раз, попробуй второй.</td>
        </tr>
      </table>
    <br>
    <table id="Table" width="100%" border="0" cellpadding="0" cellspacing="0" class="formLabel">
    <thead>
    <th>Дата</th><th>Отправитель</th><th></th><th>Получатель</th><th>Статус</th><th>Сcылка</th>
    </thead>
    <tbody>
<?php
include "config.php";
$connect = mysql_connect($db_location,$db_user,$db_pwd) or die($error_mess);
mysql_select_db($db_name);
mysql_query('SET names utf8');
$getlog_query = mysql_query("SELECT source,destination,time,status,error,file FROM `$db_name` ORDER BY fax.time DESC LIMIT 10");
while ($getlog_result = mysql_fetch_assoc($getlog_query))
{
    echo '<tr><td>'.date('d-m-Y H:i:s', $getlog_result['time']).'</td><td>'.$getlog_result['source'].'</td><td></td><td>'.$getlog_result['destination'].'</td>';
    echo '<td>'.($getlog_result['status'] == "SUCCESS" ? ($getlog_result['source'] == $own_phone_number ? "<font color='green'>Отправлен</font>" : "<font color='blue'>Получен<font>") : "<font color='red'>Увы и ах</font>").'</td><td><a href="/files/'.date('m_Y', $getlog_result['time']).'/'.$getlog_result['file'].'.pdf">Файл</a></td></tr>';
}
mysql_close($connect);
?>
    </tbody>
  </table>
 </form>
</div>
<p class="footer-copyrights">Web Fax for Asterisk. Released under GPLv3. <a href="<?=$domain; ?>/changelog"><?=$version; ?></a></p>
</div>
<script type="text/javascript">
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "integer");
$(document).ready(function()
    {
        $("#Table").tablesorter();
    }
);
</script>
</body>
</html>