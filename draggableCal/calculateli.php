<?php
require_once('time.lib.php');

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
  	<script type="text/javascript" src="jquery.contextMenu.js"></script>
  	<script>
  	
  	function setSelectedElementsToSave (collection,name,start,end,count){
  		
  		var txtStart = start.replace(/:/,"-");
  	
		// now clear out the ui-selected class from the selected panels
		// and then set it to the saved class to block it out
        collection.each(function() {
                    
                    	
                    
			//set the new height based on the height of the li height:10px  and bottom of 2px
			// also set the colors and background to be different, even though it is technically irrelevant
			// as the div will be placed over it
            $(this).removeClass('ui-selected').addClass(txtStart).addClass("saved")
            .unbind("click").css("background","red").css("color","white").css("border-bottom","0px").css("height","12px");
           				
    							           					    		
		});
                    

		// this is to get the collection's parent and find it's id
		// eg. timeslotsMonday
		var parent = collection.parent().attr('id');
                    
                    
		// get the position of the first element so we can stick the div directly on top of it
		var offset = collection.eq(0).offset();
		var newHeight = (count * 15) - 2; // the 15 is for the height of each li and the -2 is to take into account the 2px bottom line
		var newDivSave = '<div id="' + parent + txtStart + '" class = "savedDiv" >';
                    
		if (count == 1){
			newDivSave = newDivSave + '<div style="background:red; height=10px;" id=handle ><img height=10px src="images/zaneinthebaththumb.png"></div>';
		}
		else {
			newDivSave = newDivSave + '<div style="background:red; height=10px;" id=handle ><img height=10px src="images/zaneinthebaththumb.png"></div>';
		} 
                    
		// the ; is important as it is used as a delimiter to calculate stuff later on
		newDivSave = newDivSave + name + ' Duration: ' + count * 0.25;
		newDivSave = newDivSave + '<span id=' + parent + txtStart + 'data class=hideData style="visible: hidden">' + name + ';' + start + ';' + end + ';' + count * 0.25 + ';' + count + ';' + parent + '</span>'; 
		newDivSave = newDivSave + '</div>';	
                    
                    
                    
		// append the new div	                    	
		$("#overCalendar").append(newDivSave);
                    
		//hide the data span
		$('#' + parent + txtStart + 'data').hide();
                    
		// set the div id to be the name of the parent and the start time
		// eg. timeslotsMonday07-15
                    
		$("#" + parent + txtStart).show().css("top",offset.top).css("left",offset.left).addClass(txtStart)	            	
		.css("height",newHeight + "px").css("border-bottom","2px solid black")
				    
		// make the overlying div draggable with a handle that helps make the move accurate
		// set the grid - 102 is the width of each column and 15 is the size of each li 
		.draggable({
			revert:	true,
			handle:	"#handle",
			grid: 	[102,15]
				    
		})
		// set to delete if double clicked
		.dblclick(function () { 
			deleteSavedDiv($(this));
		})
		// show menu when Right Mouse Clicked
		.contextMenu({
			menu: 'savedDivMenu',
			inSpeed: 150,
			outSpeed: 150
						
		},
		function(action, el, pos) {
			/* alert(
				'Action: ' + action + '\n\n' +
				'Element ID: ' + $(el).attr('id') + '\n\n' + 
				'X: ' + pos.x + '  Y: ' + pos.y + ' (relative to element)\n\n' + 
				'X: ' + pos.docX + '  Y: ' + pos.docY+ ' (relative to document)'
				); 
			*/
							
			if (action == "dailyTotals"){
							
							
				var actionDayChosen = el.attr('id').slice(9,-5);
				// var actionDayChosen = "Tuesday";
							 
				// alert(actionDayChosen);
				calculateDailyTotals(actionDayChosen,el);
														
			}		
			
			
			if (action == "showDetails"){

				// find the data hidden in the span of the element el
				var spanName = el.attr('id') + 'data';  		
				  		
				  		 
				// get an array of the data that is hidden in the span
				var spanText = $('#' + spanName).text();

						
				$("#textPopup").text(spanText);
    						
				var offset = el.offset();
							
				var topOffset = offset.top + 18; 
				var leftOffset = offset.left + 18;
							
				$("#hoverPopup").show().css("top",topOffset).css("left",leftOffset)
				.click(function(){
					$("#hoverPopup").hide();	
				});
							
				$("#textPopup").append("<br> Click to hide");																	
			}	
			
			if (action == "delete"){
				deleteSavedDiv(el);
			}	
							
		});
  	
  		
  	
  	}
  	
  	function calculateDailyTotals (dayChosen,el){
		
		// i have set hidden spans that have the day in the id
		// eg. span id =  timeSlotsMonday07-45data
		// this jquery will get all the spans that have an id that has 
		// the dayChosen (eg. tuesday) 
		var collection = jQuery("span[id*='" + dayChosen +  "']");
		
		var totals  = new Array();
		collection.each(function(){
		
			// get an array of the data that is hidden in the span
			var spanValues = $(this).text().split(";");
			
			var key = spanValues[0];
			var value = spanValues[3];
			
			// name  and time in hours
			// alert(spanValues[0] + ' ; ' + spanValues[3]);
			if (totals[key] == undefined){
				totals[key] = 0;
				//alert (key + '::' + totals[key]);
			} 	
			
			
			
			totals[key] = totals[key] + parseFloat(value);
						
			
		});
		
		
		
		$("#showTotals").remove();
		


		
		var newDivTotals = '<div id="showTotals" style="position:absolute; background:black; color:white" >';
		newDivTotals = newDivTotals + '<table bgcolor="#0000FF">';
		newDivTotals = newDivTotals + '<tr><td id=titleTotals color="#FFFFFF">Details</td></tr>';
		newDivTotals = newDivTotals + '<tr><td id=textTotals bgcolor="#8888FF">Hello I am a popup table</td></tr></table></div>';
		$("#overCalendar").append(newDivTotals);  
		
		 
		$("#titleTotals").text(" Totals : " + dayChosen); 
		var textTotals = '<table>';
		for (key in totals){
			textTotals = textTotals + '<tr><td>' +  key + ":" + totals[key] + "</td></tr>";
		}		
		
		textTotals = textTotals + "<tr><td> Please click to hide.</td></tr></table>";
		
		$("#textTotals").html(textTotals);
			
		
		var offset = el.offset();
		
		
		
		var topOffset = offset.top + 18; 
		var leftOffset = offset.left + 18;
		
		$("#showTotals").show().css("top",topOffset).css("left",leftOffset)
		.click(function(){
			$("#showTotals").hide();	
		})
		
																			
		
	}
	
	
	
  	// this function is to be able to double click on the div and then delete it and remove selections
  	// passing in the element that was clicked
  	function deleteSavedDiv (el){
  	

  		
  		
  		var delSaved = confirm("Do you wish to delete?");
  		
  		
  		if (delSaved){
  		
	  		// find the data hidden in the span of the element el
  			var spanName = el.attr('id') + 'data';  		
  		
  		 
			// get an array of the data that is hidden in the span
			var spanValues = $('#' + spanName).text().split(";");
			
			var start = spanValues[1];
			var parent = spanValues[5];

  		
  			// convert each li back to normal.
  			// each section has as a class the start date
  			// for any child of the parent
  			// eg. #timeslotsTuesday > li.07:15
  			
  			// couldn't use li.07:15 as a class, so changed the : to a -
  			// eg. li.07-15 as the class for a selection that started at 07:15
  			var txtStart = start.replace(/:/,"-");
  			
  			
  			// this is the jquery query we will be using
  			// eg. #timeslotsMonday > li.07-15
  			// the #timeslotsMonday is the object (in this case a ul) with the id of timeslotsMonday
  			// the > denotes all children of that object 
  			// the li.07-15 is all li children of timeslotsMonday that has a class of 07-15
  			// since 
  			var query = "#" + parent + " > li."+txtStart;
  			 
  			// run the actual query
  			var deleteCollection = jQuery(query);
  			
  			
  			// if it returns something
  			if(deleteCollection) {
  			
  			
  				// do the for each of the list returned
            	deleteCollection.each(function() {
                	
	                // now reset this back to what it used to be
    	            // removing classes that were used when saving the block
    	            // remove the .css by setting the attribute to blank
        	        $(this).removeClass('saved').removeClass(txtStart).css("background","").css("color","").css("border-bottom","").css("height","");
        	        
	
        	        
                });		
                
                
  			}
  			
  			
  			// delete the appropriate div
  			// note the div id is the parent + the start 
  			// eg. id=timeslotsMonday07-15 
  			$("#" + parent + txtStart).remove();
			  			
  			
  			
  		} //if delsaved = y
  	}
  	
  	
  	
  	$(document).ready(function(){


		$("#hoverPopup").hide();
		
        var collection;
          
          
          
        // This is to set all ul tags that are in the overCalendar DIV to be selectable - includes li tags too
       	$('ul').selectable({
       	
       		// this is what to do when you are selecting
       		// want to stop li's with class of saved from being selected
       		// as they have already been selected.
       		selecting: function(ev, ui) {

               if ($(ui.selecting).hasClass("saved")){
               		// can we stop it from being selected?     
               		// class is added ui-selecting when you are selecting li's          	
               		$(ui.selecting).removeClass('ui-selecting');

               		
               
               }
                

            },
       	

            // this sets a permanent saved class to each element selected
            // if the name is set 
            // it also creates a div to overlay the elements selected.
        	stop: function(e,ui){
				                               	
				// once the selection has stopped, it will chnage elements
				// from class ui-selecting to ui-selected
				// the :visible is to get all the li's that have a class
				// of ui-selected that are visible
                collection = jQuery('li.ui-selected:visible');
                    
				
				
				
				if(collection) {
                	
                	
                	
                	// can find out the start and end times
                	// and also the number of elemetns selected
                	var count = collection.size();
                	var start = collection.eq(0).text();
                	var end = collection.eq(count - 1).text();
                    
                    
                    
                    // want to check that the start and end dates
                    // have all elements selected between them
                    // eg. from 9:00 to 9:45 there are 4 elements
                    // 9:00, 9:15, 9:30 and 9:45
                    // if the count says there are only 3 elements
                    // it means thatone of those elements is
                    // already saved and the selection is invalid
                    var startDate = new Date();
                    var endDate = new Date();
                    
                    var timeStartArray = start.split(":");
                    
                    startDate.setHours(timeStartArray[0]);
                    startDate.setMinutes(timeStartArray[1]);
                    
                    var timeEndArray = end.split(":");
                    
                    endDate.setHours(timeEndArray[0]);
                    endDate.setMinutes(timeEndArray[1]);
                    
                    var diffms = endDate.getTime() - startDate.getTime();
                    
                    
                    diffhours = diffms / (1000 * 60 * 60); 
                    diffhours = diffhours + 0.25;
                    
                    expectantCount = diffhours / 0.25; 
                    
                    
                    
                    // check that there the difference between the start and end times match up to the number of counts we expect
                    if (expectantCount != count){
						alert ("Invalid selection.");
						// reset all the selections if invalid selection
    	                collection.each(function() {
        	            	//set the new height based on the height of the li height:20px  and bottom of 2px
            	        	$(this).removeClass('ui-selected');
           				
                	    });						
						
					}
					else {
					
						if (start != ""){
						
							var name = prompt("Please enter in the name of the appointment from " + start + " to " + end, "");
								
							// going to add the start to the class, and it has problems
							// seeing : when in the class, so changing it to -
														
							if (name != null && name != ""){  							
								
								setSelectedElementsToSave (collection,name,start,end,count);								
								
							                	
							} // end of if name != ""	
							else {
								// if no name then 
								// reset all the selections
    			                collection.each(function() {
        	    	    	    	
            	    	    		$(this).removeClass('ui-selected');
           					
                	    		});						
								
							}
														
								
						} // end of start !+ "" 
					
					}
		
					                                  
            	}
            }				

         });    	


		$("#savedDivMenu").hide();
		

         
         
        // this is to allow the draggable to drop into something 
		$("li").droppable({ 
		
			// only accept savedDiv class objects
		    accept: ".savedDiv", 
		    tolerance:		'pointer', // this along with the handle option in draggable is to reduce the area that the user can drop to improve accuracy 
		    drop: function(ev, ui) { 
		        

		        
		        
		        
		        
			    // find the data hidden in the span of the element el
	  			var spanName = ui.draggable.attr('id') + 'data';  		
	  		
	  		 	
	  		 	
	  		 	
				// get an array of the data that is hidden in the span
				var spanValues = $('#' + spanName).text().split(';');
		        var divLength = spanValues[4];		        
		        
		        
		        $("#overCalendar").append("<div id=divLength>" + divLength + "</div>");
		        $("#divLength").hide();
		        
		        if ($(this).hasClass('saved')){
		        	alert ('Invalid Move');
		        	
		        }
		        else {
		        	
		        	//set the class so we can find it later
		        	$(this).addClass('moved');
		        	
		        	$("#overCalendar").append($(this).parent().attr('id') + ' ' + $(this).text() + '<br>');
			        
		        
			        // get all the rest of the siblings to check if they can be moved 
			        $(this).nextAll()
			        .each(function (i){
			        	
			        	
			        	
			        	$("#overCalendar").append(i + '::' + $(this).text() + '<Br>');
			        	
			        	//set the class so we can find it later
			        	$(this).addClass('moved');
			        	
			        	if ($(this).hasClass('saved')){
			        		alert ('Invalid Move');
			        		
			        		// clear out any addClass('moved') that was set
			        		$(this).siblings().removeClass('moved');
			        		return false;
			        	}
			        	
			        	

						// retrieve the length fromt the hidden div
						// we take away 2 as the header of the draggable is not 
						// in this list and the first of the siblings starts at 0
				        var divLengthValue = $("#divLength").text();
				        divLengthValue = parseFloat(divLengthValue) - 2;
			        	if (i == divLengthValue ){
			        		
			        		// if it gets to here everything has been successful so far
			        		return false;
			        	}   
			        
			        });
		        
				}		        
		        
				// convert all addClass('moved') to be a new div
										        
		        
		        
		        
		    }  
		});         
		
		

		
  	});
  	</script>


