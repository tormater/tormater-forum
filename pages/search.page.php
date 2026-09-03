<?php
// search.page.php
// Allows users to search through all threads on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if (get_role_from_session() == "Guest" && $config['searchMembersOnly'])
{
    include "header.php";
    message(sprintf($lang["nav.LoginRequired"], genURL("login")));
    include "footer.php";
    exit;
}

include "header.php";

$data = array
(
    "title" => $lang["search.Button"],
    "searchText" => "",
    "authorText" => "",
    "userText" => "",
    "category_options" => "<option value=''>" . $lang["search.CategoryPlaceholder"] . "</option>",
    "user_placeholder" => $lang["search.UserPlaceholder"],
    "title_label" => $lang["search.TitleLabel"],
    "author_label" => $lang["search.AuthorLabel"],
    "category_label" => $lang["search.CategoryLabel"],
    "user_label" => $lang["search.UserLabel"],
    "sort_by_label" => $lang["userlist.SortBy"],
    "label_label" => $lang["search.LabelLabel"],
    "draft" => $lang["label.Draft"],
    "locked" => $lang["label.Locked"],
    "sticky" => $lang["label.Sticky"],
    "pinned" => $lang["label.Pinned"],
    "draft_checked" => isset($_GET["draft"]) ? "checked=''" : "",
    "locked_checked" => isset($_GET["locked"]) ? "checked=''" : "",
    "sticky_checked" => isset($_GET["sticky"]) ? "checked=''" : "",
    "pinned_checked" => isset($_GET["pinned"]) ? "checked=''" : "",
    "sort_options" => "",
    "sort_order_options" => "",
    "submit" => $lang["search.Submit"],
    "table" => ""
);
if (isset($_GET["search"])) {
    $data["searchText"] = htmlspecialchars($_GET["search"]);
}
if (isset($_GET["author"])) {
    $data["authorText"] = htmlspecialchars($_GET["author"]);
}
if (isset($_GET["user"])) {
    $data["userText"] = htmlspecialchars($_GET["user"]);
}

$categories = $db->query("SELECT * FROM categories");

while($row = $categories->fetch_assoc()) {
    if (isset($_GET["category"]) && $_GET["category"] == $row["categoryid"]) $selected = "selected=''";
    else $selected = "";
        
    $data["category_options"] .= '<option ' . $selected . 'value="' . $row["categoryid"] . '">' . htmlspecialchars($row["categoryname"]) . '</option>';
}

foreach ($thread_sortoptions as $s => $v) {
    if (isset($_GET["sort_by"]) && $_GET["sort_by"] == $s) $selected = "selected=''";
    else $selected = "";
        
    $data["sort_options"] .= '<option ' . $selected . 'value="' . $s . '">' . $lang["userlist.sort.".$s] . '</option>';
}
foreach ($thread_sortorderoptions as $s => $v) {
    if (isset($_GET["sort_order"]) && $_GET["sort_order"] == $s) $selected = "selected=''";
    else $selected = "";
        
    $data["sort_order_options"] .= '<option ' . $selected . 'value="' . $s . '">' . $lang["userlist.sort_order.".$s] . '</option>';
}

if (isset($_GET["search"]) && strlen($_GET["search"]) > 64)
{
    $data["table"] = message($lang["search.TooLong"], 1);
    echo $template->render("templates/search/search_page.html", $data);
    include 'footer.php';
    exit;
}

$search = buildSearchQuery($_GET);

$data["table"] = generator_threads(["query" => $_SERVER['QUERY_STRING'],"pagination" => "true"]);

echo $template->render("templates/search/search_page.html", $data);

include 'footer.php';
