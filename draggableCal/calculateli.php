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
  	
  	function setSelectedElementsToSave (collection,name){
  		
  		
		var count = collection.size();
		var start = collection.eq(0).text();
		var end = collection.eq(count - 1).text();
  
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
			// newDivSave = newDivSave + '<div style="background:red; height=10px;" id=handle ><img height=10px src="images/zaneinthebaththumb.png"></div>';
			handleID = "#" + parent + txtStart;
		}
		else {
			newDivSave = newDivSave + '<div style="background:red; height=10px;" id=handle ><img height=10px src="images/zaneinthebaththumb.png"></div>';
			handleID = "#handle";
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
			handle:	handleID,
			grid: 	[102,1]
				    
		})
		.resizable({
			handles: "s",
			transparent: true,
			helper: "proxy",
			grid: [0,3],
			stop: function(ev, ui){

					
					/* Two scenarios
					
						expanding from 30 minutes starting at 09-00 to 1 hour from 09-00 to 09-45 inclusive
						
						li's and their classes 
						
								current 		resize				final
						09-00	save 09-00		save 09-00 resize	save 09-00	
						09-15	save 09-00		save 09-00 resize	save 09-00
						09-30					resize 				save 09-00
						09-45					resize 				save 09-00
						10-00
						
						shrinking

						reducing from 1 hour starting at 09-00 to 15 minutes at 09-00
						
						li's and their classes 
						
								current 		resize				final
						09-00	save 09-00		save 09-00 resize	save 09-00	
						09-15	save 09-00		save 09-00 	
						09-30	save 09-00		save 09-00 		
						09-45	save 09-00		save 09-00 		
						10-00
						
						
						PROBLEM when resizing to down to 1 element,
						have to reset the droppable handle div and background color
											
					*/
					
					// get the height of the expanded element ui.element.height()
					// get the height of the original element ui.originalSize.height
					// then round it based on the height of the li's (in this case it's 15
					// eg. if the old height was 30 then it would be two columns
					// if the new height is 60 then it is 4 columns (or count of 4)					
					var diffHeight = parseFloat(ui.element.height()) - parseFloat(ui.originalSize.height);
					var diffCount = diffHeight / 15;
					var diffRoundCount = Math.round(diffCount) ; // 15 is the height of the li's and rounds it
					
					

					
										
					// then we have to go through the all the current li elements (if the count is -ve) 
					// extra li elements eg. extra 2 columns in this example
					// and check that they are not set as saved
					// set the class to be "resized" for all elements, including the current elements (in case they shrink the element)
					// just to keep track and also set the start date as a class
					// need to keep count of how many extra elements have been processed so far


					// collect the current elements but have to find the parent and the start date
					 
					// find the data hidden in the span of the div
					// eg. timeslotsWednesday09-00
					var spanName = ui.element.attr('id') + 'data';  		
				  		
				  		 
					// get an array of the data that is hidden in the span
					var spanText = $('#' + spanName).text();
					
					var spanValues = spanText.split(";");
					
					var start = spanValues[1];
					var oldCount = spanValues[4];
					var parent = spanValues[5];
					 
					var txtStart = start.replace(/:/,"-");
					var newEnd = '';
					var newCount = diffRoundCount + parseFloat(oldCount);
					
					// now setup the query for current (ie before the resize) li elements
					// only if the count is < 0
					// eg. #timeslotsWednesday > li.09-00
					
					var query = "#" + parent + ' > li.'+ txtStart;
					collectionCurrent = jQuery(query);					
					
					// if diffRoundCount < 0
					if (diffRoundCount < 0){					
	
						// for each collection Current, only do this if the count is -ve
						// that means we are shrinking. note i is a counter
						collectionCurrent.each(function (i){
							
							// get elements count eg. 4 and then add the -ve count eg. -3 then 
							// you will get the number of elements to traverse to add the class 'resize'
							// eg. updated count = 1
							
							//$("#textPopup").text('hello');
							//$("#hoverPopup").show();

							

							if (i == (newCount -1)){
								newEnd = $(this).text();
							}

							// check if the updated count is over the current count
							if ( i > (newCount -1)){ 
							
								//$(this).text('in here');
								// then remove the saved class and the txtStart class for all elements from here on in
								$(this).removeClass('saved').removeClass(txtStart).css("background","").css("color","").css("border-bottom","").css("height","");
								 
								 
								
						 	}
						});						
					
						
					
						if (newCount == 1){
							// also reset the draggable handle of the div etc if count = 1
							
							
						}
					
					
					} // end if diffRoundCount < 0
					// else of diffRoundCount < 0 - so adding li's
					else {
					
						// find the last collectionCurrent
						
						

	
						// for each collection Current, only do this if the count is -ve
						// that means we are shrinking. note i is a counter
						collectionCurrent.eq(parseFloat(oldCount) - 1).nextAll()
				        .each(function (i){
				        	
				        	$('#overCalendar').append(i + '::' + diffRoundCount +  '::' + $(this).text() + '<br>');
				        	
				        });
				        
 						
						
 						
				
					} // end else
					
					// once the number have been reached, at the end of the loop
					// if they are all ok, then set all the class of resized to be saved
					// have to change the count for the div element and also the end date and the duration
					 	
					// delete any saved elements for this event that have not been set a class of resized
										
					// now reset the div stuff
					// have to change the 
					// - count for the div element
					spanValues[4] = newCount;
					 
					// -  the end date
					spanValues[2] = newEnd; 
					
					// -  the duration
					spanValues[3] = 0.25 * newCount;
					
					
					// - the height of the div
					var newHeight = (newCount * 15) - 2; // the 2px is for the border at the bottom 
					ui.element.height(newHeight + 'px');
					
					/* ui.element					
					var spanName = ui.element.attr('id') + 'data';
					*/  		
					$('#' + spanName).text(spanValues[0] + ';' + spanValues[1] + ';' + spanValues[2] + ';' + spanValues[3] + ';' + spanValues[4] + ';' + spanValues[5]);					
					var newHTML = ui.element.html();
					
					
					var oldDuration = oldCount * 0.25;
					
					newHTML = newHTML.replace('Duration: ' + oldDuration + '<span','Duration: ' + spanValues[3] + '<span' );
					
					ui.element.html(newHTML);
					
					
					 
					
							
			
			} // function for stop
			
			
			

		})
		// set to delete if double clicked
		.dblclick(function () { 
		
		  	var delSaved = confirm("Do you wish to delete?");
  		
  		
  			if (delSaved){
				deleteSavedDiv($(this));
			}
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
			
			  	var delSaved = confirm("Do you wish to delete?");
	  		
	  		
	  			if (delSaved){			
					deleteSavedDiv(el);
				}	
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
								
								setSelectedElementsToSave (collection,name);								
								
							                	
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
		    tolerance:	'pointer', // this along with the handle option in draggable is to reduce the area that the user can drop to improve accuracy 
		    drop: function(ev, ui) { 
		        
			    // find the data hidden in the span of the element el
	  			var spanName = ui.draggable.attr('id') + 'data';  		
	  		 	
				// get an array of the data that is hidden in the span
				var spanValues = $('#' + spanName).text().split(';');
				
				
				
		        var divLength = spanValues[4];	
		        var parent = spanValues[5];
		        var start = spanValues[1];	  
		        var oldName = spanValues[0];      
		        var txtStart = start.replace(/:/,"-");
		        
		        if ($(this).hasClass('saved') && 
        		    !(($(this).parent().attr('id') == parent) && 
        		       $(this).hasClass(txtStart))) 		        
		        {
		        	// alert ('Invalid Move');

		        }
		        else {
		        	
		        	//set the class so we can find it later
		        	$(this).addClass('moved');
		        	
		        	// if only 1 li long, then set things in now
		        	if (divLength ==1) {
		        		// if it gets to here everything has been successful so far
						// convert all addClass('moved') to be a new div
						
						deleteSavedDiv ($('#' + parent + txtStart));
						
						collection = jQuery('li.moved:visible');
						
						setSelectedElementsToSave (collection,oldName);	
						
						// delete the old details
						// deleteOldElements(
						
						
						
						
				        
				        
     	
		        		
		        	}
		        	else {
			        	
			        	// $("#overCalendar").append($(this).parent().attr('id') + ' ' + $(this).text() + '<br>');
				        
			        	
				        // get all the rest of the siblings to check if they can be moved 
				        $(this).nextAll()
				        .each(function (i){
				        	
				        	
				        	//$("#overCalendar").append(i + '::' + $(this).text() + '<Br>');
				        	
				        	//set the class so we can find it later
				        	$(this).addClass('moved');
				        	
					        if ($(this).hasClass('saved') && 
			        		    !(($(this).parent().attr('id') == parent) && 
			        		       $(this).hasClass(txtStart))) 				        	
				        	{
				        		// alert ('Invalid Move');
				        		

				        		return false;
				        	}
				        	
				        	
	
							// retrieve the length fromt the hidden div
							// we take away 2 as the header of the draggable is not 
							// in this list and the first of the siblings starts at 0
					        //var divLengthValue = $("#divLength").text();
					        divLengthValue = parseFloat(divLength) - 2;
				        	if (i == divLengthValue ){
				        		
				        		
				        		// if it gets to here everything has been successful so far

								
								// delete the old details before creating the saved details
								/*	example where moving 30 minute appointment back by 15 minutes
									these are the classes of the li
								
											before			during move				after move
									09-00	saved 09-00		saved 09-00				
									09-15	saved 09-00		saved 09-00 moved		saved 09-15
									09-30					moved					saved 09-15
									09-45
								
									So if we converted the moved to saved first, 09-15 li would then be
									
									saved 09-15
									
									So if we deleted the saved second, then the 09-15 li would then be
									
									09-15
									
									which would cause problems later on
								
									so we delete the saved from the old ones first before we convert the moved
								
								
								*/ 
															
								
								
								deleteSavedDiv ($('#' + parent + txtStart));

								// convert all addClass('moved') to be a new div								
								collection = jQuery('li.moved:visible');
								
								setSelectedElementsToSave (collection,oldName);	
								

								
								// alert (parent +  '::' + txtStart + '::' +  oldName + '::' + divLength );
								
								
	        		
				        		return false;
				        	}   
				        
				        });
			        
					}		        
			} // else if first one is saved
			 
		    // clear out any addClass('moved') that was set
		    // regardless of failure or not
			$("* > .moved").removeClass('moved');
		        
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
<style> 
.proxy {
		
		border: 1px dashed red;
}
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