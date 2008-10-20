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
    $("#timeslots0").selectable();
  });
  </script>
<?php
}
?>
  

<style>
.blk {background: black; width: 100px;  font-size: 10px; font-family: Arial;color:white;margin-left:2px;padding:1px }
.ui-selected { background: #CFD499; color: black;}
.ui-selecting { background: #727EA3;margin:0px;}
td {background: #CFD4E6; width: 100px;  font-size: 10px; font-family: Arial;}
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
<script src="jquery-ui-personalized-1.6rc2.min.js"></script>

<div style='display: table-cell'>

		<table id="timeslotsLbl">
		<?php
		foreach($timeslotArray as $value){
		?>
			 <tr><td style='background:#CCCCCC;color:black;'><?php echo $value;?></td></tr>
		<?php
		}
		?>
		</table>

</div>
<div style='display: table-cell'>		
		
		<table class="ava" id="timeslots0">
		<?php
		$nBlocked=0;
		$timeslotArray = timeslotArray("08:00","13:30","15","24");
		foreach($timeslotArray as $value){
			if(in_array($value, $blockedArray)){
			$nBlocked++;
		?>
			</table>
			<div class='blk'><?php echo $value;?></div>
			<table class="ava" id="timeslots<?php echo $nBlocked;?>">
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
	var idFirst="";
	var count = 0;
	var o = document.getElementById(tableName).getElementsByTagName("td");
	for(var i=0;i<o.length;i++){
	    if(o[i].className == "ui-selectee "+className){
			if(idFirst==""){
				idFirst = o[i];
			}else{
				document.getElementById('tr'+o[i].id).deleteCell(o[i].cellIndex);
				//alert(o[i].cellIndex);
			}
	        count ++;
		}
	}
	idFirst.rowSpan=count;
	idFirst.style.height="400px";
	//alert(idFirst.rowSpan);
}
</script>
		
<input type="button" onclick="merge('timeslots0','ui-selected')">
</body>
</html>