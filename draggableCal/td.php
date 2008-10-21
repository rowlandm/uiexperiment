<?php
require_once('time.lib.php');
$timeslotArray = timeslotArray("08:00","13:30","15");

//temp, 2b filled from db
$blockedArray = array("10:30","13:00")
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
                    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <script src="jquery-1.2.6.js"></script>

<?php  
$blkCount=0;
foreach($timeslotArray as $key => $value){
?>
  <script>
  $(document).ready(function(){
    $("#timeslots<?php echo $blkCount;?>").selectable();
  });
  </script>
<?php
$blkCount++;
}

if($blkCount==0){
?>
  <script>
	$(document).ready(function(){          
       	$('td').selectable({                   	
        	stop: function(e,ui){
				alert('edfefe');
            }
       	};        
	};
  </script>
<?php
}
?>
  

<style>
.blk {background: black; width: 100px;  font-size: 10px; font-family: Arial;color:white;margin-left:1px;padding:1px }
.ui-selected { background: #CFD499; color: black;}
.ui-selecting { background: #727EA3;}
td {background: #CFD4E6; width: 100px;  font-size: 10px; font-family: Arial;}
</style>

</head>
<body>
<script src="jquery-ui-personalized-1.6rc2.min.js"></script>

<div style='display: table-cell;'>
&nbsp;
		<table id="timeslotsLbl" cellspacing='1px' cellpadding='1'>
		<?php
		foreach($timeslotArray as $value){
		?>
			 <tr><td style='background:#CCCCCC;color:black;'><?php echo $value;?></td></tr>
		<?php
		}
		?>
		</table>

</div>
<div style='display: table-cell;'>		
&nbsp;
		<table class="ava" id="timeslots0" cellspacing='1px' cellpadding='1'>
		<?php
		$nBlocked=0;
		$timeslotArray = timeslotArray("08:00","13:30","15","24");
		foreach($timeslotArray as $value){
			if(in_array($value, $blockedArray)){
			$nBlocked++;
		?>
			</table>
			<div class='blk'><?php echo $value;?></div>
			<table class="ava" id="timeslots<?php echo $nBlocked;?>" cellspacing='1px' cellpadding='1'>
		<?php
			}else{
		?>
				<tr id='tr<?php echo $value;?>'>
					<td id='<?php echo $value;?>'><?php echo $value;?></td>
				</tr>
		<?php
			}
		}
		?>
		</table>
</div>

<script>
function merge(tableName,className){
	var spanedHeight = 0;
	var toBeDel = [];
	var idFirst="";
	var count = 0;
	var o = document.getElementById(tableName).getElementsByTagName("td");
	
	for(var i=0;i<o.length;i++){
    	//alert(o[i].id+': '+o[i].className);
	    if(o[i].className == "ui-selectee "+className){
			if(idFirst==""){
				idFirst = o[i];
				spanedHeight = spanedHeight + o[i].offsetHeight;
			}else{
				toBeDel[toBeDel.length] = o[i].id;
				spanedHeight = spanedHeight + o[i].offsetHeight;
			}
	        count ++;
		}
	}

	for(var i=0;i<toBeDel.length;i++){
		document.getElementById('tr'+toBeDel[i]).deleteCell(document.getElementById(toBeDel[i]).cellIndex);
	}
	idFirst.rowSpan=count;
	spanedHeight = spanedHeight+count-1;
	idFirst.height=spanedHeight+'px';
	idFirst.style.padding='0px';
	
	new_element = document.createElement("textarea");
	new_element.setAttribute("type", "text");
	new_element.setAttribute("name", "element_name");
	new_element.setAttribute("id", "element_id");
	new_element.setAttribute("value", "element_value");
	new_element.setAttribute("style", "width:90%");
	idFirst.appendChild(new_element); 
	
}
</script>
		
<input type="button" onclick="merge('timeslots0','ui-selected')">
</body>
</html>