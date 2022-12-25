<?php
// extensions.php
// Loads extensions installed on the software.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$plugins = scandir("extensions");

foreach ($plugins as $p) {
    if ((file_exists("extensions/" . $p . "/manifest.json" )) && (file_exists("extensions/" . $p . "/extension.php" )))
    {
        require_once("extensions/" . $p . "/extension.php");
    }
}

?>
