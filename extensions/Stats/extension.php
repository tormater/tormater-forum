<?php
if (!defined("INDEXED")) exit;

$stats_CustomLang = array (
    "stats.Threads" => " threads • ",
    "stats.Posts" => " posts • ",
    "stats.Users" => " users",
);

function customLang($val) {
    global $lang, $stats_CustomLang;

    if (!in_array($val, $lang)) {
        return $stats_CustomLang[$val];
    }
    else {
        return $lang[$val];
    }
}

// Define the functions
$start_time = microtime(true);

function Stats_displayPageLoadTime() {
    global $start_time, $db;

    // Get counts of threads, users, and posts
    $threads = $db->query("SELECT * FROM threads");
    $threadcount = $threads->num_rows;
    $users = $db->query("SELECT * FROM users");
    $usercount = $users->num_rows;
    $posts = $db->query("SELECT * FROM posts");
    $postcount = $posts->num_rows;
    
    // Display all the information
    print '<br/>';
    print '<div class="statsLeft">This page was generated in ' . number_format(microtime(true) - $start_time, 4) . ' microseconds.</div>';
    print '<div class="statsRight">' . number_format($threadcount) .  customLang("stats.Threads")  . number_format($postcount) . customLang("stats.Posts") . number_format($usercount) . customLang("stats.Users") . '</div>';
}

function Stats_addStyles() {
    echo '<link rel="stylesheet" href="' . genURL("extensions/" . basename(__DIR__) . "/style.css?v=0.1") . '" type="text/css">';
}

// Hook the functions
hook("meta", "Stats_addStyles");
hook("afterFooter", "Stats_displayPageLoadTime");
?>
