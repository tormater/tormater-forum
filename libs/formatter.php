<?php
// formatter.php
// Allows for formatting inside posts

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

function formatPost($post)
{
    $returnPost = htmlspecialchars($post);
    $returnPost = formatNewline($returnPost);
    $returnPost = formatBold($returnPost);
    $returnPost = formatItalic($returnPost);
    $returnPost = formatUnderline($returnPost);
    $returnPost = formatStrikethrough($returnPost);
    $returnPost = formatPreformatted($returnPost);
    $returnPost = formatCode($returnPost);
    $returnPost = formatTable($returnPost);
    $returnPost = formatTablerow($returnPost);
    $returnPost = formatTabledata($returnPost);
    $returnPost = formatTableheader($returnPost);
    $returnPost = formatLink($returnPost);
    $returnPost = formatImage($returnPost);
    return $returnPost;
}

function formatNewline($returnPost)
{
    return str_replace("\n","</br>",$returnPost);
}

function formatBold($returnPost)
{
    if ((strpos($returnPost, "[b]") !== false) and (strpos($returnPost, "[/b]") !== false)) {
        $returnPost = str_replace("[b]","<b>",$returnPost);
        $returnPost = str_replace("[/b]","</b>",$returnPost);
    }
    return $returnPost;
}

function formatItalic($returnPost)
{
    if ((strpos($returnPost, "[i]") !== false) and (strpos($returnPost, "[/i]") !== false)) {
        $returnPost = str_replace("[i]","<i>",$returnPost);
        $returnPost = str_replace("[/i]","</i>",$returnPost);
    }
    return $returnPost;
}

function formatUnderline($returnPost)
{
    if ((strpos($returnPost, "[u]") !== false) and (strpos($returnPost, "[/u]") !== false)) {
        $returnPost = str_replace("[u]","<u>",$returnPost);
        $returnPost = str_replace("[/u]","</u>",$returnPost);
    }
    return $returnPost;
}

function formatStrikethrough($returnPost)
{
    if ((strpos($returnPost, "[s]") !== false) and (strpos($returnPost, "[/s]") !== false)) {
        $returnPost = str_replace("[s]","<s>",$returnPost);
        $returnPost = str_replace("[/s]","</s>",$returnPost);
    }
    return $returnPost;
}

function formatPreformatted($returnPost)
{
    if ((strpos($returnPost, "[pre]") !== false) and (strpos($returnPost, "[/pre]") !== false)) {
        $returnPost = str_replace("[pre]","<pre>",$returnPost);
        $returnPost = str_replace("[/pre]","</pre>",$returnPost);
    }
    return $returnPost;
}

function formatCode($returnPost)
{
    if ((strpos($returnPost, "[code]") !== false) and (strpos($returnPost, "[/code]") !== false)) {
        $returnPost = str_replace("[code]","<code>",$returnPost);
        $returnPost = str_replace("[/code]","</code>",$returnPost);
    }
    return $returnPost;
}

function formatTable($returnPost)
{
    if ((strpos($returnPost, "[table]") !== false) and (strpos($returnPost, "[/table]") !== false)) {
        $returnPost = str_replace("[table]","<table>",$returnPost);
        $returnPost = str_replace("[/table]","</table>",$returnPost);
    }
    return $returnPost;
}

function formatTablerow($returnPost)
{
    if ((strpos($returnPost, "[tr]") !== false) and (strpos($returnPost, "[/tr]") !== false)) {
        $returnPost = str_replace("[tr]","<tr>",$returnPost);
        $returnPost = str_replace("[/tr]","</tr>",$returnPost);
    }
    return $returnPost;
}

function formatTabledata($returnPost)
{
    if ((strpos($returnPost, "[td]") !== false) and (strpos($returnPost, "[/td]") !== false)) {
        $returnPost = str_replace("[td]","<td>",$returnPost);
        $returnPost = str_replace("[/td]","</td>",$returnPost);
    }
    return $returnPost;
}

function formatTableheader($returnPost)
{
    if ((strpos($returnPost, "[th]") !== false) and (strpos($returnPost, "[/th]") !== false)) {
        $returnPost = str_replace("[th]","<th>",$returnPost);
        $returnPost = str_replace("[/th]","</th>",$returnPost);
    }
    return $returnPost;
}

function formatLink($returnPost)
{
    if ((strpos($returnPost, "[url]http") !== false) and (strpos($returnPost, "[/url]") !== false)) {
        $returnPost = str_replace("[url]http","<a href='http",$returnPost);
        $returnPost = str_replace("[/url]","'>link text</a>",$returnPost);
    }
    return $returnPost;
}

function formatImage($returnPost)
{
    if ((strpos($returnPost, "[img]http") !== false) and (strpos($returnPost, "[/img]") !== false)) {
        $returnPost = str_replace("[img]http","<img height='200' width='200' src='http",$returnPost);
        $returnPost = str_replace("[/img]","'>",$returnPost);
    }
    return $returnPost;
}

?>
