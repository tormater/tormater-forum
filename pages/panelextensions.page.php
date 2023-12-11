<?php
// panelextensions.page.php
// Lets Admins enable/disable extensions.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($_POST["enable"])
	{
		$name = $_POST["enable"];
        $extensions[htmlspecialchars_decode($name)] = true;
        saveExtensionConfig("config/extensions.php", $extensions);
        message($lang["panel.EnableSuccess"]);
        include "footer.php";
        header("Refresh:1; url=" . genURL("panel/extensions"));
		exit;
	}
	if ($_POST["disable"])
	{
		$name = $_POST["disable"];
        $extensions[htmlspecialchars_decode($name)] = false;
        saveExtensionConfig("config/extensions.php", $extensions);
        message($lang["panel.DisableSuccess"]);
		include "footer.php";
        header("Refresh:1; url=" . genURL("panel/extensions"));
		exit;
	}
}

    foreach ($allExtensions as $e) {
        if ((file_exists("extensions/" . htmlspecialchars($e) . "/manifest.json" )) && (file_exists("extensions/" . $e . "/extension.php" )))
        {
            $manifest = json_decode(file_get_contents("extensions/" . $e . "/manifest.json"), true);

            if (!$manifest["title"])
            {
                $manifest["title"] = $e;
            }

    		echo '<div class="category"><tr>';
    		echo '<td class="leftpart">';
    		echo '<h3><span>' . htmlspecialchars($manifest["title"]) . '</span></h3>';
			echo '<label>' . $lang["panel.Readme"] . '</label>' . '<span>' . formatPost($manifest["readme"]) . '</span>';
    		echo '<div><div style="float:right">';
            if ($extensions[$e] != true || $extensions[$e] == false)
            {
                echo '<form style="display:inline-block;" method="post" action=""><button name="enable" value="' . htmlspecialchars($e) . '">'.$lang["panel.Enable"].'</button></form>';
            }
            if ($extensions[$e] == true || $extensions[$e] != false)
            {
                echo '<form style="display:inline-block;" method="post" action=""><button name="disable" value="' . htmlspecialchars($e) . '">'.$lang["panel.Disable"].'</button></form>';
            }
			echo '</div>';
			echo '<label>' . $lang["panel.Author"] . '</label>' . '<span>' . formatPost($manifest["author"]) . '</span>';
    		echo '</tr></div></div>';
        }
    }
?>
