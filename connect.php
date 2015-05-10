<?
// all these files are identical: changes to any should be copied to all the others

include "site/connection.php";

$conn = mysql_connect($db_host, $db_user, $db_pass) or die(mysql_error());
mysql_select_db($db_name, $conn) or die(mysql_error());

# ------------------------------------------------------------
# FUNCTION DEFINITIONS SECTION
# ------------------------------------------------------------

function do_query($sql) {
    echo "<!-- SQL:\n$sql\n-->\n";
    $qDo = mysql_query($sql)
        or die(mysql_error() . "<pre>\nfor SQL:\n$sql\n</pre>\n");
  return $qDo;
}
// end function do_query

function count_where($table, $where_clause) {
  $sql = "SELECT count(*) AS num FROM $table WHERE $where_clause";
    echo "<!-- SQL:\n$sql\n-->\n";
    $qCount = mysql_query($sql) or die(mysql_error());
  if (! $qCount) { return 0; }
    if ($rsCount = mysql_fetch_array($qCount)) {
    $num = $rsCount['num'];
  } else {
    $num = 0;
  }
  return $num;
}
// end function count_where
?>