<?php
// language.php
// Handles user language settings

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$validLanguages = scandir("lang/");
$validLanguages = array_diff($validLanguages, array('.', '..'));

foreach ($validLanguages as &$v) {
    $v = pathinfo($v, PATHINFO_FILENAME);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST["lang"]) && in_array($_POST["lang"], $validLanguages)) {
        if (session_id()) {
            if ($_POST["lang"] == $config["forumLang"] && isset($_SESSION["language"])) {
                unset($_SESSION["language"]);
            }
            else {
                $_SESSION["language"] = $_POST["lang"];
            }
        }
    }
}
if (session_id() && isset($_SESSION["language"])) {
    if (in_array($_SESSION["language"], $validLanguages)) {
        $currentLang = $_SESSION["language"];
    }
}

if (isset($currentLang)) {
   include "lang/" . $currentLang . ".php";
}
else {
   $currentLang = $config["forumLang"];
}

$languageSelector = '<form method="post"><select name="lang" id="lang" onchange="this.form.submit()">';

foreach ($validLanguages as $l) {
    if (isset($currentLang) && $l == $currentLang) $selected = "selected=''";
    else $selected = "";
    $languageSelector .= '<option ' . $selected . 'value="' . $l . '">' . $l . '</option>';
}

$languageSelector .= '</select></form>';

?>
