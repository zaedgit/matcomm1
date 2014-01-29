<?php
$varRootBasePath	= dirname($_SERVER['DOCUMENT_ROOT']);
//FILE INCLUDES
include_once($varRootBasePath."/conf/config.cil14");
include_once($varRootBasePath."/conf/cookieconfig.cil14");
$varAct		= isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
if($varAct == "logout"){ clearCookie(); }//if

//SESSION OR COOKIE VALUES
$sessMatriId	= $varGetCookieInfo["MATRIID"];
$sessGender		= $varGetCookieInfo["GENDER"];
$sessPublish	= $varGetCookieInfo["PUBLISH"];
$sessPaidStatus	= $varGetCookieInfo["PAIDSTATUS"];

?>
<html>
<head>
	<title><?=ucfirst($arrDomainInfo[$varDomain][2])?> Matrimony - Find <?=ucfirst($arrDomainInfo[$varDomain][2])?> Brides & Grooms</title>
	<meta name="description" content="<?=ucfirst($arrDomainInfo[$varDomain][2])?> Matrimony, the Perfect Place to search for a  <?=ucfirst($arrDomainInfo[$varDomain][2])?> partner. View 1000s of profiles from <?=ucfirst($arrDomainInfo[$varDomain][2])?> community. Register Now for Free!">
	<meta name="keywords" content="<?=$confPageValues['KEYWORDS']?>">
	<meta name="abstract" content="<?=$confPageValues['ABSTRACT']?>">
	<meta name="Author" content="<?=$confPageValues['AUTHOR']?>">
	<meta name="copyright" content="<?=$confPageValues['COPYRIGHT']?>">
	<meta name="Distribution" content="<?=$confPageValues['DISTRIBUTION']?>">
	<link rel="stylesheet" href="<?=$confValues['CSSPATH']?>/global-style.css">
	<script language="javascript" src="<?=$confValues['SERVERURL']?>/template/commonjs.php" ></script>
	<script language="javascript" src="<?=$confValues['JSPATH']?>/global.js" ></script>
</head>
<body>
<center>
<!-- main body starts here -->
<div id="maincontainer">
	<div id="container">
		<div class="main">
			<?php include_once("../../template/header.php"); ?>
			<div class="innerdiv">
				<div class="fleft"><br><br>
				<?php include_once('../../template/leftpanel.php'); ?>
				<?php
					if($varAct != "")
						{
							$varAct	= preg_replace("'\.\./'", '', $varAct);
							if(file_exists($varAct.'.php')){ include_once($varAct.'.php'); }//if
							else{ include_once('community-search.php'); }
						}else{ include_once('community-search.php'); }
				?></div>
				<br clear="all" />
			</div>
			<?php include_once('../../template/footer.php'); ?>
		</div>
	</div>
</div>
</center>
</body>
</html>