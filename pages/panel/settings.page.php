<?php
// panelsettings.page.php
// The Forum Settings portion of the admin panel.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{        
    $config["forumName"] = htmlspecialchars($_POST["newforumname"]);
    $config["threadsPerPage"] = (int)$_POST["newthreadsPerPage"];
    $config["postsPerPage"] = (int)$_POST["newpostsPerPage"];
    $config['userlistEnabled'] = (bool)isset($_POST["newuserlistEnabled"]);
    $config['userlistMembersOnly'] = (bool)isset($_POST["newuserlistMembersOnly"]);
    $config["forumTheme"] = $_POST["newtheme"];
    $config["forumLang"] = $_POST["newlang"];
    $config["registration"] = $_POST["registration"];
    $config["showDeletedInUserlist"] = (bool)isset($_POST["showDeletedInUserlist"]);
    $config["footer"] = $_POST["newfooter"];

    if (isset($_POST["color"]) && $_POST["color"] == "new")  $config["forumColor"] = $_POST["newcolor"];
    else $config["forumColor"] = null;

    saveConfig("./config/config.php", $config);
    message($lang["panel.ChangesSaved"]);
}

echo "<script type='text/javascript' src='" . genURL("assets/panel.js") . "'></script>";
echo '<br/>';
echo '<h3>' . $lang["panel.BasicSettings"] . '</h3><form method="post" action=""><div class="formcontainer">';

echo '<div class="forminput"><label>' . $lang["panel.NewForumName"]  . '</label><input type="text" name="newforumname" value="' . $config["forumName"] . '"></div>';

echo '<div class="forminput"><label>' . $lang["panel.NewLang"]  . '</label>';

// We are re-using the language selector found on every page here for one reason or another,
// so we have to remove the automatic submit functionality.

$adminLanguageSelector = str_replace('<select name="lang" id="lang" aria-label="Language" onchange="this.form.submit()">', "<select name='newlang'>", $languageSelector);
$adminLanguageSelector = str_replace('</form>', "", $adminLanguageSelector);
echo $adminLanguageSelector;
echo '</div>';

echo '<div class="forminput"><label>' . $lang["panel.NewTheme"]  . '</label>';
echo '<select name="newtheme">';
$files = scandir(dirname(__DIR__,2) . '/themes');
foreach ($files as $file) {
    if ($file == '.' || $file == '..') continue;
    if ($file == $config["forumTheme"])  $selected = "selected ";
    else $selected = "";
    echo '<option ' . $selected . 'value="' . $file . '">' . $file . '</option>';
}
echo '</select></div>';

$presetColor = "#000000";
$defaultchecked = "";
$newchecked = "";
if ($config["forumColor"] != null)
{
    $presetColor = $config["forumColor"];
    $newchecked = 'checked=""';
}
else $defaultchecked = 'checked=""';

echo '</div><h3>' . $lang["panel.ForumColor"] . '</h3><div class="formcontainer">';
echo '<div class="forminput"><label class="colorLabel">' . $lang["panel.NewColor"] . '</label>';
echo '<input type="radio" class="colorRadio" id="newcolor" name="color" ' . $newchecked . ' value="new">';
echo "<input type='color' class='forumColor' onchange='checkColorRadio();' name='newcolor' value='" . $presetColor . "';></div>";
echo '<div class="forminput"><label class="colorLabel">' . $lang["panel.Reset"] . '</label>';
echo '<input type="radio" class="colorRadio" id="olcolor" name="color" ' . $defaultchecked . ' value="default">';
echo '</div></div>';

echo '<h3>' . $lang["panel.AdvancedSettings"] . '</h3>';
echo '<div class="formcontainer">
<div class="forminput"><label>' . $lang["panel.ThreadsPerPage"] . '</label>
<input type="text" name="newthreadsPerPage" value="' . $config["threadsPerPage"] . '"></div>
<div class="forminput"><label>' . $lang["panel.PostPerPage"] . '</label>
<input type="text" name="newpostsPerPage" value="' . $config["postsPerPage"] . '"></div>
<div class="forminput"><label>' . $lang["panel.Userlist"] . '</label>
<input type="checkbox" name="newuserlistEnabled" id="userlist" value="'. $config["userlistEnabled"] .'" ';
if ((bool)$config["userlistEnabled"]) echo 'checked';
echo '></div>
<div class="forminput"><label>' . $lang["panel.UserlistMemberOnly"] . '</label>
<input type="checkbox" name="newuserlistMembersOnly" id="userlistMember" value="'. $config["userlistMembersOnly"] .'" ';
if ((bool)$config["userlistMembersOnly"])  echo 'checked';
echo '></div>';

echo '<div class="forminput"><label>' . $lang["panel.showDeletedInUserlist"] . '</label>
<input type="checkbox" name="showDeletedInUserlist" id="userlistDeleted" value="'. $config["showDeletedInUserlist"] .'" ';
if ((bool)$config["showDeletedInUserlist"]) echo 'checked';
echo '></div>';

$options = array("open", "closed", "approval");
echo "<label>" . $lang["panel.Registration"] . "</label><select name='registration'>";
foreach ($options as $option) {
    $selected = "";
    if ($config["registration"] == $option) $selected = "selected='' ";
    echo "<option ".$selected."value='" . $option . "'>" . $lang["panel." . $option] . "</option>";
}
echo "</select></div>";

echo '<h3>' . $lang["panel.ChangeFooter"]  . '</h3><div class="formcontainer">';

BBCodeButtons(1);

echo '<div class="forminput"><textarea name="newfooter" id="textbox1">' . htmlspecialchars($config["footer"]) . '</textarea></div>
<div class="forminput"><input type="submit" class="buttonbig" value="' . $lang["panel.ChangeSettingsBtn"] . '"></div></div></form>';
?>
