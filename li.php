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

  <script>
  $(document).ready(function(){
    $("#timeslots").selectable();
  });
  </script>

<style>
ul { list-style: none; margin:0px; padding:0px;}
.ui-selected { background: #727EA3; color: #FFF; border-bottom: 2px solid #727EA3;}
.ui-selecting { background: #CFD499; }
li {border-bottom: 2px solid black;background: #CFD4E6; width: 100px; margin-top:0px; font-size: 10px; font-family: Arial; padding-top: 3px; }
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

<script src="jquery-ui-personalized-1.6rc2.min.js"></script>
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
	    <script>
	    $(document).ready(function(){
	      $("#timeslots<?php echo $nBlocked;?>").selectable();
	    });
	    </script>

		</ul>
		<ul><li style='background:black;color:white;'><?php echo $value;?></li></ul>
		<ul id="timeslots<?php echo $nBlocked;?>">
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