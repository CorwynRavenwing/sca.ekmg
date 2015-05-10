<?
# NOTE: all these files are identical. Changes to one
#	should be copied to all the others.

include "site/vars.inc";

# LINKING FUNCTIONS

# do an arbitrary <a href=...> link
function a_href($url, $newwindow) {
  global $boldlinks;
  echo "\n<a href='" . $url . "'";
  if ($newwindow) echo " target='_blank'";
  echo ">";
  if ($boldlinks) echo "<b>";
}

# do </a> end link
function a_end() {
  global $boldlinks;
  if ($boldlinks) echo "</b>";
  echo "</a>\n";
}

# do an entire link
function do_link($url, $tag, $newwindow) {
  a_href($url, $newwindow);
  echo $tag;
  a_end();
}

# SOOTHING FUNCTIONS

# translate a URL into a soothed link
function soothed_url($url) {
  global $webroot;
  return $webroot . "/soothlink.html?url=" . urlencode($url);
}

# do an arbitrary <a href=...> link, soothed
function a_sooth($url) {
  a_href( soothed_url($url), 1);	# sooth -> always new window
}

# do the entire link, soothed; if no tag is given, use url as tag
function sooth($url, $tag, $verify=0) {
	if ($verify) { verify_url($url); }
	if ($tag == "") { $tag = $url; }
	a_sooth($url);
	echo $tag;
	a_end();
}

function sooth_flag($url, $tag, $recognized) {
  if ($url == "") {
    // no link
    echo $tag;
  } elseif ($recognized) {
    // recognized link
    do_link($url, $tag, 1);	// always new window
  } else {
    // unrecognized link
    sooth($url, $tag);
  }
}

# EMAIL FUNCTIONS
function email($address) {
  // $address = str_replace("@", "&#64;", $address);
  // or, could replace *each* character with "&#" . ascii(c) . ";"
// Can no longer do this, per Kingdom Webminister [Corwyn 2012]
// $address = str_replace("@", " (at) ", $address);
// $address = str_replace(".", " (dot) ", $address);
  echo "\n<a href='mailto:"
    . $address
    . "'>"
    . $address
    . "</a>\n";
}

function no_email($address) {
  # NOTE: doesn't print $address anywhere!  This is by design!
  echo "<b>(no email on file)</b>";
}

function email_flag($address, $permission) {
    if ($address == "") {
        // blank address
        no_email($address);
    } elseif ($permission) {
        // address with permission
        email($address);
    } else {
        // address without permission
        no_email($address);
    }
}

# DATA VALIDATION FUNCTIONS

function validate_href($url) {
	#print("DEBUG: 1<br />\n"); ob_flush();
	
	$answer = "";
	$success = 0;
	
	$url = strtolower($url);
	
	#print("DEBUG: 2<br />\n"); ob_flush();
	
	$http = "http://";
	if (strlen($url) > strlen($http)) {
		if (substr_compare($url, $http, 1, strlen($http))) {
			$url = substr($url, strlen($http));
		}
	}
	
	#print("DEBUG: 3<br />\n"); ob_flush();
	
	$website = "";
	
	if (ereg("@", $url)) {
		$answer = "error: invalid format (@)";
	} elseif (ereg(" ", $url)) {
		$answer = "error: invalid format (space)";
	} else {
		#print("DEBUG: 3a<br />\n"); ob_flush();
		
		$website = $url;
		$slashpos = strpos($url, "/");
		if ($slashpos !== "") {
			$website = substr($url, 0, $slashpos);
		}
		if ($website == "") {
			$website = "localhost";
		}
		
		if (ereg("[^A-Za-z0-9._-]", $website)) {
			$answer = "error: invalid format (other)";
			$success = 0;
		}
	}
	
	#print("DEBUG: 4<br />\n"); ob_flush();
	
	if ($website) {
		#print("DEBUG: 4a ($website)<br />\n"); ob_flush();
		
		if ( ($website == "localhost") or checkdnsrr($website,"A") ) {
			
			#print("DEBUG: 4a1<br />\n"); ob_flush();
			
			$text = fetch_url("$http$url", 2000);	// 20
			
			$size = strlen($text);
			
			if ($size) {
				$answer = "ok: $size bytes";
			} else {
				$answer = "warning: $size bytes";
			}
			$success = 1;
		} else {
			#print("DEBUG: 4a2<br />\n"); ob_flush();
			
			$answer = "error: NO DNS";
			$success = 0;
		}
	}
	
	#print("DEBUG: 5<br />\n"); ob_flush();
	
	return $answer;
} # end function validate_href

// code adapted from freeware program risearch_php [Corwyn 2008]
function fopen_url($url) {
	@list($method, $dummy, $hostname, $url_rest) = @explode("/", $url, 4);
	
	#print "DEBUG: method $method<br />";
	#print "DEBUG: dummy $dummy<br />";
	#print "DEBUG: hostname $hostname<br />";
	#print "DEBUG: url_rest $url_rest<br />";
	
	$errno = $errstr = "";
	
	$port = 80;
	$timeout = 10;
	$ip = @gethostbyname($hostname);
	if (! $ip) {
		# die("failed gethostbyname: " . posix_strerror( posix_get_last_error() ) );
		return 0;
	}
	// $socket = fsockopen($hostname, 80, $errno, $errstr, 12);	// SLOW WAY
	$socket = @fsockopen($ip,$port,$errno,$errstr,$timeout);		// FAST WAY
	if (!$socket) {
		# die("failed fsockopen: $errstr ($errno)");
		return 0;
	}
	
	fputs($socket, "GET /$url_rest HTTP/1.0\r\n");
	fputs($socket, "Host: $hostname\r\n");
	fputs($socket, "Referer: http://$hostname\r\n");
	fputs($socket, "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n\r\n");
	
	return $socket;
} // end function fopen_url

// max_bytes = 0 means no maximum
function fetch_url($url, $max_bytes=0) {
	$retval = "";
	
	$fp = fopen_url($url);
	
	if ($fp == FALSE) {
		$retval = FALSE;
	} else {
		$text = "";
		$in_headers = 1;
		while (!feof($fp)) {
			$line = fgets($fp, 4096);
			if ($in_headers) {
				$line = rtrim($line);
				if ($line == "") {
					$in_headers = 0;
				}
				continue;
			} else {
				$text .= $line;
				if ($max_bytes) {
					if ($max_bytes > strlen($text)) {
						break;
					}
				}
			} // endif in_headers
		} // next feof
		$retval = $text;
	} // endif fp
	
	return $retval;
} // end function fetch_url

function verify_url($url) {
	$answer = validate_href($url);
	
	list($type, $message) = split(":", $answer);
	
	if ($type == "ok") {
		$color = "green";
	} elseif ($type == "warning") {
		$color = "yellow";
	} elseif ($type == "error") {
		$color = "red";
	} else {
		$color = "blue";
	}
	
	?>
	<div style="background-color:<?=$color?>; height:50; width:50; display:inline; margin-right: 20; ">
		<a title="<?=$answer?>">&nbsp;&nbsp;&nbsp;</a>
	</div>
	<?
} // end function verify_url

// end code adapted from risearch_php [Corwyn 2008]
?>
