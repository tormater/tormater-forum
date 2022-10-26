<?php
// formatter.php
// Allows for formatting inside posts

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

function formatBBCode($post)
{
    $returnPost = htmlspecialchars($post);
    $find = array(
        '/"[^"]+"|\[b\](.+?)\[\/b\](?=[\s\S]*?\[code\][\s\S]*?\[\/code\]+|(?![\s\S]*?\[\/code\]))/is',
        '/\[i\](.+?)\[\/i\]/is',
        '/\[u\](.+?)\[\/u\]/is',
        '/\[s\](.+?)\[\/s\]/is',
        '/\[url=(http|ftp|https):\/\/(.+?)\](.+?)\[\/url\]/is',
        '/\[url=(.+?)\](http|ftp|https):\/\/(.+?)\[\/url\]/is',
        '/\[url\](http|ftp|https):\/\/(.+?)\[\/url\]/is',
        '/\[img\](http|https):\/\/(.+?)\[\/img\]/is',
        '/\[spoiler\](.+?)\[\/spoiler\]/is',
        '/\[spoiler=(.+?)\](.+?)\[\/spoiler\]/is',
        '/\[color=(.+?)\](.+?)\[\/color\]/is',
        '/\[code\](.+?)\[\/code\]/is',
        '/\[pre\](.+?)\[\/pre\]/is'
    );

    $replace = array(
        '<b>$1</b>',
        '<i>$1</i>',
        '<u>$1</u>',
        '<s>$1</s>',
        '<a href="$1://$2" target="_blank">$3</a>',
        '<a href="$2://$3" target="_blank">$1</a>',
        '<a href="$1://$2" target="_blank">$2</a>',
        '<img class="postimg" src="$1://$2">',
        '<details class="postspoiler"><summary>Spoiler</summary>$1</details>',
        '<details class="postspoiler"><summary>Spoiler: $1</summary>$2</details>',
        '<font color="$1">$2</font>',
        '<code class="postcode">$1</code>',
        '<pre class="postpre">$1</pre>'
    );

    $returnPost = preg_replace($find, $replace, $returnPost);
    return $returnPost;
}
function formatPost($post)
{
    $returnPost = formatBBCode($post);
    $returnPost = str_replace("\n","<br>",$returnPost);
    return $returnPost;
}
function formatFooter($post)
{
    $returnPost = formatBBCode($post);
    return $returnPost;
}
?>
