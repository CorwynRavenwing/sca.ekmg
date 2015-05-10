<?
// gallery/index.html - Simple Gallery 0.9.1
// 
// Written by Lord Corwyn Ravenwing
// 
// Versions:
//	0.9	2011-12-19
//	0.9.1	2012-01-02

$thisdir = @$_GET['d'];	if (!$thisdir) { $thisdir = "."; }
$thefile = @$_GET['f'];
$cmd     = @$_GET['cmd'];
$what    = @$_GET['what'];

$dirs_array = split("/", $thisdir); if ($thefile) { array_push($dirs_array, $thefile); }

$this_hilite = "$thisdir/hilite.jpg";

$breadcrumb_array = array();

$up_array = array();
foreach ($dirs_array as $crumb) {
	array_push($up_array, $crumb);
	$up_dir = join("/", $up_array);
	$gallery_label = $crumb;
	if ($gallery_label == ".") {
		$gallery_label = "top";
	}
	$breadcrumb_array[ $up_dir ] = $gallery_label;
}
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <title>Gallery <?=$gallery_label?> <?=$thefile?></title>
  <link rel="stylesheet" type="text/css" href="gallery.css" />
</head>
<body>

<p align="center">
<b>Simple Gallery v0.9.1</b>
</p>

<?
// Parse with sections
$ini_array = parse_ini_file("gallery.ini", true);
# print("<pre>"); print_r($ini_array); print("</pre>");

$my_ip = $_SERVER['REMOTE_ADDR'];
# print("Your IP: $my_ip<br/>\n");

$admin = 0;

foreach ($ini_array['admin'] as $label => $ip) {
	if ($ip == $my_ip) {
		# print "IP $ip == $my_ip: ADMIN<br/>\n";
		print "ADMIN ($ip)<br/>\n";
		$admin = 1;
	} elseif (fnmatch($ip, $my_ip)) {
		# print "IP $ip match $my_ip<br/>\n";
		print "ADMIN ($ip)<br/>\n";
		$admin = 1;
	} else {
		# print "IP $ip != $my_ip<br/>\n";
	}
} // next ip

$allow = array();
$settings = array();

foreach ($ini_array['allow'] as $action => $value) {
	# print("DEBUG: action $action value '$value'<br/>\n");
	
	switch($value) {
	    case "ADMIN":
		// must precede "true" because any string == true
		$allow[ $action ] = $admin;
		break;
		
	    case true:
		$allow[ $action ] = 1;
		break;
		
	    case false:
		$allow[ $action ] = 0;
		break;
		
	    default:
		// this won't actually happen, either TRUE or FALSE will always match
		$allow[ $action ] = 0;
		break;
	} // end switch value
} // next action

# could we replace the following with << $settings = $ini_array['settings'] >> ?
foreach ($ini_array['settings'] as $var => $value) {
	$settings[ $var ] = $value;
} // next var

# print("<pre>"); print_r($allow); print("</pre>");

$random1 = "sdfjnawklehfyshfweidhsf";
$random2 = "ieruwemnmnbiucxyvwemnbr";

