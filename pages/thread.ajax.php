<?php

include_once dirname(__DIR__, 1) . "/libs/ajax.php";

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($_POST["postid"] && is_numeric($_POST["postid"]))
	{
        $post = $db->query("SELECT content, deletedby FROM posts WHERE postid='" . $db->real_escape_string($_POST["postid"]) . "'");
        while($row = $post->fetch_assoc())
	    {
            if (!isset($row["deletedby"]))
            {
                echo "[quote]";
                $quotecontent = preg_replace("/\[quote\](.+?)\[\/quote\]\s+/is", "", htmlspecialchars($row["content"]));
                $quotecontent = preg_replace("/\[blockquote\](.+?)\[\/blockquote\]\s+/is", "", $quotecontent);
                echo $quotecontent;
                echo "[/quote]";
                echo "\n";
            }
        }
    }
}
?>
