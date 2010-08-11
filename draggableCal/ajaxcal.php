<?php

if (!class_exists('DateTime')) {
	class DateTime {
		public $date;
		
		public function __construct($date) {
			$this->date = strtotime($date);
		}
		
		public function setTimeZone($timezone) {
			return;
		}
		
		private function __getDate() {
			return date(DATE_ATOM, $this->date);    
		}
		
		public function modify($multiplier) {
			$this->date = strtotime($this->__getDate() . ' ' . $multiplier);
		}
		
		public function format($format) {
			return date($format, $this->date);
		}
	}
}

include('adodb/adodb.inc.php');
include("adodb/adodb-active-record.inc.php");
require_once('time.lib.php');

$server = 'ncascr-dev';
$user = 'uiexperiment';
$pwd = 'temp211';
$db = 'uiexperiment';
$DB = NewADOConnection('mysql');
$DB->Connect($server, $user, $pwd, $db);
$hourlyRate = 100; // $ per hour

$username = $_POST['username'];
$action = $_POST['action'];




ADOdb_Active_Record::SetDatabaseAdapter($DB);
class appointments extends ADOdb_Active_Record {
    var $_table = 'appointments';
    
}

function convertDateTimes($htmlID,$start,$end){
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
		
		$datetimes['start'] = $setDate . ' ' . $start . ':00'; // format 2008-10-23 09:30:00
		$datetimes['end'] 	= $setDate . ' ' . $end . ':00'; // format 2008-10-23 10:30:00
			
		return $datetimes;
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
		
		
		$datetimes = convertDateTimes($htmlID,$start,$end);
		
		$start = $datetimes['start'];
		$end = $datetimes['end'];
		
		 
				
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
		$showNumDays = $_POST['showNumDays']; // eg 7 days in a week or 14 for a fortnight
		
		$changeDate = explode('-',$mondayDate); 
		
		$mondayDate = $changeDate[2] . '-'. $changeDate[1]. '-'. $changeDate[0];
		
		// want the format 2008-10-23 00:00:00
		$start = $mondayDate. ' 00:00:00';
		
		// end date is +7 days and - 1 second
		$sundayDate = new DateTime($mondayDate);
		$sundayDate->modify("+". ($showNumDays - 1) ." day");
		
		$end = $sundayDate->format('Y-m-d') . ' 23:59:59';
		
		
		$returnAppointments  = new appointments();
		
		
		
		$queryAppointments = 'username="' . $username . '" AND appt_start >= "' . $start . '" AND appt_start <= "' . $end . '"';
		
		// die($showNumDays . '::' . $queryAppointments);
		
		$returnAppointmentsArray = $returnAppointments->Find($queryAppointments);
		
		echo json_encode($returnAppointmentsArray);
		
		/*
		foreach ($returnAppointmentsArray as $appt){
			
			// echo json_encode($appt);
			
			
			
		}
		*/		
			
	break;
	
	case "delete":
		
		
		$htmlID = $_POST['htmlID']; // format timeslotsThursday23-10-200809:30
		
		$deleteQuery = 'DELETE FROM appointments WHERE username = "' . $username . '" AND htmlid = "' . $htmlID . '"';
		
		$ok = $DB->Execute($deleteQuery);
		if (!$ok) {
			$err = $DB->ErrorMsg();
			die($err);
		}	
		echo 'SUCCESS: deleted successfully.'; 
	break;
	
	
	case "returnUserNames":
		
		
		$returnUserNamesQuery = 'SELECT distinct(username) FROM appointments';
		
		$results = $DB->Execute($returnUserNamesQuery);
		if (!$results) {
			$err = $DB->ErrorMsg();
			die($err);
		}	
		$returnList = '';
		if ($results->RecordCount() > 0) {
			while ($row = $results->fetchRow()) {
				$returnList .= $row['username'].",";
			}
		}
		$returnList = substr($returnList,0,-1);
		
		echo $returnList;
		
	break;	
	
	case "listAllProjects":
		
		$project = $_POST['project'];
		$username = $_POST['username'];
				
		$projectQuery = ($project == "")? '': ' AND appt_name ="' .$project. '" ';
		
		$returnUserNamesQuery = 'SELECT appt_name, SUM( TIMESTAMPDIFF( 
			MINUTE , appt_start, appt_end ) /60 ) + 0.25 AS person_hours 
			FROM  `appointments` WHERE username = "'. $username.'"'.$projectQuery.
			' GROUP BY appt_name
			ORDER BY person_hours DESC';
		
		$results = $DB->Execute($returnUserNamesQuery);
		if (!$results) {
			$err = $DB->ErrorMsg();
			die($err);
		}	
		
		if ($results->RecordCount() > 0) {
			while ($row = $results->fetchRow()) {
				$returnList .= $row['appt_name']."::".round($row['person_hours']/7.25,2)."::".number_format($row['person_hours']*$hourlyRate,2).";";
			}
		}
		$returnList = substr($returnList,0,-1);
		
		echo $returnList;
		
	break;		
	
	
	case "resize":
		
		
		$htmlID = $_POST['htmlID']; // format timeslotsThursday23-10-200809:30
		$end = $_POST['end']; // format is 10:30
		$start = '06:00'; // just need to pass in a dummy, not used
		
		$datetimes = convertDateTimes($htmlID,$start,$end);
		
		$end = $datetimes['end'];
		
		// first check that the resize doesn't go over any other appointments
		$checkEndDate  = new appointments();
		
		$queryCheckEndDate = 'username="' . $username . '" AND appt_start <= "' . $end . '" AND appt_end >= "' . $end . '" AND htmlid <> "' . $htmlID .'"';
		
		$checkEndArray = $checkEndDate->Find($queryCheckEndDate);
		
		if (count($checkEndArray) > 0){
			// die("0:" . $queryCheckStartDate. "::" . $queryCheckEndDate ."::" . count($checkStartArray) .'::'. count($checkEndArray) );
			die("0:Failure to save");
		}
		
		
		$resizeQuery = 'UPDATE appointments SET appt_end = "'.$end.'" WHERE username = "' . $username . '" AND htmlid = "' . $htmlID . '"';
		
		$ok = $DB->Execute($resizeQuery);
		if (!$ok) {
			$err = $DB->ErrorMsg();
			die($err);
		}	
		echo 'SUCCESS: resized successfully.'; 
	break;	
	case "returnInitialHTML":
		
		
		/*
		$showNumDays = 7;
		$start = "05:45";
		$end = "22:30";
		$slots = "15";
		$format = "24";
		*/
		
		$dateInWeek = $_POST['dateInWeek']; // 23-12-2008
		
		$showNumDays = $_POST['showNumDays']; // eg 7 days in a week or 14 for a fortnight
		$start = $_POST['start']; // eg. 05:45
		$end = $_POST['end'];  // eg. 22:30
		$slots = $_POST['slots']; // eg 15 is every 15 minutes
		$extraDays = $_POST['extraDays'];
		$format = "24";
		
		
		// die($showNumDays . $start . $end . $slots);
		//get current local time and day of week

		
		$date = new DateTime($dateInWeek);

		
		// if today is monday then leave
		
		
		// if today is not monday then revert back to last monday
		$date->modify("+1 day");
		$date->modify("last Monday");
		
		
		// die($date->format('ld-m-Y') . '::' . $showNumDays);
		
		$timeslotArray = timeslotArray($start,$end,$slots,$format);
		
		$returnInitialHTML = '<table CELLSPACING=0>
			<tr>
				<td></td>';
				
		
		for ($count = 0;$count < $showNumDays;$count++ ){
			
			
			
			$returnInitialHTML.= '<td>'. $date->format("l") . '<br>'.$date->format("d-m-Y");
	
			$returnInitialHTML.= '<ul id="timeslots'. $date->format("ld-m-Y").'">';
	
	
			foreach($timeslotArray as $value){
				
				$returnInitialHTML.='<li>'. $value.'</li>';
				
			}
			$returnInitialHTML.=' </ul>	</td>';
			
			
		
			$date->modify("+1 day");
		} // for  loop
				
				
		$returnInitialHTML.='	</tr> </table>';
		echo $returnInitialHTML;	
		
	break;
	
	case "emailDaily":

		// check the session ID first
		$sessionIDReceived = $_POST['sessionid'];
		
		
			
		// unsure about security implications
		
		$emailFromAddress = $_POST['emailFromAddress'];
		$emailToAddress = $_POST['emailToAddress'];
		$actionDayChosen = $_POST['actionDayChosen']; // format actionDayChosen is tuesday24-10-2008
		$dataDetails = $_POST['dataDetails']; //format eg. OVH:9 hours;GCMIA:4 hours;


		$dataDetails = str_replace(";","\n\n",$dataDetails);
		
		$body = str_replace(":","\n",$dataDetails);
		

		
		
		$subject = "Timesheet - ". substr($actionDayChosen,0,-10) . '  '. substr($actionDayChosen,-10);
		
		$headers = 'From: ' . $emailFromAddress.  "\r\n";
		$headers .= 'Cc: ' . $emailFromAddress . "\r\n"; 
		
		mail($emailToAddress,$subject,$body,$headers);
		
		
		echo 'Email successfully sent';
	
		
		
	break;
	case "edit":

		
		$inputName = $_POST['inputName'];
		$htmlID = $_POST['htmlID']; // format timeslotsThursday23-10-200809:30
		$inputType = $_POST['inputType'];
		$inputCode = $_POST['inputCode'];
		$inputDetails = $_POST['inputDetails'];
		
		
		$editQuery = 'UPDATE appointments SET appt_name = "'.$inputName.'",appt_code = "'.$inputCode.'",appt_type = "'.$inputType.'",appt_details = "'.$inputDetails.'" WHERE username = "' . $username . '" AND htmlid = "' . $htmlID . '"';
		
		$ok = $DB->Execute($editQuery);
		if (!$ok) {
			$err = $DB->ErrorMsg();
			die($err);
		}	
		echo 'SUCCESS: edited successfully.'; 
	break;		
	
}





?>
