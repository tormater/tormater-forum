<?php
// login.page.php
// Logs the user in.

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$next = "";
if (isset($_GET["next"])) {
    $next = $_GET["next"];
    $next = urldecode($next);
}

if (isset($_SESSION['signed_in']) && ($_SESSION['signed_in'] == true))
{
    include 'header.php';
			                     
    $data = array(
    "title" => $lang["login.Header"],
    "message" => message(sprintf($lang["error.AlreadyLoggedIn"], genURL("logout")), true)
    );
    echo $template->render("templates/generic/page_message.html", $data);
    include 'footer.php';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') 
{
    include 'header.php';
	        
    $data = array(
      "title" => $lang["login.Header"],
      "username" => $lang["login.Username"],
      "password" => $lang["login.Password"],
      "submit" => $lang["login.Submit"]
    );
    echo $template->render("templates/login/login_form.html", $data);
    include 'footer.php';
    exit();
}

$errors = array();

if (!isset($_POST['user_name']) || strlen($_POST['user_name']) == 0) {
    $errors[] = $lang["error.UsernameNull"];
}
		
if (!isset($_POST['user_pass']) || strlen($_POST['user_pass']) == 0) {
    $errors[] = $lang["error.PasswordNull"];
}
		
$db->query("DELETE FROM logins WHERE time<" . time() . "-300");

$logins = $db->query("SELECT * FROM logins WHERE ip='" . $db->real_escape_string(hashstring($_SERVER["REMOTE_ADDR"])) . "'");

if ($logins->num_rows >= $config["maxLoginAttempts"]) {
    $errors[] = $lang["error.TooManyLogins"];
}
		
if (count($errors)) {
    include 'header.php';
    $data = array(
       "title" => $lang["login.Header"],
       "badfields" => $lang["error.BadFields"],
       "errors" => "",
       "go_back" => $lang["error.GoBack"]
    );
                        		
    foreach($errors as $key => $value) {
      $data["errors"] .= $template->render("templates/login/login_error.html", array("error" => $value));
    }
    echo $template->render("templates/login/login_errors.html", $data);
    include 'footer.php';
    exit();
}

$username = $db->real_escape_string($_POST['user_name']);
$resemail = $db->query("SELECT username FROM users WHERE email = '" . $username . "'");

if (strpos($username, '@') !== "0") {
    while($rowe = $resemail->fetch_assoc()) {
        $username = $rowe["username"];
    }
}

$res = $db->query("SELECT salt FROM users WHERE username = '" . $username . "'");
if (!$res) {
    $errors[] = $lang["error.Database"];
}

if (!$res->num_rows) {
    $errors[] = $lang["error.UsernameWrong"];
}

$row = $res->fetch_assoc();
$salt = $row["salt"];

$hash = $db->real_escape_string(hashstring($salt . $_POST["user_pass"]));
$result = $db->query("SELECT userid, username, role, password, verified FROM users WHERE username = '" . $username . "'");

if (!$result) {
    $errors[] = $lang["error.Database"];
}
else {
    if (!$result->num_rows) {
         $errors[] = $lang["error.UsernameWrong"];
    }
    $row = $result->fetch_assoc();
    $userid = $row["userid"];
    $username = $row["username"];
    $role = $row["role"];
    if ($row["verified"] == "0") {
        $errors[] = $lang["error.NeedsApproval"];
    }
    
    // If the password doesn't match the hash in the database, we need some additional checks.
    if ($row["password"] != $hash) {
        // Check if the hash is a bcrypt hash.
        if (!password_verify($_POST["user_pass"], $row["password"])) {
            $errors[] = $lang["error.PasswordWrong"];
        }
    }
    // If it does, we want to upgrade it to a bcrypt hash.
    else {
                        $db->query("UPDATE users SET password='" . $db->real_escape_string(password_hash($_POST["user_pass"], PASSWORD_DEFAULT)) . "' WHERE userid='" . $db->real_escape_string($userid) . "'");
    }
}

if (count($errors)) {
    $db->query("INSERT INTO logins (ip, time) VALUES ('" . $db->real_escape_string(hashstring($_SERVER["REMOTE_ADDR"])) . "', '" . time() . "')");
				
    include 'header.php';
    $data = array(
       "title" => $lang["login.Header"],
       "badfields" => $lang["error.BadFields"],
       "errors" => "",
       "go_back" => $lang["error.GoBack"]
    );
                        		
    foreach($errors as $key => $value) {
        $data["errors"] .= $template->render("templates/login/login_error.html", array("error" => $value));
    }
    echo $template->render("templates/login/login_errors.html", $data);
    include 'footer.php';
    exit();
}

$_SESSION['signed_in'] = true;
$_SESSION['userid'] = $userid;
$_SESSION['username'] = $username;
$_SESSION['role'] = $role;
					
$db->query("UPDATE users SET ip='" . $db->real_escape_string(hashstring($_SERVER["REMOTE_ADDR"])) . "' WHERE userid='" . $_SESSION["userid"] . "'");
header("Refresh:1; url=" . genURL($next));
include 'header.php';
			                     
$data = array(
  "title" => $lang["login.Header"],
  "message" => message(sprintf($lang["login.Welcome"], htmlspecialchars($_SESSION['username']), genURL($next)),true)
);
echo $template->render("templates/generic/page_message.html", $data);

include 'footer.php';

?>
