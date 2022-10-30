<?php
// formatter.php
// Allows for formatting inside posts

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

function formatBBCode($post)
{
    $returnPost = htmlspecialchars($post);
    $find = array(
        '/\[b\](.+?)\[\/b\]/is',
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
        '/\[size=(\d\d)\](.+?)\[\/size\]/is',
        '/\[size=(\d)\](.+?)\[\/size\]/is',
        '/\[code\](.+?)\[\/code\]/is',
        '/\[pre\](.+?)\[\/pre\]/is',
        '/\[h\](.+?)\[\/h\]/is',
        '/\[h2\](.+?)\[\/h2\]/is',
    );

    $replace = array(
        '<b class="postbold">$1</b>',
        '<i class="postitalic">$1</i>',
        '<u class="postunderline">$1</u>',
        '<s class="poststrike">$1</s>',
        '<a href="$1://$2" target="_blank">$3</a>',
        '<a href="$2://$3" target="_blank">$1</a>',
        '<a href="$1://$2" target="_blank">$2</a>',
        '<img class="postimg" src="$1://$2">',
        '<details class="postspoiler"><summary>Spoiler</summary>$1</details>',
        '<details class="postspoiler"><summary>Spoiler: $1</summary>$2</details>',
        '<font color="$1">$2</font>',
        '<font style="font-size: $1px;">$2</font>',
        '<font style="font-size: $1px;">$2</font>',
        '<code class="postcode">$1</code>',
        '<pre class="postpre">$1</pre>',
        '<span class="postheader">$1</span>',
        '<span class="postsubheader">$1</span>',
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
