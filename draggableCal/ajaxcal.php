<?php

include('adodb/adodb.inc.php');
include("adodb/adodb-active-record.inc.php");

$server = 'cel-bne-dev1';
$user = 'uiexperiment';
$pwd = 'p878p';
$db = 'uiexperiment';
$DB = NewADOConnection('mysql');
$DB->Connect($server, $user, $pwd, $db);


$username = $_POST['username'];
$action = $_POST['action'];




ADOdb_Active_Record::SetDatabaseAdapter($DB);
class appointments extends ADOdb_Active_Record {
    var $_table = 'appointments';
    
}




switch ($action){
	case "add":
		
		
		$name = $_POST['name'];
		$start = $_POST['start'];  // format is 09:30
		$end = $_POST['end']; // format is 10:30
		$htmlID = $_POST['htmlID']; // format timeslotsThursday23-10-200809:30
		$inputType = $_POST['inputType'];
		$inputCode = $_POST['inputCode'];
		$inputDetails = $_POST['inputDetails'];
		
		// get the date from htmlID eg. timeslotsThursday23-10-200809:30
		$dateArray = explode('-',$htmlID);
		
		// now separate the timeslotsThursday23 to be 23 in the second array
		$dayOfMonth = explode('day',$dateArray[0]);
		
		
		
		// now $dayofMonth[1] will be 23
		$dateArray[0] = $dayOfMonth[1];
		
		
		
		// now $dateArray[1] will be 10
		
		// now separate the 200809:30 in $dateArray[2] by picking the first four chars
		$dateArray[2] = substr($dateArray[2],0,4);
		
		
		
		$setDate = $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];
		
		$start 	= $setDate . ' ' . $start . ':00'; // format 2008-10-23 09:30:00
		$end 	= $setDate . ' ' . $end . ':00'; // format 2008-10-23 10:30:00
		
		// die($start . ' ::' . $end);
		
		
		 
				
		// username basically a separate calendar for each username
		
		
		// check for other appointments start date
		// check for other appointments end date
		
		/*
		 * 				db appt#1		db appt#2		exampleinput
		 * 				09-15>09-30		10-30>10-45		09-30>10-30
		 * 		09-15	X				
		 * 		09-30	X								X
		 * 		09-45									X
		 * 		10-00									X
		 * 		10-15									X
		 * 		10-30					X				X
		 * 		10-45					X
		 * 
		 */
		$checkStartDate  = new appointments();
		$checkEndDate  = new appointments();
		
		$queryCheckStartDate = 'username="' . $username . '" AND appt_start <= "' . $start . '" AND appt_end >= "' . $start . '"';
		$queryCheckEndDate = 'username="' . $username . '" AND appt_start <= "' . $end . '" AND appt_end >= "' . $end . '"';
		
		$checkStartArray = $checkStartDate->Find($queryCheckStartDate);
		$checkEndArray = $checkEndDate->Find($queryCheckEndDate);
		
		if ((count($checkStartArray) > 0) || (count($checkEndArray) > 0)){
			// die("0:" . $queryCheckStartDate. "::" . $queryCheckEndDate ."::" . count($checkStartArray) .'::'. count($checkEndArray) );
			die("0:Failure to save");
		}
		
		$addAppt = new appointments();
		
		
		// die($htmlID);
		
		
		$addAppt->username = $username;
		$addAppt->htmlid = $htmlID;
		$addAppt->appt_name = $name;
		$addAppt->appt_type = $inputType;
		$addAppt->appt_code = $inputCode;
		$addAppt->appt_details = $inputDetails;
		$addAppt->appt_start = $start;
		$addAppt->appt_end = $end;
		
		$ok = $addAppt->Save();

		if (!$ok) {
			$err = $addAppt->ErrorMsg();
			die($err);
		}
			
		die("1:SUCCESS: This was saved successfully.");
		
	break;
	
	case "retrieve":
		$mondayDate = $_POST['mondayDate'];
		
		
		$changeDate = explode('-',$mondayDate); 
		
		$mondayDate = $changeDate[2] . '-'. $changeDate[1]. '-'. $changeDate[0];
		
		// want the format 2008-10-23 00:00:00
		$start = $mondayDate. ' 00:00:00';
		
		
		
		// end date is +7 days and - 1 second
		$sundayDate = new DateTime($mondayDate);
		$sundayDate->modify("+7 day");
		
		$end = $sundayDate->format('Y-m-d') . ' 23:59:59';
		
		
		$returnAppointments  = new appointments();
		
		
		
		$queryAppointments = 'username="' . $username . '" AND appt_start >= "' . $start . '" AND appt_start <= "' . $end . '"';
		
		// die($queryAppointments);
		
		$returnAppointmentsArray = $returnAppointments->Find($queryAppointments);
		
		echo json_encode($returnAppointmentsArray);
		
		/*
		foreach ($returnAppointmentsArray as $appt){
			
			// echo json_encode($appt);
			
			
			
		}
		*/		
			
	break;
}





?>