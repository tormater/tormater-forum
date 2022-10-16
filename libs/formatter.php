<?php
// formatter.php
// Allows for formatting inside posts

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;
function formatPost($post)
{
    $returnPost = htmlspecialchars($post);
    $returnPost = str_replace("\n","</br>",$returnPost);
    return $returnPost;
}

?>
