<?php
// thread.page.php
// Shows the inside of a thread and allows users and mods/admins to perform many actions involving posts and the thread itself.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$thread = $db->query("SELECT * FROM threads WHERE threadid='" . $db->real_escape_string($q2) . "'");

if ($thread->num_rows < 1) {
    include 'header.php'; 
    message($lang["thread.ThreadDoesntExist"]);
    include 'footer.php';
    exit();
}

$postsPerPage = (is_numeric($config["postsPerPage"]) ? (int)$config["postsPerPage"] : 10);
if ($postsPerPage < 1) $postsPerPage = 1;

$currentPage = 1;
if (isset($q3) && is_numeric($q3)) {
    $currentPage = $q3;
}

$row = $thread->fetch_assoc();

$categoryID = $row['category'];
$title = $row['title'];
$locked = $row['locked'];
$stickied = $row['sticky'];
$draft = $row['draft'];
$startuser = $row['startuser'];
$pinned = $row['pinned'];
$viewerid = NULL;
if (isset($_SESSION["userid"])) $viewerid = $_SESSION["userid"];

if ($draft == 1 and $viewerid != $startuser) {
    include 'header.php';
    message($lang["thread.ThreadDoesntExist"]);
    include "footer.php";
    exit();
}
    
$numPosts = $row['posts'];
$pages = ceil($numPosts / $postsPerPage);
if ($currentPage > $pages) $currentPage = $pages;
$offset = (($currentPage * $postsPerPage) - $postsPerPage);

$posts = $db->query("SELECT * FROM posts WHERE thread='" . $db->real_escape_string($q2) . "' ORDER BY timestamp LIMIT " . $postsPerPage . " OFFSET " . $offset . "");

if (!$posts) {
    include 'header.php';
    message($lang["thread.ThreadsNoPosts"]);
    include "footer.php";
    exit();
}

$authorinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["startuser"] . "'");
$author = $authorinfo->fetch_assoc();
$username = $author["username"];
if ($author["deleted"] == 1) $username = $lang["user.Deleted"] . $author["userid"];

if (isset($_SESSION["userid"])) {
    $draftp = $db->query("SELECT content FROM drafts WHERE user='" . $_SESSION["userid"] . "' AND thread='" . $db->real_escape_string($q2) . "'");
    if ($draftp->num_rows > 0) {
        $d = $draftp->fetch_assoc();
        $draftPost = $d["content"];
    }
}

$thread_data = array(
    "title" => "",
    "info" => sprintf($lang["thread.Info"],$author["role"], genURL("user/" . htmlspecialchars($author["userid"])), htmlspecialchars($username), date('m-d-Y h:i:s A', $row['starttime']), relativeTime($row["starttime"])),
    "modtools" => "",
    "labels" => "",
    "pagination" => pagination_return("thread"),
    "posts" => "",
    "reply" => "",
    "error" => "",
);

if (($author["userid"] == $viewerid and get_role_permissions() & PERM_CREATE_THREAD) or get_role_permissions() & PERM_EDIT_THREAD) {
    $thread_data["title"] = $template->render("templates/thread/thread_title_edit.html",array("title" => htmlspecialchars($title),"maxtitle" => $config["maxCharsPerTitle"]));
}
else $thread_data["title"] = $template->render("templates/thread/thread_title.html",array("title" => htmlspecialchars($title)));

