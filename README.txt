apt-get install libtiff-tools
apt-get install imagemagick
apt-get install ghostscript
apt-get install unoconv

XXXXXX - telephone number.

/etc/asterisk/extensions.conf

[fax_with_threads]
exten => send,1,Dial(SIP/trunk_name/${RECEIVER})             ; trunk_name
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
exten => faxout,n,NoOP(------------------- FAX to ${RECEIVER} ------------------)
exten => faxout,n,Wait(1)
exten => faxout,n,SendDTMF(${send_fax_disa_number},250)
exten => faxout,n,Background(/var/lib/asterisk/sounds/ru/out_fax,skip)
exten => faxout,n,Set(FAXFILE=${TIFF_2_SEND})
exten => faxout,n,Set(FAXFILESENT=Sent-to-${RECEIVER}-${STARTTIME})
exten => faxout,n,Set(FAXOPT(ecm)=yes)
exten => faxout,n,Set(FAXOPT(headerinfo)=${TAGLINE})
exten => faxout,n,Set(FAXOPT(maxrate)=14400)
exten => faxout,n,Set(FAXOPT(minrate)=2400)
exten => faxout,n,Set(FAXOPT(localstationid)=${LOCALSTATIONID})
exten => faxout,n,WaitForSilence(250,1,15)
exten => faxout,n,SendFAX(${FAXFILE},dfzs)
exten => faxout,n,NoOP(— ${FAXSTATUS} —${FAXERROR} —)
exten => faxout,n,System(/usr/bin/tiff2pdf ${FAXFILE} -o /var/spool/asterisk/fax/mnt/${FAXFILESENT}.pdf)
exten => faxout,n,System(curl http://192.168.100.223/not.php?XXXXXX,${RECEIVER},${STARTTIME},${FAXSTATUS},${FAXERROR},${FAXFILESENT})
exten => faxout,n,HangUp()

[fax-rx]
exten => _XXXXXX,1,NoOP(------------------- FAX from ${CALLERID(number)} ------------------)
 same => n,Answer(5)
 same => n,Background(/var/lib/asterisk/sounds/ru/in_fax)
; same => n,Set(DT=${TIMESTAMP}-${CALLERIDNUM}-${UNIQUEID})
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


