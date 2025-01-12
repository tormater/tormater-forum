<?php
// header.php
// Placed at the top of every page on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$m_pages = array(
	"home" => array($lang["header.Home"], genURL(""), 0),
	"userlist" => array($lang["header.Userlist"], genURL("userlist"), 0),
	"userpanel" => array($lang["header.UserPanel"], genURL("userpanel"), 1),
	"newthread" => array($lang["header.NewThread"], genURL("newthread"), 1),
	"panel" => array($lang["header.Panel"], genURL("panel"), 4),
	"logout" => array($lang["header.Logout"], genURL("logout"), 1),
);

if (isset($categoryID)) $m_pages["newthread"][1] = genURL('newthread/' . $categoryID);
if (isset($_GET['url']) && strlen($_GET['url']) > 0) {
    $login_redirect = "?next=" . ltrim($_GET['url'], '/');
}

$data = array(
	    "locale" => str_replace("_", "-", $lang["locale"]),
	    "search" => genURL("assets/search.js"),
	    "title" => (empty($lang["page." . $q1])) ? $config["forumName"] : $lang["page." . $q1] . ' • ' . $config["forumName"],
	    "stylesheet" => genURL('themes/' . $config["forumTheme"] . '/style.css?v=0.1'),
	    "favicon" => genURL('themes/' . $config["forumTheme"] . '/icon.ico'),
	    "favicon_svg" => genURL('themes/' . $config["forumTheme"] . '/icon.svg'),
	    "favicon_png" => genURL('themes/' . $config["forumTheme"] . '/icon.png'),
	    "pages" => "",
	    "welcome" => '<a href="' . genURL("login" . $login_redirect ) . '">' . $lang["header.Login"] . '</a>'.$lang["header.or"].'<a href="' . genURL("signup") . '">' . $lang["header.Signup"] . '</a>',
	    "homeURL" => genURL(""),
	    "searchText" => "",
	    "searchPlaceholder" => $lang["search.Placeholder"],
	    "searchButton" => $lang["search.Button"],
	    "meta" => "",
	    "color" => "",
	    "keywords" => strtolower($config["forumName"]),
	    "navigation" => drawNavigation(),
	    "header" => $template->render("templates/header/title.html", null),
);

if ($config["forumColor"] != null)
{
    $grTop = hexAdjustLight($config["forumColor"], 0.9);
    $grMedium = hexAdjustLight($config["forumColor"], 1);
    $grBottom = hexAdjustLight($config["forumColor"], -0.4);
    $grBottomDarker = hexAdjustLight($config["forumColor"], -0.6);
    $grHighlight = hexAdjustLight($config["forumColor"], 0.4);
    $grBorder = hexAdjustLight($config["forumColor"], -0.8);
    $data["color"] = "<style>
    :root {
    --c-gradient-top: " . $grTop .";
    --c-gradient-medium: " . $grMedium .";
    --c-gradient-bottom: " . $grBottom .";
    --c-gradient-bottom-darker: " . $grBottomDarker .";
    --c-highlight: " . $grHighlight .";
    --c-border: " . $grBorder .";
    }
    </style>";
}

$files = scandir("assets/");
$matches = preg_grep("/forumLogo\.(png|jpg|svg|gif)/i", $files);
sort($matches);

if (isset($matches[0]))
{
    $data["header"] = $template->render("templates/header/title_img.html", array("url" => genURL("assets/" . $matches[0])));
}

ob_start();
listener("meta");
$data["meta"] = ob_get_contents();
ob_end_clean();

if (isset($_GET["search"]) && !empty($_GET["search"])) {
    $data["searchText"] = htmlspecialchars(urldecode($_GET["search"]));
}

if (isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
{
	$data["welcome"] = $lang["header.Hello"] . '<b><a href="' . genURL('user/' . $_SESSION["userid"]) . '" class="' . $_SESSION["role"] . '">' . htmlspecialchars($_SESSION["username"]) . '</a></b>';
}

if (($q1 == "thread") and ($threadExists == true))
{
    if (($draft == 0) or ($_SESSION["userid"] == $startuser) or (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator"))) {
        $data["title"] = htmlspecialchars($title) . ' • ' . $config["forumName"];
    }
}
else if ($q1 == "category" && isset($categoryName))
    $data["title"] = htmlspecialchars($categoryName) . ' • ' . $config["forumName"];
else if ($q1 == "user")
    $data["title"] = htmlspecialchars($username) . ' • ' . $config["forumName"];


foreach ($m_pages as $v) {
    if (getNumForRole(@$_SESSION["role"]) >= $v[2]) {
        $p_data = array(
            "label" => $v[0],
            "url" => $v[1]
        );
        $data["pages"] = $data["pages"] . $template->render("templates/header/menu_page.html", $p_data);
    }
}

echo $template->render("templates/header/header.html", $data);

?>
