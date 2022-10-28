<?php
// EN_US.php
// English, United States language file

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$lang = array(
"error.PageNotFound" => "Requested page not found.",
"error.BadFields" => "Uh-oh.. a couple of fields are not filled in correctly..",
"error.Database" => "Something went wrong. Please try again later.",
"error.UsernameAlphNum" => "The username can only contain alphanumeric characters.",
"error.UsernameSmall" => "The username must be at least 3 characters.",
"error.UsernameBig" => "The username cannot be longer than 24 characters.",
"error.UsernameNull" => "The username field must not be empty.",
"error.PasswordNull" => "The password field must not be empty.",
"error.CategoryMisc" => "The category could not be displayed, please try again later.",
"error.PasswordWrong" => "Incorrect password.",
"error.UsernameWrong" => "The specified user doesn't exist.",
"error.CategoryNotFound" => "This category does not exist.",
"error.CategoryThreadMisc" => "The threads in this category could not be displayed.",
"error.CategoryEmpty" => "There are no threads in this category yet.",
"error.AlreadyLoggedIn" => "You are already logged in, you can <a href='/logout/'>log out</a> if you want.",

"nav.AdminsOnly" => "Sorry, this page is unavailable to non-admins.",
"nav.FirstPage" => "First Page",
"nav.PrevPage" => "Previous Page",
"nav.NextPage" => "Next Page",
"nav.LastPage" => "Last Page",

"label.Locked" => "Locked",
"label.Sticky" => "Sticky",

"action.Generic" => "Viewing: ",
"action.Homepage" => "Viewing: Homepage",
"action.Panel" => "Viewing: Admin Panel",

"header.Home" => "Home",
"header.Userlist" => "Userlist",
"header.Settings" => "Settings",
"header.NewThread" => "Create a thread",
"header.NewCategory" => "Create a category",
"header.Panel" => "Admin Panel",
"header.Logout" => "Logout",
"header.Login" => "Login",
"header.Signup" => "Sign up",

"homepage.CatThreads" => "Threads: ",

"category.ThreadsIn" => "Threads in ",
"category.Thread" => "Thread",
"category.Posts" => "Posts",
"category.CreatedBy" => "Created by",
"category.LastPost" => "Last post",

"panel.ForumSettings" => "Forum Settings",
"panel.ChangeForumName" => "Change forum name",
"panel.NewForumName" => "New forum name",
"panel.ChangeFooter" => "Change footer",
"panel.NewFooter" => "New footer",
"panel.ChangeTheme" => "Change theme",
"panel.NewTheme" => "Select theme",
"panel.ChangeLang" => "Change language",
"panel.NewLang" => "Select language",

"register.Header" => "Sign up",
"register.Username" => "Username",
"register.UsernameDesc" => "(3-24 characters, alphanumeric)",
"register.Email" => "Email",
"register.Password" => "Password",
"register.PasswordDesc" => "(minimum ",
"register.PasswordDesc2" => " characters)",
"register.PasswordConf" => "Confirm Password",
"register.Submit" => "Register",
"register.Success" => "Successfully registered. You can now <a href='login'>log in</a> and start posting!",

"login.Header" => "Log in",
"login.Username" => "Username/Email",
"login.Password" => "Password",
"login.Submit" => "Log in",
"login.WelcomeStart" => "Welcome, ",
"login.WelcomeEnd" => ". <a href='/'>Proceed to the forum overview</a>.",
);

?>
