<?php
// permission.php
// Defines all the constants and handles permission checks.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

define('PERM_EDIT_POST', 1);
define('PERM_EDIT_THREAD', 2);
define('PERM_EDIT_USER', 4);
define('PERM_EDIT_CATEGORY', 8);
define('PERM_EDIT_EXTENSIONS', 16);
define('PERM_EDIT_FORUM', 32);
define('PERM_VIEW_AUDIT', 64);
define('PERM_CREATE_THREAD', 128);
define('PERM_CREATE_POST', 256);
define('PERM_VIEW_USERLIST', 512);

$permissions = array(
    "Administrator" => PERM_EDIT_POST|PERM_EDIT_THREAD|PERM_EDIT_USER|PERM_EDIT_CATEGORY|PERM_EDIT_FORUM|PERM_VIEW_AUDIT|PERM_CREATE_THREAD|PERM_CREATE_POST|PERM_VIEW_USERLIST,
    "Moderator" => PERM_EDIT_POST|PERM_EDIT_THREAD|PERM_EDIT_USER|PERM_VIEW_AUDIT|PERM_CREATE_THREAD|PERM_CREATE_POST|PERM_VIEW_USERLIST,
    "Member" => PERM_CREATE_THREAD|PERM_CREATE_POST|PERM_VIEW_USERLIST,
    "Guest" => PERM_VIEW_USERLIST,
);

if ($config["userlistMembersOnly"]) {
    $permissions["Guest"] ^= PERM_VIEW_USERLIST;
}

listener("afterDefaultPerms");

function get_role_permissions() {   
    global $permissions;
    
    if (isset($_SESSION["role"])) $role = $_SESSION["role"];
    else if (isset($_SESSION['signed_in']) && $_SESSION['signed_in']) $role = "Member";
    else $role = "Guest";
    
    return $permissions[$role];
}
