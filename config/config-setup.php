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
    'footer' => '', // The text that appears on the footer. Supports BBCode.
    'postsPerPage' => 10, // Number of posts to display on a page in a thread.
    'threadsPerPage' => 20, // Number of threads to display on a page in a category.
    'usersPerPage' => 30, // Number of users to display per page in the userlist.
    'maxCharsPerPost' => 50000, // Maximum number of characters allowable in a single post.
    'maxUsernameLength' => 24, // Maximum username length.
    'postDelay' => 5, // Number of seconds between posts during which a user can't make another post.
    'maxCharsPerTitle' => 35, // Maximum number of characters allowable in a title. Includes category names along with thread titles.
    'forumTheme' => 'Aurora', // The directory that the forum should look in for skin files
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
    'mainAdmin' => 1, // Userid of the main administrator.
    'forumDescription' => 'Tormater Forum is a lightweight, easy-to-use forum software created with PHP and MySQL.',
    'draftsPerMinute' => 5, // Number of draft posts a user can make per minute.
    'forumColor' => null, // Forum color
    'registration' => 'open', // Registration options. 'open' for unrestricted signups, 'closed' for no signups, 'approval' for signups requiring manual approval by mods and admins.
    'onlinePeriod' => '600', // The amount of time a user is shown as "online".
    'showDeletedInUserlist' => 0, // If deleted users should be shown in the userlist.
    'modRewriteDisabled' => 0, // If mod_rewrite is disabled, use query parameters [127.0.0.1/thread/10] vs [127.0.0.1/index.php?url=thread/10]
    'parentSite' => '', // The URL of the parent website (for example, if the forum is at 127.0.0.1/forum, the parent site would be 127.0.0.1)
    'parentSiteName' => '', // The name of the parent website (see above)
    'maxAvatarSize' => 5000000, // The maximum acceptable filesize (in bytes) for an avatar upload
);

?>
