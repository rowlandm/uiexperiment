<?php
require_once('time.lib.php');

session_start();

$sessionID = session_id();

$_SESSION[$sessionID] = $sessionID;


//temp, 2b filled from db
//$blockedArray = array("10:30","13:00")
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
                    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script src="jquery-1.2.6.js"></script>
<script src="jquery-ui-personalized-1.6rc2.min.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.contextMenu.css" />
<link rel="stylesheet" type="text/css" href="jquery-ui-themeroller.css" />


<script type="text/javascript" src="jquery.contextMenu.js"></script>
<script type="text/javascript" src="jquery.drag.resize.js"></script>
<script type="text/javascript" src="json.js"></script> 
<script type="text/javascript" src="jiffy.js"></script>  
<script>
  	
  	
	function refreshCalendar(showNumDays){
	
		Jiffy.mark("refreshCalendar");
	
		/*
		$showNumDays = 7;
		$start = "05:45";
		$end = "22:30";
		$slots = "15";
		*/
		
		var start = '05:45';
		var end = '18:00';
		var slots = '15';
		
		
		
		var dateInWeek = $('#dateChosen').val(); // default to today if none selected in date picker
		if (dateInWeek == ''){
			var d = new Date();
			var curr_date = d.getDate();
			var curr_month = d.getMonth();
			curr_month++;
			var curr_year = d.getFullYear();
			dateInWeek = curr_date + "-" + curr_month + "-" + curr_year;			
			
			
		}
		
		var postData = 'username='+ $('#userNameInput').val() + '&showNumDays=' + showNumDays   + '&dateInWeek=' + dateInWeek
        			+ '&start=' + start + '&end=' + end + '&slots=' + slots  + '&action=returnInitialHTML';  
        
        
        // initialise the calendar
		// call ajax from the database to return the records and use them to create events
  		
  		
  		$.ajax({
			type: "POST",
		   	url: "ajaxcal.php",
		   	data: postData,
		   	success: function(msg){
		   	
		   		Jiffy.measure("ajax success start", "refreshCalendar");
		   	
		   		$('#overCalendar').html(msg);
		   		
		   		
				
		        var collection;
		
				$('#refreshDiv').remove();
				// radio button to turn refresh on and off when submitting data for events
				newRadioRefreshHTML = '<div id=refreshDiv><br>Refresh after every action? <br><input type="radio"  name="refreshRadio" value="on"> ON '  
							+  '<input type="radio"  name="refreshRadio" value="off" checked> OFF <br></div>';
				$('#otherChoices').prepend(newRadioRefreshHTML);
				
				//alert($('#refreshDiv > :radio:checked').val());
		
		
				// radio button to choose week or fortnight View
				$('#refreshdaysViewDiv').remove();
				refreshdaysViewDivHTML = '<div id=refreshdaysViewDiv> ' +
										' <input id=showWeeklyView type=submit value="Show Weekly View"> <br>' + 
										' <input id=showFortnightlyView type=submit value="Show Fortnightly View"> ' +
										'</div>';
				$('#otherChoices').prepend(refreshdaysViewDivHTML);

				$('#showWeeklyView').click(function(){
					
					refreshCalendar(7);
					$('#showNumDays').val("7");
					$('#weeklyTotals').text('Weekly Totals');
					
					
				});
				$('#showFortnightlyView').click(function(){
				
					refreshCalendar(28);
					$('#showNumDays').val("28");
					
					$('#weeklyTotals').text('F/n Totals');
					
				});
				
				
				
				
				
		        // radio button to choose 15,30 or 60 minute intervals
				/* 
				$('#refreshDIV').remove();
				newRadioHTML = '<div id=refreshDiv><br>Refresh after every action? <br><input type="radio"  name="refreshRadio" value="on"> ON '  
							+  '<input type="radio"  name="refreshRadio" value="off" checked> OFF <br></div>';
				$('#overCalendar').prepend(newRadioHTML);
				
				*/ 
				
				
		        Jiffy.measure("ajax success initialise", "refreshCalendar");
		          
		        // This is to set all ul tags that are in the overCalendar DIV to be selectable - includes li tags too
		        
		        // for speed, try to find the lowest level element to reduce time finding that element
		        // #overCalendar ul means show all ul elements that are descendants of #overCalendar DIV
		        // #overCalendar > ul didn't work - this is all ul elements that are children of #overCalendar DIV
		       	$('#overCalendar li').dragResize();    	
					
				Jiffy.measure("over Calendar > li dragResize", "refreshCalendar");
		
				
		
		   		
		   		
		   	}
		});		
		
		
		
	}
  	
  	
  	
  	
  	
  	/*
  	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Start of the document ready !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Start of the document ready !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Start of the document ready !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Start of the document ready !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Start of the document ready !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Start of the document ready !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Start of the document ready !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  	!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Start of the document ready !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  	*/
  	
  	$(document).ready(function(){

		$("#hoverPopup").hide();

		$("#inlineDatePicker").datepicker( {
			dateFormat: "dd-mm-yy",
			highlightWeek: true,
			onSelect: function(date) 
				{         
					// alert("The chosen date is " + date); 
					$('#dateChosen').val(date);
					refreshCalendar($('#showNumDays').val());  
					
				},
			firstDay: 1,     
			changeFirstDay: false
			});

		$('#newUser').keyup(function(e) 
		{
			//alert(e.keyCode);
			if(e.keyCode == 13) {
				
				var newUserName = $('#newUser').val();
				
				$('#userNameInput').append('<option value="' + newUserName + '">' + newUserName + '</option>');
				
				alert(newUserName + ' added to the drop down list ');
			}
		});
		
		// get distinct list of all users in the database
		
		var postData = 'action=returnUserNames';  
        
        
        // initialise the calendar
		// call ajax from the database to return the records and use them to create events
  		$.ajax({
			type: "POST",
		   	url: "ajaxcal.php",
		   	data: postData,
		   	success: function(msg){
		   		
				//	<option value=rowland.mosbergen>rowland.mosbergen</option>
				
				// rowland.mosbergen,david, new
				var retrievedUserNames = msg.split(',');
				
				if (retrievedUserNames.length > 0 ){
				
					for (key in retrievedUserNames){
		   				$('#userNameInput').append('<option value="' + retrievedUserNames[key] + '">' + retrievedUserNames[key] + '</option>');
		   			}
		   		}
		   		else{
		   			$('#userNameInput').append('<option value="' + msg + '">' + msg + '</option>');
		   		}
		   		
		   		
		   		
		   	}
		});
		   		
		   		
		$('#userNameInput').change(function () {
		
			refreshCalendar($('#showNumDays').val());  
			
		});


		//initial refresh with 7 days
		refreshCalendar(28);
		
  	});
  	</script>


