<?php

include_once dirname(__DIR__, 1) . "/libs/ajax.php";

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (isset($_POST["postid"]) && is_numeric($_POST["postid"]))
	{
        $post = $db->query("SELECT content, deletedby FROM posts WHERE postid='" . $db->real_escape_string($_POST["postid"]) . "'");
        while($row = $post->fetch_assoc())
	    {
            if (!isset($row["deletedby"]))
            {
                echo "[quote]";
                $quotecontent = preg_replace("/\[quote\](.+?)\[\/quote\]\s+/is", "", $row["content"]);
                $quotecontent = preg_replace("/\[blockquote\](.+?)\[\/blockquote\]\s+/is", "", $quotecontent);
                echo $quotecontent;
                echo "[/quote]";
                echo "\n";
            }
        }
    }
	if ($_POST["postRaw"] && strlen($_POST["postRaw"]) <= $config['maxCharsPerPost'])
	{
        echo formatPost(htmlspecialchars_decode($_POST["postRaw"]));
    }
}
?>
