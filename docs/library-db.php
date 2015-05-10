<? include "../sooth.inc"; ?>
<? include "../connect.php"; ?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<?
$code = $_REQUEST['code'];
if (! $code) {
	$code = "guild";
}

$sql = "SELECT * FROM library WHERE code = '$code'";
echo "\n<!-- quote SQL =\n{$sql}\n -->\n";
$query = mysql_query($sql) or die(mysql_error());
if (! $result = mysql_fetch_array($query)) {
	$title  = "Invalid Library '$code'";
	$header = $title;
	$email  = "";
	$blurb  = "
		You have gotten to this page in error, because there is no library called '$code'.
		Please click <a href='?'>here</a> for the main Guild library.  Thanks.
	";
	$backlink = "?";
} else {
	$title  = $result['library_title'];
	if ($code == "guild") {
		$header = $title;
		$blurb  = "
			The following books are in the EK Metalsmiths' Guild Library,
			and are available to be lent to Guild members.  If you are
			interested in borrowing one of these books, or if you would like
			to donate an appropriate book to the Guild library, please contact the
			Guildmistress
		";
		$backlink = "../index.html";
	} else {
		$header = "$result[librarian_name]'s Personal Library";
		$blurb  = "
			The following books are in the personal library of $result[librarian_name],
			who has been gracious enough to list them here in case they are of
			interest to EK Metalsmiths' Guild members.
		";
		
		if ($result['borrowable']) {
			$blurb .= "
				If you are interested in borrowing one of these books,
			";
		} else {
			$blurb .= "
				If you would like $result[librarian_short] to bring any of these books
				along to an event,
			";
		}
		
		$blurb .= "
			please contact $result[librarian_short] directly
		";
		$backlink = "?";
	}
	$email  = $result['librarian_email'];
}
?>
<title><?=$title?></title>
</head>

<body bgcolor="#FFFF8F">

<table border=0>
  <tr>
    <td>
      <img src="../device/cs/librarian-100.gif" alt="Librarian Badge" />
    </td>
    <td>
      <h2>
	  <b><?=$title?></b>
	</h2>
	<h4>
	  <?=$blurb?><? if($email) { echo " at "; echo email($email); } ?>.
	  <br />
	</h4>
    </td>
    <td>
      <a href="<?=$backlink?>">
	<img width="33" height="50" src="../device/ekmg.gif" alt="EK Metalsmiths' Guild Shield" />
	(BACK)
      </a>
    </td>
  </tr>
</table>

<br />
<h2><?=$header?></h2>
<?
if (! $result) {
	exit(0);
}
?>
<h4>(alphabetical by title, within broad categories)</h4>

<table border=1>
  <tr>
    <td><b>Title</b></td>
    <td><b>Author</b></td>
    <td><b>Year</b></td>
    <td><b>ISBN or LCC</b></td>
  </tr>
<ul>
<!-- eventually wants categories "silver - american - postperiod" -->
<!-- for now, sort alphabetically by title -->
<?
$sql = "SELECT * FROM book WHERE code = '$code' AND approved = 1 ORDER BY category, subcat, title, author, year";
echo "\n<!-- quote SQL =\n{$sql}\n -->\n";
$query = mysql_query($sql) or die(mysql_error());
$prev_category = "";
$prev_subcat   = "";
while ($result = mysql_fetch_array($query)) {
	$category = $result['category'];
	if ($prev_category != $category) {
		$prev_category = $category;
		$prev_subcat   = "";
		if ($category != "") {
			?>
	<tr>
		<td colspan="5" align="left" valign="bottom">
			<font size="+2"><b><?=$category?></b></font>
		</td>
	</tr>
			<?
		} # endif category ""
	} # endif prev_category
	
	$subcat = $result['subcat'];
	if ($prev_subcat != $subcat) {
		$prev_subcat = $subcat;
		if ($subcat != "") {
			?>
	<tr>
		<td colspan="5" align="left" valign="bottom">
			<font size="+1"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$subcat?></b></font>
		</td>
	</tr>
			<?
		} # endif subcat ""
	} # endif prev_subcat
	
	if ($result['flag']) {
		$style = "color:blue; font-weight:bold";
	} else {
		$style = "";
	}
	
	$title	= $result['title'];
	$author = $result['author'];
	$year	= $result['year'];
	$isbn	= $result['isbn'];
	
	if ( strpos($title, "://") !== false ) {
		$title = "<a href='$title' target='_blank'>$title</a>";
	}
	?>
	<tr>
		<td align="left" valign="top" style="<?=$style?>"><?=$title?>&nbsp;</td>
		<td align="left" valign="top"><?=$author?> &nbsp;</td>
		<td align="left" valign="top"><?=$year?> &nbsp;</td>
		<td align="left" valign="top"><?=$isbn?> &nbsp;</td>
		<!-- <?=$result['comment']?> -->
	</tr>
	<?
} // end while $result
?>
</ul>
</table>
<?
if ($code == "guild") {
	$sql = "SELECT * FROM library where code != 'guild'";	# yes, NOT EQUAL, show headers for all OTHER libraries
	echo "\n<!-- quote SQL =\n{$sql}\n -->\n";
	$query = mysql_query($sql) or die(mysql_error());
	?>
	
<h2>You may also want to look in the following EKMG members' private
libraries, which they have put on-line for your convenience:</h2>
<h4>
<ul>
	<?
	while ($result = mysql_fetch_array($query)) {
		?>
<li><a href="?code=<?=$result['code']?>"><?=$result['librarian_link']?></a></li>
		<?
	} # wend $result
	?>
</ul>
</h4>
	<?
} // endif code == "guild"
?>
</body>
</html>
