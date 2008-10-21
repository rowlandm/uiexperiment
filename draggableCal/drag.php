<?php
require_once('time.lib.php');
$timeslotArray = timeslotArray("08:00","13:30","15");
?>
<!-- ORGINAL: http://pastebin.me/4878f6c4784a4 -->

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>


<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Selectables demo - based on Interface demo</title>
<script src="jquery-1.2.6.js"></script>
<script src="jquery-ui-personalized-1.6rc2.min.js"></script>
<style type="text/css" media="all">
*
{
	margin: 0;
	padding: 0;
}
html{
	height: 100%;
}
body
{
	height: 100%;
}

.selectable
{
	margin-left:50px;
	width:90%;
	height:100%;
}

.selectableItem
{
	display: block;
	width: 140px !important;
	width /**/: 160px;
	padding: 0 10px;
	line-height: 24px;
	height: 24px;
	position: absolute;
	background-color: #CFD499;
	font-weight: bold;
}

.timeLbl
{
	display: block;
	width: 140px !important;
	width /**/: 160px;
	padding: 0 10px;
	line-height: 24px;
	height: 24px;
	position: absolute;
	background-color: #CFD4E6 ;
	font-weight: bold;
}

.ui-selected
{
	background-color: #727EA3;
	font-weight: none;
}
.ui-draggable-dragging {
        cursor: move;
}


</style>
</head><body>
<?php
$n=20;
foreach($timeslotArray as $value){
?>
		 <div style="top:<?php echo $n?>px;left: 50px;" class="timeLbl"><?php echo $value;?></div>
<?php
	$n=$n+30;
	}
?>
	<div style="" class="selectable" id="selectable1">
<?php	
$n=20;
foreach($timeslotArray as $value){
?>
		 <div style="top:<?php echo $n?>px;left: 220px;" class="selectableItem"><?php echo $value;?></div>
<?php
	$n=$n+30;
	}
?>    
</div>

<script type="text/javascript">
var initPos = false;
var collection = false;
$(document).ready(
	function()
	{
		$('#selectable1').selectable(
			{
				
			}
		);
		$('div.selectableItem').draggable(
                    {
                        helper: 'original',
                       start: function(e, ui) {
                           if ($(this).is('div.ui-selected')) {
                                   initPos = {x: parseInt(this.style.left,10),y:parseInt(this.style.top,10)};
                                   collection = jQuery('div.ui-selected:visible').not(this);
                                   if (collection.size() == 0) {
                                           initPos = false;
                                           collection = false;
                                   }
                           }
                       },
                       drag: function(e, ui) {
                               if(collection) {
                                   var x = ui.position.left;
                                   var y = ui.position.top;
                                   collection.each(
                                       function() {
                                           this.style.left = parseInt(this.style.left,10)+ x - initPos.x + 'px';
                                           this.style.top = parseInt(this.style.top,10)+y - initPos.y + 'px';
                                           }
                                   );
                                   initPos = {x: x, y:y};
                               }
                       },
                       stop : function(e,ui)
                       {
                           initPos = false;
                           collection = false;
                    
                       }
                    }
		);
	}
);


</script>
</body></html>