<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<title>Class Sheets</title>
</head>

<body bgcolor="#FFFF8F">

<? include "../sooth.inc"; ?>
<? include "../connect.php"; ?>

<?
$event = $_GET['event'];
if ($event == "") {
	// no event chosen: show error
	?>
<center>
<h2>Sorry, you shouldn't have gotten here without an event id.</h2>
	<?
	exit;
} else {
	// an event chosen: show only this event
		
	$sql = " SELECT * FROM events WHERE event_code = '$event' ";
	$qEvent = mysql_query($sql) or die(mysql_error());
	if ($rEvent = mysql_fetch_array($qEvent)) {
		// event found: display it

		$event_id		= $rEvent['event_id'];
		$event_short	= $rEvent['event_short'];
		$event_long		= $rEvent['event_long'];
		$owner			= $rEvent['owner'];
		$location		= $rEvent['location'];
		$status			= $rEvent['status'];

		$class_id = $_GET['class_id'];
		$sched_id = $_GET['sched_id'];
		
$sql = " SELECT * FROM class WHERE event_id = '$event_id' and class_id = '$class_id' ";
echo "<!-- SQL:\n$sql\n-->\n";
$qClass = mysql_query($sql) or die(mysql_error());
$rClass = mysql_fetch_array($qClass);	// unconditional: neither if() nor while() here

$locations_list = "";
$datetimes_list = "";
$sql2 = "SELECT * FROM schedule
			LEFT JOIN days ON (schedule.day=days.day_num)
			LEFT JOIN timeslots ON (schedule.day=timeslots.day_num
				AND schedule.start_time=timeslots.start_time)
		WHERE schedule.event_id = '$event_id'
			AND schedule.schedule_id = '$sched_id'
			AND days.event_id = '$event_id'
			AND timeslots.event_id = '$event_id'
			AND class_short_name = '$rClass[class_name]'
		";
echo "<!-- SQL2:\n$sql2\n-->\n";
$qSched = mysql_query($sql2) or die(mysql_error());
while ($rSched = mysql_fetch_array($qSched) ) {
	if ($locations_list != "") { $locations_list .= "<br />\n"; }
	if ($datetimes_list != "") { $datetimes_list .= "<br />\n"; }
	$locations_list .= $rSched['location'];
	$datetimes_list .= $rSched['day_abbr'] . " " . $rSched['english'];
} // wend rSched
    ?>
<table border="0" width="100%">
  <tr>
    <td align="right" width="200">Class Name:</td>
    <td align="left"> <TABLE BORDER="1"><TR><TD WIDTH="200"> <b><?=$rClass['class_name']?></b>&nbsp; </TD></TR></TABLE> </td>
    <td align="right" width="200">Level:</td>
    <td align="left"> <TABLE BORDER="1"><TR><TD WIDTH="200"> <b><?=$rClass['level']?></b>&nbsp; </TD></TR></TABLE> </td>
    <td align="right" width="200">Teacher:</td>
    <td align="left"> <TABLE BORDER="1"><TR><TD WIDTH="200"> <b><?=$rClass['teacher_name']?></b>&nbsp; </TD></TR></TABLE> </td>
  </tr>
  <tr>
    <td align="right" width="200">Length:</td>
    <td align="left"> <TABLE BORDER="1"><TR><TD WIDTH="50"> <b><?=$rClass['class_length']?></b>&nbsp; </TD></TR></TABLE> </td>
    <td align="right" width="200">Limit:</td>
    <td align="left"> <TABLE BORDER="1"><TR><TD WIDTH="50"> <b><?=$rClass['class_limit']?></b>&nbsp; </TD></TR></TABLE> </td>
    <td align="right" width="200">Cost:</td>
    <td align="left"> <TABLE BORDER="1"><TR><TD WIDTH="50"> <b>$<?=$rClass['cost']?></b>&nbsp; </TD></TR></TABLE> </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right" width="200">Location:</td>
    <td align="left"> <TABLE BORDER="1"><TR><TD WIDTH="200"> <b><?=$locations_list?></b>&nbsp; </TD></TR></TABLE> </td>
    <td align="right" width="200">Date and Time:</td>
    <td align="left"> <TABLE BORDER="1"><TR><TD WIDTH="200"> <b><?=$datetimes_list?></b>&nbsp; </TD></TR></TABLE> </td>
  <tr>
    <td align="left" colspan="6">
		Description: <b><?=$rClass['description']?></b>
	  <? if ($rClass['bring_with']) { ?>
		<br />
		Bring with you to class: <b><?=$rClass['bring_with']?></b>
	  <? } ?>
    </td>
  </tr>
  <tr>
  	<td colspan="6">
	  <table border="1" width="100%">
		<tr>
			<td width="25" align="right">&nbsp;</td>
			<td width="300" align="center"><b>Student Name</b></td>
		</tr>
    <?
	for ($i = 1; $i<=20; $i++) {
		?>
		<tr>
			<td align="right"><b><?=$i?>.</b></td>
			<td>&nbsp;</td>
		</tr>
		<?
		if ($i == $rClass['class_limit']+0) {
			?>
		<tr>
			<td>&nbsp;</td>
			<td align="center">
				<b>--- People beyond the CLASS LIMIT are on the waiting list: please sign up below --- </b>
			</td>
		</tr>
			<?
		}
	}
	?>
		</tr>
	  </table border="1" width="100%">
<?
// neither if nor while
?>
</table>

<?

	} else {
		// event not found
		echo "<h2>Error: no event named '$event' found in the database.  Please contact the Webminister about this error.</h2>\n";
	} // endif rEvent

} // endif event blank
?>

</body>
</html>
