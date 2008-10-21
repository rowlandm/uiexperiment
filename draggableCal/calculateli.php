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
  	<script>
  	
  	function doubleClickSaved (name,count,start,end,parent){
  	
  		var delSaved = confirm("Do you wish to delete?");
  		
  		if (delSaved){
  			// convert each li back to normal.
  			// each section has as a class the start date
  			// for any child of the parent
  			// eg. #timeslotsTuesday > li.07:15
  			
  			var txtStart = start.replace(/:/,"-");
  			
  			var query = "#" + parent + " > li."+txtStart;
  			 
  			
  			var deleteCollection = jQuery(query);
  			
  			
  			
  			if(deleteCollection) {
            	deleteCollection.each(function() {
                	
	                // now reset this back to what it used to be
    	            
        	        $(this).removeClass('saved').removeClass(txtStart).css("background","").css("color","").css("border-bottom","").css("height","").unbind("click");
        	        
	
        	        
                });		
                
                
  			}
  			
  			
  			// delete the appropriate div
  			$("#" + parent + txtStart).remove();
			  			
  			
  			
  		} //if delsaved = y
  	}
  	
  	
  	
  	$(document).ready(function(){


        var collection;
          
       	$('ul').selectable({
       	
       		selecting: function(ev, ui) {

               if ($(ui.selecting).hasClass("saved")){
               		// can we stop it from being selected?               	
               		$(ui.selecting).removeClass('ui-selecting');

               		//alert( $(ui.selecting).attr('class'));
               
               }
                

            },
       	

                   	
        	stop: function(e,ui){
				                               	
                collection = jQuery('li.ui-selected:visible');
                    
				
				
				
				if(collection) {
                	
                	var count = collection.size();
                	var start = collection.eq(0).text();
                	var end = collection.eq(count - 1).text();
                    
                    
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
						alert ("invalid selection");
						// reset all the selections
    	                collection.each(function() {
        	            	//set the new height based on the height of the li height:20px  and bottom of 2px
            	        	$(this).removeClass('ui-selected');
           				
                	    });						
						
					}
					else {
					
						if (start != ""){
						
							var name = prompt("Please enter in the name of the appointment from " + start + " to " + end, "");
								
							var txtStart = start.replace(/:/,"-");
														
							if (name != null && name != ""){  							
								// now clear out the ui-selected class from the selected panels
								// and then set it to the saved class to block it out
		    	                collection.each(function() {
		                    	
		                    		
		                    	
									//set the new height based on the height of the li height:20px  and bottom of 2px
		            	        	$(this).removeClass('ui-selected').addClass(txtStart).addClass("saved");
		           					$(this).unbind("click").css("background","red").css("color","white").css("border-bottom","0px").css("height","12px");
		           					    		
		                	    });
		                    

		                    	var parent = collection.parent().attr('id');
		                    	
		                    	var offset = collection.eq(0).offset();
		                    	var newHeight = (count * 15) - 2;
		                    	var newDivSave = '<div id="' + parent + txtStart + '" class = "savedDiv" >';
		                    	newDivSave = newDivSave + name + ';' + start + ';' + end + ';' + count * 0.25 + '</div>';	
		                    	
		                    	
		                    	
		                    		                    	
			                    $("#overCalendar").append(newDivSave);
			                    
			                    
			                    
			                    $("#" + parent + txtStart).show().css("top",offset.top).css("left",offset.left).addClass(txtStart);	            	
							    $("#" + parent + txtStart).css("height",newHeight + "px").css("border-bottom","2px solid black").draggable();
							    
							    
							    $("#" + parent + txtStart).dblclick(function () { 
	      	 							doubleClickSaved(name,count,start,end,parent)
	      	 							});
							                	
							} // end of if name != ""	
							else {
								// reset all the selections
    			                collection.each(function() {
        	    	    	    	//set the new height based on the height of the li height:20px  and bottom of 2px
            	    	    		$(this).removeClass('ui-selected');
           					
                	    		});						
								
							}
														
								
						} // end of start !+ "" 
					
					}
		
					                                  
            	}
            }				

         });    	
         
		$("li").droppable({ 
		    accept: ".savedDiv", 
		    drop: function(ev, ui) { 
		        $(this).append("Dropped! "); 
		        
		        
		        //  find the position of the current droppable object on that day eg. 11
		        
		        // get the count of the draggable object eg. 4 ( or one hour) and then divide it by 2 and round up
		        // these are the number of li's above the current li that need to be selected
		        
		        // go through the collection of these li's - if any are of class saved then error out
		        
		        // now go through and add a new div and change the li's as if it was selected
		        
		        
		        
		        
		        
		        
		    }  
		});         
		
		$("#calculate").click(function () {
			var dayChosen = $("#daySelect").val();
			
			var collection = jQuery("div[id*='" + dayChosen +  "']");
			
			var totals  = new Array();
			
							
			collection.each(function(){
			
				
				var divValues = $(this).text().split(";");
				
				var key = divValues[0];
				var value = divValues[3];
				
				// name  and time in hours
				// alert(divValues[0] + ' ; ' + divValues[3]);
				if (totals[key] == undefined){
					totals[key] = 0;
					//alert (key + '::' + totals[key]);
				} 	
				
				
				
				totals[key] = totals[key] + parseFloat(value);
							
				
			});
			
			
			var newDivTotals = '<div id="showTotals" style="background:black; color:white" >';
			
			for (key in totals){
				newDivTotals = newDivTotals + key + ":" + totals[key] + "<br>";
			}		
			
			newDivTotals = newDivTotals + "</div>";
			
			$("#overCalendar").append(newDivTotals);
			
			
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
	
	$start = "07:00";
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

<select id=daySelect>
	<option value=Monday>Monday</option>
	<option value=Tuesday>Tuesday</option>
	<option value=Wednesday>Wednesday</option>
	<option value=Thursday>Thursday</option>
	<option value=Friday>Friday</option>
	<option value=Saturday>Saturday</option>
</select>
<input type=submit id=calculate value=calculate>

</body>
</html>