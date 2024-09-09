<?php
// extensions.php
// Loads extensions installed on the software.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$allExtensions = scandir("extensions");

if (file_exists("config/extension_config.php")) {
    require "config/extension_config.php";
}
saveExtensionSettingsConfig("config/extension_config.php", $extension_config);

foreach ($allExtensions as $e) {
    if ((file_exists("extensions/" . $e . "/manifest.json" )) && (file_exists("extensions/" . $e . "/extension.php" )))
    {
        if (isset($extensions[$e]) && ($extensions[$e] == true || $extensions[$e] != false))
        {
            require_once("extensions/" . $e . "/extension.php");
        }
    }
}

?>
