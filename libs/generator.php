<?php
// generator.php
// Widget-based page generator

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$custom_pages = array(
    "homepage" => ['{"title":"$homepage.Title","categories":{},"threads":{"query":"sort_by=activity&sort_order=asc","limit":5}}']
);
$custom_page_depth = 2;

function try_load_custom_pages() {
    global $url, $db, $config, $custom_pages,$custom_page_depth;
    $category = $db->query("SELECT * FROM categories");
    while ($row = $category->fetch_assoc()) 
    {
        $custom_pages["category/" . $row["categoryid"]] = [json_encode([
        "title" => ["title"=>$row["categoryname"],"desc"=>formatPost($row["categorydescription"])],
        "threads" => ["query"=>'sort_by=activity&sort_order=asc&category='.$row["categoryid"],"pagination"=>"true"]
        ])];
    }
    $size = count($url);
    for ($i = 0; $i < $size; $i++) {
        if ($i > 0) $urlpart .= "/" . $url[$i];
        else $urlpart = $url[0];
        if (array_key_exists($urlpart,$custom_pages)) {
            $custom_page_depth = 2+$i;
            return page_generate($custom_pages[$urlpart][0]);
        }
    }
    return false;
}

$layout_widgets = array();

$widgets = array(
    "bbcode" => "generator_bbcode",
    "threads" => "generator_threads",
    "categories" => "generator_categories",
    "title" => "generator_title",
);

function localize($string) {
    global $lang;
    if ($string[0] == "$" && array_key_exists(substr($string,1),$lang)) {
        return $lang[substr($string,1)];
    }
    else return htmlspecialchars($string);
}

function page_generate($page) {
    global $widgets;
    try {
        $dpage = json_decode($page,true,512,JSON_THROW_ON_ERROR);
    }
    catch (Exception $e) {
        return message("JSON parse error while generating page: " . $e->getMessage(),true);
    }
    $data = "";
    foreach($dpage as $w => $v) {
        if (!array_key_exists($w,$widgets)) continue;
        if (!is_callable($widgets[$w])) continue;
        $data .= call_user_func($widgets[$w],$v);
    }
    return $data;
}

// Generator functions

function generator_bbcode($widget) {
    global $template;
    if (!is_string($widget)) return;
    return $template->render("templates/generator/text.html",array("text"=>formatBBCode($widget)));
}

function generator_title($widget) {
    global $template;
    if (is_string($widget)) return $template->render("templates/generator/title.html",array("title"=>localize($widget)));
    else return $template->render("templates/generator/titledesc.html",array("title"=>localize($widget["title"]),"desc"=>$widget["desc"]));
}

function generator_threads($widget) {
    global $config, $db, $template, $lang, $url, $custom_page_depth;
    if (!isset($widget["query"])) return;
    $pagination = "";
    $offset = "";
    if (isset($widget["limit"])) {
         if (intval($widget["limit"]) < 1) unset($widget["limit"]);
         else $limit = "LIMIT " . intval($widget["limit"]);
    }
    else {
         $threadsPerPage = (is_numeric($config["threadsPerPage"]) ? (int)$config["threadsPerPage"] : 20);
         if ($threadsPerPage < 1) $threadsPerPage = 1;
         $limit = "LIMIT " . $threadsPerPage;
    }
    if (isset($widget["pagination"])) {
         $thread_count = $db->query("SELECT * FROM threads " . buildSearchQuery(createQueryArray($widget["query"])));
         $threadsPerPage = (isset($widget["limit"]) ? intval($widget["limit"]) : 10);
         $numThreads = $thread_count->num_rows;
         $pages = ceil($numThreads / $threadsPerPage);
         $GLOBALS["pages"] = $pages;
         $currentPage = @$url[$custom_page_depth-1];
         if (!is_numeric($currentPage) || $currentPage < 1) $currentPage = 1;
         $GLOBALS["currentPage"] = $currentPage;
         $offset = " OFFSET " . (($currentPage * $threadsPerPage) - $threadsPerPage);
         $pagination = renderPagination($custom_page_depth,1);
    }    
    $threads = $db->query("SELECT * FROM threads " . buildSearchQuery(createQueryArray($widget["query"])) . " " . $limit . $offset);
    
    
    $data = array(
      "th_lastpost" => $lang["category.LastPost"],
      "th_recentthreads" => $lang["homepage.Threads"],
      "th_posts" => $lang["category.Posts"],
      "threads" => ""
    );
    if (isset($widget["title"])) $data["th_recentthreads"] = localize($widget["title"]);
    
    if ($threads->num_rows == 0) $data["threads"] = $template->render("templates/thread/thread_display_blank.html", array("title" => $lang["error.CategoryEmpty"]));
    
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
        $labels = array();
        if ($row["locked"] == true) $labels["locked"] = $lang["label.Locked"];
        if ($row["sticky"] == true) $labels["sticky"] = $lang["label.Sticky"];
        if ($row["draft"] == true) $labels["draft"] = $lang["label.Draft"];
        if ($row["pinned"] == true) $labels["pinned"] = $lang["label.Pinned"];
        listener("beforeRenderThreadLabels",$labels);
        foreach ($labels as $k => $v) {
            $label_data = array("text" => $v, "class" => $k);
            $thread_data["labels"] .= $template->render("templates/thread/label.html",$label_data);
        }           
        $data["threads"] .= $template->render("templates/thread/thread_display.html", $thread_data);
    }
    return $pagination . $template->render("templates/generator/thread_table.html",$data) . $pagination;
}

