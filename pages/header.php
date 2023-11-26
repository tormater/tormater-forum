<?php
// header.php
// Placed at the top of every page on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$m_pages = array(
	"home" => array($lang["header.Home"], genURL(""), 0),
	"userlist" => array($lang["header.Userlist"], genURL("userlist"), 0),
	"userpanel" => array($lang["page.userpanel"], genURL("userpanel"), 1),
	"newthread" => array($lang["header.NewThread"], genURL("newthread"), 1),
	"panel" => array($lang["header.Panel"], genURL("panel"), 4),
	"logout" => array($lang["header.Logout"], genURL("logout"), 1),
);

if (isset($categoryID)) $pages["newthread"][1] = genURL('newthread/' . $categoryID);

$data = array(
	    "locale" => str_replace("_", "-", $lang["locale"]),
	    "search" => genURL("assets/search.js"),
	    "title" => $config["forumName"],
	    "stylesheet" => genURL('themes/' . $config["forumTheme"] . '/style.css?v=0.1'),
	    "favicon" => genURL('themes/' . $config["forumTheme"] . '/icon.ico'),
	    "pages" => "",
	    "welcome" => '<a href="' . genURL("login") . '">' . $lang["header.Login"] . '</a>'.$lang["header.or"].'<a href="' . genURL("signup") . '">' . $lang["header.Signup"] . '</a>',
	    "homeURL" => genURL(""),
);

if (isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
{
	$data["welcome"] = $lang["header.Hello"] . '<b><a href="' . genURL('user/' . $_SESSION["userid"]) . '" id="' . $_SESSION["role"] . '">' . htmlspecialchars($_SESSION["username"]) . '</a></b>';
}

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
