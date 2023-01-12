<?php
// functions.php
// Contains functions for use throughout the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

// Display buttons for adding BBCode to textareas.
function BBCodeButtons($num = "") {
    global $lang;
    // Serve the JavaScript.
    echo("<script type='text/javascript'>
    function formatText" . $num . "(tag) {
        var Field = document.getElementById('textbox" . $num . "');
        var val = Field.value;
        var selected_txt = val.substring(Field.selectionStart, Field.selectionEnd);
        var before_txt = val.substring(0, Field.selectionStart);
        var after_txt = val.substring(Field.selectionEnd, val.length);
        Field.value += '[' + tag + ']' + '[/' + tag + ']';
        var endTag = '[/' + tag + ']'
        Field.focus();
        Field.setSelectionRange(Field.selectionStart - endTag.length,Field.selectionEnd - endTag.length);
    }
    function formatTextWithDetails" . $num . "(tag, input) {
        var Field = document.getElementById('textbox" . $num . "');
        var val = Field.value;

        var Details = document.getElementById(input + '" . $num . "');
        if (Details.nodeName == 'SELECT')
        {
            var detailsVal = Details.options[Details.selectedIndex].value;
        }
        else {
            var detailsVal = Details.value;
        }

        var selected_txt = val.substring(Field.selectionStart, Field.selectionEnd);
        var before_txt = val.substring(0, Field.selectionStart);
        var after_txt = val.substring(Field.selectionEnd, val.length);
        Field.value += '[' + tag + '=' + detailsVal + ']' + '[/' + tag + ']';
        var endTag = '[/' + tag + ']'
        Field.focus();
        Field.setSelectionRange(Field.selectionStart - endTag.length,Field.selectionEnd - endTag.length);
    }
    </script>");

    listener("beforeRenderBBCodeTray");

    // Serve the BBCode buttons.
    echo "<div class='bbcodetraycontainer'>";

    listener("beforeRenderBBCodeButtons");

    echo "<div class='bbcodetray'>";
    echo "<input type='button' class='bbcode bbold' value='" . $lang["BBCode.Bold"] . "' onclick=formatText" . $num . "('b');>";
    echo "<input type='button' class='bbcode bitalic' value='" . $lang["BBCode.Italic"] . "' onclick=formatText" . $num . "('i');>";
    echo "<input type='button' class='bbcode bunderline' value='" . $lang["BBCode.Underline"] . "' onclick=formatText" . $num . "('u');>";
    echo "<input type='button' class='bbcode bstrike' value='" . $lang["BBCode.Strikethrough"] . "' onclick=formatText" . $num . "('s');>";
    echo "<input type='button' class='bbcode bheader' value='" . $lang["BBCode.Header"] . "' onclick=formatText" . $num . "('h');>";
    echo "<input type='button' class='bbcode bcode' value='" . $lang["BBCode.Code"] . "' onclick=formatText" . $num . "('code');>";

    echo "</div><div class='bbcodetray'>";
    echo "<input type='button' class='bbcode blink' value='" . $lang["BBCode.Link"] . "' onclick=formatText" . $num . "('url');>";
    echo "<input type='button' class='bbcode bimage' value='" . $lang["BBCode.Image"] . "' onclick=formatText" . $num . "('img');>";
    echo "<input type='button' class='bbcode bspoiler' value='" . $lang["BBCode.Spoiler"] . "' onclick=formatText" . $num . "('spoiler');>";

    echo "</div><div class='bbcodetray'>";
    echo "<input type='color' class='bbcode bcolor' id='colorbox" . $num . "' value='#000000'" .  "' onchange=formatTextWithDetails" . $num . "('color','colorbox');>";
    echo "<select name='font-size' class='bbcode bsize' id='sizebox" . $num . "'" .  "' onchange=formatTextWithDetails" . $num . "('size','sizebox');>";
    echo "<option value='80'>80%</option>";
    echo "<option value='100'>100%</option>";
    echo "<option value='150'>150%</option>";
    echo "<option value='200'>200%</option>";
    echo "<option value='300'>300%</option>";
    echo "</select>";
    echo "</div>";

    listener("afterRenderBBCodeButtons");

    echo "</div>";
}

// Hash a string depending on what algorithm is desired by the configuration.
function hashstring($text) {
    global $config;
    
    if ($config["hashAlgo"] == "md5") {
        $hashedstring = md5($text);
    }
    elseif ($config["hashAlgo"] == "sha512") {
        $hashedstring = hash("sha512", $text);
    }
    // Default to md5.
    else {
        $hashedstring = md5($text);
    }

    listener("beforeReturnHash");

    return $hashedstring;
}

// Hugely important function which generates a random string.
function random_str($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~`!@#$%^&*()-_=+[{]};:\'"<>,./?|\\';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    listener("beforeReturnRandomString");

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

    listener("beforeLogout");
	// Log out so long as the user isn't requesting an image.
	if ($_SERVER["HTTP_ACCEPT"] != "image/avif,image/webp,*/*") {
		session_unset();
		session_destroy();
		redirect("");
	}
    listener("afterLogout");
}

// Makes sure there aren't any bad characters in usernames
function checkUsername($username) {
if (!preg_match("#^[a-zA-Z0-9\s_-]+$#", $username)) {
   $returned = false;   
} 
else {
   $returned = true;
}
listener("beforeReturnUsernameCheck");
return $returned;
}

// Update the user's last action and set their last active time to now.
function update_last_action($action) {
	global $db;
	$result = $db->query("UPDATE users SET lastactive='" . time() . "', lastaction='" . $db->real_escape_string($action) . "' WHERE userid='" . $_SESSION["userid"] . "'");
    listener("afterUpdateAction");
}

// Parse the user's last action as a language string if it is one.
// (you have to specify the array as an argument otherwise it doesn't work)
function parseAction($action, $array) {
    if (stripos($action, "action.") !== false )
    {
        $returned = $array[$action];
    }
    else
    {
        $returned = $action;
    }
    listener("beforeReturnAction");
    return $returned;
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
    listener("beforeReturnGeneratedURL");
    return $generated;
}

// Display a nice message.
function message($content) {
	$message = "<div class='message'>" . $content . "</div>";
    listener("beforeReturnMessage");
    echo $message;
}

// Outputs the data of an array into a file, like the config.
function saveConfig($file, $array) {
    $getArray = var_export($array, true);
    listener("beforeSaveConfig");
    file_put_contents($file, '<?php '.PHP_EOL. '$config = '. $getArray .';' .PHP_EOL. '?>');
    listener("afterSaveConfig");
}

function saveExtensionConfig($file, $array) {
    $getArray = var_export($array, true);
    listener("beforeSaveExtensionConfig");
    file_put_contents($file, '<?php '.PHP_EOL. '$extensions = '. $getArray .';' .PHP_EOL. '?>');
    listener("afterSaveExtensionConfig");
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
	// if ($currentPage > 1) {
	// 	echo '<a class="buttonbig" href="/'.$pageName.'/'.$q2.'/1/">'.$lang["nav.FirstPage"].'</a>';
	// }
    echo '<div class="pagination"><div class="paginationend">';
	if ($currentPage <= 1) {
        echo '<span class="pageButtonDisabled">'.$lang["nav.FirstPage"].'</span>';
		echo '<span class="pageButtonDisabled">'.$lang["nav.PrevPage"].'</span>';
	}
	if ($currentPage > 1) {
        echo '<a class="pageButton" href="' . genURL($pageName.'/'.$q2.'/'. "1") .'/">'.$lang["nav.FirstPage"].'</a>';
		echo '<a class="pageButton" href="' . genURL($pageName.'/'.$q2.'/'.($currentPage - 1)) .'/">'.$lang["nav.PrevPage"].'</a>';
	}
    echo '</div>';
    echo '<div class="paginationinside">';
	if ($pages <= 10) {
		for ($x = 1; $x <= $pages; $x++){
			if ($x == $currentPage) {
				echo '<span class="pageButtonDisabled pageNow">'.$x.'</span>';
			} else {
				echo '<a class="pageButton" href="'. genURL($pageName.'/'.$q2.'/'.$x.'/">'.$x) .'</a>';
			}
		}
	} elseif ($pages > 10) {
		if ($currentPage <= 4) {
			for ($x = 1; $x < 8; $x++) {
				if ($x == $currentPage) {
					echo '<span class="pageButtonDisabled pageNow">'.$x.'</span>';
				} else {
					echo '<a class="pageButton" href="'. genURL($pageName.'/'.$q2.'/'.$x.'/">'.$x) .'</a>';
				}
			}
			echo '<span class="paginationdots">…</span>';
			echo '<a class="pageButton" href="'. genURL($pageName.'/'.$q2.'/'.$pages.'/">'.$pages) .'</a>';
		} elseif ($currentPage > 4 && $currentPage < $pages - 4) {
			echo '<a class="pageButton" href="'. genURL($pageName.'/'.$q2) .'/1/">1</a>';
			echo '<span class="paginationdots">…</span>';
			for ($x = $currentPage - $adjacents; $x <= $currentPage + $adjacents; $x++) {
				if ($x == $currentPage) {
					echo '<span class="pageButtonDisabled pageNow">'.$x.'</span>';
				} else {
					echo '<a class="pageButton" href="'. genURL($pageName.'/'.$q2.'/'.$x.'">'.$x) .'</a>';
				}
			}
			echo '<span class="paginationdots">…</span>';
			echo '<a class="pageButton" href="'. genURL($pageName.'/'.$q2.'/'.$pages.'/">'.$pages) .'</a>';
		} else {
			echo '<a class="pageButton" href="'. genURL($pageName.'/'.$q2).'/1/">1</a>';
			echo '<span class="paginationdots">…</span>';
			for ($x = $pages - 6; $x <= $pages; $x++) {
				if ($x == $currentPage) {
					echo '<span class="pageButtonDisabled pageNow">'.$x.'</span>';
				} else {
					echo '<a class="pageButton" href="'. genURL($pageName.'/'.$q2.'/'.$x.'/">'.$x).'</a>';
				}
			}
		}
	}
    echo "</div>";
    echo '<div class="paginationend pagendr">';
	if ($currentPage >= $pages) {
		echo '<span class="pageButtonDisabled">'.$lang["nav.NextPage"].'</span>';
        echo '<span class="pageButtonDisabled">'.$lang["nav.LastPage"].'</span>';
	}
	if ($currentPage < $pages) {
		echo '<a class="pageButton" href="'. genURL($pageName.'/'.$q2.'/'.($currentPage + 1)).'/">'.$lang["nav.NextPage"].'</a>';
	}
	if ($currentPage < $pages) {
		echo '<a class="pageButton" href="'. genURL($pageName.'/'.$q2.'/'.$pages).'/">'.$lang["nav.LastPage"].'</a>';
	}
	echo '</div></div>';
}

$hooks = array();

function listener($hook)
{
    global $hooks;
	if (isset($hooks[$hook])) {		
		foreach ($hooks[$hook] as $function) {
			call_user_func($function);
		}
	}
}

function hook($hook, $function)
{
    global $hooks;
    if (isset($hooks[$hook])) {
        array_push($hooks[$hook], $function);
    }
    else {
        $hooks[$hook] = array($function);
    }
    
}

// Generates a random string for the captcha.
function randomCaptcha($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    listener("beforeReturnCaptchaString");
    return $randomString;
}

// Simple captcha generator.
function generateCaptcha($numChars)
{
    $chars = randomCaptcha($numChars);
    $width = 15 * $numChars;
    $height = 30;

    // Create a blank image and add some content
    $new_image = imagecreatetruecolor($width, $height);
    $bg_color = imagecolorallocate($new_image,  rand(125, 255), rand(125, 255), rand(125, 255));

    // Set the background color
    imagefill($new_image, 0, 0, $bg_color);

    // Add some noise to the background.
    for ($i = 0; $i < $numChars; $i++) {
        imagefilledrectangle($new_image, $width/$numChars*$i+rand(-10, 10), 1, $width*0.8-rand(1, 2), $height*1.4-rand(1, 3), imagecolorallocate($new_image, rand(125, 250), rand(125, 250), rand(125,250)));
    }

    // Add some noise to the background.
    for ($i = 0; $i < $numChars*6; $i++) {
        imagefilledellipse($new_image, $width/$numChars*$i/6+rand(-10, 10), $height/2+rand(-10, 10), $width*0.3-rand(1, 3), $height*0.7-rand(1, 3), imagecolorallocate($new_image, rand(125, 250), rand(125, 250), rand(125,250)));
    }

    // Add the text to the image.
    for ($i = 0; $i < $numChars; $i++) {
        $text = imagecolorallocate($new_image, rand(10, 110), rand(10, 110), rand(10, 110));
        imagestring($new_image, rand(3,5), $width/$numChars+$i*rand(9, 10), 7,  substr($chars, $i, 1), $text);
    }

    ob_start();

    // Save the image as png
    imagepng($new_image, NULL, 9);

    $rawImageBytes = ob_get_clean();

    listener("beforeDrawCaptcha");

    echo "<img src='data:image/jpeg;base64," . base64_encode($rawImageBytes) . "' class='captcha'>";

    // Free the memory
    imagedestroy($new_image);

    return $chars;
}
?>
