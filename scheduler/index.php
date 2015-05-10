<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<title>Scheduler</title>
</head>

<body bgcolor="#FFFF8F">

<? include "../sooth.inc"; ?>
<? include "../connect.php"; ?>

<?
$show_only_day = $_GET['day'];
if ($show_only_day == "") {
?>

<table border=0>
  <tr>
    <td>
      <h1 align="center">EK Metalsmiths' Guild Scheduler</h1>
    </td>
    <td>
      <a href="../index.html">
  <img width="33" height="50" src="../device/ekmg.gif" alt="EK Metalsmiths' Guild Shield" />
  <br />
  (BACK)
      </a>
    </td>
  </tr>
</table>

<font size=4>
  This page is to allow people to sign up for teaching classes
  at a symposium or a schola, and similarly to schedule themselves
  for taking classes.  Questions, suggestions, or problems using this
  tool should be directed to the maintainer, Lord Corwyn Ravenwing,
  who can be reached at
  <a href="mailto:webminister@ekmg.eastkingdom.org">webminister@ekmg.eastkingdom.org</a>.
</font>

<br /><br />

<?
} // endif $show_only_day

$magic = $_GET['magic'];
if ($magic == "elfin") {
  $user = "admin";
} else {
  $user = "";
}

$event = $_GET['event'];
# and sooth it here
if ($event == "") {
  // no event chosen: show list
  ?>

<center>

<h2>Events whose classes are scheduled using this tool:</h2>

<table>
  <tr bgcolor="silver">
    <td> <b>Event</b> </td>
    <td> <b>Description</b> </td>
    <!-- <td> <b>Owner</b> </td> -->
    <td> <b>Location</b> </td>
    <td> <b>Status</b> </td>
    <td> <b>Can Teachers<br />Create Classes?</b> </td>
    <td> <b>Can Students<br />Join Classes?</b> </td>
    <!-- <td> <b>EK Event</b> </td> -->
  </tr>
  <?
  $sql = " SELECT * FROM events ";

  echo "<!-- SQL:\n$sql\n-->\n";
  $qEvent = mysql_query($sql) or die(mysql_error());
  while ($rEvent = mysql_fetch_array($qEvent)) {
      ?>
  <tr>
      <td align="center">
      <b><?=$rEvent['event_short']?></b>&nbsp;<a href="?event=<?=$rEvent['event_code']?>">(details)</a>
    </td>
    <td><?=$rEvent['event_long']?></td>
    <!-- <td><?=$rEvent['owner']?></td> -->
    <td><?=$rEvent['location']?></td>
    <td><?=$rEvent['status']?></td>
    <td><? if ($rEvent['add_class_p']) { echo "Yes"; } else { echo "No"; } ?></td>
    <td><? if ($rEvent['add_student_p']) { echo "Yes"; } else { echo "No"; } ?></td>
    <!-- <td><?=$rEvent['ek_eid']?></td> -->
  </tr>
    <?
  } // wend rEvent
  ?>
</table>

<br /><br />

<font size="4">If you would like to use this tool to schedule classes for an upcoming event, please contact the Webminister.</font>

<!-- should be automated -->

</center>

  <?
} else {
  // an event chosen: show only this event

  $sql = " SELECT * FROM events WHERE event_code = '$event' ";
  $qEvent = mysql_query($sql) or die(mysql_error());
  if ($rEvent = mysql_fetch_array($qEvent)) {
    // event found: display it

    $event_id    = $rEvent['event_id'];
    $event_short  = $rEvent['event_short'];
    $event_long    = $rEvent['event_long'];
    $owner      = $rEvent['owner'];
    $owner_email  = $rEvent['owner_email'];
    $location    = $rEvent['location'];
    $status      = $rEvent['status'];
    $ek_eid      = $rEvent['ek_eid'];
  ?>

<h1 align="center"><?=strtoupper($event_long)?> CLASSES</h1>

<? if ($show_only_day == "") { ?>

<? if ($rEvent['add_student_p']) { ?>

<h2>Sign up to take a class!</h2>

If you see a class (or several classes) that you'd like to take in the list below, please

<!-- not possible yet
<a href="join_class.html" target="_blank">click here</a>
-->
send an email to the chancellors at <? email( $rEvent['owner_email'] ); ?>

to register for them.  But make sure to send in your reservation for the event itself
as soon as possible: unfortunately signing up for a class doesn't automatically reserve
space for you at the event!
<? if ($ek_eid) { ?>
  <br />
  See the official
  <a href="http://eastkingdom.org/event-detail.html?eid=<?=$ek_eid?>" target="_new">event announcement</a>
  for more information about the event itself.
<? } ?>

<? } else { ?>

<h2>Sign up to take a class!</h2>

It's too late to sign up on-line to take classes: <b>be sure to come to the Class Coordinator table when you get to the event to see what changes have been made that aren't shown on this page,</b> as well as to sign up for classes.

<? } // endif add_student_p ?>

<? if ($rEvent['add_class_p']) { ?>

<h2>Volunteer to teach a class!</h2>

If you are willing to teach any subject relevant to the event theme, please
<a href="new_class.php?event=<?=$event?>" target="_blank">click here</a> and take a moment to fill out the form to
register your class.

<? } else { ?>

<h2>Volunteer to teach a class!</h2>

It's too late to sign up on-line to teach classes: come to the Class Coordinator table when you get to the event and sign up there.

<? } // endif add_class_p ?>

<h3>Please note:<br/>
* More classes will be added Friday night as people arrive and volunteer to teach something.<br />
* If you would like any changes to a class you are scheduled to teach, please contact us and we will correct it as soon as possible.<br />
* Teachers, please consider bringing extra handouts for people who want to take both your course and another that conflicts with it.<br />
* Also, teachers, if your class is scheduled opposite a class you would like to take, please tell us as soon as possible and we'll try to adjust it.<br />
* Anyone can sign up for classes, naturally, but please be aware that teachers will be given scheduling priority for classes they want to take.<br />
* Teachers, if you are NOT signed up for feast, and you plan on doing so, please drop the event planners a note right away so there are no surprises.<br />
* See you there!
</h3>

<h2>Class List section</h2>

<? } // endif show_only_day ?>

<? if (! $show_only_day) { ?>

<table border="1" width="100%">
  <tr>
    <td align="center" width="100"><b>Class Name</b></td>
    <td align="center"><b>Level</b></td>
    <td align="center" width="100"><b>Teacher</b></td>
    <td align="center"><b>Length</b></td>
    <td align="center"><b>Limit</b></td>
    <td align="center"><b>Cost</b></td>
    <td align="center"><b>Location</b></td>
    <td align="center"><b>Day/Time</b></td>
  </tr>
<?
$sql = " SELECT * FROM class WHERE event_id = '$event_id' ORDER BY class_name, description ";
echo "<!-- SQL:\n$sql\n-->\n";
$qClass = mysql_query($sql) or die(mysql_error());
while ($rClass = mysql_fetch_array($qClass)) {
  $locations_list = "";
  $datetimes_list = "";
  $sql2 = "SELECT * FROM schedule
        LEFT JOIN days ON (schedule.day=days.day_num)
        LEFT JOIN timeslots ON (schedule.day=timeslots.day_num
          AND schedule.start_time=timeslots.start_time)
      WHERE schedule.event_id = '$event_id'
        AND days.event_id = '$event_id'
        AND timeslots.event_id = '$event_id'
        AND class_short_name = '$rClass[class_name]'
      ";
  echo "<!-- SQL2:\n$sql2\n-->\n";
  $qSched = mysql_query($sql2) or die(mysql_error());
  while ($rSched = mysql_fetch_array($qSched) ) {
    if ($locations_list != "") { $locations_list .= "<br />\n"; }
    if ($datetimes_list != "") { $datetimes_list .= "<br />\n"; }
    $locations_list .= str_replace(" ", "&nbsp;", $rSched['location'] );
    $datetimes_list .= str_replace(" ", "&nbsp;", $rSched['day_abbr'] . " " . $rSched['english'] );

    if ($user == "admin") {
      $datetimes_list .= "&nbsp;<a href='class_sheets.php"
        . "?event=$event"
        . "&class_id=$rClass[class_id]"
        . "&sched_id=$rSched[schedule_id]' "
        . "target='_blank'>"
        . "<font size='-2'>"
        . "(*)"
        . "</font>"
        . "</a>\n";
    } // endif $user
  } // wend rSched

  if ($locations_list == "") { $locations_list .= "--\n"; }
  if ($datetimes_list == "") { $datetimes_list .= "--\n"; }
    ?>
  <a name="class_<?=$rClass['class_id']?>" />
  <tr>
    <td align="center"><b>          <?=$rClass['class_name']?> </b>&nbsp;</td>
    <td align="center">             <?=$rClass['level']?>          &nbsp;</td>
    <td align="center">             <?=$rClass['teacher_name']?>   &nbsp;</td>
    <td rowspan="2" align="center"> <?=$rClass['class_length']?>   &nbsp;</td>
    <td rowspan="2" align="center"> <?=$rClass['class_limit']?>    &nbsp;</td>
    <td rowspan="2" align="center"> <?=$rClass['cost']?>           &nbsp;</td>
    <td rowspan="2" align="center"> <?=$locations_list?>           &nbsp;</td>
    <td rowspan="2" align="center"> <?=$datetimes_list?>           &nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" align="left">
    <table>
      <tr>
        <td align="right" valign="top" width="175"><b>Description:</b></td>
      <td align="left" valign="top"><?=$rClass['description']?></td>
      </tr>
  <? if ($rClass['bring_with']) { ?>
      <tr>
      <td align="right" valign="top" width="175"><b>Bring with you to class:</b></td>
      <td align="left" valign="top"><?=$rClass['bring_with']?></td>
      </tr>
  <? } ?>
    </table>
    </td>
  </tr>
    <?
} // wend $rClass
?>
<? if ($user == "admin") { ?>
  <tr>
  <td align="center">
    <a href="class_sheets.html?event=<?=$event?>&class_id=<?=$rClass['class_id']?>" target="_blank">
      <font size="-2">Blank&nbsp;Signup&nbsp;Sheet</font>
    </a>
  </td>
  <td align="center">
    <a href="blank_schedule.html?event=<?=$event?>" target="_blank">
      <font size="-2">Blank&nbsp;Schedule&nbsp;Sheet</font>
    </a>
  </td>
  <td align="center">
    <a href="?event=<?=$event?>&day=0" target="_blank">
      <font size="-2">Class&nbsp;List&nbsp;Only</font>
    </a>
  </td>
  <td colspan="6">
    <!-- other admin-only links go here -->&nbsp;
  </td>
  </tr>
<? } // endif $user ?>
</table>

<? } // endif $show_only_day ?>

<? if ($show_only_day == "") { ?>
<h2>Class Schedule section</h2>

<!--
Some classes have been planned but don't yet appear on the schedule.  We'll be adding them as the schedule shakes itself out.

<br />
-->
<? } // endif $show_only_day ?>

<?
$locations = 0;
$sql = "SELECT * FROM locations WHERE event_id = '$event_id' ";
echo "<!-- SQL:\n$sql\n-->\n";
$qLoc = mysql_query($sql) or die(mysql_error());
while ($rLoc = mysql_fetch_array($qLoc)) {
    $locations++;
    $loc_label[$locations] = $rLoc['location'];
} // wend $rLoc

if (! $locations) {
  // no list of locations means no schedule.
  ?>
  <h3>No schedule has been created yet for the preceeding classes.  Please come back when the schedule shakes itself out.  Thanks.</h3>
  <?
} else {
  // show the schedule
  ?>

<TABLE BORDER="0" WIDTH="100%">
  <TR>
    <TD>

<?
$sql = "SELECT * FROM days WHERE event_id = '$event_id' ";
if ($show_only_day != "") { $sql .= " AND day_num = '$show_only_day' "; }
echo "<!-- SQL:\n$sql\n-->\n";
$qDays = mysql_query($sql) or die(mysql_error());
while($rDays = mysql_fetch_array($qDays)) {
    $timeslots = 0;
    $all_loc = "";
    $sql = "SELECT * FROM timeslots
      WHERE event_id = '$event_id'
      AND day_num = $rDays[day_num] ";
    echo "<!-- SQL:\n$sql\n-->\n";
    $qTime = mysql_query($sql) or die(mysql_error());
    while ($rTime = mysql_fetch_array($qTime)) {
        $timeslots++;
        $time_number[$timeslots] = $rTime['start_time'];
        $time_label[$timeslots]  = $rTime['english'];
        $sql = "SELECT * FROM schedule
        WHERE event_id = '$event_id'
                   AND day = $rDays[day_num]
                   AND start_time = $rTime[start_time]
                   AND location = '*' ";
        $qAllLoc = mysql_query($sql) or die(mysql_error());
        if ($rAllLoc = mysql_fetch_array($qAllLoc)) {
            $all_loc[$timeslots] = $rAllLoc['class_short_name'];
        } else {
            $all_loc[$timeslots] = "";
        }
    } // wend $rTime
    ?>
<table border="1" align="center">
  <tr>
    <td colspan="<?=$timeslots+1?>" height="40" align="center">
    <h2>
  <? if (! $show_only_day) { ?>
      <a href="?event=<?=$event?>&day=<?=$rDays['day_num']?>" target="_blank">
  <? } ?>
      <?=$rDays['day_long']?>
  <? if (! $show_only_day) { ?>
      </a>
  <? } ?>
    </h2>
  </td>
  </tr>
  <tr>
    <td width="75" align="center">&nbsp;  </td>
    <?
    for($j=1; $j<=$timeslots; $j++) {
        ?>
    <td width="75" align="center"><b><?=$time_label[$j]?></b></td>
        <?
    } // next $j
    ?>
  </tr>
    <?
    for($i=1; $i<=$locations; $i++) {
        ?>
  <tr>
    <td align="center"><b><?=$loc_label[$i]?></b></td>
        <?
        for($j=1; $j<=$timeslots; $j++) {
      ?>
    <!-- <?=$rDays['day_abbr']?> <?=$time_label[$j]?> / <?=$loc_label[$i]?> -->
            <?
      if ($all_loc[$j]) {
          if ($all_loc[$j] == "*") {
        echo "<!-- all-location event: skip td -->";
    } else {
        ?>
    <td rowspan='<?=$locations?>' align="center"><?=$all_loc[$j]?></td>
                    <?
        $all_loc[$j] = "*";
                }
      } else {
                $sql = "SELECT * FROM schedule
            WHERE event_id = '$event_id'
                           AND day = $rDays[day_num]
                           AND start_time = $time_number[$j]
                           AND location = '$loc_label[$i]' ";
                echo "<!-- SQL:\n$sql\n-->\n";
                $qSched = mysql_query($sql) or die(mysql_error());
                if ($rSched = mysql_fetch_array($qSched)) {
        $len = $rSched['class_length'];
        if ($len <= 0) { echo "<!-- replacing zero len '$len' -->\n"; $len = 1; }
              ?>
    <td colspan='<?=$len?>' align="center"><?=$rSched['class_short_name']?></td>
                    <?
        if ($len > 1) { echo "<!-- skipping $len column -->\n"; $j += $len - 1; }
    } else {
        echo "<td>&nbsp;</td>\n";
    } // endif $rSched
      } // endif $all_loc
        } // next $j
        ?>
  </tr>
        <?
    } // next $i
    ?>
</table>

    </TD>
  </TR>

    <?
} // wend $rDays
?>

</TABLE>

  <?
} // endif locations

  } else {
    // event not found
    echo "<h2>Error: no event named '$event' found in the database.  Please contact the Webminister about this error.</h2>\n";
  } // endif rEvent

} // endif event blank
?>

</body>
</html>