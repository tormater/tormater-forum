<?php
// EN_US.php
// English, United States language file

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$lang = array(
    "locale" => "en_US",

    "error.PageNotFound" => "Requested page not found.",
    "error.BadFields" => "Uh-oh.. a couple of fields are not filled in correctly..",
    "error.Database" => "Something went wrong. Please try again later.",
    "error.UsernameNull" => "The username field must not be empty.",
    "error.PasswordNull" => "The password field must not be empty.",
    "error.CategoryMisc" => "The category could not be displayed, please try again later.",
    "error.PasswordWrong" => "Incorrect password.",
    "error.UsernameWrong" => "The specified user doesn't exist.",
    "error.CategoryNotFound" => "This category does not exist.",
    "error.CategoryThreadMisc" => "The threads in this category could not be displayed.",
    "error.CategoryEmpty" => "There are no threads in this category yet.",
    "error.ForumEmpty" => "There are no threads in this forum yet.",
    "error.AlreadyLoggedIn" => "You are already logged in, you can <a href='%s'>log out</a> if you want.",
    "error.UserlistMembersOnly" => "Only registered users can view the userlist. If you want to see it, <a href='%s'>signup</a> or <a href='%s'>login</a>.",
    "error.UserlistDisabled" => "Sorry, the forum administrators have decided to disable the userlist. Maybe if you ask them nicely, they'll re-enable it.",
    "error.GoBack" => "Go back",
    "error.SomethingWentWrong" => "Something went wrong.",
    "error.FailedFetchUsers" => "Failed to fetch users.",
    "error.NoUsers" => "Sadly, there are currently no users on the forum.",
    "error.TooManyLogins" => "You've tried to log in too many times. Please wait a few minutes and try again later.",
    "error.MySQLNoResult" => "An error occurred whilst attempting to connect to the database, please try again later.",
    "error.NeedsApproval" => "Your account must be approved by a mod or an admin before you can log in.",
    
    "nav.AdminsOnly" => "You do not have permission to view this page.",
    "nav.LoginRequired" => "Hey, you must be <a href='%s'>logged in</a> to see this page!",
    "nav.FirstPage" => "<<",
    "nav.PrevPage" => "<",
    "nav.NextPage" => ">",
    "nav.LastPage" => ">>",
    "nav.ShowPreview" => "Preview",
    "nav.HidePreview" => "Hide preview",
    "nav.Online" => "Online",

    // Searching

    "search.Placeholder" => "Type here to search...",
    "search.Button" => "Search",
    "search.Header" => "Search results for '%s'",
    "search.NoResults" => "No thread matches that search.",
    "search.EmptySearch" => "Your search query must contain at least one character.",
    "search.TooLong" => "Your search was too long.",
    
    // Viewing:
    "action.Generic" => "Viewing: ",
    "action.Homepage" => "Viewing: Homepage",
    "action.Userlist" => "Viewing: Userlist",
    "action.CreateAThread" => "Creating a thread...",
    "action.UserProfile" => "'s Profile", // e.g. Tormater's Profile
    "action.Settings" => "Viewing: Settings",
    "action.Search" => "Searching the forum...",
    "action.Panel" => "Viewing: Admin Panel",
    "action.AccountSettings" => "Viewing: Account Settings",
    "action.ProfileSettings" => "Viewing: Profile Settings",
    "action.AvatarSettings" => "Viewing: Avatar Settings",
    
    "header.Home" => "Home",
    "header.Userlist" => "Userlist",
    "header.NewThread" => "New thread",
    "header.Panel" => "Admin Panel",
    "header.Logout" => "Logout",
    "header.Login" => "Login",
    "header.Signup" => "Sign up",
    "header.or" => " or ",
    "header.Hello" => "Hello, ",
    "header.UserPanel" => "User Panel",
    
    "homepage.CatThreads" => "Threads",
    "homepage.Threads" => "Recent Threads",
    "homepage.Cats" => "Categories",
    "homepage.Title" => "Homepage",
    
    "category.ThreadsIn" => "Threads in ",
    "category.Thread" => "Thread",
    "category.Posts" => "Posts",
    "category.CreatedBy" => "Created by",
    "category.LastPost" => "Last post",
    "category.ReplyTo" => "Re: %s",

    // Threads & Posts
    "thread.Info" => "by <a id='%s' href='%s'>%s</a>, <span title='%s'>%s</span>",
    "thread.BackToCategory" => "Back to category",
    "thread.DeleteThreadBtn" => "Delete",
    "thread.UnlockThreadBtn" => "Unlock",
    "thread.LockThreadBtn" => "Lock",
    "thread.UnstickyThreadBtn" => "Unsticky",
    "thread.StickyThreadBtn" => "Sticky",
    "thread.MoveThreadBtn" => "Move",
    "thread.PinThreadBtn" => "Pin",
    "thread.UnpinThreadBtn" => "Unpin",
    "thread.PostInTitle" => "Posts in",
    "thread.ContentTitle" => "Content:",
    "thread.HiddenBy" => "(hidden by <a href='%s' id='%s'>%s</a>)",
    "thread.QuotePost" => "Quote",
    // Labels & Buttons
    "thread.PostReplyBtn" => "Post reply",
    "thread.PostSaveDraftBtn" => "Save draft",
    "thread.PostDiscardDraftBtn" => "Discard draft",
    "thread.PublishDraftBtn" => "Publish Draft",
    "thread.Labels" => "Labels:",
    "label.Locked" => "Locked",
    "label.Sticky" => "Sticky",
    "label.Draft" => "Draft",
    "label.Pinned" => "Pinned",
    "post.RestoreHiddenBtn" => "Restore",
    "post.EditBtn" => "Edit",
    "post.HideBtn" => "Hide",
    "post.DeleteBtn" => "Delete",
    "post.SaveEditBtn" => "Save edit",
    "post.DiscardEditBtn" => "Discard edit",
    // Errors
    "thread.ThreadDoesntExist" => "The specified thread doesn't exist.",
    "thread.NoPosts" => "Couldn't find any posts.",
    "thread.ThreadsNoPosts" => "There are no posts in this thread.",
    "thread.LoginFirst" => "You must be signed in for any action within a thread.",
    "thread.PostSoon" => "You tried to post too soon after a previous post. The post delay is currently %s seconds between posts.",
    "thread.PostEmpty" => "Your post cannot be blank.",
    "thread.PostBig" => "Your post was too long. The maximum number of characters a post may contain is currently set to %s.",
    "thread.PostError" => "Your reply has not been saved, please try again later.",
    "thread.PostDeleteError" => "Sorry, post couldn't be deleted.",
    "thread.ThreadDataError" => "Something went wrong with resynchronizing the conversation. Perhaps there are no posts left?",
    "thread.PostHiddenError" => "Sorry, post couldn't be hidden.",
    "thread.PostRestoredError" => "Sorry, post couldn't be restored.",
    "thread.PostEditError" => "Hey, you don't have permission to edit that post.",
    "thread.ThreadDeleteError" => "Sorry, thread couldn't be deleted.",
    "thread.ThreadPostDeleteError" => "Sorry, the thread's posts couldn't be deleted.",
    "thread.ThreadLockError" => "Sorry, thread couldn't be locked.",
    "thread.ThreadStickError" => "Sorry, thread couldn't be stickied.",
    "thread.ThreadUnlockError" => "Sorry, thread couldn't be unlocked.",
    "thread.ThreadUnstickError" => "Sorry, thread couldn't be unstickied.",
    "thread.ThreadPinError" => "Sorry, thread couldn't be pinned.",
    "thread.ThreadUnpinError" => "Sorry, thread couldn't be unpinned",
    "thread.SuspendCantPost" => "Unfortunately, you're suspended. Suspended users cannot post.",
    "thread.ThreadLocked" => "Sorry, this thread is locked. Only moderators and administrators can post in it.",
    "thread.LoginToReply" => "You must <a href='%s'>sign in</a> or <a href='%s'>register</a> to reply to threads.",
    "thread.DraftError" => "Sorry, you've made too many draft posts recently. Please wait a minute or two and try again.",
    
    // New Thread
    "newthread.Header" => "Create a thread",
    "newthread.Title" => "Title: ",
    "newthread.Category" => "Category: ",
    "newthread.Content" => "Content:",
    "newthread.CreateBtn" => "Create thread",
    // Error
    "newthread.LoginToCreate" => "You must <a href='%s'>sign in</a> to create a thread.",
    "newthread.SuspendCantCreate" => "Your account has been suspended. You can no longer start threads.",
    "newthread.TitleEmpty" => "Your title cannot be blank.",
    "newthread.TitleBig1" => "Your title must be shorter than %s characters long.",
    "newthread.PostEmpty" => "Your post cannot be blank.",
    "newthread.PostBig1" => "Your post must be shorter than %s characters long.",
    "newthread.InvalidCategory" => "There is no category with that name.",
    "newthread.PostSoon1" => "You tried to post too soon after a previous post. The post delay is currently %s seconds between posts.",
    "newthread.CreateError" => "An error occured while creating your thread. Please try again later.",
    "newthread.InsertThreadError" => "An error occured while inserting your thread. Please try again later.",
    "newthread.InsertPostError" => "An error occured while inserting your post. Please try again later.",
    "newthread.SuccessCreate1" => "You have successfully created",
    "newthread.SuccessCreate2" => "your new thread.",
    "newthread.DataError" => "Error while selecting from database. Please try again later.",
    "newthread.NoCategoryAdmin" => "You have not created categories yet.",
    "newthread.NoCategoryUser" => "Before you can post a thread, you must wait for an admin to create some categories.",
    
    // Admin panel
    "panel.ForumSettings" => "Forum Settings",
    "panel.BasicSettings" => "Basic Settings",
    "panel.NewForumName" => "New forum name",
    "panel.ForumColor" => "Forum color",
    "panel.NewColor" => "New color",
    "panel.Reset" => "Reset to default",
    "panel.NewLang" => "Select language",
    "panel.NewTheme" => "Select theme",
    "panel.AdvancedSettings" => "Advanced Settings",
    "panel.ThreadsPerPage" => "Threads per page:",
    "panel.PostPerPage" => "Post per page:",
    "panel.Userlist" => "Enable userlist",
    "panel.UserlistMemberOnly" => "Let only members view userlist",
    "panel.showDeletedInUserlist" => "Show deleted users in the userlist",
    "panel.EnabledBtn" => "Enabled",
    "panel.ChangeFooter" => "Change footer",
    "panel.ChangeSettingsBtn" => "Change settings",
    "panel.ChangesSaved" => "Your changes were saved.",
    "panel.ChangeError" => "Some fields are left blank, or something went wrong.",
    "panel.Registration" => "Registration options",
    "panel.open" => "Open",
    "panel.closed" => "Closed",
    "panel.approval" => "Approval",
    "panel.CategoryUpBtn" => "Move up",
    "panel.CategoryDownBtn" => "Move down",

    // Admin panel / Users
    "panel.Users" => "Users",
    "panel.DeleteUser" => "Delete User",
    "panel.RestoreUser" => "Restore User",

    // Admin panel / Categories
    "panel.Categories" => "Categories",
    "panel.CreateACategory" => "Create a category",
    "panel.CategoryName" => "Category name: ",
    "panel.CategoryDescription" => "Category description: ",
    "panel.CategoryEditBtn" => "Edit",
    "panel.CategoryDeleteBtn" => "Delete",
    "panel.AddCategory" => "Add category",
    "panel.InputCategoryTitle" => "Title",
    "panel.InputCategoryDesc" => "Description",
    "panel.CreateCategoryBtn" => "Create category",
    "panel.SuccessAddedCategory" => "New category successfully added.",
    "panel.EditCategory" => "Edit category",
    "panel.EditCategoryBtn" => "Edit category",
    "panel.EditCategoryDisBtn" => "Discard edit",
    "panel.CategoryNameBlank" => "The category name cannot be blank.",
    "panel.CategoryDescBlank" => "The category description cannot be blank.",
    "panel.CategoryNameTooLong" => "The category name must be shorter than 256 characters.",
    "panel.CategoryDescTooLong" => "The category description must be shorter than 256 characters.",
    "panel.CantUpdateCategory" => "Sorry, the category couldn't be updated. Try again later.",
    "panel.SuccessUpdateCategory" => "The category was successfully updated.",
    "panel.DeleteCategory" => "Delete category",
    "panel.CategoryDataWillGone" => "All threads and posts in this category will be gone forever (a really long time)...",
    "panel.CategoryDeleteLastCheck" => "Are you sure?",
    "panel.CantDeleteCategory" => "Sorry, the category couldn't be deleted. Try again later.",
    "panel.DeleteCategoryBtn" => "Delete category",
    "panel.DeleteCategoryBackBtn" => "Go back",
    "panel.SuccessDeleteCategory" => "Successfully deleted.",
    "panel.NoNewCategoriesCreate" => "Sorry, no more new categories can be created.",
    "panel.CatThreads" => "Threads: ",

    // Admin panel / User Admin
    "panel.DangerZone" => "Danger Zone!",
    "panel.DeleteUserMessage" => "Attention, these options can permanently delete and remove any content created by a user. Use with caution.",
    "panel.DeleteKeepPosts" => "Delete user and keep posts",
    "panel.DeleteHidePosts" => "Delete user and hide posts",
    "panel.DeleteRemovePosts" => "Delete user and remove posts",
    "panel.DeleteAllUsersOnIP" => "Delete all users that share this IP address",
    "panel.DeleteAllIPSuccess" => "Successfully deleted all users on this IP address.",
    "panel.DeleteUserSuccess" => "Successfully deleted user.",
    "panel.UseridError" => "That user does not exist.",
    "panel.DeleteUserSuccessHide" => "Successfully deleted user and hid all of their posts.",
    "panel.PurgeUser" => "Purge User",
    "panel.PurgeWarning" => "Purging a user will delete all their threads, posts, and account forever (a long time!).<br/>You cannot recover a purged account.",
    "panel.sameIP" => "Accounts on the same IP as '%s'",
    "panel.noSameIP" => "There are no accounts that share the same IP as this user.",
    "panel.Administrate" => "Administrate",
    
    // Admin panel / Extensions
    "panel.Extensions" => "Extensions",
    "panel.Disable" => "Disable",
    "panel.Enable" => "Enable",
    "panel.Author" => "Author",
    "panel.Readme" => "Info",
    "panel.EnableSuccess" => "Successfully enabled extension.",
    "panel.DisableSuccess" => "Successfully disabled extension.",

    // Admin panel / Audit Log
    "panel.AuditLog" => "Audit Log",
    "panel.NoLogsFound" => "There are no logs saved on this forum.",
    "panel.UnknownError" => "An error occurred whilst trying to perform that action.<br>Please try again later.",
    "panel.LogEdit" => "%s edited post with ID '%s'",
    "panel.BeforeEdit" => "Before edit",
    "panel.AfterEdit" => "After edit",
    "panel.Restored" => "restored",
    "panel.Hid" => "hid",
    "panel.LogHide" => "%s %s post with ID '%s'<br>",
    "panel.LogDelete" => "%s deleted %s's post",
    "panel.PostContent" => "Post content",
    "panel.LogDeleteThread" => "%s deleted %s's thread titled '%s'<br>",
    
    // User register
    "register.Header" => "Sign up",
    "register.UsernameDesc" => "Username must be between 3 and 24 characters long.",
    "register.PasswordDesc" => "Password must be at least %s characters long.",
    "register.EmailDesc" => "eg. tormater@example.com",
    "register.Username" => "Username",
    "register.Email" => "Email",
    "register.Password" => "Password",
    "register.PasswordConf" => "Confirm password",
    "register.Submit" => "Register",
    "register.Success" => "Successfully registered. You can now <a href='%s'>log in</a> and start posting!",
    "error.UsernameAlphNum" => "The username can only contain alphanumeric characters.",
    "error.UsernameSmall" => "The username must be at least 3 characters.",
    "error.UsernameBig" => "The username cannot be longer than 24 characters.",
    "register.UsernameExists" => "This username already exists.",
    "register.EmailExists" => "An account with this email already exists.",
    "register.InvalidEmailFormat" => "Invalid email.",
    "register.EmailEmpty" => "The email field must not be empty.",
    "register.PasswordSmall" => "The password must be at least 6 characters.",
    "register.PasswordEmpty" => "The password field must not be empty.",
    "register.ConfirmPasswordEmpty" => "The confirm password field must not be empty.",
    "register.ConfirmPasswordWrong" => "Your passwords do not match.",
    "register.TooManyAccounts" => "You've created too many accounts. Try logging into an existing one instead.",
    "register.Captcha" => "Captcha",
    "register.CaptchaHint" => "Type the letters and numbers you see in the captcha image.",
    "register.CaptchaWrong" => "The captcha was filled out incorrectly.",
    "register.EmailShort" => "The email cannot be less than 1 character in length.",
    "register.EmailLong" => "The email cannot be greater than 254 characters in length.",
    "register.Approval" => "Successfully registered. Your account must be approved by a mod or an admin before you can <a href='%s'>log in</a> and start posting. Come back and check in on your account later on.",
    "register.Closed" => "Sorry, account registration is disabled on this forum. Try coming back and checking in again later.",
    
    // User login
    "login.Header" => "Log in",
    "login.Username" => "Username or Email",
    "login.Password" => "Password",
    "login.Submit" => "Log in",
    "login.Welcome" => "Welcome, %s. <a href='%s'>Proceed to the forum overview</a>.",

    // User page
    "user.FaildFindUser" => "Sorry, that user couldn't be displayed. Try again later.",
    "user.NoSuchUser" => "Sorry, that user doesn't exist.",
    "user.FaildChangeRole" => "Failed to change role.",
    "user.ViewingProfile" => "Viewing profile ",
    "user.OptionAdmin" => "Administrator",
    "user.OptionMod" => "Moderator",
    "user.OptionMember" => "Member",
    "user.OptionSuspended" => "Suspended",
    "user.ChangeRole" => "Change role",
    "user.TitleRegistered" => "Registered:",
    "user.TitleLastActive" => "Last active:",
    "user.TitlePosts" => "Posts:",
    "user.TitleThreads" => "Threads:",
    "user.TitleVerified" => "Verified:",
    "user.TitleDeleted" => "Deleted: ",
    "user.VerifiedYes" => "Yes",
    "user.VerifiedNo" => "No",
    "user.DeletedYes" => "Yes",
    "user.DeletedNo" => "No",
    "user.Deleted" => "deleted",
    "user.UserInformation" => "User Information",
    "user.RecentPosts" => "Recent Posts",
    "user.ViewThread" => "View Thread",

    // Roles
    "role.Admin" => "Administrator",
    "role.Mod" => "Moderator",
    "role.Member" => "Member",
    "role.Suspend" => "Suspended",

    "role.Administrator" => "Administrator",
    "role.Moderator" => "Moderator",
    "role.Member" => "Member",
    "role.Suspended" => "Suspended",

    // User settings
    "settings.LoginFirst" => "You must be logged in to change user settings.",
    "settings.CurrentPassword" => "Current password",

    // Set post color
    "settings.PostColor" => "Post color",
    "settings.SetColorError" => "Sorry, couldn't set requested color.",
    "settings.SetColorSuccess" => "Successfully set post color.",

    // Change username
    "settings.ChangeUsername" => "Change Username",
    "settings.NewUsername" => "New username",
    "settings.ChangeUsernameBtn" => "Change Username",
    "settings.NewUsernameEmpty" => "You must enter a username.",
    "settings.PasswordEmpty" => "You must enter a password.",
    "settings.NewUsernameSame" => "New username cannot be the same as old username.",
    "settings.NewUsernameBig" => "New username cannot be longer than 24 characters.",
    "settings.NewUsernameNonAlph" => "New username cannot contain non-alphanumeric characters.",
    "settings.NewUsernameError" => "Unable to change username.",
    "settings.NewUsernameChanged" => "Username has been changed.",

    // Change password
    "settings.ChangePassword" => "Change Password",
    "settings.NewPassword" => "New password",
    "settings.ConfirmNewPassword" => "Confirm new password",
    "settings.ChangePasswordBtn" => "Change Password",
    "settings.NewPasswordEmpty" => "New password cannot be empty.",
    "settings.ConfirmNewPasswordEmpty" => "Confirm new password cannot be empty.",
    "settings.OldPasswordEmpty" => "Current password cannot be empty.",
    "settings.ConfirmNewPasswordError" => "Confirm new password is not the same.",
    "settings.ChangePasswordError" => "Unable to change password.",
    "settings.ChangePasswordChanged" => "Password has been changed.",

    // Time
    "time.Never" => "Never",
    "time.JustNow" => "Just now",
    "time.1SecAgo" => "1 second ago",
    "time.SecAgo" => "%s seconds ago",
    "time.1MinAgo" => "1 minute ago",
    "time.MinAgo" => "%s minutes ago",
    "time.1HrAgo" => "1 hour ago",
    "time.HrsAgo" => "%s hours ago",
    "time.1DayAgo" => "1 day ago",
    "time.DaysAgo" => "%s days ago",
    "time.1WeekAgo" => "1 week ago",
    "time.WeeksAgo" => "%s weeks ago",
    "time.1MonthAgo" => "1 month ago",
    "time.MonthsAgo" => "%s months ago",
    "time.1YearAgo" => "1 year ago",
    "time.YearsAgo" => "%s years ago",
    
    // Page bar
    "page.homepage" => "Home",
    "page.userlist" => "Users",
    "page.login" => "Login",
    "page.signup" => "Register",
    "page.settings" => "Settings",
    "page.newthread" => "New Thread",
    "page.panel" => "Admin Panel",
    "page.panel.category" => "Categories",
    "page.panel.user" => "Users",
    "page.panel.extensions" => "Extensions",
    "page.panel.auditlog" => "Audit Log",
    "page.upgrade" => "Upgrader",
    "page.userpanel" => "User Panel",
    "page.userpanel.accountsettings" => "Account Settings",
    "page.userpanel.profilesettings" => "Profile Settings",
    "page.search" => "Search",
    "page.useradmin" => "User Admin",

    // Upgrade
    "upgrade.Success" => "The forum was successfully upgraded.",
    "upgrade.None" => "No upgrades to peform!",
    
    // User panel
    "userpanel.AccountSettings" => "Account Settings",
    "userpanel.ProfileSettings" => "Profile Settings",
    "userpanel.AvatarSettings" => "Avatar Settings",
    "userpanel.Signature" => "Signature",
    "userpanel.Bio" => "Bio",
    "userpanel.UpdateSignature" => "Update Signature",
    "userpanel.UpdateBio" => "Update Bio",
    "userpanel.CurrentAvatar" => "Current Avatar",
    "userpanel.ChangeAvatar" => "Change Avatar",
    "userpanel.UploadAvatar" => "Upload Avatar",
    "userpanel.RemoveAvatar" => "Remove Avatar",
    "userpanel.RemoveAvatarSuccess" => "Successfully removed avatar.",
    "userpanel.AvatarUploadSuccess" => "Successfully uploaded avatar.",
    "userpanel.AvatarUploadsDisabled" => "Sorry, avatar uploads have been disabled.",
    "userpanel.NoAvatar" => "None.",
    "userpanel.WaitToUpload" => "You've already uploaded an avatar too recently. Please wait a few minutes and try again.",
    "userpanel.UnsupportedImageType" => "Error: unsupported image type. Make sure your image is a png/gif/jpg/webp.",
    "userpanel.InvalidDimensions" => "Error: invalid image width/height.",
    "userpanel.FileUploadFail" => "Error: file upload failed.",
    "userpanel.FileTooBig" => "Error: avatar filesize too large.",
    "userpanel.Fail" => "Error: failed to upload avatar. Make sure it's a valid image file.",

    // BBCode
    "BBCode.Bold" => "B",
    "BBCode.Italic" => "I",
    "BBCode.Underline" => "U",
    "BBCode.Strikethrough" => "S",
    "BBCode.Header" => "H",
    "BBCode.Link" => "URL",
    "BBCode.Image" => "Image",
    "BBCode.Spoiler" => "Spoiler",
    "BBCode.Code" => "</>",
    
    // Userlist
    "userlist.Approve" => "Approve",

    // Installer
    "installer.Title" => "Tormater Forum Installer",
    "installer.ConfigWarning" => "Warning: config directory isn't writeable. Make sure it exists and its permissions are set properly.",
    "installer.AvatarWarning" => "Warning: avatars directory isn't writeable. Make sure it exists and its permissions are set properly.",
    "installer.SQLMissing" => "Error: Please input SQL details.",
    "installer.SQLConnectFail" => "Error: SQL connection failed.",
    "installer.DBAlreadyExists" => "Error: SQL database has already been written. Create a new database or drop all forum-related tables from the existing one and try again.",
    "installer.UsernameTooShort" => "Error: Your username must be at least 1 character in length.",
    "installer.UsernameTooLong" => "Error: Your username is too long.",
    "installer.PasswordTooShort" => "Error: Your password must be at least 1 character in length.",
    "installer.EmailTooShort" => "Error: Your email must be at least 1 character in length.",
    "installer.EmailTooLong" => "Error: Your email is too long.",
    "installer.PasswordsMismatch" => "Error: Your passwords don't match.",
    "installer.GeneralCategoryName" => "General",
    "installer.GeneralCategoryDescription" => "Tormater Forum's default category.",
    "installer.InstallSuccess" => "Database and config successfully written. You can now view your forum.",
    "installer.ConfigWriteFail" => "Error: failed to write config file. Make sure the config directory exists and is writeable.",
    "installer.Welcome" => "<h1>Welcome</h1>
Welcome to the Tormater Forum Installer. In order to proceed, you must fill out your database settings, and your forum administrator account credentials below.
<br>If you need assistance with Tormater Forum, feel free to make an <a href='https://github.com/tormater/tormater-forum/issues'>issue</a> on our GitHub, or a <a href='http://forum.tormater.com/'>thread</a> on our forum.",
    "installer.SQLDetails" => "MySQL Details",
    "installer.SQLServer" => "MySQL Server",
    "installer.SQLDatabase" => "MySQL Database",
    "installer.SQLUser" => "MySQL User",
    "installer.SQLPassword" => "MySQL Password",
    "installer.AdministratorAccount" => "Administrator Account",
    "installer.InstallButton" => "Install Tormater Forum",
);

?>
