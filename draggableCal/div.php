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
  	
  	function doubleClickSaved (name,count,start,secondTime,end,parent){
  	
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
    	            
        	        $(this).removeClass('saved').removeClass(txtStart);
        	        
           			
                });		
                  				
                if (count > 2){
                    deleteCollection.eq(0).text(start).removeClass('firstSaved');	
                    deleteCollection.eq(1).text(secondTime).removeClass('saved').removeClass('firstSaved');
                    deleteCollection.eq(count - 1).text(end).removeClass('lastSaved');
                }
		                    	
                if (count == 1) {	
                
                 	deleteCollection.eq(0).text(start).removeClass('onlySaved');	
                }
                
				if (count == 2) {	
                   	deleteCollection.eq(0).text(start).removeClass('saved').removeClass('firstSaved');
                   	deleteCollection.eq(1).text(end).removeClass('saved').removeClass('lastSaved');
                    		
                }	
                
                
                
  			}
  			
			  			
  			
  			
  		} //if delsaved = y
  	}
  	
  	
  	
  	$(document).ready(function(){

        var collection;
          
       	$('.ui-selectee').selectable({
       	
       		selecting: function(ev, ui) {

               if ($(ui.selecting).hasClass("saved")){
               	
               		// can we stop it from being selected?
               		$(ui.selecting).removeClass().addClass("saved");
               
               }
                
               $(ui.selecting).text("selected");

            },
       	

                   	
        	stop: function(e,ui){
				                               	
                collection = jQuery('div.ui-selected:visible');
                    
				
				
				
				if(collection) {
                	
                	var count = collection.size();
                	var start = collection.eq(0).text();
                	var end = collection.eq(count - 1).text();
                    
                    
                    // this is to keep the text of the second selected
                    // li as it is used to display the date range
                    var secondTime = collection.eq(1).text();
                    
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
						
							
							
							
							var name = prompt(" please enter in the name of the appointment from " + start + " to " + end, "");
								
							var txtStart = start.replace(/:/,"-");
														
							if (name != null && name != ""){  							
								// now clear out the ui-selected class from the selected panels
								// and then set it to the saved class to block it out
		    	                collection.each(function() {
		                    	
		                    		
		                    	
									//set the new height based on the height of the li height:20px  and bottom of 2px
		            	        	$(this).removeClass('ui-selected').addClass(txtStart).addClass("saved");
		           					$(this).unbind("click").click(function () { 
	      	 							doubleClickSaved(name,count,start,secondTime,end,$(this).parent().attr('id'));
			    					});    		
		                	    });
		                    
		                    	// set the name of the person at the top
		                    
		                    	if (count > 2){
		                    		collection.eq(0).text(name).removeClass('saved').addClass('firstSaved');	
		                    		collection.eq(1).text(start + " to " + end).removeClass('saved').addClass('firstSaved');
		                    		collection.eq(count - 1).addClass('lastSaved').removeClass('saved');		                    			
		                    	}
		                    	
		                    	
		                    	if (count == 1) {	
		                    	
		                    		collection.eq(0).text(name + " " + start).removeClass('saved').addClass('onlySaved');	
		                    	}
								if (count == 2) {	
		                    		collection.eq(0).text(name + " " + start).removeClass('saved').addClass('firstSaved');
		                    		collection.eq(1).removeClass('saved').addClass('lastSaved');
		                    			
		                    	}		                    	
							                	
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
  	});
  	</script>


<style>
ul { list-style: none; margin:0px; padding:0px;}
.ui-selected { background: #black; color: #FFF; border-bottom: 2px solid #727EA3;}
.ui-selecting { background: #CFD499; } 


.firstSaved {background: green ; color: black; border-bottom: 0px; height: 12px;}
.saved {background: green ; color: green; border-bottom: 0px; height: 12px;}
.lastSaved {color: green; background: green ;border-bottom: 2px solid black; height: 10px;}
.onlySaved {background: green ; color: black; border-bottom: 2px solid black; height: 10px;}

.ui-selectee {border-bottom: 2px solid black;background: #CFD4E6; height:10px; width: 100px; margin-top:0px; font-size: 9px; font-family: Arial; padding-top: 3px; }
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


<table CELLSPACING=0>
	<tr><td>


	</td>
	<td>
	Monday
	<div id="timeslotsMonday">
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
		
		<div class=saved style='background:black;color:white;'><?php echo $value;?></div>
		
	<?php
		}else{
	?>
			<div class=ui-selectee ><?php echo $value;?></div>
	<?php
		}
	}
	?>
	</ul>
	</td>

				
	</tr>
</table>
</body>
</html>