switch ($cmd) {
	case "":
		break;
	
	case "hilite":
		if (! $allow['change_hilite'] ) {
			echo "<div class='error'>ERROR: permissions do not include change_hilite</div>\n";
		} elseif (! is_writeable($thisdir) ) {
			echo "<div class='error'>ERROR: directory '$thisdir' is not writeable</div>\n";
		} elseif (file_exists($this_hilite) and ! is_writeable($this_hilite) ) {
			echo "<div class='error'>ERROR: hilite file is not writeable</div>\n";
		} elseif (! @copy($what, $this_hilite)) {
			echo "<div class='error'>ERROR: failed to set hilite</div>\n";
		} else {
			echo "<div class='message'>updated hilite</div>\n";
		}
		break;
		
	case "redo":
		if (! $allow['redo_thumb']) {
			echo "<div class='error'>ERROR: permissions do not include redo_thumb</div>\n";
		} elseif (! preg_match('{/th_[.]?[^/.]*[.](GIF|JPG)$}i', $what)) {
			echo "<div class='error'>ERROR: '$what' is not a thumb file</div>\n";
		} elseif (! file_exists($what)) {
			echo "<div class='error'>ERROR: thumb file '$what' does not exist</div>\n";
		} elseif (! is_writeable($thisdir) ) {
			echo "<div class='error'>ERROR: directory '$thisdir' is not writeable</div>\n";
		} elseif (! is_writeable($what) ) {
			echo "<div class='error'>ERROR: thumb file '$what' is not writeable</div>\n";
		} elseif (! unlink($what)) {
			echo "<div class='error'>ERROR: failed to delete thumb file</div>\n";
		} else {
			if (file_exists($what)) {
				echo "<div class='error'>ERROR: thumb file '$what' still exists after delete</div>\n";
			} else {
				echo "<div class='message'>deleted thumb file $what</div>\n";
			}
		}
		break;
	
	case "edit":
		if (! $allow['edit_file']) {
			echo "<div class='error'>ERROR: permissions do not include edit_file</div>\n";
		} elseif (! preg_match('{/[.]?[^/.]*[.]TXT$}i', $what)) {
			echo "<div class='error'>ERROR: '$what' is not a text file</div>\n";
		} elseif (! is_writeable($thisdir) ) {
			echo "<div class='error'>ERROR: directory '$thisdir' is not writeable</div>\n";
		} elseif ( @$_POST['cancel'] ) {
			echo "<div class='message'>Cancelling edit of '$what'</div>\n";
		} elseif ( @$_POST['submit'] ) {
			if ($_REQUEST[$random1] != $random2) {
				echo "<div class='error'>ERROR: bad security code passed</div>\n";
			} else {
				$new_content = $_POST['content'];
				$new_content = get_magic_quotes_gpc() ? stripslashes($new_content) : $new_content;
				if (! $new_content) {
					if (file_exists($what)) {
						unlink($what);
						echo "<div class='message'>Deleted blank '$what'</div>\n";
					} else {
						echo "<div class='message'>Not saving blank '$what'</div>\n";
					}
				} else {
					if (! is_writeable($what) ) {
						unlink($what);
						echo "<div class='error'>Deleting non-writeable text file '$what'</div>\n";
					}
					file_put_contents( $what, $new_content );
					echo "<div class='message'>Saved edit of '$what'</div>\n";
				}
			}
		} else {
			if (file_exists($what)) {
				$original_contents = file_get_contents($what);
			} else {
				echo "<div class='message'>File was missing: creating ...</div>\n";
				$original_contents = "";
			} // endif exists
			$original_lines = count( split("\n", $original_contents) );
			?>
		<form id="edit" name="edit" method="post" action="">
			<div class='message'>
				Editing file '<?=$what?>':<br/>
				<input type="hidden" name="<?=$random1?>" value="<?=$random2?>" />
				<textarea name="content" rows="<?=$original_lines+5?>" cols="100"><?=$original_contents?></textarea>
			</div>
			<input type="submit" name="submit" value="Save Changes" />
			<input type="submit" name="cancel" value="CANCEL" />
		</form>
			<?
		}
		break;
	
	case "rename":
		$full_what = "$thisdir/$what";
		if (! $allow['rename_file']) {
			echo "<div class='error'>ERROR: permissions do not include rename_file</div>\n";
		} elseif ( (! is_dir($full_what)) and (! preg_match('{^[.]?[^/.]*[.](GIF|JPG)$}i', $what)) ) {
			echo "<div class='error'>ERROR: '$full_what' is neither a direcory nor an image file</div>\n";
		} elseif (! file_exists($full_what)) {
			echo "<div class='error'>ERROR: image file '$full_what' does not exist</div>\n";
		} elseif (! is_writeable($thisdir) ) {
			echo "<div class='error'>ERROR: directory '$thisdir' is not writeable</div>\n";
		} elseif ( @$_POST['cancel'] ) {
			echo "<div class='message'>Cancelling rename of '$full_what'</div>\n";
		} elseif ( @$_POST['submit'] ) {
			if ($_REQUEST[$random1] != $random2) {
				echo "<div class='error'>ERROR: bad security code passed</div>\n";
			} else {
				$new_what = $_POST['new_what'];
				$full_new_what = "$thisdir/$new_what";
				if (file_exists($full_new_what)) {
					echo "<div class='error'>ERROR: new name '$new_what' already exists</div>\n";
				} elseif ( (! is_dir($full_what)) and (! preg_match('{^[.]?[^/.]*[.](GIF|JPG)$}i', $new_what)) ) {
					echo "<div class='error'>ERROR: new name '$new_what' is not an image file</div>\n";
				} else {
					if (is_dir($full_what)) {
						if (rename($full_what, $full_new_what)) {
							echo "<div class='message'>Renamed '$what' to '$new_what'</div>\n";
						} else {
							echo "<div class='error'>ERROR: Rename of '$what' to '$new_what' failed</div>\n";
						}
					} else {
						$smallfile_old	= "$thisdir/sm_$what";
						$smallfile_new	= "$thisdir/sm_$new_what";
					
						$basename_old	= image_basename($what);
						$basename_new	= image_basename($new_what);
					
						$caption_old	= "$thisdir/${basename_old}.txt";
						$caption_new	= "$thisdir/${basename_new}.txt";
					
						$comments_old	= "$thisdir/co_${basename_old}.txt";
						$comments_new	= "$thisdir/co_${basename_new}.txt";
					
						$thumb_old	= "$thisdir/th_$what";
						$thumb_new	= "$thisdir/th_$new_what";
					
						safe_move($smallfile_old,	$smallfile_new);
						safe_move($caption_old,		$caption_new);
						safe_move($comments_old,	$comments_new);
						safe_move($thumb_old,		$thumb_new);
						safe_move($full_what,		$full_new_what);
					
						echo "<div class='message'>Renamed '$what' to '$new_what' with all attendant files</div>\n";
					} // endif is_dir
				} // endif new name exists
			}
		} else {
			?>
		<form id="rename" name="rename" target="" method="POST">
			<div class='message'>
				File old name: <?=$what?><br/>
				File new name: <input name="new_what" type="text" value="<?=$what?>" size="100" />
			</div>
			<input type="hidden" name="<?=$random1?>" value="<?=$random2?>" />
			<input type="submit" name="submit" value="RENAME" />
			<input type="submit" name="cancel" value="CANCEL" />
		</form>
			<?
		} // endif
		break;
		
	case "move":
		$full_what = "$thisdir/$what";
		if (! $allow['move_file']) {
			echo "<div class='error'>ERROR: permissions do not include move_file</div>\n";
		} elseif (! file_exists($full_what)) {
			echo "<div class='error'>ERROR: file '$full_what' does not exist</div>\n";
		} elseif ( (! is_dir($full_what)) and (! preg_match('{[^/.]*[.](GIF|JPG)$}i', $what)) ) {
			echo "<div class='error'>ERROR: '$full_what' is neither a direcory nor an image file</div>\n";
		} elseif (! is_writeable($thisdir) ) {
			echo "<div class='error'>ERROR: directory '$thisdir' is not writeable</div>\n";
		} elseif ( @$_POST['cancel'] ) {
			echo "<div class='message'>Cancelling rename of '$full_what'</div>\n";
		} elseif ( @$_POST['submit'] ) {
			if ($_REQUEST[$random1] != $random2) {
				echo "<div class='error'>ERROR: bad security code passed</div>\n";
			} else {
				$new_dir = $_POST['new_dir'];
				$full_new_what = "$new_dir/$what";
				if (file_exists($full_new_what)) {
					echo "<div class='error'>ERROR: subdir '$what' already exists in '$new_dir' </div>\n";
				} elseif (! is_dir($new_dir)) {
					echo "<div class='error'>ERROR: new name '$new_dir' is not a directory</div>\n";
				} else {
					if (is_dir($full_what)) {
						if (rename($full_what, $full_new_what)) {
							echo "<div class='message'>Moved '$what' to '$new_dir'</div>\n";
						} else {
							echo "<div class='error'>ERROR: Move of '$what' to '$new_dir' failed</div>\n";
						}
					} else {
						$smallfile_old	= "$thisdir/sm_$what";
						$smallfile_new	= "$new_dir/sm_$what";
					
						$basename	= image_basename($what);
					
						$caption_old	= "$thisdir/${basename}.txt";
						$caption_new	= "$new_dir/${basename}.txt";
					
						$comments_old	= "$thisdir/co_${basename}.txt";
						$comments_new	= "$new_dir/co_${basename}.txt";
					
						$thumb_old	= "$thisdir/th_$what";
						$thumb_new	= "$new_dir/th_$what";
					
						safe_move($smallfile_old,	$smallfile_new);
						safe_move($caption_old,		$caption_new);
						safe_move($comments_old,	$comments_new);
						safe_move($thumb_old,		$thumb_new);
						safe_move($full_what,		$full_new_what);
					
						echo "<div class='message'>Moved '$what' to '$new_dir' with all attendant files</div>\n";
					} // endif is_dir
				} // endif new name exists
			}
		} else {
			?>
		<form id="move" name="move" target="" method="POST">
			<div class='message'>
				File name: <?=$what?>
				File old directory: <?=$thisdir?><br/>
				File new directory:
			<?
			$all_dirs = get_all_dirs();
			?>
				<select name="new_dir">
			<?
			foreach ($all_dirs as $dir) {
				if ($dir == $thisdir) {
					$sel = " selected='selected'";
				} else {
					$sel = "";
				}
				?>
					<option<?=$sel?>><?=$dir?></option>
				<?
			} // next dir
			?>
				</select>
			</div>
			<input type="hidden" name="<?=$random1?>" value="<?=$random2?>" />
			<input type="submit" name="submit" value="MOVE" />
			<input type="submit" name="cancel" value="CANCEL" />
		</form>
			<?
		} // endif
		break;
		
	case "delete":
		$full_what = "$thisdir/$what";
		if (! $allow['delete_file']) {
			echo "<div class='error'>ERROR: permissions do not include delete_file</div>\n";
		} elseif (! preg_match('{/[.]?[^/.]*[.](GIF|JPG)$}i', $full_what)) {
			echo "<div class='error'>ERROR: '$full_what' is not an image file</div>\n";
		} elseif (! file_exists($full_what)) {
			echo "<div class='error'>ERROR: image file '$full_what' does not exist</div>\n";
		} elseif (! is_writeable($thisdir) ) {
			echo "<div class='error'>ERROR: directory '$thisdir' is not writeable</div>\n";
		} else {
			?>
			<div class='message'>Confirm deletion of file '<?=$full_what?>':<br/>
	<a href="?d=<?=$thisdir?>&amp;cmd=delete_confirm&amp;what=<?=$what?>&amp;f=<?=$thefile?>">YES, DELETE</a>
	<a href="?d=<?=$thisdir?>&amp;f=<?=$thefile?>">NO, cancel</a>
			</div>
			<?
		}
		break;
		
	case "delete_confirm":
		$full_what = "$thisdir/$what";
		if (! $allow['delete_file']) {
			echo "<div class='error'>ERROR: permissions do not include delete_file</div>\n";
		} elseif (! preg_match('{/[.]?[^/.]*[.](GIF|JPG)$}i', $full_what)) {
			echo "<div class='error'>ERROR: '$full_what' is not an image file</div>\n";
		} elseif (! file_exists($full_what)) {
			echo "<div class='error'>ERROR: image file '$full_what' does not exist</div>\n";
		} elseif (! is_writeable($thisdir) ) {
			echo "<div class='error'>ERROR: directory '$thisdir' is not writeable</div>\n";
		} elseif (! unlink($full_what)) {
			echo "<div class='error'>ERROR: failed to delete image file</div>\n";
		} else {
			if (file_exists($what)) {
				echo "<div class='error'>ERROR: image file '$full_what' still exists after delete</div>\n";
			} else {
				echo "<div class='message'>deleted image file $full_what</div>\n";
			}
		}
		break;
		
	case "merge":
		$full_what = "$thisdir/$what";
		if (! $allow['merge_dir']) {
			echo "<div class='error'>ERROR: permissions do not include merge_dir</div>\n";
		} elseif (! file_exists($full_what)) {
			echo "<div class='error'>ERROR: file '$full_what' does not exist</div>\n";
		} elseif (! is_dir($full_what)) {
			echo "<div class='error'>ERROR: '$full_what' is not a direcory</div>\n";
		} elseif (! is_writeable($thisdir) ) {
			echo "<div class='error'>ERROR: directory '$thisdir' is not writeable</div>\n";
		} elseif (! is_writeable($full_what) ) {
			echo "<div class='error'>ERROR: directory '$full_what' is not writeable</div>\n";
		} elseif ( @$_POST['cancel'] ) {
			echo "<div class='message'>Cancelling merge of '$full_what'</div>\n";
		} elseif ( @$_POST['submit'] ) {
			if ($_REQUEST[$random1] != $random2) {
				echo "<div class='error'>ERROR: bad security code passed</div>\n";
			} else {
				$new_dir = $_POST['new_dir'];
				if (! is_dir($new_dir)) {
					echo "<div class='error'>ERROR: merge target '$new_dir' is not a directory</div>\n";
				} else {
					echo "<div class='message'>Merging '$full_what' into '$new_dir'</div>\n";
					
					$files_array = get_files_in_dir($full_what);
					
					foreach ($files_array as $f) {
						$old_file = "$full_what/$f";
						$new_file = "$new_dir/$f";
						
						echo "<div>... move '$f'</div>\n";
						
						delete_older($old_file,	$new_file);
						safe_move($old_file,	$new_file);
					}
					
					rmdir($full_what);
					
					echo "<div class='message'>Done.</div>\n";
				} // endif new name exists
			}
		} else {
			?>
		<form id="merge" name="merge" target="" method="POST">
			<div class='message'>
				Merge directory: <?=$thisdir?>/<?=$what?><br/>
				With directory: 
			<?
			$all_dirs = get_all_dirs();
			?>
				<select name="new_dir">
			<?
			foreach ($all_dirs as $dir) {
				if ($dir == $full_what) {
					$sel = " selected='selected'";
				} else {
					$sel = "";
				}
				?>
					<option<?=$sel?>><?=$dir?></option>
				<?
			} // next dir
			?>
				</select>
			</div>
			<input type="hidden" name="<?=$random1?>" value="<?=$random2?>" />
			<input type="submit" name="submit" value="MERGE" />
			<input type="submit" name="cancel" value="CANCEL" />
		</form>
			<?
		} // endif
		break;

	default:
		print "<div class='error'>ERROR: invalid cmd '$cmd'</div>\n";
		break;
}