if (get_role_permissions() & PERM_EDIT_THREAD) 
{
    $modtools_data = array(
       "id" => $q2,
       "movethread" => $lang["thread.MoveThreadBtn"],
       "categories" => "",
       "buttons" => "",
    );
    
    $categories = $db->query("SELECT * FROM categories");
    while ($row = $categories->fetch_assoc()) {
        $modtools_data["categories"] .= '<option ';
        if ($categoryID == $row["categoryid"]) $modtools_data["categories"] .= "selected ";
        $modtools_data["categories"] .= 'value="' . $row['categoryid'] . '">' . htmlspecialchars($row['categoryname']) . '</option>';
    }
    
    $tools = array(
        "deletethread" => $lang["thread.DeleteThreadBtn"],
    );
    
    if ($locked == true) $tools["unlockthread"] = $lang["thread.UnlockThreadBtn"];
    else $tools["lockthread"] = $lang["thread.LockThreadBtn"];
    if ($stickied == true) $tools["unstickythread"] = $lang["thread.UnstickyThreadBtn"];
    else $tools["stickythread"] = $lang["thread.StickyThreadBtn"];
    if (get_role_permissions() & PERM_EDIT_FORUM) 
    {
        if ($pinned == true) $tools["unpinthread"] = $lang["thread.UnpinThreadBtn"];
        else $tools["pinthread"] = $lang["thread.PinThreadBtn"];
    }
    
    listener("beforeRenderModTools",$tools);
    
    foreach ($tools as $k => $v) {
        $button_data = array("label" => $v, "name" => $k, "id" => $q2);
        $modtools_data["buttons"] .= $template->render("templates/thread/modtools_button.html",$button_data);
    }
    
    $thread_data["modtools"] = $template->render("templates/thread/modtools.html",$modtools_data);
}

if ($locked == true or $stickied == true or $draft == true or $pinned == true)
{
    $thread_data["labels"] .= '<div>'.$lang["thread.Labels"];
    $labels = array();
    if ($locked == true) $labels["locked"] = $lang["label.Locked"];
    if ($stickied == true) $labels["sticky"] = $lang["label.Sticky"];
    if ($draft == true) $labels["draft"] = $lang["label.Draft"];
    if ($pinned == true) $labels["pinned"] = $lang["label.Pinned"];
    listener("beforeRenderThreadLabels",$labels);
    foreach ($labels as $k => $v) {
        $label_data = array("label" => $v, "class" => $k);
        $thread_data["labels"] .= $template->render("templates/thread/thread_label.html",$label_data);
    }
    $thread_data["labels"] .= '</div>';
}

while ($post = $posts->fetch_assoc())
{
    $post_author_info = $db->query("SELECT * FROM users WHERE userid='" . $post["user"] . "'");
    $post_author = $post_author_info->fetch_assoc();
    
    $post_data = array(
      "id" => $post["postid"],
      "class" => "",
      "color" => $post_author["color"],
      "hidden_class" => "",
      "deleted_class" => "",
      "profile" => "",
      "buttons" => "",
      "date" => date('m-d-Y h:i:s A', $post["timestamp"]),
      "timestamp" => relativeTime($post["timestamp"]),
      "quote" => "",
      "body" => "",
      "hidden_info" => "",
    );
    
    if (isset($post["deletedby"])) $post_data["hidden_class"] = " hidden";
    if ($post_author["deleted"] == true) $post_data["deleted_class"] = " deleteduser";
    
    ob_start();
    drawUserProfile($post_author["userid"], 0, isset($post["deletedby"]));
    $post_data["profile"] = ob_get_contents();
    ob_end_clean();
    
    if (($post_author["userid"] == $viewerid and get_role_permissions() & PERM_CREATE_POST) or get_role_permissions() & PERM_EDIT_POST) {
        $buttons_data = array(
          "id" => $post["postid"],
          "edit_label" => $lang["post.EditBtn"],
          "hide_label" => $lang["post.HideBtn"],
          "delete_label" => $lang["post.DeleteBtn"]
        );
        $post_data["buttons"] = $template->render("templates/post/post_buttons.html",$buttons_data);
    }
    
    if ((isset($_POST["edit"]) && $_POST["edit"] == $post["postid"]) && 
      (($post_author["userid"] == $viewerid and get_role_permissions() & PERM_CREATE_POST) or get_role_permissions() & PERM_EDIT_POST) ) 
    {
        $edit_data = array(
          "id" => $post["postid"],
          "bbcodebar" => BBCodeButtons(2,false),
          "content" => htmlspecialchars($post["content"]),
          "save_edit" => $lang["post.SaveEditBtn"],
          "discard_edit" => $lang["post.DiscardEditBtn"],
          "maxlength" => $config["maxCharsPerPost"],
        );
        $post_data["body"] = $template->render("templates/post/post_edit.html",$edit_data);
        $post_data["body"] .= "<script>editbox = document.getElementById('edit'); editbox.scrollIntoView({block:'center'});</script>";
    }
    else {
        $post_data["body"] = formatPost($post["content"]);
        if (isset($post_author["signature"]) && strlen($post_author["signature"]) && $post_author["deleted"] != true) {
            $post_data["body"] .= '<hr class="sigline"><p class="signature">' . formatPost($post_author["signature"]) . '</p>';
        }
    }
    if ((($post_author["userid"] == $viewerid and get_role_permissions() & PERM_CREATE_POST) or get_role_permissions() & PERM_EDIT_POST) and !isset($_POST["edit"])) 
    {
        $post_data["quote"] = $template->render("templates/post/post_quote.html",array("id" => $post["postid"],"quote_label" => $lang["thread.QuotePost"]));
    }
    if (isset($post["deletedby"])) {
        $hider = $db->query("SELECT * FROM users WHERE userid='" . $post["deletedby"] . "'");
        $h = $hider->fetch_assoc();
        if ($h["deleted"] == "1") $hideusername = $lang["user.Deleted"] . $post["deletedby"];
        else $hideusername = $h["username"];
        
        if (($post["deletedby"] == $viewerid and get_role_permissions() & PERM_CREATE_POST) or get_role_permissions() & PERM_EDIT_POST) {
            $post_data["quote"] = $template->render("templates/post/post_restore.html",array("id" => $post["postid"],"label" => $lang["post.RestoreHiddenBtn"]));
        }
        else $post_data["quote"] = "";
        
        $role = $h["role"];
        $post_data["hidden_info"] = sprintf($lang["thread.HiddenBy"], (genURL('user/' . $post["deletedby"] . '/')), $role, htmlspecialchars($hideusername));
        $thread_data["posts"] .= $template->render("templates/post/post_display_hidden.html",$post_data);        
    }
    else $thread_data["posts"] .= $template->render("templates/post/post_display.html",$post_data);
}

