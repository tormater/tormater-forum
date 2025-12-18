<?php
// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

function symbolismAddCSS() {
    echo '<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">';
    echo '<style>fa {font-family:FontAwesome} .threadcontent a:before, .Administrator:before, .Moderator:before, .Member:before, .Suspended:before {font-family:FontAwesome; margin-right:3px}.threadcontent a:before {content:"\f08e";} .Member:before {content:"\f007"} .Moderator:before {content:"\f132"} .Administrator:before {content:"\f0e3"} .Suspended:before {content:"\f05e"} .fabbr {text-decoration: none}</style>';
}

hook("meta","symbolismAddCSS");

$lang["header.Home"] = "<fa class='fa-home'></fa> " . $lang["header.Home"];
$lang["header.Userlist"] = "<fa class='fa-users'></fa> " . $lang["header.Userlist"];
$lang["header.UserPanel"] = "<fa class='fa-gear'></fa> " . $lang["header.UserPanel"];
$lang["header.NewThread"] = "<fa class='fa-plus'></fa> " . $lang["header.NewThread"];
$lang["header.Panel"] = "<fa class='fa-gears'></fa> " . $lang["header.Panel"];
$lang["header.Logout"] = "<fa class='fa-sign-out'></fa> " . $lang["header.Logout"];
//$lang["header.Login"] = "<fa class='fa-sign-in'></fa> " . $lang["header.Login"];
//$lang["header.Signup"] = "<fa class='fa-external-link'></fa> " . $lang["header.Signup"];
$lang["label.Sticky"] = "<fa class='fa-sticky-note-o'></fa> " . $lang["label.Sticky"];
$lang["label.Locked"] = "<fa class='fa-lock'></fa> " . $lang["label.Locked"];
$lang["label.Pinned"] = "<fa class='fa-thumb-tack'></fa> " . $lang["label.Pinned"];
$lang["thread.MoveThreadBtn"] = "<fa class='fa-exchange'></fa> " . $lang["thread.MoveThreadBtn"];
$lang["thread.DeleteThreadBtn"] = "<fa class='fa-trash-o'></fa> " . $lang["thread.DeleteThreadBtn"];
$lang["thread.LockThreadBtn"] = "<fa class='fa-lock'></fa> " . $lang["thread.LockThreadBtn"];
$lang["thread.StickyThreadBtn"] = "<fa class='fa-sticky-note'></fa> " . $lang["thread.StickyThreadBtn"];
$lang["thread.UnlockThreadBtn"] = "<fa class='fa-unlock'></fa> " . $lang["thread.UnlockThreadBtn"];
$lang["thread.UnstickyThreadBtn"] = "<fa class='fa-sticky-note-o'></fa> " . $lang["thread.UnstickyThreadBtn"];
$lang["thread.PinThreadBtn"] = "<fa class='fa-thumb-tack'></fa> " . $lang["thread.PinThreadBtn"];
$lang["thread.UnpinThreadBtn"] = "<fa class='fa-times'></fa> " . $lang["thread.UnpinThreadBtn"];

$lang["post.DeleteBtn"] = "<abbr class='fabbr' title='" . $lang["post.DeleteBtn"] . "'><fa class='fa-trash-o'></fa></abbr>";
$lang["post.EditBtn"] = "<abbr class='fabbr' title='" . $lang["post.EditBtn"] . "'><fa class='fa-pencil'></fa></abbr>";
$lang["post.HideBtn"] = "<abbr class='fabbr' title='" . $lang["post.HideBtn"] . "'><fa class='fa-eye-slash'></fa></abbr>";
$lang["search.Button"] = "<fa class='fa-search'></fa> ". $lang["search.Button"];
$lang["thread.QuotePost"] = "<abbr class='fabbr' title='" . $lang["thread.QuotePost"] . "'><fa class='fa-quote-left'></fa></abbr>";
$lang["post.RestoreHiddenBtn"] = "<abbr class='fabbr' title='" . $lang["post.RestoreHiddenBtn"] . "'><fa class='fa-eye'></fa></abbr>";



?>