function generator_categories($widget) {
    global $config, $db, $template, $lang;
    $where = "";
    if (isset($widget["which"])) {
        $where .= " WHERE categoryid in (";
        foreach ($widget["which"] as $v) {
            $where .= intval($v) . ",";
        }
        $where = substr($where,0,-1) . ")";
    }
    $query = "SELECT * FROM `categories`" . $where . " ORDER BY `order` ASC";
    $categories = $db->query($query);
    $data = array(
      "categories" => "",
      "th_category" => $lang["homepage.Cats"],
      "th_threads" => $lang["homepage.CatThreads"],
      "th_lastpost" => $lang["category.LastPost"]
    );
    
    if (isset($widget["title"])) $data["th_category"] = localize($widget["title"]);
    
    while($row = $categories->fetch_assoc()) 
    {
        $numthreads = $db->query("SELECT * FROM threads WHERE draft='0' AND category='" . $row["categoryid"] . "' ORDER BY lastposttime DESC");
        $trow = $numthreads->fetch_assoc();
        $title = "";
        $user = "";
        if ($numthreads->num_rows > 0) {
            $uinfo = $db->query("SELECT * FROM users WHERE userid='" . $trow["lastpostuser"] . "'");
            $title = htmlspecialchars($trow['title']);
            $u = $uinfo->fetch_assoc();
            if ($u["deleted"] == 1) $username = $lang["user.Deleted"] . $u["userid"];
            else $username = $u["username"];
            if ($trow["posts"] > 1) $title = sprintf($lang["category.ReplyTo"], $title);
            $user = sprintf("<span>" .$lang["thread.Info"] . "</span>", $u["role"], genURL("user/" . htmlspecialchars($trow["lastpostuser"])), htmlspecialchars($username), date('m-d-Y h:i:s A', $trow['lastposttime']), relativeTime($trow["lastposttime"]));
        }

        $category_data = array
        (
            "url" => genURL("category/" . $row["categoryid"]),
	    "title" => htmlspecialchars($row["categoryname"]),
	    "desc" => formatPost($row["categorydescription"]),
	    "threads" => $numthreads->num_rows,
	    "lastpost" => $title,
	    "lastposturl" => !isset($trow['threadid']) ?: genURL('thread/' . $trow['threadid']),
	    "user" => $user,
        );
	
        $data["categories"] .= $template->render("templates/category/category_display.html", $category_data);
    }
    
    return $template->render("templates/generator/category_table.html",$data);
}

// Search query

function addToQuery($add, &$query, &$and) {
    if ($and == 1) $query .= "AND ";
    $query .= $add . " ";
    $and = 1;
}

function createQueryArray($string) {
    $array = array();
    $parameters = explode('&', $string);

    foreach ($parameters as $p) {
        $parameter = explode('=', $p);
        $array[$parameter[0]] = $parameter[1];
    }
    return $array;
}

$thread_sortoptions = array(
  "activity" => "lastposttime",
  "time" => "starttime",
  "alphabet" => "title",
  "posts" => "posts"
);

$thread_sortorderoptions = array(
  "asc" => "ASC",
  "desc" => "DESC"
);

function buildSearchQuery($get) {
    global $db, $thread_sortoptions, $thread_sortorderoptions;
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
    if (isset($get["sort_by"]) && $get["sort_by"] != null && array_key_exists($get["sort_by"],$thread_sortoptions)) {
        $query .= "ORDER BY " . $thread_sortoptions[$get["sort_by"]] . " ";
        $sort_by = $thread_sortoptions[$get["sort_by"]];
    }
    else $query .= "ORDER BY lastposttime ";
    
    $order = "ASC";
    if (isset($get["sort_order"]) && $get["sort_order"] != null && array_key_exists($get["sort_order"],$thread_sortorderoptions)) {
        $order = $thread_sortorderoptions[$get["sort_order"]];
    }
    
    if ($sort_by == "lastposttime" || $sort_by == "starttime" || $sort_by == "posts") {
        if ($order == "DESC") $order = "ASC";
        else $order = "DESC";
    }
    
    return $query . $order;
}

?>
