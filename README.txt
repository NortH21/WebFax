

<img src="http://joxi.ru/n2YaBgKcjVola2.png">

apt-get install libtiff-tools
apt-get install imagemagick
apt-get install ghostscript
apt-get install unoconv
apt-get install curl libcurl3 libcurl3-dev php5-curl

 CREATE TABLE `fax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `error` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1397 DEFAULT CHARSET=latin1;

CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `ip_local` varchar(255) DEFAULT NULL,
  `send_phone` varchar(255) DEFAULT NULL,
  `send_file_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=434 DEFAULT CHARSET=latin1;

cat /etc/asterisk/extensions.conf

[fax_with_threads]
exten => send,1,Dial(SIP/trunk_name/${RECEIVER})             
exten => send,n,Goto(send-${DIALSTATUS},1)
exten => send-CANCEL,1,Hangup()
exten => send-ANSWER,1,Hangup()
exten => send-NOANSWER,1,Hangup()
exten => send-BUSY,1,Hangup()
exten => send-CONGESTION,1,Wait(10)
exten => send-CONGESTION,n,GoTo(send,1)
exten => send-CHANUNAVAIL,1,Wait(10)
exten => send-CHANUNAVAIL,n,GoTo(send,1)

[faxsend-t38]
exten => faxout,1,Set(STARTTIME=${STRFTIME(${EPOCH},,%s)})
 same => n,NoOP(------------------- FAX to ${RECEIVER} ------------------)
 same => faxout,n,Wait(1)
 same => faxout,n,SendDTMF(${send_fax_disa_number},250)
 same => faxout,n,Background(/var/lib/asterisk/sounds/ru/out_fax,skip)
 same => faxout,n,Set(FAXFILE=${TIFF_2_SEND})
 same => faxout,n,Set(FAXFILESENT=Sent-to-${RECEIVER}-${STARTTIME})
 same => faxout,n,Set(FAXOPT(ecm)=yes)
 same => faxout,n,Set(FAXOPT(headerinfo)=${TAGLINE})
 same => faxout,n,Set(FAXOPT(maxrate)=14400)
 same => faxout,n,Set(FAXOPT(minrate)=2400)
 same => faxout,n,Set(FAXOPT(localstationid)=${LOCALSTATIONID})
 same => faxout,n,WaitForSilence(250,1,15)
 same => faxout,n,SendFAX(${FAXFILE},dfzs)
 same => faxout,n,NoOP(— ${FAXSTATUS} —${FAXERROR} —)
 same => faxout,n,System(/usr/bin/tiff2pdf ${FAXFILE} -o /var/spool/asterisk/fax/mnt/${FAXFILESENT}.pdf)
 same => faxout,n,System(curl http://192.168.100.223/not.php?XXXXXX,${RECEIVER},${STARTTIME},${FAXSTATUS},${FAXERROR},${FAXFILESENT})
 same => faxout,n,HangUp()

[fax-rx]
exten => _XXXXXX,1,NoOP(------------------- FAX from ${CALLERID(number)} ------------------)
 same => n,Answer(5)
 same => n,Background(/var/lib/asterisk/sounds/ru/in_fax)
 same => n,Set(FAXOPT(headerinfo)=Received-from-${CALLERID(number)}-${STRFTIME(${EPOCH},,%d%m%Y-%H%M)})
 same => n,Set(FAXOPT(localstationid)=${LOCALSTATIONID})
 same => n,Set(FAXOPT(maxrate)=14400)
 same => n,Set(FAXOPT(minrate)=2400)
 same => n,NoOp(FAXOPT(ecm) : ${FAXOPT(ecm)})
 same => n,NoOp(FAXOPT(headerinfo) : ${FAXOPT(headerinfo)})
 same => n,NoOp(FAXOPT(localstationid) : ${FAXOPT(localstationid)})
 same => n,NoOp(FAXOPT(maxrate) : ${FAXOPT(maxrate)})
 same => n,NoOp(FAXOPT(minrate) : ${FAXOPT(minrate)})
 same => n,NoOp(**** RECEIVING FAX : ${DT} ****)
 same => n,WaitForSilence(250,1,15)
 same => n,ReceiveFax(/var/spool/asterisk/fax/${FAXOPT(headerinfo)}.tif)
 same => n,System(/usr/bin/tiff2pdf /var/spool/asterisk/fax/${FAXOPT(headerinfo)}.tif -o /var/spool/asterisk/fax/${FAXOPT(headerinfo)}.pdf)
 same => n,System(/bin/cp /var/spool/asterisk/fax/${FAXOPT(headerinfo)}.pdf /var/spool/asterisk/fax/mnt/${STRFTIME(${EPOCH},,%m_%Y)} )
 same => n,System(curl http://192.168.100.223/not.php?${CALLERID(number)},XXXXXX,${STRFTIME(${EPOCH},,%s)},${FAXSTATUS},${FAXERROR},${FAXOPT(headerinfo)})
 same => n,HangUp()


