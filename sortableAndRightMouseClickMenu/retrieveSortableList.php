<?php
// set adodb parameters for database
include('adodb/adodb.inc.php');
include("adodb/adodb-exceptions.inc.php");


$server = 'localhost';
$user = 'root';
$pwd = null;
$db = 'zane';
$DB = NewADOConnection('mysql');
$DB->Connect($server, $user, $pwd, $db);


include("adodb/adodb-active-record.inc.php");
ADOdb_Active_Record::SetDatabaseAdapter($DB);
class zane_whattodo extends ADOdb_Active_Record {
    var $_table = 'whattodo';
    
}


$loadListObject = new zane_whattodo();
$arrayList = $loadListObject->Find("id >= 0");
$returnLoadList = '';


foreach ($arrayList as $list){
	
	$returnLoadList = $returnLoadList . $list->list_name . ",";

}


//strip off last ,
$returnLoadList = substr($returnLoadList,0,-1);



echo "$returnLoadList" ;


?>