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
<script type="text/javascript" src="json.js"></script> 
<script>
  	
  	function sendEmailDaily(actionDayChosen,el){
  	
  		// actionDayChosen is tuesday24-10-2008
  		var emailToAddress = prompt("Which email address would you like it sent to?","katina.omeros@celentia.com");
  		// var emailToAddress = prompt("Which email address would you like it sent to?","rowland.mosbergen@gmail.com");
  		
  		if (emailToAddress != null) {
  		
	  		var emailDefault = $('#userNameInput').val() + '@celentia.com';
	  		var emailFromAddress = prompt("Which email address would you like it sent from?",emailDefault);
	
			// eg. OVH:9 hours;GCMIA:4 hours;
			var details = calculateTotals(actionDayChosen,el,false);

			if (emailFromAddress != null) {
					
				var postData = 'emailFromAddress=' + emailFromAddress + '&dataDetails=' + details  + '&emailToAddress=' + emailToAddress   + '&actionDayChosen=' + actionDayChosen
		        			 + '&action=emailDaily' + '&sessionid=<?php echo $sessionID ?>';  
		
				// $('#overCalendar').append(postData);
				
				$.ajax({
					type: "POST",
				   	url: "ajaxcal.php",
				   	data: postData,
				   	success: function(msg){
						alert(msg);   		
		  			}
		  		});
		  		
			} // if emailFromAddress not null  	
		} // if emailToaddress not null 	
  	
  	}
  	
  	
	function refreshCalendar(showNumDays){
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
		   		$('#overCalendar').html(msg);
		   		
		   		refreshCalendarEvents();
				
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
				
					refreshCalendar(14);
					$('#showNumDays').val("14");
					
					$('#weeklyTotals').text('F/n Totals');
					
				});
				
				
				
				
				
		        // radio button to choose 15,30 or 60 minute intervals
				/* 
				$('#refreshDIV').remove();
				newRadioHTML = '<div id=refreshDiv><br>Refresh after every action? <br><input type="radio"  name="refreshRadio" value="on"> ON '  
							+  '<input type="radio"  name="refreshRadio" value="off" checked> OFF <br></div>';
				$('#overCalendar').prepend(newRadioHTML);
				
				*/ 
				
				
		        
		          
		        // This is to set all ul tags that are in the overCalendar DIV to be selectable - includes li tags too
		        
		        // for speed, try to find the lowest level element to reduce time finding that element
		        // #overCalendar ul means show all ul elements that are descendants of #overCalendar DIV
		        // #overCalendar > ul didn't work - this is all ul elements that are children of #overCalendar DIV
		       	$('#overCalendar ul').selectable({
		       	
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
		
									// clear out any old input divs
									$("#inputDiv").remove();	
									
									var newInputDiv = '<div id="inputDiv" style=" position:absolute; top:55; left:44;">' + 
									' <table bgcolor="#0000FF"> ' + 
									' <tr><td id=inputDivTitle color="#FFFFFF">Details</td></tr> ' + 
									' <tr><td bgcolor="#8888FF">Name:</td><td> <input id=inputName type=text> </td></tr> ' + 
									' <tr><td bgcolor="#8888FF">Type:</td><td> <input id=inputType type=text> </td></tr> ' +
									' <tr><td bgcolor="#8888FF">Job:</td><td> <input id=inputCode type=text> </td></tr> ' +
									' <tr><td bgcolor="#8888FF">Details:</td><td> <textarea id=inputDetails></textarea> </td></tr> ' +
									' <tr><td colspan=2 bgcolor="#8888FF"><input type=submit id=inputSubmit value=Submit ><input type=submit id=cancelSubmit value=Cancel </td></tr> ' +
									' </table></div> ';
									
									$('#overCalendar').append(newInputDiv);
					    			
									$('#inputDiv :input:visible:enabled[@type=text]').keyup(function(e) {
						
										if(e.keyCode == 27) {
											$('#cancelSubmit').click();
										}
						
									
										//alert(e.keyCode);
										if(e.keyCode == 13) {
											$('#inputSubmit').click();
										}
									});
		
												
									var topOffset  = e.pageY  - 100; 
									var leftOffset = e.pageX - 100; 
									
									$('#inputDivTitle').text('Add new appointment');
		
									$("#inputDiv").show().css("width","500px").draggable()
									.css("top",topOffset).css("left",leftOffset);
																
									$('#cancelSubmit').click(function(){
										$("#inputDiv").hide();	
										// if no name then 
										// reset all the selections
		    			                collection.each(function() {
		        	    	    	    	
		            	    	    		$(this).removeClass('ui-selected');
		           					
		                	    		});										
									});
									
									// get the focus on the first text area
									$("#inputDiv :input:visible:enabled:first").focus();
									
									
									$('#inputSubmit').click(function(){
										$("#inputDiv").hide();	
										
		
										// var name = prompt("Please enter in the name of the appointment", "");
										var name = $('#inputName').val(); 	
											
										
										// going to add the start to the class, and it has problems
										// seeing : when in the class, so changing it to -
																	
										if (name != null && name != ""){  							
											
											
											collection = jQuery('li.ui-selected:visible');
											setSelectedElementsToSave ('add',collection,name,$('#inputType').val(),$('#inputCode').val(),$('#inputDetails').val() );								
											
										                	
										} // end of if name != ""	
										else {
											// if no name then 
											// reset all the selections
			    			                collection.each(function() {
			        	    	    	    	
			            	    	    		$(this).removeClass('ui-selected');
			           					
			                	    		});						
											
										}								
									})
									
								
																
										
								} // end of start !+ "" 
							
							}
				
							                                  
		            	}
		            }				
		
		         });    	
		
		
				$("#savedDivMenu").hide();
				
		
		         
		         
		        // this is to allow the draggable to drop into something
		        
		        // for speed, try to find the lowest level element to reduce time finding that element
		        // #overCalendar li means show all li elements that are descendants of #overCalendar DIV
		        // #overCalendar > li didn't work - this is all li elements that are children of #overCalendar DIV		         
				$("#overCalendar li").droppable({ 
				
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
				        var oldType = spanValues[6];
				        var oldCode = spanValues[7];
				        var oldDetails = spanValues[8];
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
								
								setSelectedElementsToSave ('moved',collection,oldName,oldType,oldCode,oldDetails);	
								
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
										
										setSelectedElementsToSave ('moved',collection,oldName,oldType,oldCode,oldDetails);
										
		
										
										// alert (parent +  '::' + txtStart + '::' +  oldName + '::' + divLength );
										
										
			        		
						        		return false;
						        	}   
						        
						        });
					        
							}		        
					} // else if first one is saved
					 
				    // clear out any addClass('moved') that was set
				    // regardless of failure or not
				    
				    // for speed, try to find the lowest level element to reduce time finding that element
				    // hence find all class of moved that is a descendant of #overCalendar
					$("#overCalendar .moved").removeClass('moved');
				        
				    }  
				});         
				
		   		
		   		
		   	}
		});		
		
		
		
	}
  	
  	
  	function refreshCalendarEvents(){
        
        // remove divs with class of savedDiv and then remove the li.saved items to refresh
        $('#overCalendar > div').filter('.savedDiv').remove();
        
        // for speed, try to find the lowest level element to reduce time finding that element
        // #overCalendar li.saved means show all li elements with a class of saved that are descendants of #overCalendar DIV
        // #overCalendar > li.saved  didn't work - this is all li elements that are children of #overCalendar DIV		         
        
        $('#overCalendar li.saved').removeClass().addClass('ui-selectee').addClass('ui-droppable')
        .removeAttr("style");
        
        
        // want to return the monday date so that we can populate the entire week
        // $('ul[id*="timeslotsMonday"]') says get all ul's that have an id that contains timeSlotsMonday
        // .attr(id) gets the actual id eg. timeslotsMonday20-10-2008
        // split puts it into an array with day as the delimiter. so mondayDate[1] should have the date for the monday
        
        // for speed, try to find the lowest level element to reduce time finding that element - hence #overCalendar ul[id*="timeslotsMonday"]
        var mondayDate = $('#overCalendar ul[id*="timeslotsMonday"]').attr('id').split('day');
        var showNumDays = $('#showNumDays').val();
        
        var postData = 'action=retrieve&username='+ $('#userNameInput').val() + '&mondayDate=' + mondayDate[1] + '&showNumDays=' + showNumDays ;  
        
        // initialise the calendar
		// call ajax from the database to return the records and use them to create events
  		$.ajax({
			type: "POST",
		   	url: "ajaxcal.php",
		   	data: postData,
		   	success: function(msg){
		   		// debug code
		   		// $('#overCalendar > span:last').remove();
		    	// $('#overCalendar').append('<span>'  + msg + '</span>');
		    	
		    	
 		    	var myObject = json_parse(msg);
 		    	$('#overCalendar').append('<span>');
				for (key in myObject){
					
					
					
					// myObject[key].htmlid is timeslotsFriday24-10-200809:30
					// we want to get the parent which would be timeslotsFriday24-10-2008
					// so take off the last 5 chars
					
					var parent = myObject[key].htmlid.substr(0,myObject[key].htmlid.length - 5);
					
					// appt_start is format 2008-10-20 13:15:00
					var start = myObject[key].appt_start.substr(11,5);
					var end = myObject[key].appt_end.substr(11,5);

					
					
					if (start == end){
						$('#'+ parent + ' > li:contains("' + start + '")').addClass('ui-selected');
					}
					else {
						// mark all the appropriate li's as class 'ui-selected'
						$('#'+ parent + ' > li:contains("' + start + '")').addClass('ui-selected')
						.nextAll().each(function (i){
						
							$(this).addClass('ui-selected');
							if ($(this).text() == end){
								return false;
							}
						
						});
					} // end of else if start == end
				
					// now just do a collection for this object and call the details
					
					var collection = jQuery('li.ui-selected:visible');
					var name = myObject[key].appt_name;
					var inputType = myObject[key].appt_type;
					var inputCode = myObject[key].appt_code;
					var inputDetails = myObject[key].appt_details;
					setSelectedElementsToSave ('retrieved',collection,name,inputType,inputCode,inputDetails);								
					
				}	

				
				
				
				
		    	
		   	}
		});
		
		  	
  	}
  	
  	function editSavedDiv (el,pos){
		
			// find the data hidden in the span of the element el
			var spanName = el.attr('id') + 'data';  		
			  		
			  		 
			// get an array of the data that is hidden in the span
			var spanText = $('#' + spanName).text();
			
			var spanValues = spanText.split(';');				
		
			
		
			// clear out any old input divs
			$("#inputDiv").remove();	
			
			var newInputDiv = '<div id="inputDiv" style="position:absolute; top:55; left:44;">' + 
			' <table bgcolor="#0000FF"> ' + 
			' <tr><td id=inputDivTitle color="#FFFFFF">Details</td></tr> ' + 
			' <tr><td bgcolor="#8888FF">Name:</td><td> <input id=inputName type=text> </td></tr> ' + 
			' <tr><td bgcolor="#8888FF">Type:</td><td> <input id=inputType type=text> </td></tr> ' +
			' <tr><td bgcolor="#8888FF">Job:</td><td> <input id=inputCode type=text> </td></tr> ' +
			' <tr><td bgcolor="#8888FF">Details:</td><td> <textarea id=inputDetails></textarea> </td></tr> ' +
			' <tr><td colspan=2 bgcolor="#8888FF"><input type=submit id=inputSubmit value=Submit ><input type=submit id=cancelSubmit value=Cancel </td></tr> ' +
			' </table></div> ';
			
			$('#overCalendar').append(newInputDiv);
    		$('#inputDiv').hide().draggable();
    		
    		$('#inputName').val(spanValues[0]);
    		$('#inputType').val(spanValues[6]);
    		$('#inputCode').val(spanValues[7]);
    		$('#inputDetails').val(spanValues[8]);
    		
    		
    		
			$('#inputDiv :input:visible:enabled[@type=text]').keyup(function(e) {

				if(e.keyCode == 27) {
					$('#cancelSubmit').click();
				}

			
				//alert(e.keyCode);
				if(e.keyCode == 13) {
					$('#inputSubmit').click();
				}
			});
			
			
						
			var topOffset  = pos.docY + 8; 
			var leftOffset = pos.docX -100; 
			
			$('#inputDivTitle').text('Add new appointment');

			$("#inputDiv").show().css("width","500px")
			.css("top",topOffset).css("left",leftOffset);
										
			$('#cancelSubmit').click(function(){
				$("#inputDiv").hide();	
			});
			
			// get the focus on the first text area
			$("#inputDiv :input:visible:enabled:first").focus();
			
			
			$('#inputSubmit').click(function(){
				$("#inputDiv").hide();	
				

				// reset the div with the new details
				
				spanValues[0] = $('#inputName').val();
    			spanValues[6] = $('#inputType').val();
    			spanValues[7] = $('#inputCode').val();
    			spanValues[8] = $('#inputDetails').val();
				
				
				$('#' + spanName).text(spanValues[0] + ';' + spanValues[1] + ';' + spanValues[2] + ';' + spanValues[3] 
				+ ';' + spanValues[4] + ';' + spanValues[5] + ';' + spanValues[6] + ';' + spanValues[7] + ';' + spanValues[8]);					
				
				// change the display
				$('#' + el.attr('id') + 'display').text(spanValues[0] + ' Duration: ' + spanValues[3]);
				
				
				var postData = 'username=' + $('#userNameInput').val() + '&htmlID=' + el.attr('id')
								+ '&action=edit' + '&inputName=' +  spanValues[0] + '&inputType=' + spanValues[6] 
								+ '&inputCode=' + spanValues[7] + '&inputDetails=' + spanValues[8];
				
		          
		        
		        // initialise the calendar
				// call ajax from the database to return the records and use them to create events
		  		$.ajax({
					type: "POST",
				   	url: "ajaxcal.php",
				   	data: postData,
				   	success: function(msg){
				   	
				   		alert(msg);
				   	}
				   	
				});		
				   	
				   			
				  
				
			});			
	} //function editSavedDiv
	
					  	
  	function stopResizing (ev,ui){
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
		
		var savedClassFound = false;
		
		// set the div to be orange again
		$('#' + parent + txtStart).css("background","");
		
		
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
				$("#handle" + parent + txtStart).hide();
				
				
				
				
				// FIXME: RHM cannot seem to reset the handle of the draggable element to be the entire element
			}
		
		
		} // end if diffRoundCount < 0
		// else of diffRoundCount < 0 - so adding li's
		else {
		
			// find the last collectionCurrent
			
			
	
	
			// for each collection Current, only do this if the count is -ve
			// that means we are shrinking. note i is a counter
			collectionCurrent.eq(parseFloat(oldCount) - 1).nextAll()
	        .each(function (i){
	        	
	        	// $('#overCalendar').append(i + '::' + diffRoundCount +  '::' + $(this).text() + '<br>');
	        	
	        	
	        	if ( $(this).hasClass('saved')){
	        	
	        		savedClassFound = true;
	        	
	        		return false;
	        	}
	        	
	        	$(this).addClass('resized');
	        	
	        	if (i == (diffRoundCount-1)){
	        		
	        		newEnd = $(this).text();
	        		
	        		var query = "#" + parent + ' > li.resized';
					collectionConvertToSaved = jQuery(query);
					
					collectionConvertToSaved.each(function(){
						
						$(this).removeClass('resized').addClass('saved').addClass(txtStart).css("background","red").css("color","white").css("border-bottom","0px").css("height","12px");
						
					});
	        		
	        		return false;
	        	}
	        	
	        	
	        });
	        
	 		
			
	 		
	
		} // end else
		
		// once the number have been reached, at the end of the loop
		// if they are all ok, then set all the class of resized to be saved
		// have to change the count for the div element and also the end date and the duration
		 	
		if (!savedClassFound){					 	
		 	
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
			$('#' + spanName).text(spanValues[0] + ';' + spanValues[1] + ';' + spanValues[2] + ';' + spanValues[3] 
			+ ';' + spanValues[4] + ';' + spanValues[5] + ';' + spanValues[6] + ';' + spanValues[7] + ';' + spanValues[8]);					
			
			
			
			var oldDuration = oldCount * 0.25;
			
			$('#' + parent + txtStart + 'display').text(spanValues[0] + ' Duration: ' + spanValues[3]);
			
			// this actually stops the div from resizing
			// ui.element.html(newHTML);

			// if old count is 1 then it would have been expanded, so show the handle
			if (oldCount ==1){
				$("#handle" + parent + txtStart).show();
			}
			
			// ajax call to resize the htmlid
			var postData = 'username=' + $('#userNameInput').val() + '&htmlID=' + parent + txtStart  
				+ '&action=resize' + '&end=' + newEnd;
			
			
			$.ajax({
				type: "POST",
			   	url: "ajaxcal.php",
			   	data: postData,
			   	success: function(msg){
			   		
			   		// debug code
			   		// $('#overCalendar > span:last').remove();
			    	// $('#overCalendar').append('<span>'  + msg + '::' + parent + start + '</span>');
			    	
			      	
			      	var pos = msg.indexOf("SUCCESS");
					if (pos >= 0)
					{
						
						$('#' + parent + txtStart).css("background","green");
						
						if ($('#refreshDiv > :radio:checked').val() == 'on'){
							refreshCalendarEvents();
						}
					}
			    	
			   	}
			});					
			
			
					
		
		} //end of if saved 
		else {
			// reset the height back to the old height
			var oldHeight = ui.originalSize.height - 2;
			// for some reason the css background green got lost
			ui.element.height( oldHeight + 'px').css("background","green");
			
			
		} 
		
		
		
		// regardless of anything else, remove all classed of resized
		
		// increase speed by finding smallest element before searching for classes
		$("#overCalendar .resized").removeClass('resized');
		
	} //  stopResizing	
  	
  	function setSelectedElementsToSave (action,collection,name,inputType,inputCode,inputDetails){
  		
  		
  		name = name.replace(";",",");
  		inputType = inputType.replace(";",",");
  		inputCode = inputCode.replace(";",",");
  		inputDetails = inputDetails.replace(";",",");
  		
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
		
		// the 15 is for the height of each li and the -2 is to take into account the 2px bottom line -- Firefox and chrome
		var newHeight = (count * 15) - 2; 
		
		
		var newDivSave = '<div id="' + parent + txtStart + '" class = "savedDiv" >';
                    
                    
		// the ; is important as it is used as a delimiter to calculate stuff later on
		newDivSave = newDivSave + '<div style="background:red; height=10px;" id=handle'  + parent + txtStart + '><img height=10px src="images/zaneinthebaththumb.png"></div>';
		newDivSave = newDivSave + '<span id=' + parent + txtStart + 'display > ' + name + ' Duration: ' + count * 0.25 + '</span>';
		newDivSave = newDivSave + '<span id=' 
					+ parent + txtStart + 'data class=hideData style="visible: hidden">' + name + ';' + start + ';' 
					+ end + ';' + count * 0.25 + ';' + count + ';' + parent + ';' 
					+ inputType + ';' + inputCode+ ';' + inputDetails + '</span>'; 
		newDivSave = newDivSave + '</div>';	
                    
                    
		// append the new div	                    	
		$("#overCalendar").append(newDivSave);
                    
		//hide the data span
		$('#' + parent + txtStart + 'data').hide();

		// if count ==1 set handle to be the whole object
		// also hide the top handle
		if (count == 1){
			handleID = "#" + parent + txtStart;
			$("#handle" + parent + txtStart).hide();
		}
		else {
			handleID = "#handle" + parent + txtStart;
		} 

                    
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
		
			// had to hide transparent: true as it stopped ie7 from resizing probably due to the bottom line etc.
			handles: "s",
			helper: "proxy",
			grid: [0,5],
			containment: $('#overCalendar'),
			stop: function(ev, ui){

				stopResizing(ev,ui);		
			
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
				calculateTotals(actionDayChosen,el,true);
														
			}		
			if (action == "weeklyTotals"){
							
							
				// alert(actionDayChosen);
				calculateTotals('weekly',el,true);
														
			}	
			if (action == "refresh"){

				refreshCalendarEvents();
														
			}			
			if (action == "edit"){
					editSavedDiv(el,pos);
		
			}
			if (action == "emailDaily"){

				// id is timeslotsTuesday24-10-200806-00							
				// var actionDayChosen = "Tuesday24-10-2008";
				 						
				var actionDayChosen = el.attr('id').slice(9,-5);
							 
				// alert(actionDayChosen);
				sendEmailDaily(actionDayChosen,el);
														
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
							
				$("#hoverPopup").show().css("top",topOffset).css("left",leftOffset).draggable()
				.dblclick(function(){
					$("#hoverPopup").hide();	
				});
							
				$("#textPopup").append("<br> Double click to hide");																	
			}	
			
			if (action == "delete"){
			
			  	var delSaved = confirm("Do you wish to delete?");
	  		
	  		
	  			if (delSaved){			
					deleteSavedDiv(el);
				}	
			}
							
		});
  	
  		// now save this into the database
  		
		
		
		var postData = 'username=' + $('#userNameInput').val() + '&name=' + name + '&start=' + start + '&end=' + end + '&htmlID=' + parent + txtStart  + '&inputType=' + inputType
		+ '&inputCode=' + inputCode + '&inputDetails=' + inputDetails + '&action=add';
		

		// only send the ajax if 'action' is a move or an add
		
		if ((action == 'moved') || (action == 'add')){
		
	  		$.ajax({
				type: "POST",
			   	url: "ajaxcal.php",
			   	data: postData,
			   	success: function(msg){
			   		
			   		// debug code
			   		// $('#overCalendar > span:last').remove();
			    	// $('#overCalendar').append('<span>'  + msg + '::' + parent + start + '</span>');
			    	
			      	
			      	var pos = msg.indexOf("SUCCESS");
					if (pos >= 0)
					{
						
						$('#' + parent + txtStart).css("background","green");
						if ($('#refreshDiv > :radio:checked').val() == 'on'){
							refreshCalendarEvents();
						}
					}
			    	
			   	}
			});
			
			
		}// if action move or add
		else { // usually action of retrieved - already in teh database so make it green
			$('#' + parent + txtStart).css("background","green");
		}
  	
  	}
  	
  	function calculateTotals (dayChosen,el,display){
		
		// i have set hidden spans that have the day in the id
		// eg. span id =  timeSlotsMonday07-45data
		// this jquery will get all the spans that have an id that has 
		// the dayChosen (eg. tuesday)
		
		var collection = '';
		
		if (dayChosen == 'weekly') {
			collection = jQuery("span[id*='data']");
		}
		else {
			collection = jQuery("span[id*='data'][id*='" + dayChosen +  "']");
		} 
		
		var totals  = new Array();
		collection.each(function(){
		
			// get an array of the data that is hidden in the span
			var spanValues = $(this).text().split(";");
			
			// the job code is set to the 8th element of the span
			var key = spanValues[7];
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
		

		if (display){
		
			var newDivTotals = '<div id="showTotals" style="position:absolute; background:black; color:white" >';
			newDivTotals = newDivTotals + '<table bgcolor="#0000FF">';
			newDivTotals = newDivTotals + '<tr><td id=titleTotals color="#FFFFFF">Details</td></tr>';
			newDivTotals = newDivTotals + '<tr><td id=textTotals bgcolor="#8888FF">Hello I am a popup table</td></tr></table></div>';
			$("#overCalendar").append(newDivTotals);  
			
			if (dayChosen == 'weekly'){
				$("#titleTotals").text(" Weekly Total ");
			}
			else{ 
				$("#titleTotals").text(" Total for " + dayChosen);
			}
			
			 
			var textTotals = '<table>';
			for (key in totals){
				textTotals = textTotals + '<tr><td>' +  key + ":" + totals[key] + " hours</td></tr>";
			}		
			
			textTotals = textTotals + "<tr><td> Please double click to hide.</td></tr></table>";
			
			$("#textTotals").html(textTotals);
				
			
			var offset = el.offset();
			
			
			
			var topOffset = offset.top + 18; 
			var leftOffset = offset.left + 18;
			
			$("#showTotals").draggable().show().css("top",topOffset).css("left",leftOffset)
			.dblclick(function(){
				$("#showTotals").hide();	
			})
		
		} // display
		else {
			var returnTotals = '';
			
			for (key in totals){
				returnTotals = returnTotals + key + ":" + totals[key] + " hours;";
			}		
		
			return returnTotals;
		}
																			
		
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
        	        $(this).removeClass('saved').removeClass(txtStart)
        	        	.removeAttr("style");
        	        
        	        // .css("background","").css("color","").css("border-bottom","").css("height","")
	
        	        
                });		
                
                
  			}
  			
  			
  			// delete the appropriate div
  			// note the div id is the parent + the start 
  			// eg. id=timeslotsMonday07-15 
  			$("#" + parent + txtStart).remove();

			var postData = 'username=' + $('#userNameInput').val() + '&htmlID=' + parent + txtStart  + '&action=delete';

			

	  		$.ajax({
				type: "POST",
			   	url: "ajaxcal.php",
			   	data: postData,
			   	success: function(msg){
			   		
			   		// debug code
			   		// $('#overCalendar > span:last').remove();
			    	// $('#overCalendar').append('<span>'  + msg + '::' + parent + txtStart + '</span>');
			    	
			      	if ($('#refreshDiv > :radio:checked').val() == 'on'){
						refreshCalendarEvents();
					}
			      	
			      	var pos = msg.indexOf("SUCCESS");
					if (pos >= 0)
					{
						
						
					}
			    	
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
		refreshCalendar(7);
		
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
