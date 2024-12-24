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

// Handle post requests.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["uploadedFile"]) && $_FILES["uploadedFile"]["size"]) {
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

        // Check if the image's MIME type is one of the supported formats.
        if (($mime != "image/jpeg") and ($mime != "image/png") and ($mime != "image/gif") and ($mime != "image/webp")) {
            message("Error: unsupported image type. Make sure your image is a png/gif/jpg/webp.");
        }
        // Make sure the width and height is valid.
        elseif  (!isset($width) or !isset($height) or ($width < 1) or ($height < 1)) {
            message("Error: invalid image width/height.");
        }
        // Check for uploading errors.
        elseif (!isset($_FILES["uploadedFile"]["error"]) or (is_array($_FILES["uploadedFile"]["error"])) or ($_FILES["uploadedFile"]["error"] !== 0)) {
            message("Error: file upload failed.");
        }
        // Make sure the avatar isn't too big.
        elseif ($_FILES["uploadedFile"]["size"] > $config["maxAvatarSize"]) {
            message("Error: avatar filesize too large.");
        }
        else {
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
                $dst = imagecreatetruecolor((int)$w, (int)$h);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, (int)$w, (int)$h, $width, $height);
                removeAvatar($_SESSION["userid"]);
                imagejpeg($dst, "avatars/" . $_SESSION["userid"] . $extension);
                if (file_exists("avatars/" . $_SESSION["userid"] . $extension)) {
                    $db->query("UPDATE users SET avatar='jpg', avataruploadtime='" . time() . "' WHERE userid='" . $_SESSION["userid"] . "'");
                    message($lang["userpanel.AvatarUploadSuccess"]);
                }
                else {
                    message("Error: failed to upload avatar. Make sure it's a valid image file.");
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
                $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefill($dst, 0, 0, $transparent);

                imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
                removeAvatar($_SESSION["userid"]);
                imagepng($dst, "avatars/" . $_SESSION["userid"] . $extension);
                if (file_exists("avatars/" . $_SESSION["userid"] . $extension)) {
                    $db->query("UPDATE users SET avatar='png', avataruploadtime='" . time() . "' WHERE userid='" . $_SESSION["userid"] . "'");
                    message($lang["userpanel.AvatarUploadSuccess"]);
                }
                else {
                    message("Error: failed to upload avatar. Make sure it's a valid image file.");
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
                $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefill($dst, 0, 0, $transparent);

                imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
                removeAvatar($_SESSION["userid"]);
                imagegif($dst, "avatars/" . $_SESSION["userid"] . $extension);
                if (file_exists("avatars/" . $_SESSION["userid"] . $extension)) {
                    $db->query("UPDATE users SET avatar='gif', avataruploadtime='" . time() . "' WHERE userid='" . $_SESSION["userid"] . "'");
                    message($lang["userpanel.AvatarUploadSuccess"]);
                }
                else {
                    message("Error: failed to upload avatar. Make sure it's a valid image file.");
                }
            }
            elseif ($mime == "image/webp") {
                $extension = ".webp";

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

                $src = imagecreatefromwebp($_FILES["uploadedFile"]["tmp_name"]);
                $dst = imagecreatetruecolor($w, $h);
                
                imagesavealpha($dst, true);
                $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefill($dst, 0, 0, $transparent);

                imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
                removeAvatar($_SESSION["userid"]);
                imagewebp($dst, "avatars/" . $_SESSION["userid"] . $extension);
                if (file_exists("avatars/" . $_SESSION["userid"] . $extension)) {
                    $db->query("UPDATE users SET avatar='webp', avataruploadtime='" . time() . "' WHERE userid='" . $_SESSION["userid"] . "'");
                    message($lang["userpanel.AvatarUploadSuccess"]);
                }
                else {
                    message("Error: failed to upload avatar. Make sure it's a valid image file.");
                }
            }
        }
    }
    elseif (isset($_POST["removeAvatar"])) {
        if (removeAvatar($_SESSION["userid"])) message($lang["userpanel.RemoveAvatarSuccess"]);
    }
}

$avatarCheck = $db->query("SELECT avatar, avataruploadtime FROM users WHERE userid='" . $_SESSION["userid"] . "'");

while ($a = $avatarCheck->fetch_assoc()) {
    if ($a["avatar"] != "none") {
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
