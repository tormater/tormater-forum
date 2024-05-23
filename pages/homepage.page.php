<?php
// homepage.page.php
// Initializes the home page.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

include 'header.php';

$data = array(
    "title" => $lang["homepage.Title"],
    "th_category" => $lang["homepage.Cats"],
    "th_threads" => $lang["homepage.CatThreads"],
    "th_lastpost" => $lang["category.LastPost"],
    "categories" => "",
    "th_recentthreads" => $lang["homepage.Threads"],
    "th_posts" => $lang["category.Posts"],
    "threads" => "",
);

$categories = $db->query("SELECT * FROM `categories` ORDER BY `order` ASC");
$threads = $db->query("SELECT * FROM threads WHERE draft='0' ORDER BY lastposttime DESC LIMIT 5");

while($row = $categories->fetch_assoc()) 
{
    $numthreads = $db->query("SELECT * FROM threads WHERE category='" . $row["categoryid"] . "' ORDER BY lastposttime DESC");
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
	"lastposturl" => genURL('thread/' . $trow['threadid']),
	"user" => $user,
    );
	
    $data["categories"] .= $template->render("templates/category/category_display.html", $category_data);
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
        
    $data["threads"] .= $template->render("templates/thread/thread_display.html", $thread_data);
}
if ($threads->num_rows == 0) $data["threads"] = $template->render("templates/thread/thread_display_blank.html", array("title" => $lang["error.ForumEmpty"]));

echo $template->render("templates/homepage/homepage.html", $data);

include 'footer.php';

update_last_action("action.Homepage");

?>
