<?php
// userlist.page.php
// Displays a list of all the users on the forum.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$sortoptions = array(
  "time" => "userid",
  "alphabet" => "username",
  "role" => "role",
  "activity" => "lastactive"
);

$sortorderoptions = array(
  "asc" => "ASC",
  "desc" => "DESC"
);

listener("beforeUserlistLoad");

// Handle requests to approve accounts.
if ((isset($_POST["approve"])) and (is_numeric($_POST["approve"])) and (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator"))) {
    $check = $db->query("SELECT 1 FROM users WHERE userid='" . $db->real_escape_string($_POST["approve"]) . "'");
    
    if ($check->num_rows > 0) {
        $db->query("UPDATE users SET verified='1' WHERE userid='" . $db->real_escape_string($_POST["approve"]) . "'");
    }
}

$currentPage = 1;
if (isset($q2) && is_numeric($q2)) $currentPage = $q2;

$user_count = $db->query("SELECT 1 FROM users");
$numUsers = $user_count->num_rows;

if (!$numUsers) {
    include "header.php";
    echo $lang["error.NoUsers"];
    require "pages/footer.php";
    exit;
}

$pages = ceil($numUsers / $config["usersPerPage"]);
if ($currentPage > $pages) $currentPage = $pages;
if ($currentPage < 1) $currentPage = 1;

// Calculate the offset for the threads query.
$offset = (($currentPage * $config["usersPerPage"]) - $config["usersPerPage"]);

if ($config["userlistEnabled"] == false) {
    include "header.php";
    message($lang["error.UserlistDisabled"]);
    require "pages/footer.php";
    exit;
}
if (!(get_role_permissions() & PERM_VIEW_USERLIST)) {
    include "header.php";
    message(sprintf($lang["error.UserlistMembersOnly"], genURL("signup"), genURL("login")));
    require "pages/footer.php";
    exit;
}

include "header.php";

$sort = $sortoptions["time"];
$order = $sortorderoptions["asc"];
if (array_key_exists("sort_by",$_GET) && array_key_exists($_GET["sort_by"],$sortoptions)) $sort = $sortoptions[$_GET["sort_by"]];
if (array_key_exists("sort_order",$_GET) && array_key_exists($_GET["sort_order"],$sortorderoptions)) $order = $sortorderoptions[$_GET["sort_order"]];
if ($order == $sortorderoptions["asc"] && $sort == $sortoptions["activity"]) $order = $sortorderoptions["desc"];
else if ($order == $sortorderoptions["desc"] && $sort == $sortoptions["activity"]) $order = $sortorderoptions["asc"];
$result = $db->query("SELECT * FROM users ORDER BY ".$sort." ".$order." LIMIT " . $config["usersPerPage"] . " OFFSET " . $offset . "");

if (!$result) {
    echo $lang["error.FailedFetchUsers"];
}
else {	
    $sort_data = array(
      "label" => $lang["userlist.SortBy"],
      "sort_by_options" => "",
      "sort_order_options" => "",
      "label_submit" => $lang["userlist.Submit"]
    );
    
    foreach ($sortoptions as $s => $v) {
        if (isset($_GET["sort_by"]) && $_GET["sort_by"] == $s) $selected = "selected=''";
        else $selected = "";
        
        $sort_data["sort_by_options"] .= '<option ' . $selected . 'value="' . $s . '">' . $lang["userlist.sort.".$s] . '</option>';
    }
    foreach ($sortorderoptions as $s => $v) {
        if (isset($_GET["sort_order"]) && $_GET["sort_order"] == $s) $selected = "selected=''";
        else $selected = "";
        
        $sort_data["sort_order_options"] .= '<option ' . $selected . 'value="' . $s . '">' . $lang["userlist.sort_order.".$s] . '</option>';
    }
    
    $data = array(
      "title" => $lang["header.Userlist"],
      "pagination" => renderPagination(2, true),
      "users" => "",
      "sort_options" => $template->render("templates/userlist/sort_options.html",$sort_data)
    );
        
    while($row = $result->fetch_assoc())
    {
        if ($row["deleted"] == "1" && $config["showDeletedInUserlist"] != 1) continue;
        $user_data = array(
          "deleted_class" => "",
          "color" => $row["color"],
          "url" => genURL('user/' . $row["userid"]),
          "role_class" => $row["role"],
          "username" => $row["username"],
          "role" => $lang["role.".$row["role"]],
          "buttons" => "",
          "action" => parseAction($row["lastaction"], $lang),
          "time" => date('m-d-Y h:i:s A', $row["lastactive"]),
          "relative_time" => relativeTime($row["lastactive"])
        );
            
        if ($row["deleted"] == "1") {
            $user_data["deleted_class"] = " deleteduser";
	    $user_data["username"] = $lang["user.Deleted"] . $row["userid"];
	}

        if (($row["verified"] == "0") and (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator"))) {
            $user_data["buttons"] = $template->render("templates/userlist/user_button.html",array("userid" => $row["userid"], "label" => $lang["userlist.Approve"]));
        }
        else if ($config["mainAdmin"] != $row["userid"] and (isset($_SESSION["role"]) && $_SESSION["role"] == "Administrator")) {
            $user_data["buttons"] = '<small><a href="' . genURL("panel/useradmin/" . $row["userid"]) . '">' . $lang["panel.Administrate"] . '</a></small>';
        }

        $data["users"] .= $template->render("templates/userlist/user_display.html",$user_data);
    }
    
    echo $template->render("templates/userlist/userlist.html", $data);
}

include "footer.php";

// If the viewing user is logged in, update their last action.
if (isset($_SESSION['signed_in']) && ($_SESSION['signed_in'] == true))
{
	update_last_action($lang["action.Userlist"]);
}

?>
