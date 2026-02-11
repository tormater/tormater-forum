<?php
// panelextensions.page.php
// Lets Admins enable/disable extensions.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST["changesettings"]))
    {
        $e = htmlspecialchars_decode($_POST["changesettings"]);
        if (!isset($extension_config[$e])) $extension_config[$e] = array();
        if (file_exists("extensions/" . $e . "/manifest.json" ))
            {
                $manifest = json_decode(file_get_contents("extensions/" . $e . "/manifest.json"), true);
                if ($manifest["settings"]) {
                    foreach($manifest["settings"] as $setting) {
                        if ($setting["type"] == "bool" || isset($_POST[$setting["id"]])) {
                            $value = $_POST[$setting["id"]];
                            if (!isset($_POST[$setting["id"]])) $value = "0";
                            $extension_config[$e][$setting["id"]] = $value;
                        }
                    }
                }
        }
        saveExtensionSettingsConfig("config/extension_config.php", $extension_config);
    }
    if ($_POST["enable"])
    {
        $name = $_POST["enable"];
        $extensions[htmlspecialchars_decode($name)] = true;
        saveExtensionConfig("config/extensions.php", $extensions);
        message($lang["panel.EnableSuccess"]);
        require __DIR__ . "/../footer.php";
        header("Refresh:1; url=" . genURL("panel/extensions"));
        exit;
    }
    if ($_POST["disable"])
    {
        $name = $_POST["disable"];
        $extensions[htmlspecialchars_decode($name)] = false;
        saveExtensionConfig("config/extensions.php", $extensions);
        message($lang["panel.DisableSuccess"]);
        require __DIR__ . "/../footer.php";
        header("Refresh:1; url=" . genURL("panel/extensions"));
        exit;
    }
}

    foreach ($allExtensions as $e) 
    {
        if ((file_exists("extensions/" . htmlspecialchars($e) . "/manifest.json" )) && (file_exists("extensions/" . $e . "/extension.php" )))
        {
            $manifest = json_decode(file_get_contents("extensions/" . $e . "/manifest.json"), true);
            $is_outdated = false;
            
            if (isset($manifest["supported_version"])) {
                if (compare_versions(version_from_string($manifest["supported_version"]),$tormater_forum_version) != 0) {
                    $is_outdated = true;
                }
            }
            
            if (!$manifest["title"])
            {
                $manifest["title"] = $e;
            }

            if ($is_outdated) {
                echo '<div class="category extension_warning">';
            }
            else echo '<div class="category">';
            echo '<h3><span>' . htmlspecialchars($manifest["title"]) . '</span></h3>';
            echo '<label>' . $lang["panel.Readme"] . '</label>' . '<span>' . formatPost($manifest["readme"]) . '</span>';
            echo '<div><div style="float:right">';
            if (!isset($extensions[$e]) || $extensions[$e] != true || $extensions[$e] == false)
            {
                echo '<form style="display:inline-block;" method="post" action=""><button name="enable" value="' . htmlspecialchars($e) . '">'.$lang["panel.Enable"].'</button></form>';
            }
            else if ($extensions[$e] == true || $extensions[$e] != false)
            {
                echo '<form style="display:inline-block;" method="post" action=""><button name="disable" value="' . htmlspecialchars($e) . '">'.$lang["panel.Disable"].'</button></form>';
            }
        echo '</div>';
        echo '<label>' . $lang["panel.Author"] . '</label>' . '<span>' . formatPost($manifest["author"]) . '</span>';
        echo "</div>";
        if ($is_outdated) {
            echo "<div class='message extension_message'>"
            . sprintf($lang["error.ExtensionWarning"],htmlspecialchars($manifest["supported_version"]),version_to_string($tormater_forum_version))
            . "</div>";
        }
        // Output the extension settings
        if (isset($manifest["settings"])) {
            echo "<br><fieldset><legend>" . $lang["page.settings"] . "</legend>";
            echo '<form method="post" action="">';
            
                    foreach($manifest["settings"] as $setting) 
                    {
                        echo "<label>" . $setting["name"] . "</label>";
                        //var_dump($setting);
                        
                        $value = htmlspecialchars($extension_config[$e][$setting["id"]]);
                        
                        switch ($setting["type"]) {
                            case "bool":
                              if ($value == "1") $value = 'checked=""';
                              echo '<input type="checkbox" value="1" name="' . htmlspecialchars($setting["id"]) . '" ' . $value . '>';
                              break;
                            case "int":
                              echo '<input type="number" name="' . htmlspecialchars($setting["id"]) . '" value="' . $value . '">';
                              break;
                            case "string":
                              echo '<input type="text"  name="' . htmlspecialchars($setting["id"]) . '" value="' . $value . '">';
                              break;
                            case "text":
                              echo '<textarea name="' . htmlspecialchars($setting["id"]) . '">' . $value . '</textarea>';
                              break;
                            default:
                              echo "???";
                              break;
                        }
                        
                        echo "<br>";
                    }
                    echo '<br><button name="changesettings" value="' . htmlspecialchars($e) . '">'.$lang["panel.ChangeSettingsBtn"].'</button>';
                    echo '</form>';
                    echo "</fieldset>";
                }
            echo '</div>';
        }
    }
?>
