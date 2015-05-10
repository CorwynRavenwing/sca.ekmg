<?
// all these files are identical: changes to any should be copied to all the others
?>
<html>
<head>
<title>Verify PHP/MySQL connection</title>
</head>
<body>
<h1>Verify PHP/MySQL connection</h1>

<h2>( <?php echo date("m/d/Y h:i:s A"); ?> )</h2>

<h2>Connecting to MySQL server ...

<? include "connect.php"; ?>

<?php
$show_success = 0;

// TEST CONNECTION BY DOING A MYSQL QUERY
$sql = " SELECT ";
$sql .= " COUNT(*) as Num ";
$sql .= " FROM $test_table AS t ";

$query = mysql_query($sql) or die(mysql_error());
?>
done.</h2>

<?php
while ($result = mysql_fetch_array($query)){
	echo "<h3>Successfully connected to database $dbname: counted $result[Num] rows in table $test_table.</h3>\n";
}
?>
</body>
</html>