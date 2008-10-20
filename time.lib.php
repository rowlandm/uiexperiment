<?php
/**
 * Generate an array of all timeslots in a given time, for a selectable timeslot length
 *
 * @param string $startTime  24hr time
 * @param string $stopTime 24hr time
 * @param int $minIncrease
 * @return array
 */
function timeslotArray($startTime,$stopTime,$minIncrease,$format="12")
{
	$timeslotArray = array();
	$startTime=explode(":",$startTime);
	$n=1;
	while($stopTime!=date("H:i", mktime($startTime[0], $startTime[1]+$n*$minIncrease-$minIncrease, 0, 1, 1, 2000)))
	{
		if($format=="12"){
			$timeslotArray[] = date("h:ia", mktime($startTime[0], $startTime[1]+$n*$minIncrease, 0, 1, 1, 2000));
		}else{
			$timeslotArray[] = date("H:i", mktime($startTime[0], $startTime[1]+$n*$minIncrease, 0, 1, 1, 2000));
		}
		$n++;
	}
	return $timeslotArray;
}
?>