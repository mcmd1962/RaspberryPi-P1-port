<?PHP 
header('Content-Type: text/html; charset=utf-8');
$rConnect = mysql_connect('x.x.x.x', '*username*', '*password*');
$rDatabase = mysql_select_db('*database*');


$rDayGas = mysql_query("SELECT * FROM gas") or die(mysql_error());
$rTS = mysql_query("SELECT TIMEDIFF(NOW(), UTC_TIMESTAMP) as ts");
$aTS = mysql_fetch_assoc($rTS);

/* Make sure all missing hours are shows with zero's */
$aPrevGas = Array();
$sJson = '[';
while($aDayGas = mysql_fetch_assoc($rDayGas)) {
	if(count($aPrevGas) > 1) {
		$iDay = 0;
		$bDaySet = false;
		$iDatePrev = $aPrevGas['datetime']/3600;
		$iDateCur = $aDayGas['datetime']/3600;
		$iHourPrev = $aPrevGas['hour'];
		
		$iMissingHours = $iDateCur-$iDatePrev;
		$iHourNew = $iHourPrev;		

		// $aDate = new DateTime(date("Y-m-d H:i:s", $aDayGas['datetime']-(($aTS['ts']+1)*3600)), new DateTimeZone(date_default_timezone_get()));
		// if($aDate->format('I') == 0) {
			// $aDayGas['hour'] -= $aTS['ts'];
			// $aDayGas['datetime'] -= $aTS['ts']*3600;
		// }

		if($iMissingHours > 1) {
			for($i=0;$i<$iMissingHours-1;$i++) {
				if((++$iHourNew) >= 24) {
					$iHourPrev -= ($iHourNew-1);
					$iHourNew = 0;
					$iDay++;
				}
				$iDT = (($iDatePrev*3600)+($iDay*3600)+(($iHourNew-$iHourPrev)*3600));
				if(date("H", $iDT) > 23 || date("H", $iDT) <= 7) {
					$sJson .= '{"x": '.$iDT.'000, "y": 0, "color": "#2f7ed8"},';
				} else {
					$sJson .= '{"x": '.$iDT.'000, "y": 0, "color": "#ffc600"},';
				}
			}
		}

		if($aDayGas['hour'] > 23 || $aDayGas['hour'] <= 7) {
			$sJson .= '{"x": '.$aDayGas['datetime'].'000, "y": '.$aDayGas['m3'].', "color": "#2f7ed8"},';
		} else {
			$sJson .= '{"x": '.$aDayGas['datetime'].'000, "y": '.$aDayGas['m3'].', "color": "#ffc600"},';
		}
	}
	$aPrevGas = $aDayGas;
}
$sJson = substr($sJson, 0, -1);
echo $sJson .= ']';
?>
