<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<title>Scheduler - NEW CLASS</title>
</head>

<body bgcolor="#FFFF8F">

<? include "../connect.php"; ?>

<?
$event = $_REQUEST{'event'};
if ($event == "") {
  ?>
  <h2>Error: no event code passed.  Please <a href="index.php">click here</a> to return to the scheduler menu.</h2>
  <?
  exit(0);
}

$sql = " SELECT * FROM events WHERE event_code = '$event' ";
$qEvent = mysql_query($sql) or die(mysql_error());
if (! $rEvent = mysql_fetch_array($qEvent)) {
  // event not found
  echo "<h2>Error: no event named '$event' found in the database.  Please contact the Webminister about this error.</h2>\n";
  exit(0);
} // endif rEvent

$event_id    = $rEvent['event_id'];
$event_short  = $rEvent['event_short'];
$event_long    = $rEvent['event_long'];
$owner      = $rEvent['owner'];
$owner_email  = $rEvent['owner_email'];
$location    = $rEvent['location'];
$status      = $rEvent['status'];
$add_class_p  = $rEvent['add_class_p'];
$add_student_p  = $rEvent['add_student_p'];
$ek_eid      = $rEvent['ek_eid'];

if (! $add_class_p) {
  ?>
<h3>Sorry, the deadline has passed for creating classes on-line.  You will need to sign up with the Class Coordinator when you arrive.  See you there!</h3>
  <?
  exit();
}

$process = $_POST{'process'};