?>

<ul class="breadcrumbs">
<?
foreach ($breadcrumb_array as $dir => $label) {
	$link = "?d=$dir";
	if ($dir == ".") { $link = "?"; }
	if (  ($label == $thefile)  or  (($dir == $thisdir) and (! $thefile))  ) {
		?>
	<li>
		<span title="<?=$label?>"><? showfile("$dir/caption.txt", $label); ?></span>
		<?
		if ( $allow['edit_file'] and is_dir($dir) ) {
			?>
	<a href="?d=<?=$thisdir?>&amp;f=<?=$thefile?>&amp;cmd=edit&amp;what=<?=$dir?>/caption.txt" title="Edit this label">*</a>
			<?
		} // endif edit_file
		?>
	</li>
		<?
	} else {
		?>
	<li>
		<a href="<?=$link?>" title="<?=$label?>"><? showfile("$dir/caption.txt", $label); ?></a>
		<?
		if ($allow['edit_file']) {
			?>
	&nbsp;
	<a href="?d=<?=$thisdir?>&amp;f=<?=$thefile?>&amp;cmd=edit&amp;what=<?=$dir?>/caption.txt" title="Edit this label">*</a>
			<?
		} // endif edit_file
		?>
	</li>
		<?
	}
}
?>
</ul>
<?

