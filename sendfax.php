<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Факс</title>
<link href="links/styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">
  <div class="content">
    <h1>Факс</h1>
    <p>
<?php
include "config.php";
header('Refresh: 15; URL=$domain');

$outboundfax_context = $outfax_context;
$asterisk_spool_folder = $dir_call_f;
$faxHeader = $_POST["faxHeader"];
$dest = $_POST["dest"];
$send_fax_disa_number = $_POST["fax_disa_number"];
$localID = $own_phone_number;

function unique_name($path, $suffix)
{
   $file = $path."/".mt_rand().$suffix;
 return $file;
}

$error_converting_document = 1;
$error_creating_call_file = 2;
$error_no_error = 0;

$input_file_noext = unique_name("/tmp", "");
$input_file = $input_file_noext .".pdf";
$input_file_doc = $input_file_noext .".doc";
$input_file_odf = $input_file_noext .".odf";
$input_file_odt = $input_file_noext .".odt";
$input_file_ods = $input_file_noext .".ods";
$input_file_txt = $input_file_noext .".txt";
$input_file_jpg = $input_file_noext .".jpg";
$input_file_jpeg = $input_file_noext .".jpeg";
$input_file_JPG = $input_file_noext .".JPG";
$input_file_JPEG = $input_file_noext .".JPEG";
$input_file_tif = $input_file_noext .".tif";

$error = $error_no_error;

$script_local_path = $_REAL_BASE_DIR = realpath(dirname(__FILE__));
$input_file_orig_name = basename($_FILES['faxFile']['name']);
$ext = substr($input_file_orig_name, strrpos($input_file_orig_name, '.') + 1);

