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
function arrayToFile($file, $array) {
    file_put_contents($file, '<?php '.PHP_EOL.'return '.var_export($array, true).';');
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

?>
