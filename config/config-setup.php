<?php
// config-setup.php
// Use this file to setup your webforum.

// Only load the page if it's being loaded through the index.php file.
if (!defined('INDEXED')) exit;

$config = array(
    'installed' => 'no', // Whether or not the forum is installed. (yes/no)
    'MySQLServer' => '', // URL for your MySQL server. Set to localhost if it's on the same machine.
    'MySQLUser' => '', // Username for native MySQL user.
    'MySQLPass' => '', // Password for native MySQL user.
    'MySQLDatabase' => '', // Name of the MySQL database you wish to use.
    'baseURL' => 'http://example.com', // The base URL for the website that the forum is on.
    'cookieName' => 'forum', // The name of cookies set by the forum.
    'forumName' => 'Tormater Forum', // The forum's name.
    'footer' => 'Powered by [url=tormater-forum]https://github.com/tormater/tormater-forum[/url]', // The text that appears on the footer.
    'postsPerPage' => 10, // Number of posts to display on a page in a thread.
    'threadsPerPage' => 20, // Number of threads to display on a page in a category.
    'maxCharsPerPost' => 50000, // Maximum number of characters allowable in a single post.
    'postDelay' => 5, // Number of seconds between posts during which a user can't make another post.
    'maxCats' => 10, // Maximum number of categories that can be created.
    'maxCharsPerTitle' => 35, // Maximum number of characters allowable in a title. Includes category names along with thread titles.
    'forumTheme' => 'Skyline', // The directory that the forum should look in for skin files
    'forumLang' => 'EN_US', // The language file that the forum should use to display strings
    'userlistEnabled' => true, // Whether or not the userlist is enabled.
    'userlistMembersOnly' => false, // Whether or not the userlist is only viewable by members.
    'maxAccountsPerIP' => 5, // Maximum number of accounts that can be created on one IP.
    'hashAlgo' => "sha512", // The default hashing algorithm for your forum to use.
    'avatarWidth' => 200, // Maximum avatar width.
    'avatarHeight' => 200, // Maximum avatar height.
    'avatarUploadsDisabled' => false, // Whether or not avatar uploads are disabled.
    'timeBetweenAvatarUploads' => 120, // How much time (in seconds) the user should have to wait to upload another avatar.
    'maxLoginAttempts' => 3, // Number of login attempts allowed every 5 minutes.
    'captchaLength' => 5, // Number of characters to use in the captcha.
    'captchaEnabled' => true, // Whether or not the captcha is enabled.
    'mainAdmin' => 1 // Userid of the main administrator.
);

?>
