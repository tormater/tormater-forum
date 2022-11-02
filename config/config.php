<?php
// config.php
// Contains all the config options essential for the forum to function properly.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$config = array(
    "installed" => "no", // Whether or not the forum is installed. (yes/no)
    "MySQLServer" => "", // URL for your MySQL server. Set to localhost if it's on the same machine.
    "MySQLUser" => "", // Username for native MySQL user.
    "MySQLPass" => "", // Password for native MySQL user.
    "MySQLDatabase" => "", // Name of the MySQL database you wish to use.
    "baseURL" => "", // The base URL for the website that the forum is on.
    "cookieName" => "forum", // The name of cookies set by the forum.
    "forumName" => "Tormater Forum", // The forum's name.
    "footer" => "Powered by [url=tormater-forum]https://github.com/tormater/tormater-forum[/url]", // The text that appears on the footer.
    "postsPerPage" => 10, // Number of posts to display on a page in a thread.
    "threadsPerPage" => 20, // Number of threads to display on a page in a category.
    "maxCharsPerPost" => 50000, // Maximum number of characters allowable in a single post.
    "postDelay" => 5, // Number of seconds between posts during which a user can't make another post.
    "maxCats" => 10, // Maximum number of categories that can be created.
    "maxCharsPerTitle" => 35 // Maximum number of characters allowable in a title. Includes category names along with thread titles.
    "forumTheme" => "Skyline", // The directory that the forum should look in for skin files
    'userlistEnabled' => true, // Whether or not the userlist is enabled.
    'userlistMembersOnly' => false // Whether or not the userlist is only viewable by members.
);

?>