if ($locked == true && !(get_role_permissions() & PERM_IGNORE_THREAD_LABELS)) {
    $thread_data["reply"] = message($lang["thread.ThreadLocked"],true);
}
else if (get_role_permissions() & PERM_CREATE_POST) {
    $replybox_data = array(
      "bbcodebar" => BBCodeButtons(1,false),
      "content" => "",
      "discard" => "",
      "reply_label" => $lang["thread.PostReplyBtn"],
      "preview_show_label" => $lang["nav.ShowPreview"],
      "preview_hide_label" => $lang["nav.HidePreview"],
      "save_draft_label" => $lang["thread.PostSaveDraftBtn"],
      "maxlength" => $config["maxCharsPerPost"],
    );
    if (isset($contentSave)) $replybox_data["content"] = htmlspecialchars($contentSave);
    elseif (isset($draftPost)) $replybox_data["content"] = htmlspecialchars($draftPost);
    if (isset($draftPost)) {
        $replybox_data["discard"] = '<input type="submit" class="buttonbig buttonNo" name="discardDraft" value="'.$lang["thread.PostDiscardDraftBtn"].'">';
    }
    $thread_data["reply"] = $template->render("templates/thread/reply_box.html",$replybox_data);
}
else if (get_role_from_session() == "Guest") {
    $thread_data["reply"] = message(sprintf($lang["thread.LoginToReply"], genURL('login/'), genURL('signup/')),true);
}
else if (!(get_role_permissions() & PERM_CREATE_POST)) {
    $thread_data["reply"] = message($lang["thread.SuspendCantPost"],true);
}

//
// Define all the functions we need to handle user requests.
//

