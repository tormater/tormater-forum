<?php
// functions.php
// Contains functions for use throughout the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

// Hugely important function which generates a random string.
function random_str($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Redirects the user to the specified page. If blank it defaults to the homepage.
function redirect($text) {
	header("Location: " . $config["baseURL"] . "/" . $text);
	flush();
	exit();
}

// Refreshes the current page.
function refresh($time) {
	header("Refresh:" . $time . "");
	flush();
	exit();
}

// Logs the user out.
function logout() {
	session_unset();
	session_destroy();
	redirect("");
}

// Makes sure there aren't any bad characters in usernames
function checkUsername($username) {
if (!preg_match("#^[a-zA-Z0-9_-]+$#", $username)) {
   return false;   
} 
else {
   return true;
}
}

// Update the user's last action and set their last active time to now.
function update_last_action($action) {
	global $db;
	$result = $db->query("UPDATE users SET lastactive='" . time() . "', lastaction='" . $db->real_escape_string($action) . "' WHERE userid='" . $_SESSION["userid"] . "'");
}

// Parse the user's last action as a language string if it is one.
// (you have to specify the array as an argument otherwise it doesn't work)
function parseAction($action, $array) {
    if (stripos($action, "action.") !== false )
    {
        return $array[$action];
    }
    else
    {
        return $action;
    }
}

// Display a nice message.
function message($content) {
	echo "<div class='message'>" . $content . "</div>";
}

// Outputs the data of an array into a file, like the config.
function saveConfig($file, $array) {
    $getArray = var_export($array, true);
    file_put_contents($file, '<?php '.PHP_EOL. '$config ='. $getArray .';' .PHP_EOL. '?>');
}

// Convert a unix timestamp into a human readable time format.
function relativeTime($timestamp) {
	$now = time();
	$diff = $now - $timestamp;
	
    if ($timestamp == 0) {
		return "Never";
	}

	if ($diff <= 0) {
		return "Just now";
	}
	
	elseif ($diff == 1) {
		return "1 second ago";
	}
	
	elseif ($diff > 1 && $diff < 60) {
		return $diff . " seconds ago";
	}
	
	elseif ($diff >= 60 && $diff <= 120) {
		return "1 minute ago";
	}
	
	elseif ($diff > 120 && $diff < 3600) {
		return round($diff / 60) . " minutes ago";
	}
	
	elseif ($diff >= 3600 && $diff <= 7200) {
		return "1 hour ago";
	}
	
	elseif ($diff > 7200 && $diff < 86400) {
		return round(($diff / 60) / 60) . " hours ago";
	}
	
	elseif ($diff >= 86400 && $diff <= 172800) {
		return "1 day ago";
	}
	
	elseif ($diff > 172800 && $diff < 604800) {
		return round((($diff / 60) / 60) / 24) . " days ago";
	}
	
	elseif ($diff >= 604800 && $diff <= 1209600) {
		return "1 week ago";
	}
	
	elseif ($diff > 1209600 && $diff < 2419200) {
		return round(((($diff / 60) / 60) / 24) / 7) . " weeks ago";
	}
	
	elseif ($diff >= 2419200 && $diff <= 4838400) {
		return "1 month ago";
	}
	
	elseif ($diff > 4838400 && $diff < 29030400) {
		return round((((($diff / 60) / 60) / 24) / 7) / 4) . " months ago";
	}
	
	elseif ($diff >= 29030400 && $diff <= 58060800) {
		return "1 year ago";
	}
	
	elseif ($diff > 58060800) {
		return round(((((($diff / 60) / 60) / 24) / 7) / 4) / 12) . " years ago";
	}
}

// Display pagination.
function pagination($pageName) {
	global $lang, $q2, $currentPage, $pages;
	$adjacents = "2";
	echo '<div class="pagination">';
	// if ($currentPage > 1) {
	// 	echo '<a class="buttonbig" href="/'.$pageName.'/'.$q2.'/1/">'.$lang["nav.FirstPage"].'</a>';
	// }
	if ($currentPage <= 1) {
		echo '<span class="buttondisabled">'.$lang["nav.PrevPage"].'</span>';
	}
	if ($currentPage > 1) {
		echo '<a class="buttonbig" href="/'.$pageName.'/'.$q2.'/'.($currentPage - 1).'/">'.$lang["nav.PrevPage"].'</a>';
	}
	if ($pages <= 10) {
		for ($x = 1; $x <= $pages; $x++){
			if ($x == $currentPage) {
				echo '<span class="buttondisabled">'.$x.'</span>';
			} else {
				echo '<a class="buttonbig" href="/'.$pageName.'/'.$q2.'/'.$x.'/">'.$x.'</a>';
			}
		}
	} elseif ($pages > 10) {
		if ($currentPage <= 4) {
			for ($x = 1; $x < 8; $x++) {
				if ($x == $currentPage) {
					echo '<span class="buttondisabled">'.$x.'</span>';
				} else {
					echo '<a class="buttonbig" href="/'.$pageName.'/'.$q2.'/'.$x.'/">'.$x.'</a>';
				}
			}
			echo '<span>…</span>';
			echo '<a class="buttonbig" href="/'.$pageName.'/'.$q2.'/'.$pages.'/">'.$pages.'</a>';
		} elseif ($currentPage > 4 && $currentPage < $pages - 4) {
			echo '<a class="buttonbig" href="/'.$pageName.'/'.$q2.'/1/">1</a>';
			echo '<span>…</span>';
			for ($x = $currentPage - $adjacents; $x <= $currentPage + $adjacents; $x++) {
				if ($x == $currentPage) {
					echo '<span class="buttondisabled">'.$x.'</span>';
				} else {
					echo '<a class="buttonbig" href="/'.$pageName.'/'.$q2.'/'.$x.'">'.$x.'</a>';
				}
			}
			echo '<span>…</span>';
			echo '<a class="buttonbig" href="/'.$pageName.'/'.$q2.'/'.$pages.'/">'.$pages.'</a>';
		} else {
			echo '<a class="buttonbig" href="/'.$pageName.'/'.$q2.'/1/">1</a>';
			echo '<span>…</span>';
			for ($x = $pages - 6; $x <= $pages; $x++) {
				if ($x == $currentPage) {
					echo '<span class="buttondisabled">'.$x.'</span>';
				} else {
					echo '<a class="buttonbig" href="/'.$pageName.'/'.$q2.'/'.$x.'/">'.$x.'</a>';
				}
			}
		}
	}
	if ($currentPage >= $pages) {
		echo '<span class="buttondisabled">'.$lang["nav.NextPage"].'</span>';
	}
	if ($currentPage < $pages) {
		echo '<a class="buttonbig" href="/'.$pageName.'/'.$q2.'/'.($currentPage + 1).'/">'.$lang["nav.NextPage"].'</a>';
	}
	if ($currentPage < $pages) {
		echo '<a class="buttonbig" href="/'.$pageName.'/'.$q2.'/'.$pages.'/">'.$lang["nav.LastPage"].'</a>';
	}
	echo '</div>';
}

?>
