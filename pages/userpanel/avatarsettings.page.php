<?php
// userpanelavatarsettings.page.php
// Allows the user to change their avatar.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if ($config["avatarUploadsDisabled"] == true) {
    message($lang["userpanel.AvatarUploadsDisabled"]);
    require __DIR__ . "/../footer.php";
    exit;
}

if (!(get_role_permissions() & PERM_EDIT_PROFILE)) {
    message($lang["nav.BadAction"]);
    require __DIR__ . "/../footer.php";
    exit;
}

define("MIME_EXTENSION",0);
define("MIME_IMPORTFUNC",1);
define("MIME_EXPORTFUNC",2);
define("MIME_ALPHA",3);

$mimes = array(
    "image/png"  => array("png","imagecreatefrompng","imagepng",true),
    "image/gif"  => array("png","imagecreatefromgif","imagepng",true),
    "image/jpeg" => array("jpg","imagecreatefromjpeg","imagejpeg",false),
    "image/webp" => array("webp","imagecreatefromwebp","imagewebp",true),
);

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

        // Make sure the width and height is valid.
        if (!isset($width) or !isset($height) or ($width < 1) or ($height < 1)) {
            message($lang["userpanel.InvalidDimensions"]);
        }
        // Check for uploading errors.
        elseif (!isset($_FILES["uploadedFile"]["error"]) or (is_array($_FILES["uploadedFile"]["error"])) or ($_FILES["uploadedFile"]["error"] !== 0)) {
            message($lang["userpanel.FileUploadFail"]);
        }
        // Make sure the avatar isn't too big.
        elseif ($_FILES["uploadedFile"]["size"] > $config["maxAvatarSize"]) {
            message($lang["userpanel.FileTooBig"]);
        }
        else {
            if (array_key_exists($mime,$mimes)) {
                $w = $config["avatarWidth"];
                if ($config["avatarWidth"] >= $width) {
                    $w = $width;
                }
                $h = $config["avatarHeight"];
                if ($config["avatarHeight"] >= $height) {
                    $h = $height;
                }
                
                $originalRatio = $width/$height;

                if ($w/$h > $originalRatio) {
                    $w = $h*$originalRatio;
                }
                else {
                    $h = $w/$originalRatio;
                }

                $src = call_user_func($mimes[$mime][MIME_IMPORTFUNC],$_FILES["uploadedFile"]["tmp_name"]);
                $dst = imagecreatetruecolor($w, $h);
                
                if ($mimes[$mime][MIME_ALPHA]) {
                    imagesavealpha($dst, true);
                    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                    imagefill($dst, 0, 0, $transparent);
                }

                imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
                removeAvatar($_SESSION["userid"]);
                call_user_func($mimes[$mime][MIME_EXPORTFUNC],$dst, "avatars/" . $_SESSION["userid"] . "." . $mimes[$mime][MIME_EXTENSION]);
                
                if (file_exists("avatars/" . $_SESSION["userid"] . "." . $mimes[$mime][MIME_EXTENSION])) {
                    $db->query("UPDATE users SET avatar='".$mimes[$mime][MIME_EXTENSION]."', avataruploadtime='" . time() . "' WHERE userid='" . $_SESSION["userid"] . "'");
                    message($lang["userpanel.AvatarUploadSuccess"]);
                }
                else {
                    message($lang["userpanel.Fail"]);
                }
            }
            else {
                message($lang["userpanel.UnsupportedImageType"]);
            }
        }
    }
    elseif (isset($_POST["removeAvatar"])) {
        if (removeAvatar($_SESSION["userid"])) message($lang["userpanel.RemoveAvatarSuccess"]);
    }
}

$avatarCheck = $db->query("SELECT avatar, avataruploadtime FROM users WHERE userid='" . $_SESSION["userid"] . "'");

$avatarURL = "";

while ($a = $avatarCheck->fetch_assoc()) {
    if ($a["avatar"] != "none") {
        $avatarURL = "</br><img class='avatarPanel' src='" . genURL("avatars/" . $_SESSION["userid"] . "." . $a["avatar"] . "?t=" . $a["avataruploadtime"]) . "'>";
    }
}

echo("<div class='avatarForm'><h3>" . $lang["userpanel.ChangeAvatar"] . "</h3>" . $avatarURL . "</br>
    <form action='' method='post' enctype='multipart/form-data' class='inline-block'>
    <input type='file' name='uploadedFile' class='upload' accept='image/*'>
    <input type='submit' value='" . $lang["userpanel.UploadAvatar"] . "' name='submit'></form> <form action='' method='post' class='inline-block'><input type='submit' value='" . $lang["userpanel.RemoveAvatar"] . "' name='removeAvatar'></form>
    </div>");

if (get_role_from_session() != "Guest")
{
    update_last_action($lang["action.AvatarSettings"]);
}

?>
