<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
                    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" href="jquery.contextMenu.css" /> 
   	<script src="jquery-1.2.6.js" type="text/javascript" charset="utf-8"></script>
   	<script src="jquery-ui-1.6rc2.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" src="jquery.contextMenu.js"></script>
  
  	<script>
  	/*
  		You really want to put any functions that are run inside clicks or double clicks as standalone functions if it is going
  		to be used across two places. eg. right mouse click and submit button.
  	*/
  	
  	// This passes in the object, which is the actual li bound to the double click
  	function resetDoubleClickDelete ($obj) {
		var answer = confirm("About to Delete, do you want to continue?")
		if (answer){

			// completely removes the li object 
			$obj.remove();
		}  	
	}  	
	
	function saveMyList(){
    		// collect all li's of the mylist div
    		
    		// this gets a list of all li's that are descendants of the UL id=myList
			collection = jQuery('#myList > li');  
                                   
                                   
            if(collection) {
            	var inc=0;
            	var list= "";
                // go through each of the collections 
                // and then add to list - text is between <li> and </li>
                collection.each(function() {
                	inc++;
                	list = list + $(this).text() + ",";
                });
                
                // clear the last ,
                list = list.slice(0, -1)

                //alert (list);          
				
				var postData = "list=" + list;
				
				//alert(postData);
				
				
				// post with the ajax and then set everything to be unchanged
				$.ajax({
				  type: "POST",
				  url: "submitSortableList.php",
				  data: postData,
				  success: function(txt){
				    alert(txt);
				    
				    // set all li's of myList to be class unchanged
				    $("#myList > li").removeClass().addClass("unchanged");
				  }				  
				});				
				
				                              
            } // end if collection   	
	}
	
	function showLists (listType){
	    // collect all li's of the mylist div
	    
	    //listType is somethign like ".changed" and it filters
	    //the li descendants of the UL 
	    collection = jQuery('#myList > li').filter(listType);  
                                   
            if(collection) {
            var inc=0;
            var list= "";
                // i resize the first then hide others
                collection.each(function() {
                inc++;
                list = list + $(this).text() + ",";
                });
                list = list.slice(0, -1);
                alert (list);       
            }    
	}
	

	function rightMouseClickAction(action, el, pos) {
		  /* alert(
			'Action: ' + action + '\n\n' +
			'Element ID: ' + $(el).attr('id') + '\n\n' + 
			'X: ' + pos.x + '  Y: ' + pos.y + ' (relative to element)\n\n' + 
			'X: ' + pos.docX + '  Y: ' + pos.docY+ ' (relative to document)'
			); 
			*/
		if (action == "delete"){
			
			resetDoubleClickDelete($(el));
			//alert("right here");	
	
			
		}
		if (action == "edit"){
			
			//resetDoubleClickDelete($(el));
			var test = $(el).text();
			var editListName = prompt("Please enter the changed list name",$(el).text() );	
			
			$(el).text(editListName);
			
			$(el).removeClass().addClass("changed");
		}			 	

		if (action == "showUnchanged"){
			var listType = ".unchanged";
			showLists(listType);			
			
		}			
		if (action == "showAdded"){
			var listType = ".added";
			showLists(listType);			
			
		}
		if (action == "showChanged"){
			var listType = ".changed";
		 	showLists(listType);			
			
		}		
		if (action == "add"){
			addNewList();
		}	
		if (action == "save"){
			saveMyList();
		}					
		if (action == "clear"){
			$("#myList").html('');
		}
		
				
		
	}	
	
	function resetClicks (){
    		$("#myList > li").unbind("dblclick").dblclick(function () { 
      	 		resetDoubleClickDelete($(this));
		    });    		
    		$("#myList > li").contextMenu({ menu: 'myMenu',inSpeed: 150, outSpeed: 150},function (action,el,pos){ rightMouseClickAction(action,el,pos); });
	}

	function addNewList (){
    	if ($("#newListItem").val() != ""){
    	
    		var newListHTML = "<li class=\"added\">" +  $("#newListItem").val() + "</li>"; 
    		//$("#myList").html($("#myList").html() + newListHTML);
    		$("#myList").append(newListHTML);
    		// have to unbinde the old dblclick and then rebind the new after changing the list
    		resetClicks();
    		    		
    	}
    	else {
    		alert("New list cannot be blank"); 
    	} 	
	}

	
  	
  	
  $(document).ready(function(){
  
  	var htmlExplain = "Please load, add or load template to create a list. Then right mouse click on the list. ";
  	htmlExplain = htmlExplain + "This list is also sortable. You can save your changes and then load them later. ";
  	$("#explain").hide().show().html(htmlExplain).css("background-color","red"); 
  
   	$("#myList").sortable({
    
    	// when updating, change the class of the ui that was selected
    	update: function(e, ui){
    	
    		ui.item.removeClass().addClass("changed");
    	}  
        });
    
    
    $("#myMenu").hide();
        
    $("li").dblclick(function () { 
      	 
		resetDoubleClickDelete($(this));
    });
    
    // $(el).attr('id')
    $("#clearMyList").click(function () {
    	$("#myList").html('');
    });
    
    $("#addToMyList").click(function () {
    	addNewList();
    });
    
    $("#templateMyList").click(function () {
    		
		var templateList = '';
	    templateList = templateList + "<li class=\"unchanged\">Full NFL games on HD</li>";
	    templateList = templateList + "<li class=\"unchanged\">NFL Highlights on NFL.com</li>";
	    templateList = templateList + "<li class=\"unchanged\">Full 49ers games</li>";
	    templateList = templateList + "<li class=\"unchanged\">Full Qld Roar Games</li>";
	    templateList = templateList + "<li class=\"unchanged\">Full Liverpool Games</li>";
	    templateList = templateList + "<li class=\"unchanged\">Full Socceroos Games</li>";
	    templateList = templateList + "<li class=\"unchanged\">College Football Highlights on ESPN</li>";
	    templateList = templateList + "<li class=\"unchanged\">EPL highlights on theworldgame.com.au</li>";
	    templateList = templateList + "<li class=\"unchanged\">Serie A highlights on theworldgame.com.au</li>";
	    templateList = templateList + "<li class=\"unchanged\">A-League Highlights on foxsports.com.au</li>";
			
		$("#myList").html(templateList);

    	// have to unbinde the old dblclick and then rebind the new after changing the list
		resetClicks();	    	    		  		
    		
    });
    
    $("#loadMyList").click(function () {
		$.ajax({
			type: "GET",
			url: "retrieveSortableList.php",
			success: function(loadList){
				
				// comma separate list of names that need to be set into LI.
				var listArray = loadList.split(",");
				var loadListHTML = '';
				
				for(var i=0; i<listArray.length; i++){
				  loadListHTML = loadListHTML + "<li class=\"unchanged\">" + listArray[i] + "</li>";
				}				
				
				$("#myList").html(loadListHTML);
		    	// have to unbinde the old dblclick and then rebind the new
		    	
		    	// have to unbinde the old dblclick and then rebind the new after changing the list
		    	resetClicks();
				
		    }				  
		});	    		
    });
    
    $("#saveMyList").click(function () {
    		
		saveMyList();		
    		
    });
    
    $("#showUnchangedMyLists").click(function () {

		 var listType = ".unchanged";
		 showLists(listType);
	
    });

    $("#showAddedMyLists").click(function () {
		 var listType = ".added";
		 showLists(listType);  	
    });

    $("#showChangedMyLists").click(function () {
		 var listType = ".changed";
		 showLists(listType);  	
    });

    
  });
  </script>