//
// Логируем тут
//

  // Функция определения IP
  function getIP($type = 'real')
  {
    if ($type == "local")
      $client_ip = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : "";
    else
      $client_ip = (!empty($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : ((!empty($_ENV['REMOTE_ADDR'])) ? $_ENV['REMOTE_ADDR'] : "unknown");
    return $client_ip;
  }

  //подключаемся к базе
  $connect = mysql_connect($db_location,$db_user,$db_pwd) or die($error_mess);
  mysql_select_db($db_name);
  mysql_query('SET names utf8');

  $ip = getIP();
  $ip_local = getIP('local');
  $addlog = mysql_query("INSERT INTO `logs` (date,ip,ip_local,send_phone,send_file_name) VALUE ('".time()."','".$ip."','".$ip_local."','".$dest."','".$input_file_orig_name."')");
  mysql_close($connect);
// Конец логирования

if ($ext == "doc") {
    if(move_uploaded_file($_FILES['faxFile']['tmp_name'], $input_file_doc)) {
    $input_file = $input_file_noext . ".pdf";

    $wv_command = "unoconv -f pdf -e FilterOptions=UTF8 $input_file_doc";
    $wv_command_output = system($wv_command, $retval);

if ($retval != 0) {
        echo "Произошла ошибка конвертирования DOC файлов в PDF. Попробуйте загрузить файл еще раз";
        $error = $error_converting_document;
        $doc_convert_output = $wv_command_output;

}else{

$input_file_type = "pdf";
    }
} else{
    echo "Произошла ошибка при загрузке файла, повторите, пожалуйста!";
    }
}

if ($ext == "txt") {
    if(move_uploaded_file($_FILES['faxFile']['tmp_name'], $input_file_txt)) {
    $input_file = $input_file_noext . ".pdf";

    $wv_command = "unoconv -f pdf -e FilterOptions=UTF8 $input_file_txt";
    $wv_command_output = system($wv_command, $retval);

if ($retval != 0) {
        echo "Произошла ошибка конвертирования TXT файлов в PDF. Попробуйте загрузить файл еще раз";
        $error = $error_converting_document;
        $doc_convert_output = $wv_command_output;

}else{

$input_file_type = "pdf";
    }
} else{
    echo "Произошла ошибка при загрузке файла, повторите, пожалуйста!";
    }
}

if ($ext == "odt" || $ext == "odf" || $ext == "ods") {
    if(move_uploaded_file($_FILES['faxFile']['tmp_name'], $input_file_odt)) {
    $input_file = $input_file_noext . ".pdf";

    $wv_command = "unoconv -f pdf $input_file_odt";
    $wv_command_output = system($wv_command, $retval);

if ($retval != 0) {
        echo "Произошла ошибка конвертирования ODT/ODF/ODS файлов в PDF. Попробуйте загрузить файл еще раз";
        $error = $error_converting_document;
        $doc_convert_output = $wv_command_output;

}else{

$input_file_type = "pdf";
    }
} else{
    echo "Произошла ошибка при загрузке файла, повторите, пожалуйста!";
    }
}
if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG") {
  if(move_uploaded_file($_FILES['faxFile']['tmp_name'], $input_file_jpg)) {
  $input_file = $input_file_noext . ".pdf";
  $wv_command = "/usr/bin/convert $input_file_jpg $input_file" ;
  $wv_command_output = system($wv_command, $retval);

  if ($retval != 0) {
     echo "Произошла ошибка преобразования JPG в PDF. Попробуйте загрузить файл еще раз или с более старой версии PDF";
     $error = $error_converting_document;
     $jpg_convert_output = $wv_command_output;
     }else{
          $input_file_type = "pdf";
 }
} else{
echo "Произошла ошибка при загрузке файла, повторите, пожалуйста!";
    }
}

if ($ext == "pdf")  {
        if(move_uploaded_file($_FILES['faxFile']['tmp_name'], $input_file)) {
                $input_file_type = "pdf";
        }else{
                echo "Произошла ошибка при загрузке файла, пожалуйста повторите!";
        }
}

if($error == $error_no_error && $input_file_type == "pdf") {
        $gs_command = "gs -q -dNOPAUSE -dBATCH -dSAFER -sDEVICE=tiffg3 -sOutputFile=${input_file_tif} -f $input_file " ;
        $gs_command_output = system($gs_command, $retval);
        $doc_convert_output = $gs_command_output;

        if ($retval != 0) {
                echo "Произошла ошибка преобразование файла PDF в TIF. Попробуйте загрузить файл еще раз. ";
                $error = $error_converting_document;
        }

else  {

                $callfile = "Channel:Local/send@fax_with_threads\n" .
                            "CallerID:$localID\n" .
                                        "MaxRetries:3\n" .
                                        "RetryTime:60\n" .
                                        "WaitTime:100\n"  .
                                        "Archive:yes\n"  .
                                        "Context:$outboundfax_context\n"  .
                                        "Extension:faxout\n" .
                                        "Priority:1\n" .
                                        "Set: send_fax_disa_number=$send_fax_disa_number\n" .
                                        "Set:TIFF_2_SEND=$input_file_tif\n" .
                                        "Set:TAGLINE=$faxHeader\n" .
                                        "Set:TIMESTAMP=" . date("d/m/y : H:i:s",time()) . "\n" .
                                        "Set:RECEIVER=$dest\n" .
                                        "Set:LOCALSTATIONID=$localID\n";

                $callfilename = unique_name("/tmp", ".call");
                $f = fopen($callfilename, "w");
                fwrite($f, $callfile);
                fclose($f);
                rename($callfilename, $asterisk_spool_folder . substr($callfilename,4));
        }
}

if ($error == $error_no_error) {
        echo " Через 15 сек. Вы будете перенаправлены на главную страницу. ".
         " Отправка вашего факса поставлена в очередь. ";
}else if ($error == $error_converting_document) {
        echo "<span class='error'>Ваш факсимильный документ не может быть преобразован. Пожалуйста, попробуйте снова.";
}
?>
</p>
</div>
<p class="footer-copyrights">Web Fax for Asterisk. Released under GPLv3. <a href="<?=$domain; ?>/changelog"><?=$version; ?></a></p>
</div>
</body>
</html>
