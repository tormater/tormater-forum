<?php
// formatter.php
// Allows for formatting inside posts

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

function formatBBCode($post)
{
    $returnPost = htmlspecialchars($post);
    
    $returnPost = preg_replace_callback("/\[code\](.+?)\[\/code\]/is", function($match) {
        // We convert to html entities here so that the next RegEx function doesn't format inside the code block.
        return '<code class="postcode">' . str_replace(array('[', ']'), array('&#91;', '&#93;'), $match[1]) . '</code>';
    }, $returnPost);
    
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
        //'/\[code\](.+?)\[\/code\]/is',
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
        '<a href="$1://$2" target="_blank" rel="nofollow">$3</a>',
        '<a href="$2://$3" target="_blank" rel="nofollow">$1</a>',
        '<a href="$1://$2" target="_blank" rel="nofollow">$2</a>',
        '<img class="postimg" src="$1://$2">',
        '<details class="postspoiler"><summary>Spoiler</summary>$1</details>',
        '<details class="postspoiler"><summary>Spoiler: $1</summary>$2</details>',
        '<font color="$1">$2</font>',
        '<font style="font-size: $1%;">$2</font>',
        '<font style="font-size: $1%;">$2</font>',
        //'<code class="postcode">$1</code>',
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
    $returnPost = str_replace(array('&#91;', '&#93;'), array('[', ']'), $returnPost);
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
    $returnPost = formatPost($post);
    listener("beforeReturnFormattedFooter");
    return $returnPost;
}

// Display buttons for adding BBCode to textareas.
function BBCodeButtons($num = "", $echo = true) {
    global $lang;
    // Serve the JavaScript.
    echo("<script type='text/javascript' src='" . genURL("assets/bbcode.js") . "'></script>");

    listener("beforeRenderBBCodeTray");

    // Serve the BBCode buttons.
    $buttons = "<div class='bbcodetraycontainer'>";

    $buttons .= "<div class='bbcodetray'>";
    $buttons .= "<input type='button' class='bbcode bbold' value='" . $lang["BBCode.Bold"] . "' onclick=formatText('b'," . $num . ");>";
    $buttons .= "<input type='button' class='bbcode bitalic' value='" . $lang["BBCode.Italic"] . "' onclick=formatText('i'," . $num . ");>";
    $buttons .= "<input type='button' class='bbcode bunderline' value='" . $lang["BBCode.Underline"] . "' onclick=formatText('u'," . $num . ");>";
    $buttons .= "<input type='button' class='bbcode bstrike' value='" . $lang["BBCode.Strikethrough"] . "' onclick=formatText('s'," . $num . ");>";
    $buttons .= "<input type='button' class='bbcode bheader' value='" . $lang["BBCode.Header"] . "' onclick=formatText('h'," . $num . ");>";
    $buttons .= "<input type='button' class='bbcode bcode' value='" . $lang["BBCode.Code"] . "' onclick=formatText('code'," . $num . ");>";

    $buttons .= "</div><div class='bbcodetray'>";
    $buttons .= "<input type='button' class='bbcode blink' value='" . $lang["BBCode.Link"] . "' onclick=formatText('url'," . $num . ");>";
    $buttons .= "<input type='button' class='bbcode bimage' value='" . $lang["BBCode.Image"] . "' onclick=formatText('img'," . $num . ");>";
    $buttons .= "<input type='button' class='bbcode bspoiler' value='" . $lang["BBCode.Spoiler"] . "' onclick=formatText('spoiler'," . $num . ");>";

    $buttons .= "</div><div class='bbcodetray'>";
    $buttons .= "<input type='color' class='bbcode bcolor' id='colorbox" . $num . "' value='#000000'" .  "' onchange=formatTextWithDetails('color','colorbox'," . $num . ");>";
    $buttons .= "<select name='font-size' class='bbcode bsize' id='sizebox" . $num . "'" .  "' onchange=formatTextWithDetails('size','sizebox'," . $num . ");>";
    $buttons .= "<option value='80'>80%</option>";
    $buttons .= "<option value='100'>100%</option>";
    $buttons .= "<option value='150'>150%</option>";
    $buttons .= "<option value='200'>200%</option>";
    $buttons .= "<option value='300'>300%</option>";
    $buttons .= "</select>";
    $buttons .= "</div>";
    $buttons .= "</div>";
    
    listener("beforeRenderBBCodeButtons",$buttons);

    if ($echo) echo $buttons;
    else return $buttons;
}
?>