if ($thefile) {
	# show the file
	
	$smallfile = "sm_$thefile";
	
	# following DIV is necessary for formatting reasons [Corwyn 2011-12-02]
	?>
	<div class="summary"></div>
	<?
	if ( file_exists("$thisdir/$smallfile") ) {
		echo "<img src='$thisdir/$smallfile' /><br/><a href='$thisdir/$thefile' target='_blank'>(full size)</a>\n";
	} else {
		echo "<img src='$thisdir/$thefile' /><br/>\n";
	}
	$text_file  = image_basename($thefile) . ".txt";
	$comments   = "$thisdir/co_$text_file";
	?>
	<div class="comments">
	<?
	showfile($comments);
	
	if ($allow['edit_file']) {
		?>
	<a href="?d=<?=$thisdir?>&amp;f=<?=$thefile?>&amp;cmd=edit&amp;what=<?=$comments?>" title="Edit comments">*</a>
		<?
	} // endif edit_file
	?>
	</div>
	<?
} else {
	# show the directory
	
	?>
	<div class="summary">
	<?
	showfile("$thisdir/summary.txt");
	if ($allow['edit_file']) {
		?>
	<a href="?d=<?=$thisdir?>&amp;f=<?=$thefile?>&amp;cmd=edit&amp;what=<?=$thisdir?>/summary.txt" title="Edit summary">*</a>
		<?
	} // endif edit_file
	?>
	</div>
	<?
	if ($allow['show_hilite']) {
		$hilite_label = "Highlight";
		$hilite_comment = "Highlight for this directory";
		if ( ! file_exists($this_hilite) ) {
			$this_hilite = "no_hilite.jpg";
			$hilite_label = "No highlight";
			$hilite_comment = "This directory has no highlight";
		}
	
		?>
	<div class="block hilite">
		<a>
			<div class="imageblock">
				<img src="<?=$this_hilite?>" alt="<?=$hilite_label?>" />
			</div>
			<p><?=$hilite_label?></p>
		</a>
		<p class="caption"><?=$hilite_comment?></p>
	</div>
		<?
	} // endif show_hilite
	
	$files_array = array();
	
	// Open a known directory, and proceed to read its contents
	if (is_dir($thisdir)) {
		if ($dh = opendir($thisdir)) {
			while (($file = readdir($dh)) !== false) {
				$type = filetype("$thisdir/$file");
			
				switch ($type) {
				    case 'file':
					$display = 0;
				
					$extension = pathinfo($file, PATHINFO_EXTENSION);
					$extension_lower = strtolower($extension);
					
					if ($extension != $extension_lower) {
						# "$extension$" means "string with $extension at the end"
						$new_filename = ereg_replace("$extension$", $extension_lower, $file);
						
						# print("DEBUG: filename $file => lower $new_filename<br/>\n");
						
						delete_older("$thisdir/$file", "$thisdir/$new_filename");
						if (safe_move("$thisdir/$file", "$thisdir/$new_filename")) {
							$file = $new_filename;
						}
					} // endif not lower
					
					if ( preg_match('/[.]thumb[.]/', $file) ) {
						# thumbnail
					} elseif ( preg_match('/[.]sized[.]/', $file) ) {
						# downsized file
					} elseif ( preg_match('/^th_/', $file) ) {
						# thumbnail
					} elseif ( preg_match('/^sm_/', $file) ) {
						# downsized file
					} elseif ( preg_match('/^[.]/', $file) and (! $allow['show_hidden']) ) {
						# begins with a dot
					} elseif ( "no_thumb.jpg"   == $file ) {
						# downsized file
					} elseif ( "no_hilite.jpg"  == $file ) {
						# downsized file
					} elseif ( preg_match('/hilite[.](jpg|gif)/', $file) ) {
						# highlight or an alternative highlight
					} else {
						switch ($extension_lower) {
							case "jpg":
							case "gif":
								$display++;
								break;
								
							default:
								// don't display other extensions
								break;
						} // end switch extension
					} // endif thumbnail
				
					if ($display) {
						$files_array[ $file ] = 1;	# 1 means file
					} else {
						# echo "(hide $file)<br/>\n";
					}
					break;
				
				    case 'dir':
					$display = 0;
				
					if ($file == "." or $file == "..") {
						# current or parent dir
					} elseif ( preg_match('/^[.]/', $file) and (! $allow['show_hidden']) ) {
						# begins with a dot
					} else {
						$display++;
					}
					
					if ($display) {
						$files_array[ $file ] = 0;	# 0 means dir
					} else {
						# echo "(hide $file)<br/>\n";
					}
					break;
				
				    default:
					print("found type '$type'<br/>\n");
					break;
				} // switch filetype
			} // next file
			closedir($dh);
		} // endif opendir
	} // endif is_dir
	
	ksort($files_array);
	
	foreach ($files_array as $file => $is_image) {
		if ($is_image) {
			# file
			$class		= "image";
			$thumb		= "$thisdir/th_$file";
			$thumb_desc	= "th_$file";
			$link		= "?d=$thisdir&amp;f=$file";
			$alt_thumb	= "no_thumb.jpg";
			$text_file	= image_basename($file) . ".txt";
			$caption	= "$thisdir/$text_file";
			$fullsize	= "$thisdir/$file";
			$comments	= "$thisdir/co_$text_file";
			$comment_title	= "Edit photo comment";
		} else {
			# dir
			$class		= "dir";
			$thumb		= "$thisdir/$file/hilite.jpg";
			$thumb_desc	= "$file/hilite.jpg";
			$link		= "?d=$thisdir/$file";
			$alt_thumb	= "no_hilite.jpg";
			$caption	= "$thisdir/$file/caption.txt";
			$fullsize	= "";
			$comments	= "$thisdir/$file/summary.txt";
			$comment_title	= "Edit directory summary";
		}
		
		if ( ! file_exists($thumb) ) {
			if (! $is_image) {
				# print("<div class='message'>No auto-thumb for directory</div>\n");
				$class .= " auto_no";
				$thumb = $alt_thumb;
			} elseif (! fullsize) {
				# no fullsize to pull thumbnail from
				# print("<div class='message'>no fullsize for thumb</div>\n");
				$class .= " auto_error";
				$thumb = $alt_thumb;
			} elseif ($settings['max_fix_thumbnails'] <= 0) {
				# print("<div class='message'>done enough thumbs $settings['max_fix_thumbnails']</div>\n");
				# already done too many thumbnails
				$class .= " auto_enough";
				$thumb = $alt_thumb;
			} else {
				# print("<div class='message'>create thumb $settings['max_fix_thumbnails']</div>\n");
				$settings['max_fix_thumbnails']--;
				$command = "convert " . escapeshellarg($fullsize)
					. " -verbose "
					. " -resize " . escapeshellarg("150x>")
					. " -resize " . escapeshellarg("x150>")
					. " " . escapeshellarg($thumb);
				print("<!-- $command -->\n");
				flush();
				$last_line = exec($command, $all_output, $retval);
				print("<!-- Last line of the output: $last_line -->");
				print("<!-- Return value: $retval -->");
				if ($retval) {
					# print("<div class='error'>create thumb failed</div>\n");
					$class .= " auto_error";
					$thumb = $alt_thumb;
				} else {
					# don't change the thumb: it should exist now
					$class .= " auto_new";
				} 
				flush();
			} # endif fixing thumbnails
		}
		$file_desc = str_replace("_", " ", $file);
		?>
<div class="block <?=$class?>">
	<a href="<?=$link?>">
		<div class="imageblock">
			<img src="<?=$thumb?>" alt="<?=$thumb_desc?>" />
		</div>
		<p><?=$file_desc?></p>
	</a>
	<p class="caption">
	<?
	$buttons = 0;
	if (($allow['change_hilite']) and ($thumb != $alt_thumb)) {
		?>
	<a href="?d=<?=$thisdir?>&amp;cmd=hilite&amp;what=<?=$thumb?>" title="Make this image the directory hilite">HILITE</a>
		<?
		$buttons++;
	} // endif change_hilite
	
	if (($allow['redo_thumb']) and ($thumb != $alt_thumb) and ($is_image)) {
		?>
	<a href="?d=<?=$thisdir?>&amp;cmd=redo&amp;what=<?=$thumb?>" title="Delete this thumbnail and re-create it">REDO</a>
		<?
		$buttons++;
	} // endif redo_thumb
	
	if ($allow['rename_file']) {
		?>
	<a href="?d=<?=$thisdir?>&amp;cmd=rename&amp;what=<?=$file?>" title="Change this file's name">RENAME</a>
		<?
		$buttons++;
	} // endif rename_file
	
	if ($allow['move_file']) {
		?>
	<a href="?d=<?=$thisdir?>&amp;cmd=move&amp;what=<?=$file?>" title="Move this file to another folder">MOVE</a>
		<?
		$buttons++;
	} // endif move_file
	
	if (($allow['delete_file']) and ($is_image)) {
		?>
	<a href="?d=<?=$thisdir?>&amp;cmd=delete&amp;what=<?=$file?>" title="Delete this image">DELETE</a>
		<?
		$buttons++;
	} // endif delete_file
	
	if (($allow['merge_dir']) and (! $is_image)) {
		?>
	<a href="?d=<?=$thisdir?>&amp;cmd=merge&amp;what=<?=$file?>" title="Merge this directory with another one">MERGE</a>
		<?
		$buttons++;
	} // endif merge_dir
	
	if ($buttons) { print("<br/>\n"); }
	
	showfile($caption);
	if ($allow['edit_file']) {
		?>
	<a href="?d=<?=$thisdir?>&amp;f=<?=$thefile?>&amp;cmd=edit&amp;what=<?=$caption?>" title="Edit this caption">*</a>
	<a href="?d=<?=$thisdir?>&amp;f=<?=$thefile?>&amp;cmd=edit&amp;what=<?=$comments?>" title="<?=$comment_title?>">**</a>
		<?
	} // endif edit_file
	?>
	</p>
	<?
	?>
</div>
		<?
	} // next file
} // endif $thefile

