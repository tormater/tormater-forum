<?php
// category.page.php
// Shows all the threads inside a category.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$category = $db->query("SELECT * FROM categories WHERE categoryid='" . $db->real_escape_string($q2) . "'");

if (isset($q3) && is_numeric($q3)) 
{
    $currentPage = $q3;
}
else
{
    $currentPage = 1;
}

while ($row = $category->fetch_assoc()) 
{
    $categoryName = $row["categoryname"];
    $categoryID = $db->real_escape_string($q2);
    $category_data = array(
        "title" => htmlspecialchars($row["categoryname"]),
        "desc" => formatPost($row["categorydescription"]),
        "pagination" => "",
        "threads" => "",
        "th_thread" => $lang["category.Thread"],
        "th_posts" => $lang["category.Posts"],
        "th_lastpost" => $lang["category.LastPost"],
    );
}

include "header.php";

$thread_count = $db->query("SELECT 1 FROM threads WHERE category='" . $db->real_escape_string($q2) . "'");

// Important details for sorting the threads into pages.
$threadsPerPage = (is_numeric($config["threadsPerPage"]) ?? 20);
if ($threadsPerPage < 1) $threadsPerPage = 1;
$numThreads = $thread_count->num_rows;
if ($numThreads < 1) $numThreads = 1;
$pages = ceil($numThreads / $threadsPerPage);

// Calculate the offset for the threads query.
$offset = (($currentPage * $threadsPerPage) - $threadsPerPage);

if ($_SESSION["signed_in"] != true) {
    $threads = $db->query("SELECT * FROM threads WHERE category='" . $db->real_escape_string($q2) . "' AND draft='0' UNION SELECT * FROM threads WHERE pinned='1' AND draft='0' ORDER BY pinned DESC, sticky DESC, lastposttime DESC LIMIT " . $threadsPerPage . " OFFSET " . $offset . "");
}
else if (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator")) {
    $threads = $db->query("SELECT * FROM threads WHERE category='" . $db->real_escape_string($q2) . "' UNION SELECT * FROM threads WHERE pinned='1' ORDER BY pinned DESC, sticky DESC, lastposttime DESC LIMIT " . $threadsPerPage . " OFFSET " . $offset . "");
}
else {
    $threads = $db->query("SELECT * FROM threads WHERE category='" . $db->real_escape_string($q2) . "' AND draft='0' OR (draft='1' AND startuser='" . $_SESSION["userid"] . "') UNION SELECT * FROM threads WHERE pinned='1' AND draft='0' OR (draft='1' AND startuser='" . $_SESSION["userid"] . "') ORDER BY pinned DESC, sticky DESC, lastposttime DESC LIMIT " . $threadsPerPage . " OFFSET " . $offset . "");
}

if (!$category)
{
    echo $lang["error.CategoryMisc"];
    include 'footer.php';
    exit;
}
else if ($category->num_rows == 0)
{
    echo $lang["error.CategoryNotFound"];
    include 'footer.php';
    exit;
}

while($row = $threads->fetch_assoc())
{		
    $suinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["startuser"] . "'");	
    $su = $suinfo->fetch_assoc();
    if ($su["deleted"] == 1) $susername = $lang["user.Deleted"] . $su["userid"];
    else $susername = $su["username"];
    
    $uinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["lastpostuser"] . "'");	
    $u = $uinfo->fetch_assoc();
    if ($u["deleted"] == 1) $username = $lang["user.Deleted"] . $u["userid"];
    else $username = $u["username"];
    
    $thread_data = array
    (
        "labels" => "",
        "url" => genURL('thread/' . $row['threadid']),
        "title" => htmlspecialchars($row['title']),
        "startuser" => sprintf("<span>" .$lang["thread.Info"] . "</span>", $su["role"], genURL("user/" . htmlspecialchars($row["startuser"])), htmlspecialchars($susername), date('m-d-Y h:i:s A', $row['starttime']), relativeTime($row["starttime"])),
        "posts" => $row['posts'],
        "user" => '<a href="' . genURL('user/' . $row['lastpostuser']) . '" id="' . $u["role"] . '">' . htmlspecialchars($username) . '</a>',
        "date" => date('m-d-Y h:i:s A', $row['lastposttime']),
        "reldate" => relativeTime($row["lastposttime"]),
    );
    if ($row["locked"]) 
        $thread_data["labels"] .= $template->render("templates/thread/label.html", ["class"=>"locked","text"=>$lang["label.Locked"]]); 
    if ($row["sticky"]) 
        $thread_data["labels"] .= $template->render("templates/thread/label.html", ["class"=>"sticky","text"=>$lang["label.Sticky"]]);
    if ($row["draft"]) 
        $thread_data["labels"] .= $template->render("templates/thread/label.html", ["class"=>"draft","text"=>$lang["label.Draft"]]);  
    if ($row["pinned"]) 
        $thread_data["labels"] .= $template->render("templates/thread/label.html", ["class"=>"pinned","text"=>$lang["label.Pinned"]]);
        
    $category_data["threads"] .= $template->render("templates/thread/thread_display.html", $thread_data);
}

$category_data["pagination"] = pagination_return("category");

if ($threads->num_rows == 0) $category_data["threads"] = $template->render("templates/thread/thread_display_blank.html", array("title" => $lang["error.CategoryEmpty"]));

echo $template->render("templates/category/category_page.html", $category_data);

include 'footer.php';