function postReply() {
    global $db, $config, $lang, $q2, $pages, $thread_data;
    $delaycheck = $db->query("SELECT 1 FROM posts WHERE user='" . $_SESSION["userid"] . "' AND timestamp>'" . (time() - $config["postDelay"]) . "'");
            
    if ($delaycheck->num_rows > 0) {
        $thread_data["error"] .= message(sprintf($lang["thread.PostSoon"], $config["postDelay"]),true);
        return $_POST["content"];
    }
    if (mb_strlen($_POST["content"]) < 1) {
        $thread_data["error"] .= message($lang["thread.PostEmpty"],true);
        return $_POST["content"];
    }        
    if (mb_strlen($_POST["content"]) > $config["maxCharsPerPost"]) {
        $thread_data["error"] .= message(sprintf($lang["thread.PostBig"], $config["maxCharsPerPost"]),true);
        return $_POST["content"];
    }
        
    $result = $db->query("INSERT INTO posts (thread, user, timestamp, content) VALUES ('" . $db->real_escape_string($q2) . "', '" . $_SESSION["userid"] . "', '" . time() . "', '" . $db->real_escape_string($_POST["content"]) . "')");
    if (!$result) {
        $thread_data["error"] .= message($lang["error.Database"],true);
        return $_POST["content"];
    }
    $id = $db->query("SELECT MAX(postid) FROM posts");
    $row = $id->fetch_assoc();
    $update = $db->query("UPDATE threads SET posts=posts+1, lastpostuser='" . $_SESSION["userid"] . "', lastposttime='" . time() . "' WHERE threadid='" . $db->real_escape_string($q2) . "'");
    $db->query("DELETE FROM drafts WHERE user='" . $_SESSION["userid"] . "' AND thread='" . $db->real_escape_string($q2) . "'");
    redirect("post/" . $row["MAX(postid)"]);
}

function saveDraft() {
    global $db, $lang, $config, $q2, $pages, $thread_data;
    $draftCheck = $db->query("SELECT 1 FROM drafts WHERE user='" . $_SESSION["userid"] . "' AND timestamp>'" . (time() - 60) . "'");
    if ($draftCheck->num_rows >= $config["draftsPerMinute"]) {
        $thread_data["error"] .= message($lang["thread.DraftError"],true);
        return;
    }
    if (mb_strlen($_POST["content"]) < 1) {
        $thread_data["error"] .= message($lang["thread.PostEmpty"],true);
        return;
    }
    else if (mb_strlen($_POST["content"]) > $config["maxCharsPerPost"]) {
        $thread_data["error"] .= message(sprintf($lang["thread.PostBig"], $config["maxCharsPerPost"]),true);
        return;
    }
    $result = $db->query("INSERT INTO drafts (user, thread, timestamp, content) VALUES ('" . $_SESSION["userid"] . "', '" . $db->real_escape_string($q2) . "', '" . time() . "', '" . $db->real_escape_string($_POST["content"]) . "')");
    if (!$result) {
        $thread_data["error"] .= message($lang["error.Database"],true);
        return;
    }
    redirect("thread/" . $q2 . "/" . $pages . "/#footer");
}

function deletePost() {
    global $db, $lang, $q2, $categoryID, $thread_data, $viewerid;
    $permission = $db->query("SELECT user FROM posts WHERE postid='" . $db->real_escape_string($_POST["delete"]) . "'");
    $p = $permission->fetch_assoc();
    
    if (!($p["user"] == $viewerid && get_role_permissions() & PERM_CREATE_POST) && !(get_role_permissions() & PERM_EDIT_POST)) {
        // ..
        return;
    }
    
    $post = $db->query("SELECT * FROM posts WHERE postid='" . $db->real_escape_string($_POST["delete"]) . "'");
    $p = $post->fetch_assoc();
    $content = $p["content"];
    $user = $p["user"];

    $result = $db->query("INSERT INTO auditlog (`time`, `action`, `userid`, `victimid`, `before`) VALUES ('" . time() . "', 'delete_post', '" . $_SESSION["userid"] . "', '" . $user . "', '" . $db->real_escape_string($content) . "')");
    $result = $db->query("DELETE FROM posts WHERE postid='" . $db->real_escape_string($_POST["delete"]) . "'");
    if (!$result) {
        $thread_data["error"] .= message($lang["error.Database"],true);
        return;
    }
    $postCheck = $db->query("SELECT * FROM posts WHERE thread='" . $db->real_escape_string($q2) . "' ORDER BY timestamp DESC LIMIT 1");

    if (!$postCheck or $postCheck->num_rows == 0) {
        deleteThread();
        return;
    }
    $lastpost = $db->query("SELECT * FROM posts WHERE thread='" . $db->real_escape_string($q2) . "' ORDER BY timestamp DESC LIMIT 1");
    if (!$lastpost or $lastpost->num_rows == 0) {
         $thread_data["error"] .= message($lang["error.Database"],true);
         return;
    }
    $row = $lastpost->fetch_assoc();
    $update = $db->query("UPDATE threads SET posts=posts-1, lastpostuser='" . $row["user"] . "', lastposttime='" . $row["timestamp"] . "' WHERE threadid='" . $db->real_escape_string($q2) . "'");
    refresh(0);
}

