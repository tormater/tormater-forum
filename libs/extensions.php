<?php
// extensions.php
// Loads extensions installed on the software.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$allExtensions = scandir("extensions");

foreach ($allExtensions as $e) {
    if ((file_exists("extensions/" . $e . "/manifest.json" )) && (file_exists("extensions/" . $e . "/extension.php" )))
    {
        if ($extensions[$e] == true || $extensions[$e] != false)
        {
            require_once("extensions/" . $e . "/extension.php");
        }
    }
}

?>
