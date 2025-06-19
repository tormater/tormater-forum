<?php
// functions.php
// Contains functions for use throughout the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

// Display buttons for adding BBCode to textareas.
function BBCodeButtons($num = "") {
    global $lang;
    // Serve the JavaScript.
    echo("<script type='text/javascript' src='" . genURL("assets/bbcode.js") . "'></script>");

    listener("beforeRenderBBCodeTray");

    // Serve the BBCode buttons.
    echo "<div class='bbcodetraycontainer'>";

    listener("beforeRenderBBCodeButtons");

    echo "<div class='bbcodetray'>";
    echo "<input type='button' class='bbcode bbold' value='" . $lang["BBCode.Bold"] . "' onclick=formatText('b'," . $num . ");>";
    echo "<input type='button' class='bbcode bitalic' value='" . $lang["BBCode.Italic"] . "' onclick=formatText('i'," . $num . ");>";
    echo "<input type='button' class='bbcode bunderline' value='" . $lang["BBCode.Underline"] . "' onclick=formatText('u'," . $num . ");>";
    echo "<input type='button' class='bbcode bstrike' value='" . $lang["BBCode.Strikethrough"] . "' onclick=formatText('s'," . $num . ");>";
    echo "<input type='button' class='bbcode bheader' value='" . $lang["BBCode.Header"] . "' onclick=formatText('h'," . $num . ");>";
    echo "<input type='button' class='bbcode bcode' value='" . $lang["BBCode.Code"] . "' onclick=formatText('code'," . $num . ");>";

    echo "</div><div class='bbcodetray'>";
    echo "<input type='button' class='bbcode blink' value='" . $lang["BBCode.Link"] . "' onclick=formatText('url'," . $num . ");>";
    echo "<input type='button' class='bbcode bimage' value='" . $lang["BBCode.Image"] . "' onclick=formatText('img'," . $num . ");>";
    echo "<input type='button' class='bbcode bspoiler' value='" . $lang["BBCode.Spoiler"] . "' onclick=formatText('spoiler'," . $num . ");>";

    echo "</div><div class='bbcodetray'>";
    echo "<input type='color' class='bbcode bcolor' id='colorbox" . $num . "' value='#000000'" .  "' onchange=formatTextWithDetails('color','colorbox'," . $num . ");>";
    echo "<select name='font-size' class='bbcode bsize' id='sizebox" . $num . "'" .  "' onchange=formatTextWithDetails('size','sizebox'," . $num . ");>";
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

function getNumForRole($role) {
    if (!isset($_SESSION['signed_in']) || $_SESSION['signed_in'] == false) return 0;
    if ($role == "Administrator") return 4;
    else if ($role == "Moderator") return 3;
    else return 1;
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

    listener("beforeReturnHash", $hashedstring, $text);

    return $hashedstring;
}

// Hugely important function which generates a random string.
function random_str($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~`!@#$%^&*()-_=+[{]};:\'"<>,./?|\\';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    listener("beforeReturnRandomString", $randomString, $length);

    return $randomString;
}

// Redirects the user to the specified page. If blank it defaults to the homepage.
function redirect($text) {
    global $config;
    if (!headers_sent()) {
        header("Location: " . genURL($text));
        flush();
    }
    else {
        echo '<script type="text/javascript">window.location.replace("'. genURL($text) .'");</script>';
    }
    exit();
}

// Refreshes the current page.
function refresh($time) {
	if (!headers_sent()) {
	    header("Refresh:" . $time . "");
	    flush();
	}
	else {
            echo '<script type="text/javascript">window.location.reload();</script>';
        }
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
listener("beforeReturnUsernameCheck", $returned, $username);
return $returned;
}

// Update the user's last action and set their last active time to now.
function update_last_action($action) {
    if (isset($_SESSION['signed_in']) && ($_SESSION['signed_in'] == true))
    {
	global $db;
	$result = $db->query("UPDATE users SET lastactive='" . time() . "', lastaction='" . $db->real_escape_string($action) . "' WHERE userid='" . $_SESSION["userid"] . "'");
    listener("afterUpdateAction", $action);
    }
}

// Parse the user's last action as a language string if it is one.
// (you have to specify the array as an argument otherwise it doesn't work)
function parseAction($action, $array) {
    if (!is_null($action) && stripos($action, "action.") !== false )
    {
        $returned = $array[$action];
    }
    else
    {
        $returned = $action;
    }
    listener("beforeReturnAction", $returned, $action);
    return $returned;
}

// Generates a URL for a page based on the site's baseURL
function genURL($page) {
    global $config;
    $query = "";
    if ($config["modRewriteDisabled"] == 1 && empty(pathinfo($page, PATHINFO_EXTENSION))) {
        $query = "index.php";
        if (strlen($page) > 0) {
            $query .= "?url=";
            $page = strtr($page, "?", "&");
        }
    }
    if (!$config["baseURL"] || $config["baseURL"] == "http://example.com")
    {
        $generated = "/" . $query . $page;
    }
    else
    {
        $generated = $config["baseURL"] . "/" . $query . $page;
    }
    listener("beforeReturnGeneratedURL", $generated, $page);
    return $generated;
}

// Display a nice message.
function message($content, $return=false) {
    global $template;
    $message = $template->render("templates/message.html", ["content" => $content]);
    listener("beforeReturnMessage", $message, $content);
    if ($return) return $message;
    else echo $message;
}

// Outputs the data of an array into a file, like the config.
function saveConfig($file, $array) {
    if (!is_writeable("config")) return false;
    $getArray = var_export($array, true);
    listener("beforeSaveConfig", $getArray, $array, $file);
    $success = file_put_contents($file, '<?php '.PHP_EOL. '$config = '. $getArray .';' .PHP_EOL. '?>');
    listener("afterSaveConfig");
    if (!$success) return false;
    else return true;
}

function saveExtensionConfig($file, $array) {
    if (!is_writeable("config")) return false;
    $getArray = var_export($array, true);
    listener("beforeSaveExtensionConfig", $getArray, $array, $file);
    $success = file_put_contents($file, '<?php '.PHP_EOL. '$extensions = '. $getArray .';' .PHP_EOL. '?>');
    listener("afterSaveExtensionConfig");
    if (!$success) return false;
    else return true;
}

function saveExtensionSettingsConfig($file, &$array) {
    if (!is_writeable("config")) return false;
    global $allExtensions;
    foreach ($allExtensions as $e) {
        if (file_exists("extensions/" . $e . "/manifest.json"))
        {
            $manifest = json_decode(file_get_contents("extensions/" . $e . "/manifest.json"), true);
            if (isset($manifest["settings"])) {
                foreach($manifest["settings"] as $setting) {
                    if (!isset($array[$e][$setting["id"]])) {
                        $array[$e][$setting["id"]] = $setting["default"];
                    }
                }
            }
        }
    }
    $getArray = var_export($array, true);
    $success = file_put_contents($file, '<?php '.PHP_EOL. '$extension_config = '. $getArray .';' .PHP_EOL. '?>');
    if (!$success) return false;
    else return true;
}

// Get the name of an extension given it's file path
function getExtensionName($path) {
    $exploded = explode("/", $path);
    return end($exploded);
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
		return sprintf($lang["time.SecAgo"],$diff);
	}
	
	elseif ($diff >= 60 && $diff <= 60*2) {
		return $lang["time.1MinAgo"];
	}
	
	elseif ($diff > 60*2 && $diff < 60*60) {
		return sprintf($lang["time.MinAgo"],round($diff / 60));
	}
	
	elseif ($diff >= 60*60 && $diff <= 60*60*2) {
		return $lang["time.1HrAgo"];
	}
	
	elseif ($diff > 60*60*2 && $diff < 60*60*24) {
		return sprintf($lang["time.HrsAgo"],round(($diff / 60) / 60));
	}
	
	elseif ($diff >= 60*60*24 && $diff <= 60*60*24*2) {
		return $lang["time.1DayAgo"];
	}
	
	elseif ($diff > 60*60*24*2 && $diff < 60*60*24*7) {
		return sprintf($lang["time.DaysAgo"],round((($diff / 60) / 60) / 24));
	}
	
	elseif ($diff >= 60*60*24*7 && $diff <= 60*60*24*7*2) {
		return $lang["time.1WeekAgo"];
	}
	
	elseif ($diff > 60*60*24*7*2 && $diff < 60*60*24*7*4) {
		return sprintf($lang["time.WeeksAgo"],round(((($diff / 60) / 60) / 24) / 7));
	}
	
	elseif ($diff >= 60*60*24*7*4 && $diff <= 60*60*24*7*4*2) {
		return $lang["time.1MonthAgo"];
	}
	
	elseif ($diff > 60*60*24*7*4*2 && $diff < 60*60*24*7*4*12) {
		return sprintf($lang["time.MonthsAgo"],round((((($diff / 60) / 60) / 24) / 7) / 4));
	}
	
	elseif ($diff >= 60*60*24*7*4*12 && $diff <= 60*60*24*7*4*12*2) {
		return $lang["time.1YearAgo"];
	}
	
	elseif ($diff > 60*60*24*7*4*12*2) {
		return sprintf($lang["time.YearsAgo"],round(((((($diff / 60) / 60) / 24) / 7) / 4) / 12));
	}
}

// Display pagination.

function genPaginationURL($page_index, $qN) {
        global $url, $pages; 
        $page_url = "";
        for ($i = 0; $i < $qN-1; $i++) { // Output all the URL's parameters before the page string.
            if (isset($url[$i])) $page_url .= $url[$i] . "/";
        }
        
        if (strpos($_SERVER["QUERY_STRING"], '&')) $query = "?" . substr($_SERVER["QUERY_STRING"], strpos($_SERVER["QUERY_STRING"], '&')+1, null);
        else $query = "";
        if ($page_index > 1) return genURL($page_url . $page_index . $query);
        else return genURL($page_url . $query);
}
    
function renderPageButton($page_index, $qN, $label="") {
    global $template, $currentPage, $pages;
    if ($page_index < 1) $page_index = 1;
    if ($page_index > $pages) $page_index = $pages;
    $data = array(
      "label" => $label,
      "url" => genPaginationURL($page_index, $qN)
    );
    if (empty($data["label"])) $data["label"] = $page_index;
    if ($page_index == $currentPage) return $template->render("templates/pagination/page_button_disabled.html",$data);
    return $template->render("templates/pagination/page_button.html",$data);
}

function renderPagination($qN = 3, $return = 0) {
    global $template, $lang, $currentPage, $pages;
    $data = array(
      "first" => renderPageButton(1,$qN,$lang["nav.FirstPage"]),
      "previous" => renderPageButton($currentPage-1,$qN,$lang["nav.PrevPage"]),
      "page_buttons" => "",
      "next" => renderPageButton($currentPage+1,$qN,$lang["nav.NextPage"]),
      "last" => renderPageButton($pages,$qN,$lang["nav.LastPage"])
    );
    if ($pages <= 7) {
        for ($i = 1; $i <= $pages; $i++) {
            $data["page_buttons"] .= renderPageButton($i,$qN);
        }
    }
    else {
        if ($currentPage <= 4) {
	    for ($i = 1; $i < 8; $i++) {
                $data["page_buttons"] .= renderPageButton($i,$qN);
            }
        }
        else if ($currentPage > 4 && $currentPage < $pages - 4) {
            for ($i = $currentPage - 3; $i <= $currentPage + 3; $i++) {
                $data["page_buttons"] .= renderPageButton($i,$qN);
            }         
        }
        else {
            for ($i = $pages - 6; $i <= $pages; $i++) {
                $data["page_buttons"] .= renderPageButton($i,$qN);
            }
        }
    }
    $pagination = $template->render("templates/pagination/pagination.html",$data);
    if ($return) return $pagination;
    else echo $pagination;
}

function pagination($pageName) {
    renderPagination(3,0);
}
function pagination_return($pageName) {
    return renderPagination(3,1);
}

$hooks = array();

function listener($hook, &...$args)
{
    global $hooks;
	if (isset($hooks[$hook])) {		
		foreach ($hooks[$hook] as $function) {
			call_user_func_array($function, array(&$args));
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
    $characters = '123456789abcdefghijklmnpqrtuvwxyzABCDEFGHIJKLMNPQRTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    listener("beforeReturnCaptchaString", $randomString, $length);
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

    for ($i = 0; $i < $numChars*6; $i++) {
        $rchoice = rand(0,3);

        if ($rchoice == 0) {
            imageline($new_image, rand(0,$width), rand(0,$height), rand(0,$width), rand(0,$height), imagecolorallocate($new_image, rand(125, 250), rand(125, 250), rand(125,250)));
        }
        else if ($rchoice == 1) {
            imagefilledellipse($new_image, intval($width/$numChars*$i/6+rand(-10, 10)), intval($height/2+rand(-10, 10)), intval($width*0.3-rand(1, 3)), intval($height*0.7-rand(1, 3)), imagecolorallocate($new_image, rand(125, 250), rand(125, 250), rand(125,250)));
        }
        else if ($rchoice == 2) {
            imagefilledrectangle($new_image, $width/$numChars*$i+rand(-10, 10), 1, $width*0.8-rand(1, 2), $height*1.4-rand(1, 3), imagecolorallocate($new_image, rand(125, 250), rand(125, 250), rand(125,250)));
        }
        else if ($rchoice == 3) {
            imagepolygon($new_image, array(
                rand(0,$width), rand(0,$height),
                rand(0,$width), rand(0,$height),
                rand(0,$width), rand(0,$height)
            ),
            imagecolorallocate($new_image, rand(125, 250), rand(125, 250), rand(125,250)));
        }
    }

    // Add the text to the image.
    for ($i = 0; $i < $numChars*2; $i++) {
        $text = imagecolorallocate($new_image, rand(10, 110), rand(10, 110), rand(10, 110));
        imagestring($new_image, rand(3,5), $width/$numChars+$i*rand(9, 10), 7,  substr($chars, $i, 1), $text);
    }

    ob_start();

    // Save the image as png
    imagewebp($new_image, NULL, 50);

    $rawImageBytes = ob_get_clean();

    listener("beforeDrawCaptcha", $rawImageBytes, $numChars);

    echo "<img src='data:image/webp;base64," . base64_encode($rawImageBytes) . "' class='captcha'>";

    // Free the memory
    imagedestroy($new_image);

    return $chars;
}

// Remove an account's avatar.
function removeAvatar($userid)
{
    global $db;

    $typeCheck = $db->query("SELECT avatar FROM users WHERE userid='" . $db->real_escape_string($userid) . "'");

    while ($t = $typeCheck->fetch_assoc()) {
        $typeOfFile = $t["avatar"];
    }

    if ($typeOfFile != "none") {
        unlink("avatars/" . $userid . "." . $typeOfFile);
        $db->query("UPDATE users SET avatar='none' WHERE userid='" . $db->real_escape_string($userid) . "'");
        return true;
    }

    return false;
}

// Brighten or darken a color hex value
function hexAdjustLight($originalHex, $percent) {

    $hex = $originalHex;
    if (stristr($hex, '#')) {
        $hex = str_replace('#', '', $hex);
    }

    $rgb = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];

    for ($i = 0; $i < 3; $i++) {

        if ($percent > 0) 
        {
            $rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1 - $percent));
        } 

        else 
        {
            $positivePercent = $percent - ($percent * 2);
            $rgb[$i] = round($rgb[$i] * (1 - $positivePercent));
        }
        
        if ($rgb[$i] > 255) $rgb[$i] = 255;

    }

    $hex = '';

    for ($i = 0; $i < 3; $i++) {

        $hexDigit = dechex($rgb[$i]);

        if (strlen($hexDigit) == 1) {
            $hexDigit = "0" . $hexDigit;
        }

        $hex .= $hexDigit;
    }

    listener("beforeReturnHexAdjustLight", $originalHex, $hex, $percent);
    return '#' . $hex;

}

// Delete a user given their userid and a mode of deletion.
function deleteUser($mode, $userid) {
    
    global $db, $lang;

    switch ($mode) {
        case "deleteKeepPosts":
	    // Make sure the inputted userid looks valid.
            if (($userid) and (isset($userid)) and (is_numeric($userid)) and ($config["mainAdmin"] != $userid)) {
                $existCheck = $db->query("SELECT 1 FROM users WHERE userid='" . $db->real_escape_string($userid) . "'");

                // Make sure there is actually a user under the specified userid.
                if(!$existCheck or $existCheck->num_rows < 1) {
                    echo($lang["panel.UseridError"]);
                }
                else {
                    // If everything checks out, mark the user as deleted.
                    $db->query("UPDATE users SET deleted='1' WHERE userid='" . $db->real_escape_string($userid) . "'");

                    // Remove the user's avatar as well.
                    removeAvatar($userid);

                    echo("</br>" . $lang["panel.DeleteUserSuccess"]);
                }
            }
	    break;    
        case "deleteHidePosts":
            // Make sure the inputted userid looks valid.
            if (($userid) and (isset($userid)) and (is_numeric($userid)) and ($config["mainAdmin"] != $userid)) {
                $existCheck = $db->query("SELECT 1 FROM users WHERE userid='" . $db->real_escape_string($userid) . "'");

                // Make sure there is actually a user under the specified userid.
                if(!$existCheck or $existCheck->num_rows < 1) {
                    echo($lang["panel.UseridError"]);
                }
                else {
                    // If everything checks out, mark the user as deleted and hide all of their posts.
                    $db->query("UPDATE users SET deleted='1' WHERE userid='" . $db->real_escape_string($userid) . "'");
                    $db->query("UPDATE posts SET deletedby='" . $db->real_escape_string($_SESSION["userid"]) . "' WHERE user='" . $db->real_escape_string($userid) . "'");

                    // Remove the user's avatar as well.
                    removeAvatar($userid);

                    echo("</br>" . $lang["panel.DeleteUserSuccessHide"]);
                }
            }
	    break;
        case "deleteRemovePosts":
            // Make sure the inputted userid looks valid.
            if (($userid) and (isset($userid)) and (is_numeric($userid)) and ($config["mainAdmin"] != $userid)) {
                $existCheck = $db->query("SELECT 1 FROM users WHERE userid='" . $db->real_escape_string($userid) . "'");

                // Make sure there is actually a user under the specified userid.
                if(!$existCheck or $existCheck->num_rows < 1) {
                    echo($lang["panel.UseridError"]);
                }
                else {
                    // If everything checks out, mark the user as deleted.
                    $db->query("UPDATE users SET deleted='1' WHERE userid='" . $db->real_escape_string($userid) . "'");

                    // Now we need to delete any threads the user might have started and the posts within those threads.
                    // Get all the ids of the user's threads first.
                    $threads = $db->query("SELECT threadid FROM threads WHERE startuser='" . $db->real_escape_string($userid) . "'");

                    // Now delete the posts from each of the threads and the threads themselves if there are any.
                    if ($threads->num_rows > 0) {
                        while ($row = $threads->fetch_assoc()) {
                            // Delete the thread's posts.
                            $db->query("DELETE FROM posts WHERE thread='" . $db->real_escape_string($row["threadid"]) . "'");

                            // Finally, delete the thread.
                            $db->query("DELETE FROM threads WHERE threadid='" . $db->real_escape_string($row["threadid"]) . "'");
                        }
                    }

                    // Here comes the hard part. We need to delete all of the user's posts in other user's threads without casuing issues.
                    // First, get the ids of all the threads the remaining posts are in.
                    $posts = $db->query("SELECT postid, thread FROM posts WHERE user='" . $db->real_escape_string($userid) . "'");

                    // Now loop through the posts and delete them, but fix the thread they're in each time.
                    if ($posts->num_rows > 0) {
                        while ($row = $posts->fetch_assoc()) {
                            // First off, delete the post.
                            $db->query("DELETE FROM posts WHERE postid='" . $db->real_escape_string($row["postid"]) . "'");

                            // Now check if the post was the only post in the thread.
                            $lastPostCheck = $db->query("SELECT 1 FROM posts WHERE thread='" . $db->real_escape_string($row["thread"]) . "'");

                            // If so, delete the thread.
                            if ($lastPostCheck->num_rows < 1) {
                                $db->query("DELETE FROM threads WHERE threadid='" . $db->real_escape_string($row["thread"]) . "'");
                            }
                            // If not, we'll need to make sure to fix the thread's data based on the remaining posts.
                            else {
				                $lastPost = $db->query("SELECT user, timestamp FROM posts WHERE thread='" . $db->real_escape_string($row["thread"]) . "' ORDER BY timestamp DESC LIMIT 1");
                                $postCount = $db->query("SELECT 1 FROM posts WHERE thread='" . $db->real_escape_string($row["thread"]) . "'");

                                while($l = $lastPost->fetch_assoc()) {
						            $db->query("UPDATE threads SET posts='" . $db->real_escape_string($postCount->num_rows) . "', lastpostuser='" . $l["user"] . "', lastposttime='" . $l["timestamp"] . "' WHERE threadid='" . $db->real_escape_string($row["thread"]) . "'");
					            }
                            }
                        }
                    }

                    // Remove the user's avatar as well.
                    removeAvatar($userid);

                    echo("</br>" . $lang["panel.DeleteUserSuccess"]);
                }
            }
	    break;
    }
}

function drawNavigation() {
    global $lang, $config, $template, $db;
    global $q1, $q2, $q3;
    
    $nav = "";
    if (isset($config["parentSite"]) && isset($config["parentSiteName"]) && $config["parentSite"] !== '' && $config["parentSiteName"] !== '') {
        $nav .= $template->render("templates/header/nav_button.html", array("label" => $config["parentSiteName"], "url" => $config["parentSite"]));
        $nav .= $template->render("templates/header/nav_seperator.html", array("label" => "/"));
    } 
    
    $nav .= $template->render("templates/header/nav_button.html", array("label" => $config["forumName"], "url" => genURL("")));
    $nav .= $template->render("templates/header/nav_seperator.html", array("label" => "/"));
    
    if (!$q1)
    {
        $nav .= $template->render("templates/header/nav_last.html", array("label" => $lang["page.homepage"]));
        return $nav;
    }
    if ($q1 == "thread")
    {
        if (($GLOBALS["threadExists"] == true) and (($GLOBALS["draft"] == 0) or ($_SESSION["userid"] == $GLOBALS["startuser"]) or (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")))) {
        // Get the category information.
            $categoryDB = $db->query("SELECT * FROM categories WHERE categoryid='" . $db->real_escape_string($GLOBALS["categoryID"]) . "'");


            while ($row = $categoryDB->fetch_assoc()) {
	        $categoryName = $row['categoryname'];
	        $categoryDescription = $row['categorydescription'];
            }
            $nav .= $template->render("templates/header/nav_button.html", 
                    array("label" => htmlspecialchars($categoryName), "url" => genURL('category/' . $GLOBALS["categoryID"]))
                    );
                    
            $nav .= $template->render("templates/header/nav_seperator.html", array("label" => "/"));
            
            $nav .= $template->render("templates/header/nav_last.html", array("label" => htmlspecialchars($GLOBALS["title"])));
        }
        else {
            $nav .= $template->render("templates/header/nav_last.html", array("label" => $lang["page.homepage"]));
            return $nav;
        }
    }
    else if ($q1 == "category" && isset($GLOBALS["categoryName"]))
    {
        $nav .= $template->render("templates/header/nav_last.html", array("label" => htmlspecialchars($GLOBALS["categoryName"])));
    }
    else if ($q1 == "user" && isset($GLOBALS["username"]))
    {
        $nav .= $template->render("templates/header/nav_button.html", array("label" => $lang["page.userlist"], "url" => genURL("userlist")));
        $nav .= $template->render("templates/header/nav_seperator.html", array("label" => "/"));
        $nav .= $template->render("templates/header/nav_last.html", array("label" => htmlspecialchars($GLOBALS["username"])));
    }
    else
    {
        if (isset($lang["page." . $q1]) && $lang["page." . $q1])
        {
            if (isset($lang["page." . $q1 . "." . $q2]) && $lang["page." . $q1 . "." . $q2]) {
                $nav .= $template->render("templates/header/nav_button.html", array("label" => $lang["page." . $q1], "url" => genURL($q1)));
                $nav .= $template->render("templates/header/nav_seperator.html", array("label" => "/"));
                $nav .= $template->render("templates/header/nav_last.html", array("label" => $lang["page." . $q1 . "." . $q2]));
            }
            else {
                $nav .= $template->render("templates/header/nav_last.html", array("label" => $lang["page." . $q1]));
            }
        }
        else
        {
            $nav .= $template->render("templates/header/nav_last.html", array("label" => $lang["page.homepage"]));
        }
        
        return $nav;
    }
    
    return $nav;
}

function drawUserProfile($userid, $type, $isHidden=false) {

    global $db, $lang, $config;

    $result = $db->query("SELECT * FROM users WHERE userid='" . $userid . "'");

    while ($row = $result->fetch_assoc())
	{
      	if ($row["deleted"] == "1") $username = $lang["user.Deleted"] . $row["userid"];
        else $username = $row["username"];
            
        $role = $row["role"];
        $lastactive = $row["lastactive"];
        $jointime = $row["jointime"];
		$deleted = $row["deleted"];
		$avatar = $row["avatar"];
		$avatarTime = $row["avataruploadtime"];
    }
        if ((time() - $lastactive) <= $config["onlinePeriod"] && !$isHidden)
	    {
	    	echo '<span class="online">' . $lang["nav.Online"] . '</span>';
	    }
		if ($avatar == "none") $uAvatar = "";
        else $uAvatar = '<img class="avatar" src="' . genURL("avatars/" . $userid . "." . $avatar . "?t=" . $avatarTime) . '">';

        echo $uAvatar . '<b><a class="' . $role . '" href="' . genURL("user/" . $userid) . '">' . htmlspecialchars($username) . '</a>';
        echo '</b>';

        // Draw the role box
	if (!$isHidden) {
		if ($type == 1 and ($_SESSION['role'] == "Administrator") and ($_SESSION["userid"] != $userid) and ($config["mainAdmin"] != $userid))
		{
			echo '<div class="forminput"><form method="post" class="changerole" action=""><select name="role">';
				
			if ($role == "Administrator")
			{
				echo '<option value="Administrator" selected>'.$lang["user.OptionAdmin"].'</option>';
				echo '<option value="Moderator">'.$lang["user.OptionMod"].'</option>';
				echo '<option value="Member">'.$lang["user.OptionMember"].'</option>';
				echo '<option value="Suspended">'.$lang["user.OptionSuspended"].'</option>';
			}
				
			elseif ($role == "Moderator")
			{
				echo '<option value="Administrator">'.$lang["user.OptionAdmin"].'</option>';
				echo '<option value="Moderator" selected>'.$lang["user.OptionMod"].'</option>';
				echo '<option value="Member">'.$lang["user.OptionMember"].'</option>';
				echo '<option value="Suspended">'.$lang["user.OptionSuspended"].'</option>';
			}
				
			elseif ($role == "Member")
			{
				echo '<option value="Administrator">'.$lang["user.OptionAdmin"].'</option>';
				echo '<option value="Moderator">'.$lang["user.OptionMod"].'</option>';
				echo '<option value="Member" selected>'.$lang["user.OptionMember"].'</option>';
				echo '<option value="Suspended">'.$lang["user.OptionSuspended"].'</option>';
			}
				
			elseif ($role == "Suspended")
			{
				echo '<option value="Administrator">'.$lang["user.OptionAdmin"].'</option>';
				echo '<option value="Moderator">'.$lang["user.OptionMod"].'</option>';
				echo '<option value="Member">'.$lang["user.OptionMember"].'</option>';
				echo '<option value="Suspended" selected>'.$lang["user.OptionSuspended"].'</option>';
			}
				
			echo '</select><div class="forminput"><input type="submit" class="buttonrole" value="'.$lang["user.ChangeRole"].'"></div></form>';
			
				
		}

        elseif ($type == 1 and ($_SESSION['role'] == "Moderator") and ($role != "Administrator") and ($role != "Moderator") and ($_SESSION["userid"] != $userid) and ($config["mainAdmin"] != $userid))
		{
			echo '<div class="forminput"><form method="post" class="changerole" action=""><select name="role">';

				
			if ($role == "Member")
			{
				echo '<option value="Member" selected>'.$lang["user.OptionMember"].'</option>';
				echo '<option value="Suspended">'.$lang["user.OptionSuspended"].'</option>';
			}
				
			elseif ($role == "Suspended")
			{
				echo '<option value="Member">'.$lang["user.OptionMember"].'</option>';
				echo '<option value="Suspended" selected>'.$lang["user.OptionSuspended"].'</option>';
			}
				
			echo '</select><div class="forminput"><input type="submit" class="buttonrole" value="'.$lang["user.ChangeRole"].'"></div></form>';
			
				
		}
			
		else
		{				
			echo '<div class="userrole">' . $lang["role." . $role];
		}
	}
}

// Display a user's information, used by userlists.
function displayUser($deletedClass = "", $color, $userid, $role, $username, $verify = "", $lastaction, $lastactive) {
  global $lang;

  echo '<div class="userlist' . $deletedClass . '"><div class="userlist-top" postcolor="' . $color . '"><b><a href="' . genURL('user/' . $userid) . '/" class="' . $role . '">' . htmlspecialchars($username) . '</a></b>&nbsp; ' . $lang["role." . $role] . '&nbsp; ' . $verify . '<small></div><div class="userlist-bottom">' . parseAction($lastaction, $lang) . ' (<a class="date" title="' . date('m-d-Y h:i:s A', $lastactive) . '">' . relativeTime($lastactive) . '</a>)</small></div></div>';
}

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

?>
