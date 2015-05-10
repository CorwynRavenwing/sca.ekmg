<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<title>Schedule CSV file</title>
</head>

<body bgcolor="#FFFF8F">

<? include "../sooth.inc"; ?>
<? include "../connect.php"; ?>

<?
$event = $_GET['event'];
# and sooth it here
if ($event == "") {
	// no event chosen: show error
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
		$ek_eid			= $rEvent['ek_eid'];
	?>

<h1 align="center"><?=strtoupper($event_long)?> CLASSES</h1>

<h2>Schedule CSV file</h2>

# event_id, class_short_name, day, start_time, length, location, comments<br />

<?
$sql = " SELECT * FROM class WHERE event_id = '$event_id' ORDER BY class_name, description ";
echo "<!-- SQL:\n$sql\n-->\n";
$qClass = mysql_query($sql) or die(mysql_error());
while ($rClass = mysql_fetch_array($qClass)) {
    ?>
<?=$event_id?>,
"<?=$rClass['class_name']?>",
DAY,
TIME, 
<?=$rClass['class_length']?>,
LOCATION,
""
<br />
    <?
} // wend $rClass

	} else {
		// event not found
		echo "<h2>Error: no event named '$event' found in the database.  Please contact the Webminister about this error.</h2>\n";
	} // endif rEvent

} // endif event blank
?>

</body>
</html>
