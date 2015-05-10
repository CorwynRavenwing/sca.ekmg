<?
  $filename = $_SERVER['SCRIPT_NAME'];
  $longname = $_SERVER['SCRIPT_FILENAME'];

//  echo $filename . ": last updated " . date ("F d Y H:i:s.", filemtime($longname));
  echo "Last Updated " . date ("F d Y H:i:s.", filemtime($longname));
?>