function showfile($filename, $alternate_text="") {
	if (file_exists($filename)) {
		$contents = file_get_contents($filename);
		$contents = trim($contents);
		$contents = str_replace("\n", "<br/>\n", $contents);
		print $contents;
	} elseif ($alternate_text) {
		print $alternate_text;
	} else {
		print "<span style='color:red'>[" . str_replace("_", " ", $filename) . "]</span>";
	}
} // end function showfile

function image_basename($filename) {
	$extensions = array(
		".GIF",
		".gif",
		".JPG",
		".jpg",
	);
	$filename = str_replace($extensions, "", $filename);
	
	return $filename;
} // end function image_basename

function delete_older($file1, $file2, $print=0) {
	# returns filename that wasn't deleted
	$retVal = $file1;
	
	if (! file_exists($file1)) {
		if ($print) { print("file1 '$file1' doesn't exist, don't delete<br/>\n"); }
		$retVal = $file2;
	} elseif (! file_exists($file2)) {
		if ($print) { print("file2 '$file2' doesn't exist, don't delete<br/>\n"); }
		$retVal = $file1;
	} else {
		$mtime1 = filemtime($file1);
		$mtime2 = filemtime($file2);
		
		if ($mtime1 < $mtime2) {
			if ($print) { print("file1 '$file1' older, deleting<br/>\n"); }
			unlink($file1);
			$retVal = $file2;
		} else {
			if ($print) { print("file2 '$file2' older, deleting<br/>\n"); }
			unlink($file2);
			$retVal = $file1;
		}
	}
	
	return $retVal;
} // end function delete_older

