<?php
// panelsettings.page.php
// The Forum Settings portion of the admin panel.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if($_SERVER['REQUEST_METHOD'] != 'POST')
{
    echo ("<script type='text/javascript' src='" . genURL("assets/panel.js") . "'></script>");
    echo '<br/>';
	// Display the change forum name form.
	echo '<h3>' . $lang["panel.BasicSettings"] . '</h3>
   <form method="post" action=""><div class="formcontainer">
    <div class="forminput"><label>' . $lang["panel.NewForumName"]  . '</label><input type="text" name="newforumname" value="' . $config["forumName"] . '"></div>';

    // Display the change language form.
	echo '<div class="forminput"><label>' . $lang["panel.NewLang"]  . '</label>';
    $adminLanguageSelector = str_replace('<form method="post"><select name="lang" id="lang" onchange="this.form.submit()">', "<select name='newlang'>", $languageSelector);
    $adminLanguageSelector = str_replace('</form>', "", $adminLanguageSelector);
    echo $adminLanguageSelector;
    echo '</div>';

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
    $defaultchecked = "";
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
    echo '<input type="radio" class="colorRadio" id="newcolor" name="color" ' . $newchecked . ' value="new">';
    echo "<input type='color' class='forumColor' onchange='checkColorRadio();' name='newcolor' value='" . $presetColor . "';></div>";
    echo '<div class="forminput"><label class="colorLabel">' . $lang["panel.Reset"] . '</label>';
    echo '<input type="radio" class="colorRadio" id="olcolor" name="color" ' . $defaultchecked . ' value="default">';
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
    echo '></div>
    <div class="forminput"><label>' . $lang["panel.UserlistMemberOnly"] . '</label>
    <input type="checkbox" name="newuserlistMembersOnly" id="userlistMember" value="'. $config["userlistMembersOnly"] .'" ';
    if ($config["userlistMembersOnly"] == 1)
    {
        echo 'checked';
    }
    echo '></div>';

    echo '<div class="forminput"><label>' . $lang["panel.showDeletedInUserlist"] . '</label>
    <input type="checkbox" name="showDeletedInUserlist" id="userlistDeleted" value="'. $config["showDeletedInUserlist"] .'" ';
    if ($config["showDeletedInUserlist"] == 1)
    {
        echo 'checked';
    }
    echo '></div>';

    $options = array("open", "closed", "approval");
    echo "<label>" . $lang["panel.Registration"] . "</label><select name='registration'>";
    foreach ($options as $option) {
        if ($config["registration"] == $option) {
            echo "<option selected='' value='" . $option . "'>" . $lang["panel." . $option] . "</option>";
        }
        else {
            echo "<option value='" . $option . "'>" . $lang["panel." . $option] . "</option>";
        }
    }
    echo "</select></div>";

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
        if(isset($_POST["showDeletedInUserlist"]))
        {
            $showDeletedInUserlist = 1;
        }
        else
        {
            $showDeletedInUserlist = 0;
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
        $config["registration"] = $_POST["registration"];
        $config["showDeletedInUserlist"] = $showDeletedInUserlist;

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
        include "footer.php";
        refresh(3, "panel");
    }
}
?>
