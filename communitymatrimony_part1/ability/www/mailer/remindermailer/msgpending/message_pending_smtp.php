<?php

//FILE INCLUDES
//include_once('/home/mailmanager/cbsmailer/ip.inc');
include_once('/home/product/community/ability/conf/ip.inc');
include_once('/home/product/community/ability/conf/dbinfo.inc');
include_once('/home/product/community/ability/conf/config.inc');
include_once('/home/product/community/ability/conf/domainlist.inc');
include_once('/home/product/community/ability/www/mailer/classified/smtp.php');
include_once('/home/product/community/ability/lib/clsMailManager.php');
include_once('/home/product/community/ability/lib/clsMailerMatchWatch.php');

//OBJECT DECLARATION
$objMailManager			= new MailManager;
$objMailerMatchWatch	= new MailerMatchWatch;
$smtp					= new smtp_class;

//VARIABLE DECLARATION
$smtp->host_name="172.28.0.236";    /* Change this variable to the address of the SMTP server to relay, like "smtp.myisp.com" */
$smtp->host_port=587;               /* Change this variable to the port of the SMTP server to use, like 465 */
$smtp->ssl=0;                       /* Change this variable if the SMTP server requires an secure connection using SSL */
$smtp->localhost="localhost";       /* Your computer address */
$smtp->timeout=10;                  /* Set to the number of seconds wait for a successful connection to the SMTP server */
$smtp->data_timeout=0;              /* Set to the number seconds wait for sending or retrieving data from the SMTP server. Set to 0 to use the same defined in the timeout variable */

$varCurrentDateTime	= date('Y-m-d H:i:s');
$varCurrentDate	= date('Ymd');
$varSubject			= "Your message box has pending messages. Login & reply now!";
$varTrackDtls		= "trackid=00410000007&type=internal&formfeed=y";

$varContentFile	= 'Message_Pending';
$varMailerType	= 'Msg_Pending';
$varFileName	= '';
$argPurpose		= 'inbox_msg';
if($argv[1]!='') {
	$varFileName	= '/home/product/community/ability/remindermailer/msgpending/'.$argv[1];
}else{
	echo "please give input text file name";
}

//CONNECT MYSQL
$objMailerMatchWatch->dbConnect('S',$varDbInfo['DATABASE']);

// Counter Log file
$varSendFilename= '/home/product/community/ability/remindermailer/msgpending/'.date('dmY').'_'.$varContentFile.'_'.date('H:i:s').'_MailSent.txt';

