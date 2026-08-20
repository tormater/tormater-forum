<?php
// signup.page.php
// Allows users to create accounts.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

if ($config["registration"] == "closed") {
    include 'header.php';
    echo '<h2>' . $lang["register.Header"] . '</h2>';
    message($lang["register.Closed"]);
    include "footer.php";
    exit;
}
if (get_role_from_session() != "Guest") {
    include 'header.php';
    echo '<h2>' . $lang["register.Header"] . '</h2>';
    message($lang["nav.AdminsOnly"]);
    include "footer.php";
    exit;
}

include 'header.php';

$data = array(
    "header" => $lang["register.Header"],
    "error" => "",
    "username_desc" => $lang["register.UsernameDesc"],
    "username_label" => $lang["register.Username"],
    "username_maxlength" => $config['maxUsernameLength'],
    "username" => @htmlspecialchars(@$_POST["user_name"]),
    "email_desc" => $lang["register.EmailDesc"],
    "email_label" => $lang["register.Email"],
    "email" => @htmlspecialchars($_POST["user_email"]),
    "password_desc" => sprintf($lang["register.PasswordDesc"], "6"),
    "password_label" => $lang["register.Password"],
    "password" => @htmlspecialchars($_POST["user_pass"]),
    "confirm_password_label" => $lang["register.PasswordConf"],
    "confirm_password" => @htmlspecialchars($_POST["user_pass_check"]),
    "captcha" => "",
    "submit" => $lang["register.Submit"]
);

function registerUser(&$data) {
    global $db, $lang, $config;
    
    if ($config["captchaEnabled"] == true and !checkCaptcha($_SESSION["captcha"],$_POST["captcha"])) {
        $data["error"] .= message($lang["register.CaptchaWrong"],true);
        return;
    }
    
    $altCheck = $db->query("SELECT ip FROM users WHERE ip='" . $db->real_escape_string(hashstring($_SERVER["REMOTE_ADDR"])) . "'");
    if ($altCheck->num_rows >= $config["maxAccountsPerIP"]) {
        $data["error"] .= message($lang["register.TooManyAccounts"],true);
        return;
    }   
    
    $data["error"] .= validateUsername(@trim(@$_POST["user_name"]));
    $data["error"] .= validateEmail(@$_POST["user_email"]);
    $data["error"] .= validatePassword(@$_POST["user_pass"], @$_POST["user_pass_check"]);
    if (strlen($data["error"])) return;
    
    $salt = "";
    $username = trim($_POST['user_name']);
    $email = $_POST['user_email'];
    $password = password_hash($_POST['user_pass'], PASSWORD_DEFAULT);
    $role = "Member";
    $jointime = time(); $lastactive = time();
    $color = '1';
    $ip = hashstring($_SERVER["REMOTE_ADDR"]);
    if ($config["registration"] == "open") $verified = "1";
    else $verified = "0";
		
    $result = $db->query("INSERT INTO users (username, email, password, role, jointime, lastactive, color, ip, salt, verified) VALUES('" . $db->real_escape_string($username) . "', '" . $db->real_escape_string($email) . "', '" . $db->real_escape_string($password) . "', '" . $db->real_escape_string($role) . "', '" . $db->real_escape_string($jointime) . "', '" . $db->real_escape_string($lastactive) . "', '" . $db->real_escape_string($color) . "', '" . $db->real_escape_string($ip) . "', '" . $db->real_escape_string($salt) . "', '" . $db->real_escape_string($verified) . "')");
						
    if (!$result) {
        $data["error"] .= $lang["error.Database"];
        return;
    }
    if ($config["registration"] == "open") {
        $result = $db->query("SELECT userid, username, role FROM users WHERE username='" . $db->real_escape_string($username) . "'");

        $row = $result->fetch_assoc();
        $_SESSION['signed_in'] = true;
        $_SESSION['userid'] = $row["userid"];
        $_SESSION['username'] = $row["username"];
        $_SESSION['role'] = $row["role"];
        redirect("userpanel");
    }
    else {
        echo '<h2>' . $lang["register.Header"] . '</h2>';
        printf($lang["register.Approval"], genURL("login"));
        include "footer.php";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') registerUser($data);

if ($config["captchaEnabled"] == true) {
    $data["captcha"] .= "<br/><div class='forminput'><label>" . $lang["register.Captcha"] . "</label><input type='text' name='captcha'></div>";
    $data["captcha"] .=  "<small class='captchalabel'>" . $lang["register.CaptchaHint"] . "</small>";
    $_SESSION["captcha"] = randomCaptcha($config["captchaLength"]);
    $data["captcha"] .= generateCaptcha($_SESSION["captcha"]);
}

echo $template->render("templates/signup/signup_form.html",$data);

include 'footer.php';
?>
