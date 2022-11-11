<?php
// panelsettings.php
// The Forum Settings portion of the admin panel.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

echo '<h2>' . $lang["panel.ForumSettings"]  . '</h2>';

if($_SERVER['REQUEST_METHOD'] != 'POST')
{
	// Display the change forum name form.
	echo '<h3>' . $lang["panel.ChangeForumName"]  . '</h3>
    <div class="formcontainer"><form method="post" action="">
    <div class="forminput"><label>' . $lang["panel.NewForumName"]  . '</label><input type="text" name="newforumname" value="' . $config["forumName"] . '"></div>
    <div class="forminput"><label></label><input type="submit" class="buttonbig" value="' . $lang["panel.ChangeForumName"]  . '"></div>
    </form></div>';

    // Display the change theme form.
	echo '<h3>' . $lang["panel.ChangeTheme"]  . '</h3>
    <div class="formcontainer"><form method="post" action="">
    <div class="forminput"><label>' . $lang["panel.NewTheme"]  . '</label>';
    echo '<select name="newtheme">';
    $files = scandir(dirname(__DIR__,1) . '/themes');
    foreach($files as $file) {
        if($file == '.' || $file == '..') continue;
        if($file == $config["forumTheme"]) 
        {
            $selected = "selected ";
        }
        else
        {
            $selected = "";
        }
        echo '<option ' . $selected . 'value="' . $file . '">' . $file . '</option>';
    }
    echo '</select></div>
    <div class="forminput"><label></label><input type="submit" class="buttonbig" value="' . $lang["panel.ChangeTheme"]  . '"></div>
    </form></div>';

    // Display the change language form.
	echo '<h3>' . $lang["panel.ChangeLang"]  . '</h3>
    <div class="formcontainer"><form method="post" action="">
    <div class="forminput"><label>' . $lang["panel.NewLang"]  . '</label>';
    echo '<select name="newlang">';
    $files = scandir(dirname(__DIR__,1) . '/lang');
    foreach($files as $file) {
        if($file == '.' || $file == '..') continue;
        if(substr($file, 0, -4) == $config["forumLang"]) 
        {
            $selected = "selected ";
        }
        else
        {
            $selected = "";
        }
        echo '<option ' . $selected . 'value="' . substr($file, 0, -4) . '">' . substr($file, 0, -4) . '</option>';
    }
    echo '</select></div>
    <div class="forminput"><label></label><input type="submit" class="buttonbig" value="' . $lang["panel.ChangeLang"]  . '"></div>
    </form></div>';

    // Display the change footer form.
	echo '<h3>' . $lang["panel.ChangeFooter"]  . '</h3>
    <div class="formcontainer"><form method="post" action="">
    <div class="forminput"><textarea name="newfooter">' . htmlspecialchars($config["footer"]) . '</textarea></div>
    <div class="forminput"><input type="submit" class="buttonbig" value="' . $lang["panel.ChangeFooter"]  . '"></form></div></div>';
}

else
{
	if (isset($_POST["newforumname"]))
	{
		$newforumname = $_POST["newforumname"];
		
        $config["forumName"] = htmlspecialchars($newforumname);
        saveConfig("./config/config.php", $config);
        echo '<meta http-equiv="refresh" content="1;url=/panel/" />';
        message($lang["panel.ChangesSaved"]);
	}

	if (isset($_POST["newfooter"]))
	{
		$newfooter = $_POST["newfooter"];
		
        $config["footer"] = $newfooter;
        saveConfig("./config/config.php", $config);
        echo '<meta http-equiv="refresh" content="1;url=/panel/" />';
        message($lang["panel.ChangesSaved"]);
	}

	if (isset($_POST["newtheme"]))
	{
		$newtheme = $_POST["newtheme"];
		
        $config["forumTheme"] = $newtheme;
        saveConfig("./config/config.php", $config);
        echo '<meta http-equiv="refresh" content="1;url=/panel/" />';
        message($lang["panel.ChangesSaved"]);
	}

	if (isset($_POST["newlang"]))
	{
		$newlang = $_POST["newlang"];
		
        $config["forumLang"] = $newlang;
        saveConfig("./config/config.php", $config);
        echo '<meta http-equiv="refresh" content="1;url=/panel/" />';
        message($lang["panel.ChangesSaved"]);
	}
}
?>
