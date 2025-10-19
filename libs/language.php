<?php
// language.php
// Handles user language settings

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$validLanguages = glob("lang/*.php");

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

include "lang/EN_US.php";
$lang_en_us = $lang;

if (isset($currentLang)) {
   include "lang/" . $currentLang . ".php";
}
else {
   $currentLang = $config["forumLang"];
}

foreach ($lang_en_us as $k => $val) { // note: do not use $v for this because of the &$v above. Unless you want "Install Tormater Forum" to be a language.
    if (!array_key_exists($k,$lang)) {
        $lang[$k] = $k;
    }
}

$languageSelector = '<form method="post"><select name="lang" id="lang" aria-label="Language" onchange="this.form.submit()">';

foreach ($validLanguages as $l) {
    if (isset($currentLang) && $l == $currentLang) $selected = "selected='' ";
    else $selected = "";
    
    $l_display = $l;
    
    if (file_exists("lang/" . $l . ".json")) {
        $manifest = json_decode(file_get_contents("lang/" . $l . ".json"), true);
        if (isset($manifest["region_abbr"])) $l_display = $manifest["name"] . " (" . $manifest["region_abbr"] . ")";
        else $l_display = $manifest["name"];
    }
    $languageSelector .= '<option ' . $selected . 'value="' . $l . '">' . $l_display . '</option>';
}

$languageSelector .= '</select></form>';

?>