function hidePost() {
    global $db, $lang, $thread_data, $viewerid;
    $permission = $db->query("SELECT user FROM posts WHERE postid='" . $db->real_escape_string($_POST["hide"]) . "'");
    $p = $permission->fetch_assoc();
    if (!($p["user"] == $viewerid && get_role_permissions() & PERM_CREATE_POST) && !(get_role_permissions() & PERM_EDIT_POST)) {
        // ..
        return;
    }
    $result = $db->query("UPDATE posts SET deletedby='" . $_SESSION["userid"] . "' WHERE postid='" . $db->real_escape_string($_POST["hide"]) . "'");
    if (!$result) {
        $thread_data["error"] .= message($lang["error.Database"],true);
        return;
    }
    $result = $db->query("INSERT INTO auditlog (`time`, `action`, `userid`, `victimid`, `before`, `after`) 
            VALUES ('" . time() . "', 'hide_post', '" . $_SESSION["userid"] . "', '" . $db->real_escape_string($_POST["hide"]) . "',
            'restored', 'hidden')");
    redirect("post/" . $db->real_escape_string($_POST["hide"]));
}

function restorePost() {
    global $db, $lang, $viewerid;
    $permission = $db->query("SELECT user FROM posts WHERE postid='" . $db->real_escape_string($_POST["restore"]) . "'");
    $p = $permission->fetch_assoc();
    if (!($p["user"] == $viewerid && get_role_permissions() & PERM_CREATE_POST) && !(get_role_permissions() & PERM_EDIT_POST)) {
        // ..
        return;
    }
    $result = $db->query("UPDATE posts SET deletedby=NULL WHERE postid='" . $db->real_escape_string($_POST["restore"]) . "'");
    if (!$result) {
        $thread_data["error"] .= message($lang["error.Database"],true);
        return;
    }
    $result = $db->query("INSERT INTO auditlog (`time`, `action`, `userid`, `victimid`, `before`, `after`) 
            VALUES ('" . time() . "', 'hide_post', '" . $_SESSION["userid"] . "', '" . $db->real_escape_string($_POST["restore"]) . "',
            'hidden', 'restored')");            
    redirect("post/" . $db->real_escape_string($_POST["restore"]));
}

function saveEdit() {
    global $db, $lang, $thread_data, $config, $viewerid;
    $permission = $db->query("SELECT user FROM posts WHERE postid='" . $db->real_escape_string($_POST["saveeditpostid"]) . "'");
    $p = $permission->fetch_assoc();
    if (!($p["user"] == $viewerid && get_role_permissions() & PERM_CREATE_POST) && !(get_role_permissions() & PERM_EDIT_POST)) {
        $thread_data["error"] .= message($lang["thread.PostEditError"],true);
        return;
    }
    if (mb_strlen($_POST["saveedit"]) < 1) {
        $thread_data["error"] .= message($lang["thread.PostEmpty"],true);
        return;
    }
    if (mb_strlen($_POST["saveedit"]) > $config["maxCharsPerPost"]) {
        $thread_data["error"] .= message(sprintf($lang["thread.PostBig"], $config["maxCharsPerPost"]),true);
        return;
    }
    $post = $db->query("SELECT content FROM posts WHERE postid='" . $db->real_escape_string($_POST["saveeditpostid"]) . "'");
    $p = $post->fetch_assoc();
    $result = $db->query("INSERT INTO auditlog (`time`, `action`, `userid`, `victimid`, `before`, `after`) 
            VALUES ('" . time() . "', 'edit_post', '" . $_SESSION["userid"] . "', '" . $db->real_escape_string($_POST["saveeditpostid"]) . "',
            '" . $db->real_escape_string($p["content"])  . "', '" . $db->real_escape_string($_POST["saveedit"]) . "')");
    $result = $db->query("UPDATE posts SET content='" . $db->real_escape_string($_POST["saveedit"]) . "' WHERE postid='" . $db->real_escape_string($_POST["saveeditpostid"]) . "'");
    if (!$result) {
        $thread_data["error"] .= message($lang["error.Database"],true);
        return;
    }
    redirect("post/" . $db->real_escape_string($_POST["saveeditpostid"]));
}

function deleteThread() {
    global $db, $lang, $q2, $categoryID, $thread_data;
    $post = $db->query("SELECT * FROM threads WHERE threadid='" . $db->real_escape_string($q2) . "'");
    $p = $post->fetch_assoc();
    $result = $db->query("DELETE FROM threads WHERE threadid='" . $db->real_escape_string($q2) . "'");
     
    if (!$result) {
        $thread_data["error"] .= message($lang["error.Database"],true);
        return;
    }
    $result = $db->query("INSERT INTO auditlog (`time`, `action`, `userid`, `victimid`, `before`) 
    VALUES ('" . time() . "', 'delete_thread', '" . $_SESSION["userid"] . "', '" . $db->real_escape_string($p["startuser"]) . "',
    '" . $db->real_escape_string($p["title"])  . "')");
    $result = $db->query("DELETE FROM posts WHERE thread='" . $db->real_escape_string($q2) . "'");
    if (!$result) {
        $thread_data["error"] .= message($lang["error.Database"],true);
        return;
    }
    redirect("category/" . $categoryID . "/");
}

function moveThread($new_categoryID, $thread) {
    global $db, $q2, $thread_data;
    if (!is_numeric($new_categoryID) || !is_numeric($thread)) {
        $thread_data["error"] .= message($lang["error.Database"],true);
        return;
    }
    $categoryCheck = $db->query("SELECT * FROM categories where categoryid='" . $db->real_escape_string($new_categoryID) . "'");
    if ($categoryCheck->num_rows < 1) {
        $thread_data["error"] .= message($lang["error.Database"],true);
        return;
    }
    $db->query("UPDATE threads set category='" . $db->real_escape_string($new_categoryID) . "' WHERE threadid='" . $db->real_escape_string($q2) . "'");
    $result = $db->query("INSERT INTO auditlog (`time`, `action`, `userid`, `victimid`, `before`, `after`) 
    VALUES ('" . time() . "', 'move_thread', '" . $_SESSION["userid"] . "', '" . $db->real_escape_string($thread) . "',
    '" . $db->real_escape_string($categoryID)  . "','" . $db->real_escape_string($new_categoryID) . "')");
    refresh(0);
}

function stickyThread($sticky) {
    global $db, $lang, $q2, $thread_data;
    $result = $db->query("UPDATE threads SET sticky='" . intval($sticky) . "' WHERE threadid='" . $db->real_escape_string($q2) . "'");
    if (!$result) {
        $thread_data["errors"] .= message($lang["error.Database"],true);
        return;
    }
    refresh(0);
}
function lockThread($locked) {
    global $db, $lang, $q2, $thread_data;
    $result = $db->query("UPDATE threads SET locked='" . intval($locked) . "' WHERE threadid='" . $db->real_escape_string($q2) . "'");
    if (!$result) {
        $thread_data["errors"] .= message($lang["error.Database"],true);
        return;
    }
    refresh(0);
}
function pinThread($pinned) {
    global $db, $lang, $q2, $thread_data;
    $result = $db->query("UPDATE threads SET pinned='" . intval($pinned) . "' WHERE threadid='" . $db->real_escape_string($q2) . "'");
    if (!$result) {
        $thread_data["errors"] .= message($lang["error.Database"],true);
        return;
    }
    refresh(0);
}

function editTitle() {
    global $db, $lang, $q2, $startuser, $viewerid, $config, $thread_data;
    if (!($startuser == $viewerid && get_role_permissions() & PERM_CREATE_THREAD) && !(get_role_permissions() & PERM_EDIT_THREAD)) {
        $thread_data["errors"] .= message($lang["thread.PostEditError"],true);
        return;
    }
    if (mb_strlen($_POST["editthread"]) < 1) {
        $thread_data["error"] .= message($lang["thread.PostEmpty"],true);
        return;
    }
    else if (mb_strlen($_POST["editthread"]) > $config["maxCharsPerTitle"]) {
        $thread_data["error"] .= message(sprintf($lang["thread.PostBig"], $config["maxCharsPerTitle"]),true);
        return;
    }
    $result = $db->query("UPDATE threads SET title='".$db->real_escape_string($_POST["editthread"])."' WHERE threadid='".$db->real_escape_string($q2)."'");   
    if (!$result)
    {
        $thread_data["errors"] .= message($lang["error.Database"],true);
        return;
    }
    refresh(0);
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (get_role_from_session() == "Guest") {
        include 'header.php';
        message($lang["thread.LoginFirst"]);
        include "footer.php";
        exit();
    }
    if ($draft == true) {
        if (isset($_POST["publishDraft"]) && $viewerid == $author["userid"]) {
            $db->query("UPDATE threads SET draft='0' WHERE threadid='" . $db->real_escape_string($q2) . "'");
            refresh(0);
        }
    }
    else {
        if (isset($_POST["postReply"]) && isset($_POST["content"]) && 
        ($locked == false || get_role_permissions() & PERM_IGNORE_THREAD_LABELS) && get_role_permissions() & PERM_CREATE_POST) {
            postReply();
        }
        else if (isset($_POST["saveDraft"]) && isset($_POST["content"]) && 
        ($locked == false || get_role_permissions() & PERM_IGNORE_THREAD_LABELS) && get_role_permissions() & PERM_CREATE_POST) {
            saveDraft();
        }
        else if (isset($_POST["discardDraft"])) {
            $db->query("DELETE FROM drafts WHERE user='" . $_SESSION["userid"] . "' AND thread='" . $db->real_escape_string($q2) . "'");
        }
        else if (isset($_POST["delete"])) deletePost();
        else if (isset($_POST["hide"])) hidePost();
        else if (isset($_POST["restore"])) restorePost();
        else if (isset($_POST["saveedit"])) saveEdit();  
        else if (isset($_POST["editthread"])) editTitle();
        if (get_role_permissions() & PERM_EDIT_THREAD) 
        {
            if (isset($_POST["movethread"]) && isset($_POST["category"])) {
                moveThread($_POST["category"], $_POST["movethread"]);
            }
            else if (isset($_POST["lockthread"])) lockThread(true);
            else if (isset($_POST["unlockthread"])) lockThread(false);
            else if (isset($_POST["stickythread"])) stickyThread(true);
            else if (isset($_POST["unstickythread"])) stickyThread(false);
            else if (isset($_POST["deletethread"])) deleteThread(false);
        }
        if (get_role_permissions() & PERM_EDIT_FORUM)
        {
            if (isset($_POST["pinthread"])) pinThread(true);
            else if (isset($_POST["unpinthread"])) pinThread(false);
        }
    }
}

include 'header.php';
echo ("<script type='text/javascript' src='" . genURL("assets/thread.js") . "'></script>");

echo $template->render("templates/thread/thread.html",$thread_data);

include 'footer.php';

if (get_role_from_session() != "Guest")
{
    $action = $lang["action.Generic"] . ' <a href="' . genURL('thread/' . $db->real_escape_string($q2)) . '/">' . htmlspecialchars($title) . '</a>';
    update_last_action($action);
}

?>