<style>
ul { list-style: none; margin:0px; padding:0px;}
.ui-selected { background: #black; color: #FFF; border-bottom: 2px solid #727EA3;}
.ui-selecting { background: #CFD499; } 

.savedDiv {background: green; position:absolute; font-size: 9px; width: 100px; }


li {border-bottom: 2px solid black;background: #CFD4E6; height:10px; width: 100px; margin-top:0px; font-size: 9px; font-family: Arial; padding-top: 3px; }
<?php
//Detect if user's browser is IE, if so, include IE hack to improve CSS Standards compliency
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
if(strpos($user_agent, 'MSIE') !== false)
{
	echo "li {margin-top:-1px;}";
}
?>
</style>


</head>
<body>

<div id=overCalendar>
<table CELLSPACING=0>
	<tr><td>


	</td>
	<td>
	Monday
	<ul id="timeslotsMonday">
	<?php
	
	$start = "05:45";
	$end = "17:30";
	$slots = "15";
	$format = "24";
	
	$nBlocked=0;
	$timeslotArray = timeslotArray($start,$end,$slots,$format);
	foreach($timeslotArray as $value){
		if(in_array($value, $blockedArray)){
		$nBlocked++;
	?>
		
		<li class=saved style='background:black;color:white;'><?php echo $value;?></li>
		
	<?php
		}else{
	?>
			<li><?php echo $value;?></li>
	<?php
		}
	}
	?>
	</ul>
	</td>
	<td>
	Tuesday
	<ul id="timeslotsTuesday">
	<?php
	$nBlocked=0;
	$timeslotArray = timeslotArray($start,$end,$slots,$format);
	foreach($timeslotArray as $value){
		if(in_array($value, $blockedArray)){
		$nBlocked++;
	?>
		
		<li class=saved style='background:black;color:white;'><?php echo $value;?></li>
		
	<?php
		}else{
	?>
			<li><?php echo $value;?></li>
	<?php
		}
	}
	?>
	</ul>	
	</td>
	<td>
	Wednesday
	<ul id="timeslotsWednesday">
	<?php
	$nBlocked=0;
	$timeslotArray = timeslotArray($start,$end,$slots,$format);
	foreach($timeslotArray as $value){
		if(in_array($value, $blockedArray)){
		$nBlocked++;
	?>
		
		<li class=saved style='background:black;color:white;'><?php echo $value;?></li>
		
	<?php
		}else{
	?>
			<li><?php echo $value;?></li>
	<?php
		}
	}
	?>
	</ul>	
	</td>
	<td>
	Thursday
	<ul id="timeslotsThursday">
	<?php
	$nBlocked=0;
	$timeslotArray = timeslotArray($start,$end,$slots,$format);
	foreach($timeslotArray as $value){
		if(in_array($value, $blockedArray)){
		$nBlocked++;
	?>
		
		<li class=saved style='background:black;color:white;'><?php echo $value;?></li>
		
	<?php
		}else{
	?>
			<li><?php echo $value;?></li>
	<?php
		}
	}
	?>
	</ul>	
	</td>
	<td>
	Friday
	<ul id="timeslotsFriday">
	<?php
	$nBlocked=0;
	$timeslotArray = timeslotArray($start,$end,$slots,$format);
	foreach($timeslotArray as $value){
		if(in_array($value, $blockedArray)){
		$nBlocked++;
	?>
		
		<li class=saved style='background:black;color:white;'><?php echo $value;?></li>
		
	<?php
		}else{
	?>
			<li><?php echo $value;?></li>
	<?php
		}
	}
	?>
	</ul>	
	</td>
	<td>
	Saturday
	<ul id="timeslotsSaturday">
	<?php
	$nBlocked=0;
	$timeslotArray = timeslotArray($start,$end,$slots,$format);
	foreach($timeslotArray as $value){
		if(in_array($value, $blockedArray)){
		$nBlocked++;
	?>
		
		<li class=saved style='background:black;color:white;'><?php echo $value;?></li>
		
	<?php
		}else{
	?>
			<li><?php echo $value;?></li>
	<?php
		}
	}
	?>
	</ul>	
	</td>				
	</tr>
</table>

</div>




<ul id="savedDivMenu" class="contextMenu">
    <li class="showDetails">
        <a href="#showDetails">Show Details</a>
    </li>
    <li class="dailyTotals">
        <a href="#dailyTotals">Daily Totals</a>
    </li>    
    <li class="delete">
        <a href="#delete">Delete</a>
    </li>
    <li class="quit separator">
        <a href="#quit">Quit</a>
    </li>
</ul>

<div id="hoverPopup" style=" position:absolute; top:55; left:44;">
<table bgcolor="#0000FF">
<tr><td id=titlePopup color="#FFFFFF">Details</td></tr>
<tr><td id=textPopup bgcolor="#8888FF">Hello I am a popup table</td></tr></table></div>


</body>
</html>