function safe_move($file1, $file2, $print=0) {
	# returns 1 (true) if file rename succeeded
	if (file_exists($file2)) {
		if ($print) { print("file2 '$file2' exists, don't rename<br/>\n"); }
		$retVal = 0;
	} elseif (! file_exists($file1)) {
		if ($print) { print("file1 '$file1' doesn't exist, don't rename<br/>\n"); }
		$retVal = 0;
	} else {
		if ($print) { print("rename($file1, $file2)<br/>\n"); }
		$retVal = rename($file1, $file2);
		# return success value returned by function
	}
	
	return $retVal;
} // end function safe_move

function get_all_dirs( $dir = "." ) {
	global $allow;
	
	# print("DEBUG: called get_all_dirs($dir)<br/>\n");
	$retVal = array();
	if (is_dir($dir)) {
		array_push($retVal, $dir);
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				$type = filetype("$dir/$file");
				switch ($type) {
				    case 'file':
					// ignore files
					break;
				    
				    case 'dir':
					if ($file == "." or $file == "..") {
						# current or parent dir
					} elseif ( preg_match('/^[.]/', $file) and (! $allow['show_hidden']) ) {
						# begins with a dot
					} else {
						$subfolders = get_all_dirs("$dir/$file");
						$retVal = array_merge($retVal, $subfolders);
					} // endif parent dir
					break;
				    
				    default:
					// ignore other types, if any exist
					break;
				} // end switch
			} // next file
		} // endif opendir
	} // endif is_dir
	
	sort($retVal);
	
	return $retVal;
} // end function get_all_dirs

// returns only the local portion of the filename, not prepended by directory name
function get_files_in_dir($dir) {
	# print("DEBUG: called get_files_in_dir($dir)<br/>\n");
	$retVal = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if ($file == "." or $file == "..") {
					# current or parent dir
				} else {
					array_push($retVal, $file);
				} // endif parent dir
			} // next file
		} // endif opendir
	} // endif is_dir
	
	sort($retVal);
	
	return $retVal;
} // end function get_files_in_dir
?>

<div class="disclaimer">
<em>Simple Gallery</em> was written by Lord Corwyn Ravenwing.<br/>
Bug reports should be sent to the webminister of this website, who will forward them to the author.
<!-- If the webminister doesn't know how to contact me, he shouldn't be using this program :-) -->
</div>

<div class="admin">
	IP <?=$my_ip?>
	<?=($admin ? "ADMIN " : "")?>
	OPTIONS:
	<?
	foreach ($allow as $name => $value) {
		$status = ($value ? "YES" : "NO");
		?>
	<?=$name?>:<?=$status?>
		<?
	}
	?>
</div>

</body>
</html>
