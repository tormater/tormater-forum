<?php
// user.page.php
// Displays a given user's profile.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

// Start off by making a query using the given userid.
$result = $db->query("SELECT * FROM users WHERE userid='" . $db->real_escape_string($q2) . "'");

if (!$result)
{
    include "header.php";
    message($lang["user.FaildFindUser"]);
    include "footer.php";
    exit;
}

else
{
    
    if ($result->num_rows == 0)
    {
        include "header.php";
        message($lang["user.NoSuchUser"]);
        include "footer.php";
        exit;
    }
    
    else
    {
        while ($row = $result->fetch_assoc())
        {
            if ($row["deleted"] == "1") $username = $lang["user.Deleted"] . $row["userid"];
            else $username = $row["username"];
            
            $userid = $row["userid"];
            $color = $row["color"];
            $role = $row["role"];
            $verified = $row["verified"];
            $lastactive = $row["lastactive"];
            $jointime = $row["jointime"];
            $deleted = $row["deleted"];
            $avatar = $row["avatar"];
            $avatarTime = $row["avataruploadtime"];
        }
        
        if ((isset($_POST["role"])) and (($_SESSION["role"] == "Administrator") or ($_SESSION["role"] == "Moderator")))
        {
            if (is_numeric($q2) and ($_SESSION["userid"] != $q2) and ($config["mainAdmin"] != $q2)) {
                if ($_SESSION["role"] == "Administrator" || ($_SESSION["role"] == "Moderator" and ($_POST["role"] == "Member" or $_POST["role"] == "Suspended") and ($role == "Member" or $role == "Suspended"))) {
                    $setrole = $db->query("UPDATE users SET role='" . $db->real_escape_string($_POST["role"]) . "' WHERE userid='" . $db->real_escape_string($q2) . "'");
            
                    if (!$setrole)
                    {
                        echo $lang["user.FaildChangeRole"];
                    }
                    else
                    {
                        $db->query("INSERT INTO auditlog (`time`, `action`, `userid`, `victimid`, `before`, `after`) 
            VALUES ('" . time() . "', 'edit_role', '" . $_SESSION["userid"] . "', '" . $db->real_escape_string($q2) . "', '" . $role . "', '" . $db->real_escape_string($_POST["role"]) ."')");
                    }
                                
                    refresh(0);
                }
            }
        }
        elseif ((isset($_POST["removeAvatar"])) and (($_SESSION["role"] == "Administrator") or ($_SESSION["role"] == "Moderator")))
        {
            removeAvatar($q2);
            $db->query("INSERT INTO auditlog (`time`, `action`, `userid`, `victimid`) 
            VALUES ('" . time() . "', 'delete_avatar', '" . $_SESSION["userid"] . "', '" . $db->real_escape_string($q2) . "')");
            refresh(0);
        }

        include "header.php";
        
        // Get the profile's status
    if ($verified == "1") $verified = $lang["user.VerifiedYes"];
    else $verified = $lang["user.VerifiedNo"];

    $delClass = "";
        
    if ($deleted == "1") 
        {
            $deleted = $lang["user.DeletedYes"];
            $delClass = " deleteduser";
        }

    else $deleted = $lang["user.DeletedNo"];
        
    if ($avatar == "none") $uAvatar = "";
        else $uAvatar = '<img class="avatar" src="' . genURL("avatars/" . $userid . "." . $avatar . "?t=" . $avatarTime) . '">';
            
        echo '<h2>'.$lang["user.ViewingProfile"].' "' . htmlspecialchars($username) . '"</h2>';
        echo '<div class="post' . $delClass . '"><div class="usertop" postcolor="' . htmlspecialchars($color) . '">';    

        drawUserProfile($userid, 1);

        if (($avatar != "none") and (($_SESSION["role"] == "Moderator") or ($_SESSION["role"] == "Administrator"))) {
            echo "<form method='post' action=''><button name='removeAvatar'>" . $lang["userpanel.RemoveAvatar"] . "</button></form>";
        }
        
        if ($config["mainAdmin"] != $userid and (isset($_SESSION["role"]) && $_SESSION["role"] == "Administrator")) {
            echo '<a class="buttonsmall" href="' . genURL("panel/useradmin/" . $userid) . '">' . $lang["panel.Administrate"] . '</a>';
        }

        echo "</div></div>";
        
        // Get user statistics

        $posts = $db->query("SELECT 1 FROM posts WHERE user='" . $db->real_escape_string($q2) . "'");
        $uposts = mysqli_num_rows($posts);
                
        $threads = $db->query("SELECT 1 FROM threads WHERE startuser='" . $db->real_escape_string($q2) . "'");
        $uthreads = mysqli_num_rows($threads);

        echo '<div class="userbottom">
                    <h3>' . $lang["user.UserInformation"] . '</h3>
            <span class="userstat"><label class="shortlabel">'.$lang["user.TitleRegistered"].'</label><a title="' . date('m-d-Y h:i:s A', $jointime) . '">' . relativeTime($jointime) . '</a></span>
            <span class="userstat"><label class="shortlabel">'.$lang["user.TitleLastActive"].'</label><a title="' . date('m-d-Y h:i:s A', $lastactive) . '">' . relativeTime($lastactive) . '</a></span>
            <span class="userstat"><label class="shortlabel">'.$lang["user.TitlePosts"].'<a href="'.genURL("search?user=" . urlencode($username)).'"></label>' . $uposts . '</a></span>
            <span class="userstat"><label class="shortlabel">'.$lang["user.TitleThreads"].'</label><a href="'.genURL("search?author=" . urlencode($username)).'">' . $uthreads . '</a></span>
            <span class="userstat"><label class="shortlabel">'.$lang["user.TitleVerified"].'</label>' . $verified . '</span>';
    }

echo '</div></div>';

echo '<div class="userextra' . $delClass . '" postcolor="' . htmlspecialchars($color) . '">';

echo "<div class=userbioside>";

$bioCheck = $db->query("SELECT bio FROM users WHERE userid='" . $db->real_escape_string($q2) . "'");
        
while ($b = $bioCheck->fetch_assoc()) {
    if ((!!$b["bio"]) or (isset($b["bio"])) or ($b["bio"] != "")) 
    {
        echo '<span class="userpostsh">' . $lang["userpanel.Bio"] . '</span>';
        echo("<span class='userbio'>" . formatPost($b["bio"]) . "</span>");
    }
}
echo "</div>";
echo '<div class="userposts">';
echo '<span class="userpostsh">' . $lang["user.RecentPosts"] . '</span>';

// Get the user's last 5 recent posts, excluding any draft posts.
$postspre = $db->query("SELECT * FROM posts WHERE user='" . $db->real_escape_string($q2) . "' AND deletedby IS NULL");

$exclude = "";

while ($p = $postspre->fetch_assoc()) {
    $threads = $db->query("SELECT threadid FROM threads WHERE threadid='" . $db->real_escape_string($p["thread"]) . "' AND draft='1' AND startuser='" . $db->real_escape_string($q2) . "'");

    while ($t = $threads->fetch_assoc()) {
        $exclude .= " AND thread<>" . $t["threadid"];
    }
}

$posts = $db->query("SELECT * FROM posts WHERE user='" . $db->real_escape_string($q2) . "' AND deletedby IS NULL" . $exclude . " ORDER BY timestamp DESC LIMIT 5");

if($posts->num_rows == 0)
{
    message($lang["thread.NoPosts"]);
}
    
else
{
    while($row = $posts->fetch_assoc())
    {
        $userinfo = $db->query("SELECT * FROM users WHERE userid='" . $row["user"] . "'");
            
        while($u = $userinfo->fetch_assoc())
        {
            echo '<div class="userpost">' . formatPost($row["content"]) . '</div>';
            echo "<span class='postdate' title='" . date('m-d-Y h:i:s A', $row["timestamp"]) . "'>" . relativeTime($row["timestamp"]) . "</span> - <a class='userpostlink' href='" . genURL("thread/" . $row["thread"]) . "'>" . $lang["user.ViewThread"] . "</a>";
        }
    }
}
echo "</div></div>";
}

// If the viewing user is logged in, update their last action.
if (isset($_SESSION['signed_in']) && ($_SESSION['signed_in'] == true))
{
    $action = $lang["action.Generic"]. '<a href="' . genURL('user/' . $userid) . '/">' . htmlspecialchars($username) . $lang["action.UserProfile"] . '</a>';
    update_last_action($action);
}

include "footer.php";

?>
