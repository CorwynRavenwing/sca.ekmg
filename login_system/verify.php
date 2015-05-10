<?
/**
 * verify.php
 *
 * Check that the user is logged on.  If not, redirect to the login page.
 *
 * Last Updated: 2007-05-29 by Warren Harmon
 */
include("include/session.php");

if($session->logged_in){
   echo "<!-- logged in as user '$session->username' -->\n";
} else {
	header("Location: ".HOME_PAGE."");
}
?>