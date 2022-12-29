<?php
// userpanelavatarsettings.page.php
// Allows the user to change their avatar.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if ($config["avatarUploadsDisabled"] == true) {
    message($lang["userpanel.AvatarUploadsDisabled"]);
    require "footer.php";
    exit;
}

function removeAvatar()
{
    global $db;

    $typeCheck = $db->query("SELECT avatar FROM users WHERE userid='" . $_SESSION["userid"] . "'");

    while ($t = $typeCheck->fetch_assoc()) {
        $typeOfFile = $t["avatar"];
    }

    if ($typeOfFile != "none") {
        unlink("avatars/" . $_SESSION["userid"] . "." . $typeOfFile);
        $db->query("UPDATE users SET avatar='none' WHERE userid='" . $_SESSION["userid"] . "'");
        return true;
    }

    return false;
}

// Handle post requests.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["uploadedFile"])) {
        $timeCheck = $db->query("SELECT avataruploadtime FROM users WHERE userid='" . $_SESSION["userid"] . "'");

        while ($t = $timeCheck->fetch_assoc()) {
            $uploadTime = $t["avataruploadtime"];
        }

        if (($config["timeBetweenAvatarUploads"] > 0) and isset($uploadTime) and ((time() - $uploadTime) <= $config["timeBetweenAvatarUploads"])) {
            message($lang["userpanel.WaitToUpload"]);
            return;
        }

        list($width,$height,$type,$attr) = getimagesize($_FILES["uploadedFile"]["tmp_name"]);
        $mime = image_type_to_mime_type($type);

        // First, make sure the mime type is what we want and various other checks to ensure the file is valid.
        if ((($mime == "image/jpeg") or ($mime == "image/png") or ($mime == "image/gif")) and $width and $height and isset($width) and isset($height) and isset($_FILES["uploadedFile"]["error"]) and (!is_array($_FILES["uploadedFile"]["error"])) and ($_FILES["uploadedFile"]["error"] === 0) and ($_FILES["uploadedFile"]["size"] < 500000000)) {
            // If everything looks good, get ready to upload the image and base its extension off the mime type.
            if ($mime == "image/jpeg") {
                $extension = ".jpg";

                // Only set the width to the config's max width if it exceeds it.
                if ($config["avatarWidth"] >= $width) {
                    $w = $width;
                }
                else {
                    $w = $config["avatarWidth"];
                }

                // Only set the height to the config's max height if it exceeds it.
                if ($config["avatarHeight"] >= $height) {
                    $h = $height;
                }
                else {
                    $h = $config["avatarHeight"];
                }

                $originalRatio = $width/$height;

                if ($w/$h > $originalRatio) {
                    $w = $h*$originalRatio;
                }
                else {
                    $h = $w/$originalRatio;
                }

                $src = imagecreatefromjpeg($_FILES["uploadedFile"]["tmp_name"]);
                $dst = imagecreatetruecolor($w, $h);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
                removeAvatar();
                imagejpeg($dst, "avatars/" . $_SESSION["userid"] . $extension);
                if (file_exists("avatars/" . $_SESSION["userid"] . $extension)) {
                    $db->query("UPDATE users SET avatar='jpg', avataruploadtime='" . time() . "' WHERE userid='" . $_SESSION["userid"] . "'");
                    message($lang["userpanel.AvatarUploadSuccess"]);
                }
            }
            elseif ($mime == "image/png") {
                $extension = ".png";

                // Only set the width to the config's max width if it exceeds it.
                if ($config["avatarWidth"] >= $width) {
                    $w = $width;
                }
                else {
                    $w = $config["avatarWidth"];
                }

                // Only set the height to the config's max height if it exceeds it.
                if ($config["avatarHeight"] >= $height) {
                    $h = $height;
                }
                else {
                    $h = $config["avatarHeight"];
                }

                $originalRatio = $width/$height;

                if ($w/$h > $originalRatio) {
                    $w = $h*$originalRatio;
                }
                else {
                    $h = $w/$originalRatio;
                }

                $src = imagecreatefrompng($_FILES["uploadedFile"]["tmp_name"]);
                $dst = imagecreatetruecolor($w, $h);

                imagesavealpha($dst, true);
                $trans_colour = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefill($dst, 0, 0, $trans_colour);

                imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
                removeAvatar();
                imagepng($dst, "avatars/" . $_SESSION["userid"] . $extension);
                if (file_exists("avatars/" . $_SESSION["userid"] . $extension)) {
                    $db->query("UPDATE users SET avatar='png', avataruploadtime='" . time() . "' WHERE userid='" . $_SESSION["userid"] . "'");
                    message($lang["userpanel.AvatarUploadSuccess"]);
                }
            }
            elseif ($mime == "image/gif") {
                $extension = ".gif";

                // Only set the width to the config's max width if it exceeds it.
                if ($config["avatarWidth"] >= $width) {
                    $w = $width;
                }
                else {
                    $w = $config["avatarWidth"];
                }

                // Only set the height to the config's max height if it exceeds it.
                if ($config["avatarHeight"] >= $height) {
                    $h = $height;
                }
                else {
                    $h = $config["avatarHeight"];
                }

                $originalRatio = $width/$height;

                if ($w/$h > $originalRatio) {
                    $w = $h*$originalRatio;
                }
                else {
                    $h = $w/$originalRatio;
                }

                $src = imagecreatefromgif($_FILES["uploadedFile"]["tmp_name"]);
                $dst = imagecreatetruecolor($w, $h);
                
                imagesavealpha($dst, true);
                $trans_colour = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefill($dst, 0, 0, $trans_colour);

                imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
                removeAvatar();
                imagegif($dst, "avatars/" . $_SESSION["userid"] . $extension);
                if (file_exists("avatars/" . $_SESSION["userid"] . $extension)) {
                    $db->query("UPDATE users SET avatar='gif', avataruploadtime='" . time() . "' WHERE userid='" . $_SESSION["userid"] . "'");
                    message($lang["userpanel.AvatarUploadSuccess"]);
                }
            }
        }
    }
    elseif (isset($_POST["removeAvatar"])) {
        if (removeAvatar()) message($lang["userpanel.RemoveAvatarSuccess"]);
    }
}

$avatarCheck = $db->query("SELECT avatar, avataruploadtime FROM users WHERE userid='" . $_SESSION["userid"] . "'");

while ($a = $avatarCheck->fetch_assoc()) {
    if ($a["avatar"] == "none") {
        $avatarURL = "</br>" . $lang["userpanel.NoAvatar"] . "</br>";
    }
    else {
        $avatarURL = "</br><img class='avatarPanel' src='" . genURL("avatars/" . $_SESSION["userid"] . "." . $a["avatar"] . "?t=" . $a["avataruploadtime"]) . "'>";
    }
}

echo("<div class='avatarForm'><h3>" . $lang["userpanel.ChangeAvatar"] . "</h3>" . $avatarURL . "</br>
    <form action='' method='post' enctype='multipart/form-data' class='inline-block'>
    <input type='file' name='uploadedFile' class='upload'>
    <input type='submit' value='" . $lang["userpanel.UploadAvatar"] . "' name='submit'></form> <form action='' method='post' class='inline-block'><input type='submit' value='" . $lang["userpanel.RemoveAvatar"] . "' name='removeAvatar'></form>
    </div>");

// If the viewing user is logged in, update their last action.
if ($_SESSION['signed_in'] == true)
{
	update_last_action($lang["action.AvatarSettings"]);
}

?>
