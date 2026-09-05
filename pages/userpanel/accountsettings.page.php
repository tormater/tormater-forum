<?php
// userpanelaccountsettings.page.php
// Allows the user to change some account-related settings.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $res = $db->query("SELECT password FROM users WHERE username = '" . $db->real_escape_string($_SESSION['username']) . "'");
    if(!$res) {
        $errors[] = $lang["error.Database"];
    }
    while($row = $res->fetch_assoc()) {
        $password = $row["password"];
    }
    $errors = array();
    if (isset($_POST["newusername"])) {
        $newusername = trim(@$_POST["newusername"]);
        if (!isset($_POST["confirmpass"])) {
            $errors[] = $lang["settings.PasswordEmpty"];
        }
        else if (!password_verify($_POST["confirmpass"], $password)) {
            $errors[] = $lang["error.PasswordWrong"];
        }
        else {
            $error = validateUsername($newusername);
            if (strlen($error)) $errors[] = $error;
        }
        if (empty($errors)) {
            $result = $db->query("UPDATE users SET username='" . $db->real_escape_string($_POST["newusername"]) . "' WHERE userid='" . $_SESSION["userid"] . "'");
            if (!$result) {
                $page_output .= message($lang["settings.NewUsernameError"],true);
            }
            else {
                $_SESSION["username"] = $_POST["newusername"];
                $page_output .= message($lang["settings.NewUsernameChanged"],true);
                return;
            }
        }
    }
	
    if (isset($_POST["newpass"])) {
        $newpass = $_POST["newpass"];
        $confirmnewpass = $_POST["confirmnewpass"];
        if(empty($newpass)) {
            $errors[] = $lang["settings.NewPasswordEmpty"];
        }
        elseif (empty($confirmnewpass)) {
            $errors[] = $lang["settings.ConfirmNewPasswordEmpty"];
        }
        elseif (empty($_POST["oldpass"])) {
            $errors[] = $lang["settings.OldPasswordEmpty"];
        }
        elseif ($newpass != $confirmnewpass) {
            $errors[] = $lang["settings.ConfirmNewPasswordError"];
        }
        elseif (!password_verify($_POST["oldpass"], $password)) {
            $errors[] = $lang["error.PasswordWrong"];
        }
        else {
            $salt = "";
            $password = password_hash($_POST["newpass"], PASSWORD_DEFAULT);
            $result = $db->query("UPDATE users SET password ='" . $db->real_escape_string($password) . "', salt = '" . $db->real_escape_string($salt) . "'    WHERE userid='" . $_SESSION["userid"] . "'");
            if (!$result) {
                $page_output .=  message($lang["settings.ChangePasswordError"],true);
            }
            else {
                $page_output .=  message($lang["settings.ChangePasswordChanged"],true);
                refresh(1.5);
                return;
            }
        }
    }
	
    if (!empty($errors)) {
        $page_output .=   $lang["error.BadFields"];
        $page_output .=   '<ul>';
        foreach($errors as $key => $value) {
            $page_output .=   '<li>' . $value . '</li>';
        }
        $page_output .=   '</ul>';
        $page_output .=   '<a class="buttonbig" href="javascript:history.back()">'.$lang["error.GoBack"].'</a>';
        return;
    }
}

// Display the change username form.
$page_output .= '</br><h3>'.$lang["settings.ChangeUsername"].'</h3><div class="formcontainer">
<form method="post" action="">
<div class="forminput"><label>'.$lang["settings.NewUsername"].'</label><input type="text" name="newusername"></div>
<div class="forminput"><label>'.$lang["settings.CurrentPassword"].'</label><input type="password" name="confirmpass"></div>
<div class="forminput"><label></label><input type="submit" class="buttonbig" value="'.$lang["settings.ChangeUsernameBtn"].'"></div></form></div>';

// Display the change password form.
$page_output .=  '<h3>'.$lang["settings.ChangePassword"].'</h3><div class="formcontainer">
<form method="post" action="">
<div class="forminput"><label>'.$lang["settings.CurrentPassword"].'</label><input type="password" name="oldpass"></div>
<div class="forminput"><label>'.$lang["settings.NewPassword"].'</label><input type="password" name="newpass"></div>
<div class="forminput"><label>'.$lang["settings.ConfirmNewPassword"].'</label><input type="password" name="confirmnewpass"></div>
<div class="forminput"><label></label><input type="submit" class="buttonbig" value="'.$lang["settings.ChangePasswordBtn"].'"></div></form></div>';

if (get_role_from_session() != "Guest")
{
    update_last_action($lang["action.AccountSettings"]);
}

?>
