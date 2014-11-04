<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ярнет.Факс</title>
<link href="links/styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">
  <div class="content">
    <h1>Ярнет.Факс</h1>
    <p>
<?php

//$outbound_route = "@yarnet_gorod";
//$outboundfax_context = "outboundfax";
$asterisk_spool_folder = "/var/spool/asterisk/outgoing";

function unique_name($path, $suffix)
{
   $file = $path."/".mt_rand().$suffix;
 return $file;
}

   $ERROR_CONVERTING_DOCUMENT = 1;
   $ERROR_CREATING_CALL_FILE = 2;
   $ERROR_NO_ERROR = 0;

$input_file_noext = unique_name("/tmp", "");
$input_file = $input_file_noext . ".pdf";
$input_file_tif = $input_file_noext . ".tif";
$input_file_doc = $input_file_noext . ".doc";

?>
<?
$error = $ERROR_NO_ERROR;  // no error at beginning

$script_local_path = $_REAL_BASE_DIR = realpath(dirname(__FILE__));


$input_file_orig_name = basename($_FILES['faxFile']['name']);
$ext = substr($input_file_orig_name, strrpos($input_file_orig_name, '.') + 1);

// check if the file is a ".doc"  file
// $ext = substr($filename, strrpos($filename, '.') + 1);
// echo "File extension : " . $ext . " <br /> \n";
if ($ext == "doc")  {
	// copy the file to /tmp/
	if(move_uploaded_file($_FILES['faxFile']['tmp_name'], $input_file_doc)) {
		// echo "DOC file uploaded";
		// convert the doc file to PDF file using wvPDF
		$input_file = $input_file_noext . ".pdf";

		// $wv_command_output = passthru("touch /root/temp.tmp | sudo /usr/bin/php -f /util/wvPDF.php");
		// need this in /etc/sudoers
		// --> asterisk ALL=(ALL) NOPASSWD: /usr/bin/wvPDF (for whatever the apache user is)

	  	$wv_command = "sudo /usr/bin/wvPDF $input_file_doc $input_file" ;
		 //echo "<br /> executing : ". $wv_command . "<br />\n";
		$wv_command_output = system($wv_command, $retval);
		 //echo $wv_command_output;

		if ($retval != 0) {
			echo "Произошла ошибка конвертирования DOC файла в PDF. Попробуйте загрузить файл еще раз. ";
			$error = $ERROR_CONVERTING_DOCUMENT;
			$doc_convert_output = $wv_command_output;
			// die();
		}else{
			// set the input file type to .pdf now as it's converted
			$input_file_type = "pdf";
		}
	} else{
		echo "Произошла ошибка при загрузке файла, пожалуйста повторите! ";
	}
} // END DOC file detected

// IF it was originally a PDF
if ($ext == "pdf")  {
	if(move_uploaded_file($_FILES['faxFile']['tmp_name'], $input_file)) {
		$input_file_type = "pdf";
	}else{
		echo "Произошла ошибка при загрузке файла, пожалуйста повторите! ";
	}
}
// we should now have a PDF file which we will convert to tif

if($error == $ERROR_NO_ERROR && $input_file_type == "pdf") {

	// convert the attached PDF to .tif using ghostsccript ...
	$gs_command = "gs -q -dNOPAUSE -dBATCH -dSAFER -sDEVICE=tiffg3 -sOutputFile=${input_file_tif} -f $input_file " ;
	$gs_command_output = system($gs_command, $retval);
	$doc_convert_output = $gs_command_output;

	if ($retval != 0) {
		echo "Произошла ошибка преобразование файла PDF в TIF. Попробуйте загрузить файл еще раз. ";
		$error = $ERROR_CONVERTING_DOCUMENT;
		// die();
	}
	else  {

		$faxHeader = $_POST["faxHeader"];
		$localID = $_POST["localID"];
		$email = $_POST["email"];
		$dest = $_POST["dest"];

 //		$callfile = "Channel: IAX2/8888/$dest$outbound_route\n" .
//        $callfile = "Channel: Local/$dest$outbound_route\n" .
//                    "CallerID: $localID\n" .
//					"MaxRetries: 1\n" .
//					"RetryTime: 60\n" .
//					"WaitTime: 60\n"  .
//					"Archive: yes\n"  .
//					"Context: $outboundfax_context \n"  .
//					"Extension: s\n" .
//					"Priority: 1\n" .
//					"Set: FAXFILE=$input_file_tif\n" .
//					"Set: FAXHEADER=$faxHeader\n" .
//					"Set: TIMESTAMP=" . date("d/m/y : H:i:s",time()) . "\n" .
//					"Set: DESTINATION=$dest\n".
//					"Set: LOCALID=$localID\n" .
//					"Set: EMAIL=$email\n";
//
		// echo $callfile;
//		$callfilename = unique_name("/tmp", ".call");
//		$f = fopen($callfilename, "w");
//  		fwrite($f, $callfile);
//		fclose($f);
//		rename($callfilename, $asterisk_spool_folder .  "/" . substr($callfilename,4));
        //file_put_contents($callfilename,  $callfile);

$output = shell_exec("sendfax -n -d $dest $input_file_tif");
//echo "<pre>$output</pre>";
//$output2 = shell_exec('faxstat -s');
//echo "<pre>$output2</pre>";
	}
}
?>
<?
if ($error == $ERROR_NO_ERROR) {
	echo " Отправка вашего факса поставлена в очередь. В ближайшее время Вы получите письмо ".
	     " по результатам отправки. Если Вы не получите письмо в течение 10 минут, ".
		 " вероятнее возникла проблема с отправкой факса, попробуйте снова. ";
}else if ($error == $ERROR_CONVERTING_DOCUMENT) {
	echo "<span class='error'>Ваш факсимильный документ не может быть преобразован. Пожалуйста, попробуйте снова.".
	     "  </span> <br /><br />".
		 " $doc_convert_output ";
}
?>
</p>
</div>
</div>
</body>
</html>
