<?php
// newthread.page.php
// Creates a new thread.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if (get_role_from_session() == "Guest") {
    include 'header.php';
    message(sprintf($lang["newthread.LoginToCreate"], genURL("login")));
    include "footer.php";
    exit;
}

if (!(get_role_permissions() & PERM_CREATE_THREAD)) {
     include 'header.php';
     message($lang["newthread.SuspendCantCreate"]);
     include "footer.php";
     exit;
}
    
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $cat = $db->query("SELECT 1 FROM categories WHERE categoryid='" . $db->real_escape_string($_POST["category"]) . "'");
		
    if (mb_strlen(trim($_POST["title"])) < 1) {
        $error = message($lang["newthread.TitleEmpty"],true); goto err;
    }
    if (mb_strlen(trim($_POST["title"])) > $config["maxCharsPerTitle"]) {
        $error = message(sprintf($lang["newthread.TitleBig1"], $config["maxCharsPerTitle"]),true); goto err;
    }
    if (!mb_strlen($_POST["content"])) {
        $error = message($lang["newthread.PostEmpty"],true); goto err;
    }
    if (mb_strlen($_POST["content"]) > $config["maxCharsPerPost"]) {
        $error = message(sprintf($lang["newthread.PostBig1"], $config["maxCharsPerPost"]),true); goto err;
    }
    if ((!$cat) or ($cat->num_rows < 1)) {
        $error = message($lang["newthread.InvalidCategory"],true); goto err;
    }
    $delaycheck = $db->query("SELECT 1 FROM posts WHERE user='" . $_SESSION["userid"] . "' AND timestamp>'" . (time() - $config["postDelay"]) . "'");
    if ($delaycheck->num_rows > 0) {
        $error = message(sprintf($lang["newthread.PostSoon1"], $config["postDelay"])); goto err;
    }
    $beginwork = $db->query("BEGIN WORK");
    if (!$beginwork) {
        $error = message($lang["newthread.CreateError"],true); goto err;
    }
    $justnow = time();
    $userid = $_SESSION["userid"];
    $draft = (int)isset($_POST["saveDraft"]);
    
    $threadresult = $db->query("INSERT INTO threads (title, sticky, locked, posts, startuser, starttime, lastpostuser, lastposttime, category, draft) VALUES ('" . $db->real_escape_string(trim($_POST["title"])) . "', '0', '0', '1', '$userid', '$justnow', '$userid', '$justnow', '" . $db->real_escape_string($_POST["category"]) . "', '" . $db->real_escape_string($draft) . "')");
			
    if (!$threadresult) {
        $error = message($lang["newthread.InsertThreadError"],true);
        $db->query("ROLLBACK"); goto err;
    }
    $threadid = $db->insert_id;
    $result = $db->query("INSERT INTO posts (thread, user, timestamp, content) VALUES ('$threadid', '$userid', '$justnow', '" . $db->real_escape_string($_POST["content"]) . "')");
    
    if (!$result) {
        $error = message($lang["newthread.InsertPostError"],true);
        $db->query("ROLLBACK"); goto err;
    }
    $db->query("COMMIT");
    redirect("thread/" . $threadid);
    include 'header.php';
    message($lang["newthread.SuccessCreate1"] . ' <a href="' . genURL('thread/' . $threadid) . '/">' . $lang["newthread.SuccessCreate2"] . '</a>');
    include("footer.php");
    exit;
}

err:

include 'header.php';

$data = array(
    "new_thread" => $lang["newthread.Header"],
    "title_label" => $lang["newthread.Title"],
    "title_maxlength" => $config["maxCharsPerTitle"],
    "title_save" => "",
    "category_label" => $lang["newthread.Category"],
    "categories" => "",
    "bbcode_tray" => BBCodeButtons(1,false),
    "content_save" => "",
    "create_label" => $lang["newthread.CreateBtn"],
    "show_preview" => $lang["nav.ShowPreview"],
    "hide_preview" => $lang["nav.HidePreview"],
    "save_draft" => $lang["thread.PostSaveDraftBtn"]
);

if (isset($error)) {
    $catSave = $_POST["category"];
    $data["title_save"] = "value='" . trim(htmlspecialchars($_POST["title"])) . "'";
    $data["content_save"] = htmlspecialchars($_POST["content"]);
    echo $error;
}

$result = $db->query("SELECT * FROM categories");
if (!$result) {
    message($lang["newthread.DataError"]);
    include 'footer.php';
    exit;
}
if (!$result->num_rows) {
    if (get_role_permissions() & PERM_EDIT_CATEGORY) message($lang["newthread.NoCategoryAdmin"]);
    else message($lang["newthread.NoCategoryUser"]);
    include 'footer.php';
    exit;
}
while ($row = $result->fetch_assoc()) {
    $data["categories"] .= '<option ';
    if (isset($catSave) && $catSave == $row["categoryid"]) $data["categories"] .= "selected ";
    else if (isset($q2) && $q2 == $row["categoryid"]) $data["categories"] .= "selected ";
    $data["categories"] .= 'value="' . $row['categoryid'] . '">' . htmlspecialchars($row['categoryname']) . '</option>';
}

echo ("<script type='text/javascript' src='" . genURL("assets/thread.js") . "'></script>");

echo $template->render("templates/newthread.html",$data);

include 'footer.php';

if (get_role_from_session() != "Guest")
{
    update_last_action("action.CreateAThread");
}

?>