</head>
<body>
  <style>
  	ul { list-style: none; }
	li { background: #727EA3; color: #FFF; width: 300px; margin: 5px; font-size: 10px; font-family: Arial; padding: 3px; }
	
	.unchanged {background: green;}
	.changed {background: yellow; color: black;}
	.added {background: red;}	
	
</style>



<ul id="myMenu" class="contextMenu">
    <li class="edit">
        <a href="#edit">Edit</a>
    </li>
    <li class="add">
        <a href="#add">Add</a>
    </li>
    <li class="save">
        <a href="#save">Save</a>
    </li>
    <li class="delete">
        <a href="#delete">Delete</a>
    </li>
    <li class="showUnchanged">
        <a href="#showUnchanged">Show Unchanged</a>
    </li>
    <li class="showAdded">
        <a href="#showAdded">Show Added</a>
    </li>    
    <li class="showChanged">
        <a href="#showChanged">Show Changed</a>
    </li>                
    <li class="clear">
        <a href="#clear">Clear List</a>
    </li>    
    <li class="quit separator">
        <a href="#quit">Quit</a>
    </li>
</ul>

<div id=explain class=explain>
</div>


<input id=newListItem value=test type=text style="width:300 ; width: 300px; margin: 5px; font-size: 10px; font-family: Arial; padding: 3px;"> </input>
<input id=addToMyList type=submit value=Add>
<input id=loadMyList type=submit value=Load>
<input id=saveMyList type=submit value=Save>
<input id=clearMyList type=submit value="Clear List">
<input id=templateMyList type=submit value=" Load Template">
<input id=showUnchangedMyLists type=submit value="Show Unchanged">
<input id=showAddedMyLists type=submit value="Show Added">
<input id=showChangedMyLists type=submit value="Show Changed">
  
<ul id="myList">

</ul>




</body>
</html>