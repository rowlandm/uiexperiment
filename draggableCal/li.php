<?php
require_once('time.lib.php');

//temp, 2b filled from db
$blockedArray = array("10:30","13:00")
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
                    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  	<script src="jquery-1.2.6.js"></script>
	<script src="jquery-ui-personalized-1.6rc2.min.js"></script>
  	<script>
  	$(document).ready(function(){


        var collection;
          
       	$('#timeslots').selectable({
       	
       		// filter: ".ui-selectable",
       		
       		selecting: function(ev, ui) {

               if ($(ui.selecting).hasClass("saved")){
               	
               		// can we stop it from being selected?
               		$(ui.selecting).removeClass().addClass("saved");
               
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
                    
                    if (expectantCount != count){
						alert ("invalid selection");
						// reset all the selections
						
					}
					else {
					
						if (start != ""){
						
							// check that there the difference between the start and end times match up to the number of counts we expect
							// alert (start + " :: " + end + " :: " + count);
							
							
						
							var name = prompt(" please enter in the name of the appointment from " + start + " to " + end, "");
								
								
							
							// now clear out the ui-selected class from the selected panels
							// and then set it to the saved class to block it out
	    	                collection.each(function() {
	                    	
								
	                    	
	        	            	//set the new height based on the height of the li height:20px  and bottom of 2px
	            	        	$(this).removeClass('ui-selected').addClass("saved").selectable("disable").css("border-bottom","0px").text("").css("height","22px");
	           				
	                	    });
	                    
	                    	// set the name of the person at the top
	                    
	                    	if (count > 1){
	                    		collection.eq(1).text(start + " to " + end);
	                    		collection.eq(0).text(name);	
	                    	}
	                    	else {	
	                    		collection.eq(0).text(name + " " + start);	
	                    	}
	                    	collection.eq(count - 1).css("height","20px").css("border-bottom","2px solid black");
						                	
							
							
						}
					
					}
		
					                                  
            	}
            }				

         });    	
  	});
  	</script>


<style>
ul { list-style: none; margin:0px; padding:0px;}
.ui-selected { background: #black; color: #FFF; border-bottom: 2px solid #727EA3;}
.ui-selecting { background: #CFD499; } 


.saved {background: green }

li {border-bottom: 2px solid black;background: #CFD4E6; height:20px; width: 100px; margin-top:0px; font-size: 10px; font-family: Arial; padding-top: 3px; }
<?php
//Detect if user's browser is IE, if so, include IE hack to improve CSS Standards compliency
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
if(strpos($user_agent, 'MSIE') !== false)
{
	echo "li {margin-top:-1px;}";
}
?>
</style>








<!--
<style>
ul { list-style: none; margin:0px; margin-left:-30px;}
.ui-selected { background: #727EA3; color: #FFF; }
.ui-selecting { background: #CFD499; }
li { background: #CFD4E6; width: 100px; margin-top: 5px; font-size: 10px; font-family: Arial; padding-top: 3px; }
.fakedLi { background: #CFD4E6; width: 100px; margin-top: 5px; margin-left:10px; font-size: 10px; font-family: Arial; padding-top: 3px; }
</style>
-->

</head>
<body>


<table CELLSPACING=0>
	<tr><td>
	<ul id="timeslotsLbl">
	<?php
	$timeslotArray = timeslotArray("08:00","13:30","15");
	foreach($timeslotArray as $value){
	?>
		 <li style='background:#CCCCCC;color:black;'><?php echo $value;?></li>

	<?php 
	}
	?>
	</ul>

	</td>
	<td>

	<ul id="timeslots">
	<?php
	$nBlocked=0;
	$timeslotArray = timeslotArray("08:00","13:30","15","24");
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
	</td></tr>
</table>
</body>
</html>