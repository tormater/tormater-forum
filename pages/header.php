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

if (isset($categoryID)) $pages["newthread"][1] = genURL('newthread/' . $categoryID);

$data = array(
	    "locale" => str_replace("_", "-", $lang["locale"]),
	    "search" => genURL("assets/search.js"),
	    "title" => (empty($lang["page." . $q1])) ? $config["forumName"] : $lang["page." . $q1] . ' • ' . $config["forumName"],
	    "stylesheet" => genURL('themes/' . $config["forumTheme"] . '/style.css?v=0.1'),
	    "favicon" => genURL('themes/' . $config["forumTheme"] . '/icon.ico'),
	    "favicon_svg" => genURL('themes/' . $config["forumTheme"] . '/icon.svg'),
	    "favicon_png" => genURL('themes/' . $config["forumTheme"] . '/icon.png'),
	    "pages" => "",
	    "welcome" => '<a href="' . genURL("login") . '">' . $lang["header.Login"] . '</a>'.$lang["header.or"].'<a href="' . genURL("signup") . '">' . $lang["header.Signup"] . '</a>',
	    "homeURL" => genURL(""),
	    "searchText" => "",
	    "searchPlaceholder" => $lang["search.Placeholder"],
	    "searchButton" => $lang["search.Button"],
	    "meta" => "",
	    "keywords" => strtolower($config["forumName"]),
);

ob_start();
listener("meta");
$data["meta"] = ob_get_contents();
ob_end_clean();

if (isset($_GET["search"]) && !empty($_GET["search"])) {
    $data["searchText"] = urldecode($_GET["search"]);
}

if (isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
{
	$data["welcome"] = $lang["header.Hello"] . '<b><a href="' . genURL('user/' . $_SESSION["userid"]) . '" id="' . $_SESSION["role"] . '">' . htmlspecialchars($_SESSION["username"]) . '</a></b>';
}

if (($q1 == "thread") and ($threadExists == true))
{
    if (($draft == 0) or ($_SESSION["userid"] == $startuser) or (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator"))) {
        $data["title"] = htmlspecialchars($title) . ' • ' . $config["forumName"];
    }
}
else if ($q1 == "category")
    $data["title"] = htmlspecialchars($categoryName) . ' • ' . $config["forumName"];
else if ($q1 == "user")
    $data["title"] = htmlspecialchars($username) . ' • ' . $config["forumName"];


foreach ($m_pages as $v) {
    if (getNumForRole($_SESSION["role"]) >= $v[2]) {
        $p_data = array(
            "label" => $v[0],
            "url" => $v[1]
        );
        $data["pages"] = $data["pages"] . $template->render("templates/header/menu_page.html", $p_data);
    }
}

echo $template->render("templates/header/header.html", $data);

?>
