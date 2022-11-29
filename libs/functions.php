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
    global $config;
    if (!$config["baseURL"] || $config["baseURL"] == "http://example.com")
    {
        header("Location: " . "/" . $text);
	    flush();
    }
    else
    {
        header("Location: " . $config["baseURL"] . "/" . $text);
	    flush();
    }

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

// Generates a URL for a page based on the site's baseURL
function genURL($page) {
    global $config;
    if (!$config["baseURL"] || $config["baseURL"] == "http://example.com")
    {
        $generated = "/" . $page;
    }
    else
    {
        $generated = $config["baseURL"] . "/" . $page;
    }
    return $generated;
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
	global $lang;
	$now = time();
	$diff = $now - $timestamp;
	
    if ($timestamp == 0) {
		return $lang["time.Never"];
	}

	if ($diff <= 0) {
		return $lang["time.JustNow"];
	}
	
	elseif ($diff == 1) {
		return $lang["time.1SecAgo"];
	}
	
	elseif ($diff > 1 && $diff < 60) {
		return $diff . ' ' . $lang["time.SecAgo"];
	}
	
	elseif ($diff >= 60 && $diff <= 120) {
		return $lang["time.1MinAgo"];
	}
	
	elseif ($diff > 120 && $diff < 3600) {
		return round($diff / 60) . ' ' . $lang["time.MinAgo"];
	}
	
	elseif ($diff >= 3600 && $diff <= 7200) {
		return $lang["time.1HrAgo"];
	}
	
	elseif ($diff > 7200 && $diff < 86400) {
		return round(($diff / 60) / 60) . ' ' . $lang["time.HrsAgo"];
	}
	
	elseif ($diff >= 86400 && $diff <= 172800) {
		return $lang["time.1DayAgo"];
	}
	
	elseif ($diff > 172800 && $diff < 604800) {
		return round((($diff / 60) / 60) / 24) . ' ' . $lang["time.DaysAgo"];
	}
	
	elseif ($diff >= 604800 && $diff <= 1209600) {
		return $lang["time.1WeekAgo"];
	}
	
	elseif ($diff > 1209600 && $diff < 2419200) {
		return round(((($diff / 60) / 60) / 24) / 7) . ' ' . $lang["time.WeeksAgo"];
	}
	
	elseif ($diff >= 2419200 && $diff <= 4838400) {
		return $lang["time.1MonthAgo"];
	}
	
	elseif ($diff > 4838400 && $diff < 29030400) {
		return round((((($diff / 60) / 60) / 24) / 7) / 4) . ' ' . $lang["time.MonthsAgo"];
	}
	
	elseif ($diff >= 29030400 && $diff <= 58060800) {
		return $lang["time.1YearAgo"];
	}
	
	elseif ($diff > 58060800) {
		return round(((((($diff / 60) / 60) / 24) / 7) / 4) / 12) . ' ' . $lang["time.YearsAgo"];
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
        echo '<span class="buttondisabled">'.$lang["nav.FirstPage"].'</span>';
		echo '<span class="buttondisabled">'.$lang["nav.PrevPage"].'</span>';
	}
	if ($currentPage > 1) {
        echo '<a class="buttonbig" href="' . genURL($pageName.'/'.$q2.'/'. "1") .'/">'.$lang["nav.FirstPage"].'</a>';
		echo '<a class="buttonbig" href="' . genURL($pageName.'/'.$q2.'/'.($currentPage - 1)) .'/">'.$lang["nav.PrevPage"].'</a>';
	}
	if ($pages <= 10) {
		for ($x = 1; $x <= $pages; $x++){
			if ($x == $currentPage) {
				echo '<span class="buttondisabled">'.$x.'</span>';
			} else {
				echo '<a class="buttonbig" href="'. genURL($pageName.'/'.$q2.'/'.$x.'/">'.$x) .'</a>';
			}
		}
	} elseif ($pages > 10) {
		if ($currentPage <= 4) {
			for ($x = 1; $x < 8; $x++) {
				if ($x == $currentPage) {
					echo '<span class="buttondisabled">'.$x.'</span>';
				} else {
					echo '<a class="buttonbig" href="'. genURL($pageName.'/'.$q2.'/'.$x.'/">'.$x) .'</a>';
				}
			}
			echo '<span>…</span>';
			echo '<a class="buttonbig" href="'. genURL($pageName.'/'.$q2.'/'.$pages.'/">'.$pages) .'</a>';
		} elseif ($currentPage > 4 && $currentPage < $pages - 4) {
			echo '<a class="buttonbig" href="'. genURL($pageName.'/'.$q2) .'/1/">1</a>';
			echo '<span>…</span>';
			for ($x = $currentPage - $adjacents; $x <= $currentPage + $adjacents; $x++) {
				if ($x == $currentPage) {
					echo '<span class="buttondisabled">'.$x.'</span>';
				} else {
					echo '<a class="buttonbig" href="'. genURL($pageName.'/'.$q2.'/'.$x.'">'.$x) .'</a>';
				}
			}
			echo '<span>…</span>';
			echo '<a class="buttonbig" href="'. genURL($pageName.'/'.$q2.'/'.$pages.'/">'.$pages) .'</a>';
		} else {
			echo '<a class="buttonbig" href="'. genURL($pageName.'/'.$q2).'/1/">1</a>';
			echo '<span>…</span>';
			for ($x = $pages - 6; $x <= $pages; $x++) {
				if ($x == $currentPage) {
					echo '<span class="buttondisabled">'.$x.'</span>';
				} else {
					echo '<a class="buttonbig" href="'. genURL($pageName.'/'.$q2.'/'.$x.'/">'.$x).'</a>';
				}
			}
		}
	}
	if ($currentPage >= $pages) {
		echo '<span class="buttondisabled">'.$lang["nav.NextPage"].'</span>';
        echo '<span class="buttondisabled">'.$lang["nav.LastPage"].'</span>';
	}
	if ($currentPage < $pages) {
		echo '<a class="buttonbig" href="'. genURL($pageName.'/'.$q2.'/'.($currentPage + 1)).'/">'.$lang["nav.NextPage"].'</a>';
	}
	if ($currentPage < $pages) {
		echo '<a class="buttonbig" href="'. genURL($pageName.'/'.$q2.'/'.$pages).'/">'.$lang["nav.LastPage"].'</a>';
	}
	echo '</div>';
}

?>
