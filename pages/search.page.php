<?php
// search.page.php
// Allows users to search through all threads on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

function addToQuery($add, &$query, &$and) {
    if ($and == 1) $query .= "AND ";
    $query .= $add . " ";
    $and = 1;
}
function buildSearchQuery($get) {
    global $db;
    $query = "WHERE ";
    $and = 0;
    $author = 0;
    
    if ($get["search"] != null) {
        addToQuery("(`title` LIKE '%" . $db->real_escape_string(urldecode($get["search"])) . "%')", $query, $and);
    }
    if ($get["author"] != null) {
        $user = $db->query("SELECT 1 FROM users WHERE userid='" . $db->real_escape_string(urldecode($get["author"])) . "'");
        if ($user->num_rows) {
            addToQuery("startuser='". $db->real_escape_string(urldecode($get["author"])) . "'", $query, $and);
            $author = 1;
        }
    }
    if ($get["user"] != null) {
        $user = $db->query("SELECT 1 FROM users WHERE userid='" . $db->real_escape_string(urldecode($get["user"])) . "'");
        if ($user->num_rows) {
            $posts = $db->query("SELECT * FROM posts WHERE user='" . $db->real_escape_string(urldecode($get["user"])) . "'");
            if ($posts->num_rows) {
                $threads = array();
                while($row = $posts->fetch_assoc())
	        {
	            if (!in_array($row["thread"],$threads)) array_push($threads,$row["thread"]);
	        }
	        if (count($threads)) {
	            $query_add = "(";
	            $first = 1;
	            foreach($threads as $t) {
	                if (!$first) $query_add .= " OR ";
	                $query_add .= "threadid='" . $t . "'";
	                $first = 0;
	            }
	            $query_add .= ")";
	            addToQuery($query_add,$query,$and);
	        }
	    }
        }
    }
    if ($get["label"] != null) {
        $labels = explode(",", $get["label"]);
        if (in_array("locked",$labels)) addToQuery("locked='1'", $query, $and);
        else if (in_array("!locked",$labels)) addToQuery("locked='0'", $query, $and);
        if (in_array("sticky",$labels)) addToQuery("sticky='1'", $query, $and);
        else if (in_array("!sticky",$labels)) addToQuery("sticky='0'", $query, $and);
        if (in_array("pinned",$labels)) addToQuery("pinned='1'", $query, $and);
        else if (in_array("!pinned",$labels)) addToQuery("pinned='0'", $query, $and);
    }
    if ($get["label"] != null && in_array("draft", $labels)) {
        if (($_SESSION["signed_in"] && !$author)) {
            addToQuery("draft='1'",  $query, $and);
            addToQuery("startuser='". $_SESSION["userid"] . "'", $query, $and);
        }
        else if ((($_SESSION["role"] == "Moderator") || ($_SESSION["role"] == "Administrator")) && $author) {
            addToQuery("draft='1'",  $query, $and);
        }
        else addToQuery("draft='0'",  $query, $and);
    }
    else addToQuery("draft='0'",  $query, $and);
    
    if ($query == "WHERE draft='0' ") $query = "";
    return $query;
}

if (isset($q2) && is_numeric($q2)) 
{
    $currentPage = $q2;
}
else
{
    $currentPage = 1;
}

include "header.php";

$data = array
(
    "title" => $lang["search.Button"],
    "table" => ""
);

if (isset($_GET["search"]) && strlen($_GET["search"]) > 64)
{
    $data["table"] = message($lang["search.TooLong"], 1);
    echo $template->render("templates/search/search_page.html", $data);
    include 'footer.php';
    exit;
}

$search = buildSearchQuery($_GET);

if (!$search || strlen($search) < 1)
{
    $data["table"] = message($lang["search.EmptySearch"], 1);
    echo $template->render("templates/search/search_page.html", $data);
    include 'footer.php';
    exit;
}

// Important details for sorting the threads into pages.
$thread_count = $db->query("SELECT 1 FROM threads " . $search);

$numThreads = $thread_count->num_rows;

if (!$numThreads) {
    $data["table"] = message($lang["search.NoResults"], 1);
    echo $template->render("templates/search/search_page.html", $data);
    include 'footer.php';
    exit;
}

if ($numThreads < 1) $numThreads = 1;
$pages = ceil($numThreads / $config["threadsPerPage"]);
if ($currentPage > $pages) $currentPage = $pages;
if ($currentPage < 1) $currentPage = 1;

// Calculate the offset for the threads query.
$offset = (($currentPage * $config["threadsPerPage"]) - $config["threadsPerPage"]);

$threads = $db->query("SELECT * FROM threads " . $search . " ORDER BY lastposttime DESC LIMIT " . $config["threadsPerPage"] . " OFFSET " . $offset . "");

$table_data = array(
    "title" => $lang["search.Header"],
    "pagination" => "",
    "threads" => "",
    "th_thread" => $lang["category.Thread"],
    "th_posts" => $lang["category.Posts"],
    "th_lastpost" => $lang["category.LastPost"],
);

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
        
    $table_data["threads"] .= $template->render("templates/thread/thread_display.html", $thread_data);
}

//$table_data["pagination"] = pagination_return("search");

$data["table"] = $template->render("templates/search/search_table.html", $table_data);

echo $template->render("templates/search/search_page.html", $data);

include 'footer.php';
