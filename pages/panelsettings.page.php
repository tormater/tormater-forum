<?php
// panelsettings.page.php
// The Forum Settings portion of the admin panel.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if($_SERVER['REQUEST_METHOD'] != 'POST')
{
    echo '<br/>';
	// Display the change forum name form.
	echo '<h3>' . $lang["panel.BasicSettings"] . '</h3>
   <form method="post" action=""><div class="formcontainer">
    <div class="forminput"><label>' . $lang["panel.NewForumName"]  . '</label><input type="text" name="newforumname" value="' . $config["forumName"] . '"></div>';

    // Display the change language form.
	echo '<div class="forminput"><label>' . $lang["panel.NewLang"]  . '</label>';
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
    echo '</select></div>';

    // Display the change theme form.
	echo '<div class="forminput"><label>' . $lang["panel.NewTheme"]  . '</label>';
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
    echo '</select></div>';

    // Display the change forum color form.
    $presetColor = "#000000";
    if ($config["forumColor"] != null)
    {
        $presetColor = $config["forumColor"];
        $newchecked = 'checked=""';
    }
    else
    {
        $defaultchecked = 'checked=""';
    }
    echo '</div><h3>' . $lang["panel.ForumColor"] . '</h3><div class="formcontainer">';
    echo '<div class="forminput"><label class="colorLabel">' . $lang["panel.NewColor"] . '</label>';
    echo '<input type="radio" class="colorRadio" name="color" ' . $newchecked . ' value="new">';
    echo "<input type='color' class='forumColor' name='newcolor' value='" . $presetColor . "';></div>";
    echo '<div class="forminput"><label class="colorLabel">' . $lang["panel.Reset"] . '</label>';
    echo '<input type="radio" class="colorRadio" name="color" ' . $defaultchecked . ' value="default">';
    echo '</div></div>';

    // Display the advanced settings.
	echo '<h3>' . $lang["panel.AdvancedSettings"] . '</h3>';
    echo '<div class="formcontainer">
    <div class="forminput"><label>' . $lang["panel.ThreadsPerPage"] . '</label>
    <input type="text" name="newthreadsPerPage" value="' . $config["threadsPerPage"] . '"></div>
    <div class="forminput"><label>' . $lang["panel.PostPerPage"] . '</label>
    <input type="text" name="newpostsPerPage" value="' . $config["postsPerPage"] . '"></div>
    <div class="forminput"><label>' . $lang["panel.Userlist"] . '</label>
    <input type="checkbox" name="newuserlistEnabled" id="userlist" value="'. $config["userlistEnabled"] .'" ';
    if ($config["userlistEnabled"] == 1)
    {
        echo 'checked';
    }
    echo '> <label for="userlist">' . $lang["panel.EnabledBtn"] . '</label></div>
    <div class="forminput"><label>' . $lang["panel.UserlistMemberOnly"] . '</label>
    <input type="checkbox" name="newuserlistMembersOnly" id="userlistMember" value="'. $config["userlistMembersOnly"] .'" ';
    if ($config["userlistMembersOnly"] == 1)
    {
        echo 'checked';
    }
    echo '> <label for="userlistMember">' . $lang["panel.EnabledBtn"] . '</label></div></div>';

    // Display the change footer form.
	echo '<h3>' . $lang["panel.ChangeFooter"]  . '</h3>
    <div class="formcontainer">
    <div class="forminput"><textarea name="newfooter">' . htmlspecialchars($config["footer"]) . '</textarea></div>
    <div class="forminput"><input type="submit" class="buttonbig" value="' . $lang["panel.ChangeSettingsBtn"] . '"></div></div></form>';
}

else
{
	if (!isset($_POST))
	{
        message($lang["panel.ChangeError"]);
	}
    else
    {
        if(isset($_POST["newuserlistEnabled"]))
        {
            $newuserlistEnabled = 1;
        }
        else
        {
            $newuserlistEnabled = 0;
        }
        if(isset($_POST["newuserlistMembersOnly"]))
        {
            $newuserlistMembersOnly = 1;
        }
        else
        {
            $newuserlistMembersOnly = 0;
        }
		$newforumname = $_POST["newforumname"];
        $newthreadsPerPage = $_POST["newthreadsPerPage"];
        $newpostsPerPage = $_POST["newpostsPerPage"];
		$newtheme = $_POST["newtheme"];
		$newlang = $_POST["newlang"];
        $newcolor = $_POST["newcolor"];
        $color = $_POST["color"];
		$newfooter = $_POST["newfooter"];
        
        $config["forumName"] = htmlspecialchars($newforumname);
        $config["threadsPerPage"] = $newthreadsPerPage;
        $config["postsPerPage"] = $newpostsPerPage;
        $config['userlistEnabled'] = $newuserlistEnabled;
        $config['userlistMembersOnly'] = $newuserlistMembersOnly;
        $config["forumTheme"] = $newtheme;
        $config["forumLang"] = $newlang;

        if ($color == "new")
        {
            $config["forumColor"] = $newcolor;
        }
        else 
        {
            $config["forumColor"] = null;
        }

        $config["footer"] = $newfooter;
        saveConfig("./config/config.php", $config);
        message($lang["panel.ChangesSaved"]);
        refresh(1, "panel");
    }
}
?>
