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
	<meta http-equiv="pragma" conent="no-cache">
	<script src="jquery-1.2.6.js"></script>
	<script>
	function commentToDiv(IdFirstID){
		txt = document.getElementById('txt'+IdFirstID);
		div = document.getElementById('div'+IdFirstID);
		div.innerHTML=txt.value;
		div.style.display="block";
		txt.style.display="none";
	} 
	</script>
<?php  
$blkCount=0;
foreach($timeslotArray as $key => $value){
?>
  <script>
  $(document).ready(function(){
	
    $("#timeslots<?php echo $blkCount;?>").selectable({
    		stop: function(e, ui) {
    		merge('timeslots<?php echo $blkCount;?>','ui-selected')
		}
	});
  });
  </script>
<?php
$blkCount++;
}

if($blkCount==0){
?>
  <script>
  $(document).ready(function(){
		
	    $("#timeslots0").selectable({
	    		stop: function(e, ui) {
	    		merge('timeslots0','ui-selected')
			}
		});
	  });
  </script>
<?php
}
?>
  

<style>
.blk {background: black; width: 100px;  font-size: 12px; font-family: Arial;color:white;margin-left:1px;padding:1px }
.ui-selected { background: #CFD499; color: black;}
.ui-selecting { background: #727EA3;}
td {background: #CFD4E6; width: 100px;  font-size: 12px; font-family: Arial;}
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
					<td id='<?php echo $value;?>'>
						<div id='div<?php echo $value;?>' style='width:99px;height:99%;overflow:hidden;'>
							<?php echo $value;?>
						</div>
						<textarea id='txt<?php echo $value;?>' style='width:96%;height:90%;background-color:transparent;display:none;color:white;' onchange="commentToDiv('<?php echo $value;?>');"></textarea>
					</td>
				</tr>
		<?php
			}
		}
		?>
		</table>
</div>

<script>
function merge(tableName,className){
	var fail;
	var spanedHeight = 0;
	var toBeDel = [];
	var idFirst="";
	var count = 0;
	var o = document.getElementById(tableName).getElementsByTagName("td");
	
	for(var i=0;i<o.length;i++){
    	//alert(o[i].id+': '+o[i].className);
    	if(o[i].className == "blk" || o[i].className == "blk ui-selected"){
			fail = "yes";//as 'blk' class found, cancel action
			i=o.length;//prevent upcoming loops
    	}else if(o[i].className == "ui-selectee "+className){
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
	if(fail!="yes"){
		for(var i=0;i<toBeDel.length;i++){
			document.getElementById('tr'+toBeDel[i]).deleteCell(document.getElementById(toBeDel[i]).cellIndex);
		}
		idFirstID=idFirst.id;
		idFirst.rowSpan=count;
		spanedHeight = spanedHeight+count-1;
		idFirst.height=spanedHeight+'px';
		//alert(idFirst.className);
		idFirst.setAttribute("class", "blk"); 
		//alert(idFirst.className);
		idFirst.style.padding='0px';
	
		document.getElementById('div'+idFirstID).setAttribute("class", "blk");
		document.getElementById('div'+idFirstID).innerHTML=""//clear any current booked times
		document.getElementById('txt'+idFirstID).style.display='block';
		document.getElementById('div'+idFirstID).style.display='none';
	}else{
		alert('Sorry, one or more timeslots already booked');
	}
}
</script>
		
</body>
</html>