<style>
ul {
	list-style: none;
	margin: 0px;
	padding: 0px;
	width: 100px;
}

.ui-selected {
	background: #black;
	color: #FFF;
	border-bottom: 2px solid #727EA3;
}

.ui-selecting {
	background: #CFD499;
}

.savedDiv {
	background: orange;
	position: absolute;
	font-size: 9px;
	width: 100px;
}



li {
	border-bottom: 2px solid black;
	background: #CFD4E6;
	height: 10px;
	line-height:12px;
	margin-top: 0px;
	font-size: 9px;
	font-family: Arial;
	padding-top: 3px;
	
}

</style>
<style>
.proxy {
	border: 1px dashed red;
}
</style>


</head>
<body>



<table>

<tr>
	<td>
		Select users from dropdown list and then select a date.
		You can add new users in the text field, just then press enter. 
		<div id=userName>
			<select id=userNameInput>
				
					
					<!--   <option value=corey.evans>corey.evans</option> -->
			</select>
			<br>
			Add new User: <input type=text id=newUser></input>
		</div>	
		<div id=inlineDatePicker> 
			<input type=hidden id=dateChosen></input>
		</div>
		<div id=otherChoices>
			<input type=hidden id=showNumDays value=7></input>
		</div>
	</td>
	<td>
		<div id=overCalendar>

		</div>
	</td>	
</tr>
<tr>
	<td>
		<div id=overCalendar>

		</div>
	</td>
</tr>
</table>




<ul id="savedDivMenu" class="contextMenu">
	<li class="showDetails"><a href="#showDetails">Show Details</a></li>
	<li class="dailyTotals"><a href="#dailyTotals">Daily Totals</a></li>
	<li class="weeklyTotals"><a id=weeklyTotals href="#weeklyTotals">Weekly Totals</a></li>
	<li class="edit"><a href="#edit">Edit</a></li>
	<li class="refresh" id=refreshMenu><a href="#refresh">Refresh</a></li>
	<li class="emailDaily" ><a href="#emailDaily">Email Daily</a></li>
	<li class="delete"><a href="#delete">Delete</a></li>
	<li class="quit separator"><a href="#quit">Quit</a></li>
</ul>

<div id="hoverPopup" style="position: absolute; top: 55; left: 44;">
<table bgcolor="#0000FF">
	<tr>
		<td id=titlePopup color="#FFFFFF">Details</td>
	</tr>
	<tr>
		<td id=textPopup bgcolor="#8888FF">Hello I am a popup table</td>
	</tr>
</table>
</div>


</body>
</html>
