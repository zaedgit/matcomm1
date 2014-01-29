<?php
//BASE PATH
$varRootBasePath	= dirname($_SERVER['DOCUMENT_ROOT']);

if($varRootBasePath == ''){ $varRootBasePath = '/home/product/community'; }

//INCLUDED FILES
include_once($varRootBasePath.'/conf/ip.cil14'); 

Class DB
{
	//PRIVATE VARIABLES
	public $clsDBLink;

	//PUBLIC VARIABLES
	public $clsError		= '';
	public $clsErrorCode	= '';

	//CREATE MYSQL CONNECTION
	function dbConnect($argHostType, $argDBName) {
		global $varDbIP, $varDBUserName, $varDBPassword;
		$funHostIP	= $varDbIP[$argHostType];
		$DBLink = mysql_connect($funHostIP, $varDBUserName, $varDBPassword);

		if(!is_resource($DBLink)) {
			$this->clsErrorCode		= "DB_CONN_ERR";
			$this->ErrorLog(mysql_error());
		} else { $this->clsDBLink = $DBLink; }

		if($argDBName!='') {
			$clsDBSelect = mysql_select_db($argDBName,$DBLink);
			if (!$clsDBSelect) {
				$this->clsErrorCode		= "DB_SEL_ERR";
				$this->ErrorLog(mysql_error());
			}
		} else {
			$clsDBSelect = mysql_select_db('communitymatrimony',$DBLink);
		}

	}//dbConnect
	function numOfRecords($argTblName, $argPrimary='MatriId', $argCondition)
	{
		$funQuery	= 'SELECT COUNT('.$argPrimary.') AS CNT FROM '.$argTblName.' '.$argCondition;
		//print "<br>".$funQuery;
		$funExecute = mysql_query($funQuery,$this->clsDBLink);
		if (!$funExecute)
		{
			$this->clsErrorCode		= "CNT_ERR";
			$this->ErrorLog(mysql_error(), $funQuery);
		} else {
			$funResults = mysql_fetch_assoc($funExecute);
			return $funResults['CNT'];
		}//else

	}//numOfRecords

	function select($argTblName, $argFields, $argCondition, $argResultSetArr) {
			$funArrResults = array();

			$funQuery  = "SELECT ".join(',', $argFields). " FROM ".$argTblName.' '.$argCondition;

			//$this->ErrorLog(mysql_error(), $funQuery);
			//print "<br>".$funQuery;
			if (!$funExecute = mysql_query($funQuery,$this->clsDBLink))
			{
				$this->clsErrorCode		= "SELECT_ERR";
				$this->ErrorLog(mysql_error(), $funQuery);
			} else {
					if($argResultSetArr == 1){
					$j=0;
					$funFieldsCnt = count($argFields);
					while($funResults = mysql_fetch_assoc($funExecute))
					{
						for($i=0; $i<$funFieldsCnt; $i++)
						{ $funArrResults[$j][$argFields[$i]] = $funResults[$argFields[$i]]; }//for
						$j++;
					}//while
					return $funArrResults;
					}//if
					else
					return $funExecute;
			}//else
	}//select
	function doEscapeString($argValue,$argObj) {

	$funRegExp	= '/^[+-]?[0-9\.]*$/';
	if ($argObj) { 
		if((is_numeric($argValue)) && (preg_match($funRegExp, $argValue))) {
			return mysql_real_escape_string($argValue,$argObj->clsDBLink); 
		} else { return "'".mysql_real_escape_string($argValue,$argObj->clsDBLink)."'"; }

	} else { return mysql_escape_string($argValue); }

	}//escapeString

	function selecttest($argTblName, $argFields, $argCondition, $argResultSetArr) {
			$funArrResults = array();

			echo $funQuery  = "SELECT ".join(',', $argFields). " FROM ".$argTblName.' '.$argCondition;

			//$this->ErrorLog(mysql_error(), $funQuery);
			//print "<br>".$funQuery;exit;
			/*if (!$funExecute = mysql_query($funQuery,$this->clsDBLink))
			{
				$this->clsErrorCode		= "SELECT_ERR";
				$this->ErrorLog(mysql_error(), $funQuery);
			} else {
					if($argResultSetArr == 1){
					$j=0;
					$funFieldsCnt = count($argFields);
					while($funResults = mysql_fetch_assoc($funExecute))
					{
						for($i=0; $i<$funFieldsCnt; $i++)
						{ $funArrResults[$j][$argFields[$i]] = $funResults[$argFields[$i]]; }//for
						$j++;
					}//while
					return $funArrResults;
					}//if
					else
					return $funExecute;
			}//else
			*/
	}//select

	function selectAll($argTblName, $argCondition, $argResultSetArr) {
			$funArrResults = array();

			$funQuery  = "SELECT * FROM ".$argTblName.' '.$argCondition;
			if (!$funExecute = mysql_query($funQuery,$this->clsDBLink))
			{  
				$this->clsErrorCode		= "SELECT_ERR";
				$this->ErrorLog(mysql_error(), $funQuery);
			} else {
					if($argResultSetArr == 1){
					$j=0;
					$funResults = mysql_fetch_assoc($funExecute);
					return $funResults;
					}//if
					else
					return $funExecute;
			}//else
	}//select
	function selectAlltest($argTblName, $argCondition, $argResultSetArr) {
			$funArrResults = array();

			$funQuery  = "SELECT * FROM ".$argTblName.' '.$argCondition;
			print $funQuery;
	}//select

	function insert($argTblName, $argFields, $argFieldsValue)
	{
		if(count($argFields) == count($argFieldsValue))
		{
			$funQuery  = 'INSERT INTO '.$argTblName.'('.join(',', $argFields).') VALUES ('.join(',', $argFieldsValue).')';
			//print "<br>".$funQuery;
			if (!mysql_query($funQuery,$this->clsDBLink))
			{
				$this->clsErrorCode		= "INSERT_ERR";
				$this->ErrorLog(mysql_error(), $funQuery);
			} else { return mysql_insert_id($this->clsDBLink); }//else
		}//if
		else{ return 'no';}
	}//insert
	function inserttest($argTblName, $argFields, $argFieldsValue)
	{
		if(count($argFields) == count($argFieldsValue))
		{
			$funQuery  = 'INSERT INTO '.$argTblName.'('.join(',', $argFields).') VALUES ('.join(',', $argFieldsValue).')';
			print "<br>".$funQuery;
			
		}//if
		
	}//insert

	function insertIgnore($argTblName, $argFields, $argFieldsValue)
	{
		if(count($argFields) == count($argFieldsValue))
		{
			$funQuery  = 'INSERT IGNORE INTO '.$argTblName.'('.join(',', $argFields).') VALUES ('.join(',', $argFieldsValue).')';
			//print "<br>".$funQuery;
			if (!mysql_query($funQuery,$this->clsDBLink))
			{
				$this->clsErrorCode		= "INSERT_ERR";
				$this->ErrorLog(mysql_error(), $funQuery);
			} else { return mysql_insert_id($this->clsDBLink); }//else
		}//if
		else{ return 'no';}
	}//insert ignore


	function insertOnDuplicate($argTblName, $argFields, $argFieldsValue, $argPrimaryKeyVal)
	{
		$argPrimaryKeyVal = trim($argPrimaryKeyVal);

		if(count($argFields) == count($argFieldsValue))
		{
			$funQuery	= '';
			$funQuery  .= 'INSERT INTO '.$argTblName.' ('.join(',', $argFields).') VALUES ('.join(',', $argFieldsValue).') ON DUPLICATE KEY UPDATE ';
			unset($argFields[$argPrimaryKeyVal]);
			foreach($argFields as $funKey=>$funVal){ $funQuery  .= $funVal.'='.$argFieldsValue[$funKey].', ';}

			$funQuery	= rtrim($funQuery,', ');
			//print "<br>".$funQuery;
			if (!mysql_query($funQuery,$this->clsDBLink)) {
				$this->clsErrorCode		= "INSERT_ON_DUP_ERR";
				$this->ErrorLog(mysql_error(), $funQuery);
			} else { return true; }//else
		}//if
		else{ return 'FILEDS AND VALUES COUNT DIFFER';}
	}//tbl_insertOnDuplicate

    function insertOnDuplicatetest($argTblName, $argFields, $argFieldsValue, $argPrimaryKeyVal)
	{
		$argPrimaryKeyVal = trim($argPrimaryKeyVal);

		if(count($argFields) == count($argFieldsValue))
		{
			$funQuery	= '';
			$funQuery  .= 'INSERT INTO '.$argTblName.' ('.join(',', $argFields).') VALUES ('.join(',', $argFieldsValue).') ON DUPLICATE KEY UPDATE ';
			unset($argFields[$argPrimaryKeyVal]);
			foreach($argFields as $funKey=>$funVal){ $funQuery  .= $funVal.'='.$argFieldsValue[$funKey].', ';}

			$funQuery	= rtrim($funQuery,', ');
			print "<br>".$funQuery;
			
		}else{
			echo "wrong way";
		}
		
	}//tbl_insertOnDuplicate

	function update($argTblName, $argFields, $argFieldsValue, $argCondition)
	{
		$funQuery	= "";
		if(trim($argCondition) != "" && count($argFields)==count($argFieldsValue)){
			$funQuery	= "UPDATE ".$argTblName." SET ";

			foreach($argFields as $funKey=>$funVal) { $funQuery  .= $funVal.'='.$argFieldsValue[$funKey].', '; }

			$funQuery  = rtrim($funQuery,', ')." WHERE ".$argCondition; 
			//print "<br>UPDATE :".$funQuery;
			if (!mysql_query($funQuery,$this->clsDBLink)) {
				$this->clsErrorCode		= "UPDATE_ERR";
				$this->ErrorLog(mysql_error(), $funQuery);
			} else { return mysql_affected_rows($this->clsDBLink); }//else
		}
		else{ return 'WHERE NOT SPECIFIED or FILEDS AND VALUES COUNT DIFFER';}
	}//update
	function updatetest($argTblName, $argFields, $argFieldsValue, $argCondition)
	{
		$funQuery	= "";
		if(trim($argCondition) != "" && count($argFields)==count($argFieldsValue)){
			$funQuery	= "UPDATE ".$argTblName." SET ";

			foreach($argFields as $funKey=>$funVal) { $funQuery  .= $funVal.'='.$argFieldsValue[$funKey].', '; }

			echo $funQuery  = rtrim($funQuery,', ')." WHERE ".$argCondition; exit;
			//print "<br>UPDATE :".$funQuery;
			/*if (!mysql_query($funQuery,$this->clsDBLink)) {
				$this->clsErrorCode		= "UPDATE_ERR";
				$this->ErrorLog(mysql_error(), $funQuery);
			} else { return mysql_affected_rows($this->clsDBLink); }//else*/
		}
		else{ return 'WHERE NOT SPECIFIED or FILEDS AND VALUES COUNT DIFFER';}
	}//update


	// DELETE QUERY EXECUTE METHOD //
	function delete($argTblName, $argCondition)
	{
		$funQuery	= "";
		if (trim($argCondition) != "") {
			$funQuery  = "DELETE FROM ".$argTblName." WHERE ".$argCondition;
			//print "<br>DELETE :".$funQuery;
			if (!mysql_query($funQuery,$this->clsDBLink)) {
				$this->clsErrorCode		= "DEL_ERR";
				$this->ErrorLog(mysql_error(), $funQuery);
			}
			else{ return mysql_affected_rows($this->clsDBLink); }//else
		}
		else{ return 'WHERE NOT SPECIFIED';}
	}
	function deletetest($argTblName, $argCondition)
	{
		$funQuery	= "";
		if (trim($argCondition) != "") {
			$funQuery  = "DELETE FROM ".$argTblName." WHERE ".$argCondition;
			print "<br>DELETE :".$funQuery;
		}
	
	}

	function getEmail($argMatid) {
		global $varTable;
		$funMemInfoCond	= " WHERE MatriId=".$this->doEscapeString($argMatid,$this);
		$funMemInfoFields	= array('Email');
		$funMemEmailInfo	= $this->select($varTable['MEMBERLOGININFO'],$funMemInfoFields,$funMemInfoCond,1);
		return $funMemEmailInfo[0]['Email'];
	}
	//for messenger
	function getUsername($argMatid) {
		global $varTable;
		$funMemInfoCond	= " WHERE MatriId=".$this->doEscapeString($argMatid,$this);
		$funMemInfoFields	= array('User_Name');
		$funMemEmailInfo	= $this->select($varTable['MEMBERINFO'],$funMemInfoFields,$funMemInfoCond,1);
		return $funMemEmailInfo[0]['User_Name'];
	}

	// WRITE ERROR LOG TO FILE //
	function ErrorLog($argError, $argQuery='') {
		$funError		= debug_backtrace();
		$funFileContent	 = "\n".date('h:i:s').'#~#'.$funError[0]["file"].':~:'.$funError[1]["file"].':~:'.$funError[1]["function"].'#~#'.$argQuery.'#~#'.$argError;
		$funFileName = "/var/log/dberrorlog/".date('d-m-Y')."_".$_SERVER['SERVER_ADDR']."db_community_rm_error_log.txt";

		$funFileOpen = fopen($funFileName,"a");
		@fwrite($funFileOpen, $funFileContent);
		fclose($funFileOpen);
		return $this->clsErrorCode;
	}//ErrorLog

	function ExecuteQry($query){
          //print "<br>".$query; exit;
		  if (!mysql_query($query,$this->clsDBLink))
			{
				$this->clsErrorCode		= "INSERT_ERR";
				$this->ErrorLog(mysql_error(), $funQuery);
			} else { return mysql_affected_rows($this->clsDBLink); }//else
	}
	function ExecuteQrytest($query){
          print "<br>".$query; 
	}

	//MYSQL DB CLOSE METHOD
	function dbClose() { @mysql_close($this->clsDBLink); }
}
?>