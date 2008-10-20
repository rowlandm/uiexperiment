<?php
// set adodb parameters for database
include('adodb/adodb.inc.php');
include("adodb/adodb-exceptions.inc.php");


$postListName = $_POST['list_name'];

$server = 'localhost';
$user = 'root';
$pwd = null;
$db = 'zane';
$DB = NewADOConnection('mysql');
$DB->Connect($server, $user, $pwd, $db);


if ($_POST['type'] == "uniqueList"){
	$query = "SELECT distinct(list_name) FROM whattodo ";
		 
	$content = $DB->Execute($query);
	if(!$content){
		echo ' FAILURE: could not run query' . mysql_error();
		die;
	}
	
	while (!$content->EOF) {
		$result=$content->fields;
		   
		$list .= $result['list_name'].',';
		
		$content->MoveNext();
	}
	
	
	echo $list;
	die;	
	
}

include("adodb/adodb-active-record.inc.php");
ADOdb_Active_Record::SetDatabaseAdapter($DB);
class zane_whattodo extends ADOdb_Active_Record {
    var $_table = 'whattodo';
    
}


$loadListObject = new zane_whattodo();
$query = "list_name = '".$postListName."' order by id ";

$arrayList = $loadListObject->Find($query);
$returnLoadList = '';

foreach ($arrayList as $list){
	
	$returnLoadList = $returnLoadList . $list->list_contents . ",";

}


//strip off last ,
$returnLoadList = substr($returnLoadList,0,-1);



echo "$returnLoadList" ;


?>