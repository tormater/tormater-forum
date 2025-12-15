<?php
// post.page.php
// Allows posts to be linked to.

if (!defined("INDEXED")) exit;

if (isset($q2) && is_numeric($q2)) {
    $postid = $q2;
}
else redirect("404");

$post = $db->query("SELECT * FROM posts WHERE postid='" . $db->real_escape_string($q2) . "'");

if ($post->num_rows < 1) redirect("404");
$row = $post->fetch_assoc();

$posts = $db->query("SELECT * FROM posts WHERE thread='" . $db->real_escape_string($row["thread"]) . "' ORDER BY timestamp");

if ($posts->num_rows < 1) redirect("404");

$post_position = 0;
while ($p = $posts->fetch_assoc()) {
    $post_position++;
    if ($p["postid"] == $row["postid"]) break;
}

$postsPerPage = (is_numeric($config["postsPerPage"]) ? (int)$config["postsPerPage"] : 10);
if ($postsPerPage < 1) $postsPerPage = 1;

$pagenum = 1+floor($post_position/$postsPerPage);

redirect("thread/" . $row["thread"] . "/" . $pagenum . "/#post_" . $row["postid"]);

?>
