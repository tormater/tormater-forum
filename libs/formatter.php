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
        '/\[size=([0-3][0-9][0-9])\](.+?)\[\/size\]/is',
        '/\[size=([8-9][0-9])\](.+?)\[\/size\]/is',
        '/\[code\](.+?)\[\/code\]/is',
        '/\[pre\](.+?)\[\/pre\]/is',
        '/\[h\](.+?)\[\/h\]/is',
        '/\[h2\](.+?)\[\/h2\]/is',
        '/\[list\](.+?)\[\/list\]/is',
        '/\[list=1\](.+?)\[\/list\]/is',
        '/\[list=a\](.+?)\[\/list\]/is',
        '/\[\*\](.+?)(\n|\r\n?)/is',
        '/(\n|\r\n?)\[hr\]\[\/hr\](\n|\r\n?)/is',
        '/(\n|\r\n?)\[hr\]\[\/hr\]/is',
        '/\[quote\](.+?)\[\/quote\]/is',
        '/\[blockquote\](.+?)\[\/blockquote\]/is',
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
        '<font style="font-size: $1%;">$2</font>',
        '<font style="font-size: $1%;">$2</font>',
        '<code class="postcode">$1</code>',
        '<pre class="postpre">$1</pre>',
        '<span class="postheader">$1</span>',
        '<span class="postsubheader">$1</span>',
        '<ul class="postlist">$1</ul>',
        '<ol class="postlist" style="list-style-type: decimal;">$1</ol>',
        '<ol class="postlist" style="list-style-type: lower-alpha">$1</ol>',
        '<li>$1</li>',
        '<hr class="posthr"></hr>',
        '<hr class="posthr"></hr>',
        '<blockquote class="postquote">$1</blockquote>',
        '<blockquote class="postquote">$1</blockquote>',
    );

    listener("beforeFormatBBCode");
    $returnPost = preg_replace($find, $replace, $returnPost);
    return $returnPost;
}
function formatPost($post)
{
    $returnPost = formatBBCode($post);
    $returnPost = str_replace("\n","<br>",$returnPost);
    listener("beforeReturnFormattedPost");
    return $returnPost;
}
function formatFooter($post)
{
    $returnPost = formatBBCode($post);
    listener("beforeReturnFormattedFooter");
    return $returnPost;
}
?>