if ($process) {
    ?>
<p class="justify">Thank you for contacting us.&nbsp; The class was placed into the schedule.</p>
    <?
$teacher_modern_name  = $_POST{'teacher_modern_name'};
$teacher_name      = $_POST{'teacher_name'};
$address        = $_POST{'address'};
$email          = $_POST{'email'};
$phone          = $_POST{'phone'};
$address_perm      = $_POST{'address_perm'};
$email_perm        = $_POST{'email_perm'};
$phone_perm        = $_POST{'phone_perm'};
$teacher_description  = $_POST{'teacher_description'};
$anything_else      = $_POST{'anything_else'};

$short_name        = "*NEW*";
$class_name        = $_POST{'class_name'};
$class_description    = $_POST{'class_description'};
$class_limit      = $_POST{'class_limit'};
$class_cost        = $_POST{'class_cost'};
$class_length      = $_POST{'class_length'};
$class_time        = $_POST{'class_time'};
$class_level      = $_POST{'class_level'};
$bring_with        = $_POST{'bring_with'};
$need_from_us      = $_POST{'need_from_us'};
$bring_forge      = $_POST{'bring_forge'};
$bring_books      = $_POST{'bring_books'};

// not sure which variables we need to do this to:
// $rname    = stripslashes($rname);
// $comments = stripslashes($comments);

do_query(" INSERT INTO teacher
  (event_id,teacher_name,teacher_mundane_name,address,email,phone,email_perm,address_perm,phone_perm,teacher_description,bringing_forge,bringing_books,anything_else)
  VALUES
  ('$event_id',
  '$teacher_name',
  '$teacher_modern_name',
  '$address',
  '$email',
  '$phone',
  '$email_perm',
  '$address_perm',
  '$phone_perm',
  '$teacher_description',
  '$bring_forge',
  '$bring_books',
  '$anything_else') ");

do_query(" INSERT INTO class
  (event_id,short_name,class_name,description,level,teacher_name,class_length,class_limit,cost,class_time_desired,bring_with,need_from_us)
  VALUES
  ('$event_id',
  '$short_name',
  '$class_name',
  '$class_description',
  '$class_level',
  '$teacher_name',
  '$class_length',
  '$class_limit',
  '$class_cost',
  '$class_time',
  '$bring_with',
  '$need_from_us') ");

// NB: should link on teacher_id, not teacher_name

// SHOULD send a copy of this data to $owner_email
    ?>
We will try to keep in contact with you often, and if you think of something extra that we can do, don't hesitate to ask!  The event staff is here to help!  ;-)

<a href="new_class.php?event=<?=$event?>">Schedule another class</a>
    <?
} else {
    ?>

<table border=0>
  <tr>
    <td>
      <h1 align="center">EK Metalsmiths' Guild Scheduler: NEW CLASS</h1>
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
  at a symposium or a schola.  Questions, suggestions, or problems
  using this tool should be directed to the maintainer, Lord Corwyn
  Ravenwing, who can be reached at
  <a href="mailto:webminister@ekmg.eastkingdom.org">webminister@ekmg.eastkingdom.org</a>.
</font>

<br /><br />

<p class="justify">I would like to teach the following class at
<b><?=$event_long?></b>.<br><br>
<form name="contact" method="post" action="">

<table border=0>
  <tr>
    <td align="right"><label for="teacher_modern_name">Teacher's Modern Name:&nbsp;</label></td>
    <td align="left">&nbsp;<input type="text" size="50" maxlength="100" id="teacher_modern_name" name="teacher_modern_name" value=""></td>
  </tr>

  <tr>
    <td align="right"><label for="teacher_name">Teacher's SCA Name (if applicable):&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="text" size="50" maxlength="100" id="teacher_name" name="teacher_name" value=""></td>
  </tr>

  <tr>
    <td align="right"><label for="address">Address (for the staff contact purposes only):&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="text" size="50" maxlength="255" id="address" name="address" value=""></td>
  </tr>

  <tr>
    <td align="right"><label for="email">Email (for the staff contact purposes only):&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="text" size="20" maxlength="100" id="email" name="email" value=""></td>
  </tr>

  <tr>
    <td align="right"><label for="phone">Phone (for the staff contact purposes only):&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="text" size="20" maxlength="50" id="phone" name="phone" value=""></td>
  </tr>

  <tr>
    <td align="right"><label for="permissions">Contact Info That You're Willing to Allow in a Binder so People can Find You After the Event's Over:&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>
      <input type="checkbox" id="address_perm" name="address_perm">&nbsp;Address <br />
      <input type="checkbox" id="email_perm"   name="email_perm"  >&nbsp;Email   <br />
      <input type="checkbox" id="phone_perm"   name="phone_perm"  >&nbsp;Phone   <br />
    </td>
  </tr>

  <tr>
    <td align="right"><label for="teacher_description">A brief description of you and / or your persona (optional):&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="text" size="100" maxlength="255" id="teacher_description" name="teacher_description" value=""></td>
  </tr>

  <tr>
    <td align="right"><label for="bring_forge">Are you planning to bring a forge?&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="checkbox" id="bring_forge" name="bring_forge"></td>
  </tr>

  <tr>
    <td align="right"><label for="bring_books">We will have a library for people to look at books that they might not have access to otherwise.  Would you consider bringing a few favorite books for our library to borrow?&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="checkbox" id="bring_books" name="bring_books"></td>
  </tr>

  <tr>
    <td align="right"><label for="anything_else">Anything else the Class Coordinator will need to know:&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="text" size="100" maxlength="255" id="anything_else" name="anything_else" value=""></td>
  </tr>

<hr width="2" />

  <tr>
    <td align="right"><label for="class_limit">Class Limited to How Many People?&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="text" size="3" maxlength="10" id="class_limit" name="class_limit" value=""></td>
  </tr>

  <tr>
    <td align="right"><label for="class_cost">Is there a cost for the class?  If so, how much?&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>$<input type="text" size="3" maxlength="10" id="class_cost" name="class_cost" value=""></td>
  </tr>

  <tr>
    <td align="right"><label for="class_length">How long will the class take?&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="text" size="5" maxlength="10" id="class_length" name="class_length" value=""> hrs</td>
  </tr>

  <tr>
    <td align="right"><label for="class_time">What day and time would you like to teach your class?&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="text" size="10" maxlength="50" id="class_time" name="class_time" value=""></td>
  </tr>

  <tr>
    <td align="right"><label for="class_description">A brief description of the class:&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="text" size="100" maxlength="255" id="class_description" name="class_description" value=""></td>
  </tr>

  <tr>
    <td align="right"><label for="class_level">Skill level of the class:&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>
      <select id="class_level" name="class_level">
        <option value="">Please select a level ...</option>
        <option value="beginner">beginner</option>
        <option value="intermediate">intermediate</option>
        <option value="master">master</option>
      </select>
    </td>
  </tr>

  <tr>
    <td align="right"><label for="bring_with">What equipment should students bring with them?&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="text" size="50" maxlength="255" id="bring_with" name="bring_with" value=""></td>
  </tr>

  <tr>
    <td align="right"><label for="need_from_us">What you need from the staff? (like, electric?)&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="text" size="50" maxlength="255" id="need_from_us" name="need_from_us" value=""></td>
  </tr>

<!--
  <tr>
    <td align="right"><label for="xyzzy">PLUGH:&nbsp;</label></td>
    <td align="left" valign="middle" nowrap>&nbsp;<input type="text" size="50" maxlength="50" id="xyzzy" name="xyzzy" value="PLUGH"></td>
  </tr>
-->

  <tr>
    <td colspan="2" align="center">
      <input name="process" type="submit" value="Send">
    </td>
  </tr>
</table>
</form>
    <?
} // endif $process
?>

</body>
</html>