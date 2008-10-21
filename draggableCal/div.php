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
	<script src="jquery.simplemodal.js"></script>
  	<script>
  	
  	function doubleClickSaved (name,count,start,secondTime,end,parent){
  	
  		var delSaved = confirm("Do you wish to delete?");
  		
  		if (delSaved){
  			// convert each li back to normal.
  			// each section has as a class the start date
  			// for any child of the parent
  			// eg. #timeslotsTuesday > li.07:15
  			
  			var txtStart = start.replace(/:/,"-");
  			
  			var query = "#" + parent + " > div."+txtStart;
  			 
  			
  			var deleteCollection = jQuery(query);
  			
  			
  			
  			if(deleteCollection) {
            	deleteCollection.each(function() {
                	
	                // now reset this back to what it used to be
    	            
        	        $(this).removeClass('saved').removeClass(txtStart).css("background","").css("color","").css("border-bottom","").css("height","").unbind("click");
        	        
	
        	        
                });		
                  				
                if (count > 2){
                    deleteCollection.eq(0).text(start);	
                    deleteCollection.eq(1).text(secondTime);
                    deleteCollection.eq(count - 1).text(end);
                }
		                    	
                if (count == 1) {	
                
                 	deleteCollection.eq(0).text(start);	
                }
                
				if (count == 2) {	
                   	deleteCollection.eq(0).text(start);
                   	deleteCollection.eq(1).text(end);
                    		
                }	
                
                
                
  			}
  			
			  			
  			
  			
  		} //if delsaved = y
  	}
  	

  	
  	$(document).ready(function(){

		$("#hoverpopup").hide();
		
		

        var collection;
          
       	$('#timeslotsMonday').selectable({
       	
       		selecting: function(ev, ui) {

               if ($(ui.selecting).hasClass("saved")){
               		// can we stop it from being selected?               	
               		$(ui.selecting).removeClass('ui-selecting');

               		//alert( $(ui.selecting).attr('class'));
               
               }
                

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
		           					$(this).unbind("click").css("background","green").css("color","green").css("border-bottom","0px").css("height","12px").click(function () { 
	      	 							doubleClickSaved(name,count,start,secondTime,end,$(this).parent().attr('id'));
			    					});
			    					
			    					$(this).hover(
			    						function () {
			    						
			    							
			    							$("#textPopup").text(name+ "::" + count + ":::" + start + ":::" + secondTime + ":::" + end + ":::" + $(this).parent().attr('id'));
			    							
											var offset = $(this).offset();
											
											var topOffset = offset.top + 18; 
											var leftOffset = offset.left + 18;
											
											$("#hoverpopup").show().css("top",topOffset).css("left",leftOffset);			    							

											
	      	 							},
	      	 							function(){
	      	 								$("#hoverpopup").hide();
			    						}
			    					);    		    		
		                	    });
		                    
		                    	// set the name of the person at the top
		                    
		                    	if (count > 2){
		                    		collection.eq(0).text(name).css("color","black");	
		                    		collection.eq(1).text(start + " to " + end).css("color","black");
		                    		collection.eq(count - 1).css("border-bottom","2px solid black").css("height","10px");		                    			
		                    	}
		                    	
		                    	
		                    	if (count == 1) {	
		                    	
		                    		collection.eq(0).text(name + " " + start).css("color","black");	
		                    	}
								if (count == 2) {	
		                    		collection.eq(0).text(name + " " + start).css("color","black");;
		                    		collection.eq(1).css("border-bottom","2px solid black").css("height","10px");
		                    			
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
div { list-style: none; margin:0px; padding:0px;}
.ui-selected { background: #black; color: #FFF; border-bottom: 2px solid #727EA3;}
.ui-selecting { background: #CFD499; } 

.dialog {background: red ; color: white;}

.hovering { background: yellow; } 

.firstSaved {background: green ; color: black; border-bottom: 0px; height: 12px;font-size: 9px;}
.saved {background: green ; color: green; border-bottom: 0px; height: 12px;font-size: 9px;}
.lastSaved {color: green; background: green ;border-bottom: 2px solid black; height: 10px;font-size: 9px;}
.onlySaved {background: green ; color: black; border-bottom: 2px solid black; height: 10px;font-size: 9px;}

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
	</div>
	</td>
	
				
	</tr>
</table>


<div id="hoverpopup" style=" position:absolute; top:55; left:44;">
<table bgcolor="#0000FF">
<tr><td id=titlePopup color="#FFFFFF">Details</td></tr>
<tr><td id=textPopup bgcolor="#8888FF">Hello I am a popup table</td></tr></table></div>

</body>
</html>