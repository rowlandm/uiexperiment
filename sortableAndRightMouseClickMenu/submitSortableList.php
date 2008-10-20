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

$postList = $_POST['list'];
$postListName = $_POST['list_name'];




include("adodb/adodb-active-record.inc.php");
ADOdb_Active_Record::SetDatabaseAdapter($DB);
class zane_whattodo extends ADOdb_Active_Record {
    var $_table = 'whattodo';
    
}




$list_array = explode(",",$postList);

$count =0;



$clear = new zane_whattodo();

$query = "DELETE FROM whattodo WHERE list_name = '" . $postListName. "' ";
$DB->execute($query);



if ($postList == ""){
	die("SUCCESS: Cleared out list ". $postListName );
}


foreach ($list_array as $list){

	$newList = new zane_whattodo();
	
	$newList->list_name = $postListName;
	$newList->list_contents = $list;
	
	$newList->Save();
	
	$count++;
}



echo "SUCCESS: This was entered into the database. $postList" ;


?>