$varFileName		= file($varFileName);
$i = 1;
foreach($varFileName as $varFileInfo) {
	$varMatriId			= '';
	$varToEmailAddress	= '';
	$varLastLogin		= '';
	$varPendingCnt		= '';
	$varOppMatriId		= '';
	$varMessage			= '';
	$varFrom			= '';
	$emailfrom			= '';
	$replyto			= '';
	$varTo				= '';
	$varDomaiId			= '';
	$varDomainDetails	= array();
	$varDomainFolder	= array();

	$varFileArray		= split('~', $varFileInfo);
	$varSerialNo		= trim($varFileArray[0]);
	$varMatriId			= trim($varFileArray[1]);
	$varToEmailAddress	= trim($varFileArray[2]);
	$varLastLogin		= trim($varFileArray[3]);
	$varPendingCnt		= trim($varFileArray[4]);
	$varOppMatriId		= trim($varFileArray[5]);
	$varDomaiId			= trim($varFileArray[6]);
	$varName			= trim($varFileArray[7]);
	$varPaidstatus		= trim($varFileArray[8]);
	$varMsgId			= trim($varFileArray[9]);
	$varTo				= $varMatriId;
	
	//GET DOMAIN DETAILS
	$varDomainDetails	= $objMailManager->getDomainDetails($varMatriId);
	$varDomainFolder	= $objMailManager->getFolderName($varMatriId);

	$varDomaiPrefix		= trim($arrMatriIdPre[$varDomaiId]);
	$varDomainName		= trim($arrPrefixDomainList[$varDomaiPrefix]);
	$varShortName		= ucwords(str_replace("matrimony.com","",$varDomainName));

	if ($varDomainName !='') {
		include('message_pending_tpl.php');
		$varMessage		= stripslashes($varTemplate);

		$varLogoPath	= $varDomainDetails['IMGURL'].'/logo/'.$arrFolderNames[$varDomaiPrefix];
		$varImgPath		= $varDomainDetails['IMGURL'];
		$varServerUrl	= $varDomainDetails['SERVERURL'];
		$varMailerImgPath= $varDomainDetails['MAILERIMGURL'];
		$varImgServerPath= $varDomainDetails['IMGSERVERURL'];
		$varProductName	= $varShortName.'Matrimony';
		$varReplyLink	= $varServerUrl.'/login/index.php?'.$varTrackDtls.'&redirect='.$varServerUrl.'/mymessages/?';
		$unsubscibeLink	= $varServerUrl."/login/index.php?redirect=".$varServerUrl."/profiledetail/index.php?act=mailsetting";
		$varPendingDtl	= '';
		
		//get community wise details
		$objMailerMatchWatch->getCommunityWiseDtls($varDomaiPrefix);
		$varProfileBasicResultSet = $objMailerMatchWatch->selectDetails(array(0=>"'".$varOppMatriId."'"));
		$varProfileBasicView= $objMailerMatchWatch->getMatchWatchRegularDetails($argPurpose,$varProfileBasicResultSet,$varPaidstatus,$varMailerType,$varTrackDtls,$varMsgId);

		$varMessage		= str_replace('<--LOGO-->',$varLogoPath,$varMessage);
		$varMessage		= str_replace('<--SESSMATRIID-->',$varMatriId,$varMessage);
		$varMessage		= str_replace('<--SESSNAME-->',$varName,$varMessage);
		$varMessage		= str_replace('<--TRACK_DETAIL-->',$varTrackDtls,$varMessage);
		$varMessage		= str_replace('<--PENDING_CNT-->',$varPendingCnt,$varMessage);
		$varMessage		= str_replace('<--REPLY-->',$varReplyLink,$varMessage);
		$varMessage		= str_replace('<--BASIC-PROFILE-->',$varProfileBasicView,$varMessage);
		$varMessage		= str_replace('<--MAILERIMGSPATH-->',$varMailerImgPath,$varMessage);
		$varMessage		= str_replace('<--IMGSPATH-->',$varImgPath,$varMessage);
		$varMessage		= str_replace('<--PRODUCT_NAME-->',$varProductName,$varMessage);
		$varMessage		= str_replace('<--UNSUBSCRIBE_LINK-->',$unsubscibeLink,$varMessage);
		$varMessage		= str_replace('<--SERVERURL-->',$varServerUrl,$varMessage);

		$varFrom		= $varShortName.'Matrimony.Com';
		$emailfrom		= strtolower('info@'.$varShortName.'matrimony.com');
		$replyto		= strtolower('noreply@'.$varShortName.'matrimony.com');

		if($smtp->SendMessage("noreply@bounces.communitymatrimony.com", array($varToEmailAddress), array("From: $emailfrom", "Reply-to: $replyto", "Sender: $emailfrom", "MIME-Version: 1.0", "Content-type: text/html", "X-Priority: 1", "X-Mailer: PHP mailer", "To: ".$varToEmailAddress, "Subject: $varSubject", "Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z")), stripslashes($varMessage))) {
			$varTrackDetails	= $i.'~'.$varSerialNo.'~'.$varToEmailAddress;
			$varSendHandler		= fopen($varSendFilename,"w+");
			fwrite($varSendHandler,$varTrackDetails);
			fclose($varSendHandler);
			$i++;
		}
	}
}


//CONNECT MYSQL
$objMailerMatchWatch->dbClose();

//CONNECT MYSQL
$mysql_connection	= mysql_connect($varDbIP['M'],$varDBUserName,$varDBPassword) or die('connection_error');
$mysql_select_db	= mysql_select_db("communitymatrimony") or die('db_selection_error');

//Update into cbsmailer report table
$varInsertQuery	= "INSERT INTO cbsmailer_report (MailerType,Count,SentOn) VALUES ('".$varContentFile."',".$i.",'".$varCurrentDateTime."')";
$varInsertId	= mysql_query($varInsertQuery) or die('insert_error');


$varFrom			= 'CommunityMatrimony';
$varFromEmail		= 'CommunityMatrimony.Com';
$emailfrom			= 'info@communitymatrimony.com';
$replyto			= 'noreply@communitymatrimony.com';
$varSubject			= $varContentFile.' Completed';
$varMessage			= 'Total Count ='.$i;
$varToEmailAddress	= 'ashokkumar@bharatmatrimony.com,dhanapal@bharatmatrimony.com, jeyakumar@bharatmatrimony.com';
//$varToEmailAddress	= 'greennjk@gmail.com';
if($smtp->SendMessage("noreply@bounces.communitymatrimony.com", array($varToEmailAddress), array("From: $emailfrom", "Reply-to: $replyto", "Sender: $emailfrom", "MIME-Version: 1.0", "Content-type: text/html", "X-Priority: 1", "X-Mailer: PHP mailer", "To: ".$varToEmailAddress, "Subject: $varSubject", "Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z")), stripslashes($varMessage))) {
echo "Message Pending mails sent successfully";}

mysql_close($mysql_connection) or die('error');
?>