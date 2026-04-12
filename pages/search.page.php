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

function addToQuery($add, &$query, &$and) {
    if ($and == 1) $query .= "AND ";
    $query .= $add . " ";
    $and = 1;
}
function buildSearchQuery($get) {
    global $db, $sortoptions, $sortorderoptions;
    $query = "WHERE ";
    $and = 0;
    $author = 0;
    
    if (isset($get["search"]) && $get["search"] != null) {
        addToQuery("(`title` LIKE '%" . $db->real_escape_string(urldecode($get["search"])) . "%')", $query, $and);
    }
    if (isset($get["author"]) && $get["author"] != null) {
        $user = $db->query("SELECT * FROM users WHERE username='" . $db->real_escape_string(urldecode($get["author"])) . "'");
        if ($user->num_rows) {
            $user_row = $user->fetch_assoc();
            addToQuery("startuser='". $user_row["userid"] . "'", $query, $and);
            $author = 1;
        }
    }
    if (isset($get["category"]) && $get["category"] != null) {
        $category = $db->query("SELECT 1 FROM categories WHERE categoryid='" . $db->real_escape_string(urldecode($get["category"])) . "'");
        if ($category->num_rows) {
            addToQuery("category='". $db->real_escape_string(urldecode($get["category"])) . "'", $query, $and);
        }
    }
    if (isset($get["user"]) && $get["user"] != null) {
        $user = $db->query("SELECT * FROM users WHERE username='" . $db->real_escape_string(urldecode($get["user"])) . "'");
        if ($user->num_rows) {
            $user_row = $user->fetch_assoc();
            $posts = $db->query("SELECT * FROM posts WHERE user='" . $user_row["userid"] . "'");
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
    $labels = array();
    if (isset($get["label"]) && $get["label"] != null) {
        $labels = explode(",", $get["label"]);
    }
    if (isset($get["locked"]) && $get["locked"] != null) $labels[] = "locked";
    if (isset($get["sticky"]) && $get["sticky"] != null) $labels[] = "sticky";
    if (isset($get["pinned"]) && $get["pinned"] != null) $labels[] = "pinned";
    if (isset($get["draft"]) && $get["draft"] != null)   $labels[] = "draft";
    
    if (in_array("locked",$labels)) addToQuery("locked='1'", $query, $and);
    else if (in_array("!locked",$labels)) addToQuery("locked='0'", $query, $and);
    if (in_array("sticky",$labels)) addToQuery("sticky='1'", $query, $and);
    else if (in_array("!sticky",$labels)) addToQuery("sticky='0'", $query, $and);
    if (in_array("pinned",$labels)) addToQuery("pinned='1'", $query, $and);
    else if (in_array("!pinned",$labels)) addToQuery("pinned='0'", $query, $and);
    
    if (in_array("draft", $labels)) {
        if (($_SESSION["signed_in"] && !$author)) {
            addToQuery("draft='1'",  $query, $and);
            addToQuery("startuser='". $_SESSION["userid"] . "'", $query, $and);
        }
        else addToQuery("draft='0'",  $query, $and);
    }
    else addToQuery("draft='0'",  $query, $and);
    
    $sort_by = "lastposttime";
    if (isset($get["sort_by"]) && $get["sort_by"] != null && array_key_exists($get["sort_by"],$sortoptions)) {
        $query .= "ORDER BY " . $sortoptions[$get["sort_by"]] . " ";
        $sort_by = $sortoptions[$get["sort_by"]];
    }
    else $query .= "ORDER BY lastposttime ";
    
    $order = "ASC";
    if (isset($get["sort_order"]) && $get["sort_order"] != null && array_key_exists($get["sort_order"],$sortorderoptions)) {
        $order = $sortorderoptions[$get["sort_order"]];
    }
    
    if ($sort_by == "lastposttime" || $sort_by == "starttime" || $sort_by == "posts") {
        if ($order == "DESC") $order = "ASC";
        else $order = "DESC";
    }
    
    return $query . $order;
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

$sortoptions = array(
  "activity" => "lastposttime",
  "time" => "starttime",
  "alphabet" => "title",
  "posts" => "posts"
);

$sortorderoptions = array(
  "asc" => "ASC",
  "desc" => "DESC"
);

foreach ($sortoptions as $s => $v) {
    if (isset($_GET["sort_by"]) && $_GET["sort_by"] == $s) $selected = "selected=''";
    else $selected = "";
        
    $data["sort_options"] .= '<option ' . $selected . 'value="' . $s . '">' . $lang["userlist.sort.".$s] . '</option>';
}
foreach ($sortorderoptions as $s => $v) {
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

$threads = $db->query("SELECT * FROM threads " . $search . " LIMIT " . $config["threadsPerPage"] . " OFFSET " . $offset . "");

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
        "user" => '<a href="' . genURL('user/' . $row['lastpostuser']) . '" class="' . $u["role"] . '">' . htmlspecialchars($username) . '</a>',
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

$table_data["pagination"] = renderPagination(2,1);

$data["table"] = $template->render("templates/search/search_table.html", $table_data);

echo $template->render("templates/search/search_page.html", $data);

include 'footer.php';
