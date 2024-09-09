<?php
// FA_IR.php
// Persian language file,
// Translated by Pourdaryaei@yandex.com

// Only load the page if it's being loaded through the index.php file.
if (!defined("INDEXED")) exit;

$lang = array(
    "locale" => "fa_IR",

    "error.PageNotFound" => "صفحه درخواستی یافت نشد.",
    "error.BadFields" => "اوه اوه... یکی دوتا از کادرها به‌درستی پر نشدن...",
    "error.Database" => "مشکلی پیش آمد. لطفاً بعداً دوباره امتحان کنید.",
    "error.UsernameNull" => "کادر نام کاربری نباید خالی باشد.",
    "error.PasswordNull" => "کادر کلمه عبور نباید خالی باشد.",
    "error.CategoryMisc" => "دسته نمایش داده نشد، لطفاً بعداً دوباره امتحان کنید.",
    "error.PasswordWrong" => "کلمه عبور نادرست است.",
    "error.UsernameWrong" => "کاربر مشخص شده وجود ندارد.",
    "error.CategoryNotFound" => "این دسته وجود ندارد.",
    "error.CategoryThreadMisc" => "موضوعات این دسته‌بندی نمایش داده نمی‌شوند.",
    "error.CategoryEmpty" => "هنوز هیچ موضوعی در این دسته‌بندی جود ندارد.",
    "error.ForumEmpty" => "هنوز هیچ موضوعی در این انجمن وجود ندارد.",
    "error.AlreadyLoggedIn" => "شما قبلاً وارد انجمن شده‌اید، در صورت تمایل می‌توانید <a href='%s'> از انجمن خارج شوید </a>.",
    "error.UserlistMembersOnly" => "فقط اعضای انجمن می‌توانند لیست کاربری را مشاهده کنند. اگر می‌خواهید آن را ببینید، <a href='%s'> ثبت‌نام </a> کنید و یا <a href='%s'> وارد </a> شوید.",
    "error.UserlistDisabled" => "با عرض پوزش، مدیران انجمن تصمیم گرفته‌اند لیست کاربری را غیرفعال کنند. شاید اگر به‌خوبی از آنها درخواست کنید، دوباره آن را فعال کنند.",
    "error.GoBack" => "بازگشت",
    "error.SomethingWentWrong" => "یک مشکل رخ داد.",
    "error.FailedFetchUsers" => "در واکشی کاربر خطایی رخ داد.",
    "error.NoUsers" => "متأسفانه، در حال حاضر هیچ کاربری در انجمن وجود ندارد.",
    "error.TooManyLogins" => "شما بارها سعی کرده‌اید وارد انجمن شوید. لطفاً چند دقیقه صبر کنید و بعداً دوباره امتحان کنید.",
    "error.MySQLNoResult" => "هنگام تلاش برای اتصال به پایگاه‌داده خطایی رخ داد، لطفاً بعداً دوباره امتحان کنید.",
    "error.NeedsApproval" => "قبل از اینکه بتوانید وارد انجمن شوید، حساب کاربری شما باید توسط یک ناظم و یا مدیر انجمن تأیید شود.",
    
    "nav.AdminsOnly" => "شما اجازه مشاهده این صفحه را ندارید.",
    "nav.LoginRequired" => "سلام، برای دیدن این صفحه شما بایستی  <a href='%s'>وارد انجمن</a> شده باشید!",
    "nav.FirstPage" => "<<",
    "nav.PrevPage" => "<",
    "nav.NextPage" => ">",
    "nav.LastPage" => ">>",
    "nav.ShowPreview" => "پیش‌نمایش",
    "nav.HidePreview" => "پنهان کردن پیش‌نمایش",
    "nav.Online" => "آنلاین",

    // Searching

    "search.Placeholder" => "برای جستجو اینجا را تایپ کنید...",
    "search.Button" => "جستجو",
    "search.Header" => "نتایج جستجوی '%s'",
    "search.NoResults" => "هیچ موضوعی با جستجوی شما مطابقت ندارد.",
    "search.EmptySearch" => "جستجوی شما بایستی حداقل شامل یک کاراکتر باشد.",
    "search.TooLong" => "عبارت جستجوی شما خیلی طولانی است.",
    
    // Viewing:
    "action.Generic" => "در حال مشاهده: ",
    "action.Homepage" => "در حال مشاهده: صفحه اصلی",
    "action.Userlist" => "در حال مشاهده: لیست کاربران",
    "action.CreateAThread" => "در حال ایجاد ساخت یک موضوع...",
    "action.UserProfile" => "پروفایل", // e.g. Tormater's Profile
    "action.Settings" => "در حال مشاهده: تنظیمات",
    "action.Search" => "جستجوی برای انجمن...",
    "action.Panel" => "در حال مشاهده: پنل مدیریت",
    "action.AccountSettings" => "در حال مشاهده: تنظیمات حساب کاربری",
    "action.ProfileSettings" => "در حال مشاهده: تنظیمات پروفایل",
    "action.AvatarSettings" => "در حال مشاهده: تنظیمات آواتار",
    
    "header.Home" => "فهرست",
    "header.Userlist" => "لیست کاربران",
    "header.NewThread" => "موضوع جدید",
    "header.Panel" => "پنل مدیریت",
    "header.Logout" => "خروج",
    "header.Login" => "ورود",
    "header.Signup" => "عضویت",
    "header.or" => " یا ",
    "header.Hello" => "خوش اومدی ",
    "header.UserPanel" => "پنل کاربر",
    
    "homepage.CatThreads" => "موضوعات",
    "homepage.Threads" => "آخرین موضوعات",
    "homepage.Cats" => "دسته بندی‌",
    "homepage.Title" => "صفحه اصلی",
    
    "category.ThreadsIn" => "موضوعات ",
    "category.Thread" => "موضوع",
    "category.Posts" => "نوشته‌ها",
    "category.CreatedBy" => "نویسنده",
    "category.LastPost" => "آخرین نوشته",
    "category.ReplyTo" => "پاسخ: %s",

    // Threads & Posts
    "thread.Info" => "نوشته <a id='%s' href='%s'>%s</a> در <span title='%s'>%s</span>",
    "thread.BackToCategory" => "بازگشت",
    "thread.DeleteThreadBtn" => "حذف",
    "thread.UnlockThreadBtn" => "حذف قفل",
    "thread.LockThreadBtn" => "قفل کردن",
    "thread.UnstickyThreadBtn" => "مهم نیست",
    "thread.StickyThreadBtn" => "نوشته مهم",
    "thread.MoveThreadBtn" => "انتقال",
    "thread.PinThreadBtn" => "سنجاق زدن",
    "thread.UnpinThreadBtn" => "حذف سنجاق",
    "thread.PostInTitle" => "نوشته‌های در",
    "thread.ContentTitle" => "متن پیام:",
    "thread.HiddenBy" => "(پنهان شده توسط <a href='%s' id='%s'>%s</a>)",
    "thread.QuotePost" => "نقل قول",
    // Labels & Buttons
    "thread.PostReplyBtn" => "ارسال پاسخ",
    "thread.PostSaveDraftBtn" => "ذخیره پیش‌نویس",
    "thread.PostDiscardDraftBtn" => "لغو پیش‌نویس",
    "thread.PublishDraftBtn" => "انتشار پیش‌نویس",
    "thread.Labels" => "برچسب‌ها:",
    "label.Locked" => "قفل",
    "label.Sticky" => "موضوع مهم",
    "label.Draft" => "پیش‌نویس",
    "label.Pinned" => "سنجاق شده",
    "post.RestoreHiddenBtn" => "بازیابی",
    "post.EditBtn" => "ویرایش",
    "post.HideBtn" => "پنهان کردن",
    "post.DeleteBtn" => "حذف",
    "post.SaveEditBtn" => "ویرایش ذخیره",
    "post.DiscardEditBtn" => "لغو ویرایش",
    // Errors
    "thread.ThreadDoesntExist" => "موضوع مشخص شده وجود ندارد.",
    "thread.NoPosts" => "هیچ نوشته‌ای یافت نشد.",
    "thread.ThreadsNoPosts" => "هیچ نوشته‌ای در این موضوع وجود ندارد.",
    "thread.LoginFirst" => "برای هر اقدامی بر روی این موضوع اول باید وارد انجمن شوید.",
    "thread.PostSoon" => "شما سعی دارید خیلی زود پس از ارسال قبلی دوباره ارسال کنید. تأخیر ارسال در حال حاضر %s ثانیه بین ارسال‌ها است.",
    "thread.PostEmpty" => "ارسال شما نمی‌تواند خالی باشد.",
    "thread.PostBig" => "ارسال شما خیلی طولانی است، حداکثر تعداد کاراکترهایی که ممکن است یک ارسال داشته باشد در حال حاضر روی %s  کاراکتر تنظیم شده است.",
    "thread.PostError" => "پاسخ شما ذخیره نشده است، لطفاً بعداً دوباره امتحان کنید.",
    "thread.PostDeleteError" => "با عرض پوزش، نوشته حذف نشد.",
    "thread.ThreadDataError" => "مشکلی در همگام‌سازی مجدد مکالمه پیش آمد. شاید هیچ پستی باقی نمانده باشد؟",
    "thread.PostHiddenError" => "با عرض پوزش، نوشته پنهان نشد.",
    "thread.PostRestoredError" => "با عرض پوزش، نوشته قابل بازیابی نیست.",
    "thread.PostEditError" => "شما اجازه ویرایش آن نوشته را ندارید.",
    "thread.ThreadDeleteError" => "با عرض پوزش، موضوع حذف نشد.",
    "thread.ThreadPostDeleteError" => "با عرض پوزش، نوشته‌های موضوع حذف نشدند.",
    "thread.ThreadLockError" => "با عرض پوزش، موضوع قفل نشد.",
    "thread.ThreadStickError" => "با عرض پوزش، موضوع نمی‌تواند مهم شود.",
    "thread.ThreadUnlockError" => "با عرض پوزش، موضوع باز نشد.",
    "thread.ThreadUnstickError" => "با عرض پوزش، موضوع از حالت مهم بودن خارج نشد.",
    "thread.ThreadPinError" => "با عرض پوزش، موضوع سنجاق نشد.",
    "thread.ThreadUnpinError" => "با عرض پوزش، موضوع از حالت سنجاق خارج نشد",
    "thread.SuspendCantPost" => "متأسفانه، شما مسدود شده‌اید. کاربران مسدود نمی‌توانند ارسال کنند.",
    "thread.ThreadLocked" => "با عرض پوزش، این موضوع قفل شده است. فقط ناظرین و مدیران می‌توانند در آن نوشته‌ای ارسال کنند.",
    "thread.LoginToReply" => "برای پاسخ به موضوع بایستی به انجمن <a href='%s'>وارد شوید</a> و یا <a href='%s'>ثبت نام</a> کنید.",
    "thread.DraftError" => "متأسفیم، شما اخیراً نوشته‌های پیش‌نویس زیادی ایجاد کرده‌اید. لطفاً یک یا دو دقیقه صبر کرده و دوباره امتحان کنید.",
    
    // New Thread
    "newthread.Header" => "ایجاد موضوع",
    "newthread.Title" => "عنوان: ",
    "newthread.Category" => "دسته: ",
    "newthread.Content" => "متن نوشته:",
    "newthread.CreateBtn" => "ایجاد موضوع",
    // Error
    "newthread.LoginToCreate" => "برای ایجاد موضوع بایستی <a href='%s'>وارد انجمن</a>شوید.",
    "newthread.SuspendCantCreate" => "حساب شما معلق شده است. دیگر نمی‌توانید موضوعی ایجاد کنید.",
    "newthread.TitleEmpty" => "عنوان شما نمی‌تواند خالی باشد.",
    "newthread.TitleBig1" => "عنوان شما باید کوتاه‌تر از %s کاراکتر باشد.",
    "newthread.PostEmpty" => "نوشته شما نمی‌تواند خالی باشد.",
    "newthread.PostBig1" => "نوشته شما باید کوتاه‌تر از %s کاراکتر باشد.",
    "newthread.InvalidCategory" => "هیچ دسته‌بندی با این نام وجود ندارد.",
    "newthread.PostSoon1" => "شما سعی کردید خیلی زود پس از ارسال قبلی نوشته جدیدی ارسال کنید. تأخیر ارسال در حال حاضر %s ثانیه بین نوشته‌ها است.",
    "newthread.CreateError" => "هنگام ایجاد موضوع شما خطایی رخ داد. لطفاً بعداً دوباره امتحان کنید.",
    "newthread.InsertThreadError" => "هنگام درج موضوع شما خطایی روی داد. لطفاً بعداً دوباره امتحان کنید.",
    "newthread.InsertPostError" => "هنگام درج نوشته شما خطایی روی داد. لطفاً بعداً دوباره امتحان کنید.",
    "newthread.SuccessCreate1" => "شما با موفقیت موضوع",
    "newthread.SuccessCreate2" => "جدید خود را ایجاد کردید.",
    "newthread.DataError" => "خطا هنگام انتخاب از پایگاه‌داده. لطفاً بعداً دوباره امتحان کنید.",
    "newthread.NoCategoryAdmin" => "شما هنوز هیچ دسته‌بندی ایجاد نکرده‌اید.",
    "newthread.NoCategoryUser" => "قبل از اینکه بتوانید موضوعی را ارسال کنید، باید منتظر بمانید تا مدیر انجمن دسته‌بندی ای ایجاد کند.",
    
    // Admin panel
    "panel.ForumSettings" => "تنظیمات انجمن",
    "panel.BasicSettings" => "تنظیمات اولیه",
    "panel.NewForumName" => "نام جدید انجمن",
    "panel.ForumColor" => "رنگ انجمن",
    "panel.NewColor" => "رنگ جدید",
    "panel.Reset" => "تنظیم به حالت پیش‌فرض",
    "panel.NewLang" => "انتخاب زبان",
    "panel.NewTheme" => "انتخاب قالب",
    "panel.AdvancedSettings" => "تنظیمات پیشرفته",
    "panel.ThreadsPerPage" => "موضوع در هر صفحه:",
    "panel.PostPerPage" => "نوشته در هر صفحه:",
    "panel.Userlist" => "فعال کردن لیست کاربران",
    "panel.UserlistMemberOnly" => "فقط اعضاء اجازه دیدن لیست کاربری را داشته باشند",
    "panel.showDeletedInUserlist" => "نمایش کاربران حذف شده در لیست کاربری",
    "panel.EnabledBtn" => "فعال شده",
    "panel.ChangeFooter" => "نوشته پاورقی",
    "panel.ChangeSettingsBtn" => "ذخیره تنظیمات",
    "panel.ChangesSaved" => "تغییرات شما ذخیره شد.",
    "panel.ChangeError" => "برخی از کادرها خالی مانده‌اند یا مشکل دیگری پیش‌آمده است.",
    "panel.Registration" => "گزینه‌های ثبت‌نام",
    "panel.open" => "فعال",
    "panel.closed" => "غیرفعال",
    "panel.approval" => "تایید مدیر",
    "panel.CategoryUpBtn" => "حرکت به بالا",
    "panel.CategoryDownBtn" => "حرکت به پایین",

    // Admin panel / Users
    "panel.Users" => "کاربران",
    "panel.DeleteUser" => "حذف کاربر",
    "panel.RestoreUser" => "بازیابی کاربر",

    // Admin panel / Categories
    "panel.Categories" => "دسته‌بندی",
    "panel.CreateACategory" => "ساخت یک دسته‌بندی",
    "panel.CategoryName" => "نام دسته‌بندی: ",
    "panel.CategoryDescription" => "توضیحات دسته‌بندی: ",
    "panel.CategoryEditBtn" => "ویرایش",
    "panel.CategoryDeleteBtn" => "حذف",
    "panel.AddCategory" => "افزودن یک دسته‌بندی",
    "panel.InputCategoryTitle" => "عنوان",
    "panel.InputCategoryDesc" => "توضیحات",
    "panel.CreateCategoryBtn" => "ساخت دسته‌بندی",
    "panel.SuccessAddedCategory" => "دسته‌بندی جدید با موفقیت ایجاد شد.",
    "panel.EditCategory" => "ویرایش دسته‌بندی",
    "panel.EditCategoryBtn" => "ویرایش",
    "panel.EditCategoryDisBtn" => "لغو ویرایش",
    "panel.CategoryNameBlank" => "نام دسته‌بندی نمی‌تواند خالی باشد.",
    "panel.CategoryDescBlank" => "توضیحات دسته‌بندی نمی‌تواند خالی باشد.",
    "panel.CategoryNameTooLong" => "نام دسته‌بندی بایستی کمتر از ۲۵۶ کاراکتر باشد.",
    "panel.CategoryDescTooLong" => "توضیحات دسته‌بندی بایستی کمتر از ۲۵۶ کاراکتر باشد.",
    "panel.CantUpdateCategory" => "متأسفیم، دسته به‌روزرسانی نشد. بعداً دوباره تلاش کنید.",
    "panel.SuccessUpdateCategory" => "دسته‌بندی با موفقیت به‌روز شد.",
    "panel.DeleteCategory" => "حذف دسته‌بندی",
    "panel.CategoryDataWillGone" => "همه موضوعات و نوشته‌های این دسته‌بندی برای همیشه از بین خواهند رفت...",
    "panel.CategoryDeleteLastCheck" => "آیا شما مطمئن هستید؟",
    "panel.CantDeleteCategory" => "با عرض پوزش، دسته‌بندی قابل حذف نیست. بعداً دوباره تلاش کنید.",
    "panel.DeleteCategoryBtn" => "حذف دسته‌بندی",
    "panel.DeleteCategoryBackBtn" => "بازگشت",
    "panel.SuccessDeleteCategory" => "با موفقیت حذف شد.",
    "panel.NoNewCategoriesCreate" => "با عرض پوزش، دیگر دسته‌بندی جدیدی نمی‌توان ایجاد کرد.",
    "panel.CatThreads" => "موضوعات: ",

    // Admin panel / User Admin
    "panel.DangerZone" => "منطقه خطر!",
    "panel.DeleteUserMessage" => "توجه، این گزینه‌ها می‌توانند برای همیشه هر محتوای ایجاد شده توسط کاربر را حذف و از بین ببرند. بااحتیاط استفاده کنید.",
    "panel.DeleteKeepPosts" => "حذف کاربر و حفظ نوشته‌ها",
    "panel.DeleteHidePosts" => "حذف کاربر و پنهان‌کردن نوشته‌ها",
    "panel.DeleteRemovePosts" => "حذف کاربر و حذف نوشته‌ها",
    "panel.DeleteAllUsersOnIP" => "حذف تمام کاربرانی که این آدرس آی پی را به اشتراک دارند",
    "panel.DeleteAllIPSuccess" => "با موفقیت تمام کاربرانی که با این آدرس آی پی حذف شدند.",
    "panel.DeleteUserSuccess" => "کاربر با موفقیت حذف شد.",
    "panel.UseridError" => "این کاربر وجود ندارد.",
    "panel.DeleteUserSuccessHide" => "کاربر با موفقیت حذف شد و همه نوشته‌های آن پنهان شد.",
    "panel.PurgeUser" => "پاک کردن کاربر",
    "panel.PurgeWarning" => "پاک‌کردن یک کاربر، تمام موضوع‌ها، نوشته‌ها و حساب کاربری او را برای همیشه حذف می‌کند.<br/>شما نمی‌توانید حساب پاک‌شده را بازیابی کنید..",
    "panel.sameIP" => "حساب‌های کاربری با همین آدرس آی پی'%s'",
    "panel.noSameIP" => "حساب دیگری وجود ندارد که آدرس آی پی یکسانی با این کاربر داشته باشد.",
    "panel.Administrate" => "مدیریت",
    
    // Admin panel / Extensions
    "panel.Extensions" => "افزونه‌ها",
    "panel.Disable" => "غیرفعال",
    "panel.Enable" => "فعال",
    "panel.Author" => "مولف",
    "panel.Readme" => "اطلاعات",
    "panel.EnableSuccess" => "افزونه با موفقیت فعال شد.",
    "panel.DisableSuccess" => "افزونه با موفقیت غیرفعال شد.",

    // Admin panel / Audit Log
    "panel.AuditLog" => "دفترچه گزارشات",
    "panel.NoLogsFound" => "هیچ گزارشی در این انجمن ذخیره نشده است.",
    "panel.UnknownError" => "هنگام تلاش برای انجام عمل خطایی روی داد.<br>لطفاً بعداً دوباره امتحان کنید.",
    "panel.LogEdit" => "%s نوشته با شناسه «%s» را ویرایش کرد",
    "panel.BeforeEdit" => "قبل ویرایش",
    "panel.AfterEdit" => "بعد ویرایش",
    "panel.Restored" => "بازیابی:",
    "panel.Hid" => "پنهان کرد:",
    "panel.LogHide" => "%s %s نوشته با شناسه '%s'<br>",
    "panel.LogDelete" => "%s نوشته %s را حذف کرد",
    "panel.PostContent" => "محتوای نوشته",
    "panel.LogDeleteThread" => "%s deleted %s's thread titled '%s'<br>",
    
    // User register
    "register.Header" => "ثبت نام",
    "register.UsernameDesc" => "نام کاربری باید بین ۳ تا ۲۴ کاراکتر باشد.",
    "register.PasswordDesc" => "رمز عبور باید حداقل %s کاراکتر باشد.",
    "register.EmailDesc" => "مثال. tormater@example.com",
    "register.Username" => "نام کاربری",
    "register.Email" => "آدرس ایمیل",
    "register.Password" => "کلمه عبور",
    "register.PasswordConf" => "تأیید کلمه عبور",
    "register.Submit" => "ثبت نام",
    "register.Success" => "با موفقیت عضو انجمن شدید. هم اکنون می‌توانید <a href='%s'>وارد انجمن شده</a> و شروع به ارسال کنید!",
    "error.UsernameAlphNum" => "نام کاربری فقط می‌تواند شامل کاراکترهای الفبایی باشد.",
    "error.UsernameSmall" => "نام کاربری باید حداقل ۳ کاراکتر باشد.",
    "error.UsernameBig" => "نام کاربری نمی‌تواند بیشتر از ۲۴ کاراکتر باشد.",
    "register.UsernameExists" => "این نام کاربری از قبل وجود دارد.",
    "register.EmailExists" => "یک حساب کاربری با این ایمیل از قبل وجود دارد.",
    "register.InvalidEmailFormat" => "آدرس ایمیل معتبر نیست.",
    "register.EmailEmpty" => "کادر آدرس ایمیل نباید خالی باشد.",
    "register.PasswordSmall" => "کلمه عبور باید حداقل ۶ کاراکتر باشد.",
    "register.PasswordEmpty" => "کادر کلمه عبور نباید خالی باشد.",
    "register.ConfirmPasswordEmpty" => "کادر تأیید کلمه عبور نباید خالی باشد.",
    "register.ConfirmPasswordWrong" => "کلمه عبور شما همخوانی ندارد.",
    "register.TooManyAccounts" => "شما حساب کاربری زیادی ایجاد کرده‌اید. به‌جای ایجاد حساب جدید سعی کنید به یک حساب موجود وارد شوید.",
    "register.Captcha" => "کپچا",
    "register.CaptchaHint" => "حروف و اعدادی را که در تصویر کپچا می‌بینید تایپ کنید.",
    "register.CaptchaWrong" => "کپچا اشتباه پر شده است.",
    "register.EmailShort" => "طول آدرس ایمیل نمی‌تواند کمتر از ۱ کاراکتر باشد.",
    "register.EmailLong" => "طول آدرس ایمیل نمی‌تواند بیشتر از ۲۵۴ کاراکتر باشد.",
    "register.Approval" => "با موفقیت ثبت‌نام شدید. قبل از اینکه بتوانید <a href='%s'>وارد انجمن شوید</a> و شروع به ارسال نوشته کنید، حساب شما باید توسط ناظر یا مدیر تأیید شود. لطفاً بعداً حساب خود را بررسی کنید.",
    "register.Closed" => "با عرض پوزش، ثبت حساب در این انجمن غیرفعال است. لطفاً بعداً دوباره بررسی کنید.",
    
    // User login
    "login.Header" => "ورود به انجمن",
    "login.Username" => "نام کاربری یا ایمیل",
    "login.Password" => "کلمه عبور",
    "login.Submit" => "ورود",
    "login.Welcome" => "خوش آمدید، %s. <a href='%s'> به نمای کلی انجمن بروید</a>.",

    // User page
    "user.FaildFindUser" => "با عرض پوزش، این کاربر نمایش داده نمی‌شود. بعداً دوباره تلاش کنید.",
    "user.NoSuchUser" => "با عرض پوزش، این کاربر وجود ندارد.",
    "user.FaildChangeRole" => "تغییر نقش کاربری با خطا روبرو شد.",
    "user.ViewingProfile" => "مشاهده پروفایل ",
    "user.OptionAdmin" => "مدیر",
    "user.OptionMod" => "ناظر",
    "user.OptionMember" => "عضو",
    "user.OptionSuspended" => "معلق",
    "user.ChangeRole" => "تغییر نقش",
    "user.TitleRegistered" => "تاریخ عضویت:",
    "user.TitleLastActive" => "آخرین فعالیت:",
    "user.TitlePosts" => "نوشته‌ها:",
    "user.TitleThreads" => "موضوعات:",
    "user.TitleVerified" => "تأیید شده:",
    "user.TitleDeleted" => "حذف شده: ",
    "user.VerifiedYes" => "بله",
    "user.VerifiedNo" => "خیر",
    "user.DeletedYes" => "بله",
    "user.DeletedNo" => "خیر",
    "user.Deleted" => "حذف شده",
    "user.UserInformation" => "اطلاعات کاربر",
    "user.RecentPosts" => "نوشته‌های جدید",
    "user.ViewThread" => "مشاهده موضوع",

    // Roles
    "role.Admin" => "مدیر انجمن",
    "role.Mod" => "ناظر انجمن",
    "role.Member" => "عضو انجمن",
    "role.Suspend" => "کاربر معلق",

    "role.Administrator" => "مدیر انجمن",
    "role.Moderator" => "ناظر انجمن",
    "role.Member" => "عضو انجمن",
    "role.Suspended" => "کاربر معلق",

    // User settings
    "settings.LoginFirst" => "برای تغییر تنظیمات کاربر باید وارد انجمن شوید.",
    "settings.CurrentPassword" => "کلمه عبور فعلی",

    // Set post color
    "settings.PostColor" => "رنگ پروفایل",
    "settings.SetColorError" => "با عرض پوزش، رنگ درخواستی تنظیم نشد.",
    "settings.SetColorSuccess" => "رنگ پروفایل با موفقیت تنظیم شد.",

    // Change username
    "settings.ChangeUsername" => "تغییر نام کاربری",
    "settings.NewUsername" => "نام کاربری جدید",
    "settings.ChangeUsernameBtn" => "تغییر نام کاربری",
    "settings.NewUsernameEmpty" => "شما بایستی یک نام کاربری وارد کنید.",
    "settings.PasswordEmpty" => "شما بایستی کلمه عبور را وارد کنید.",
    "settings.NewUsernameSame" => "نام کاربری جدید نمی تواند با نام کاربری قدیمی یکی باشد.",
    "settings.NewUsernameBig" => "نام کاربری جدید نمی‌تواند بیش از ۲۴ کاراکتر باشد.",
    "settings.NewUsernameNonAlph" => "نام کاربری جدید نمی‌تواند شامل نویسه‌های غیرالفبایی باشد.",
    "settings.NewUsernameError" => "امکان تغییر نام کاربری وجود ندارد.",
    "settings.NewUsernameChanged" => "نام کاربری تغییر کرد.",

    // Change password
    "settings.ChangePassword" => "تغییر کلمه عبور",
    "settings.NewPassword" => "کلمه عبور جدید",
    "settings.ConfirmNewPassword" => "تأیید کلمه عبور جدید",
    "settings.ChangePasswordBtn" => "تغییر کلمه عبور",
    "settings.NewPasswordEmpty" => "کلمه رمز عبور جدید نمی‌تواند خالی باشد.",
    "settings.ConfirmNewPasswordEmpty" => "کلمه عبور جدید نمی‌تواند خالی باشد.",
    "settings.OldPasswordEmpty" => "کلمه عبور فعلی نمی‌تواند خالی باشد.",
    "settings.ConfirmNewPasswordError" => "کلمه عبور جدید یکسان نیست.",
    "settings.ChangePasswordError" => "تغییر کلمه عبور ممکن نیست.",
    "settings.ChangePasswordChanged" => "کلمه عبور تغییر کرد.",

    // Time
    "time.Never" => "هرگز",
    "time.JustNow" => "هم اکنون",
    "time.1SecAgo" => "1 ثانیه پیش",
    "time.SecAgo" => "ثانیه پیش",
    "time.1MinAgo" => "1 دقیقه پیش",
    "time.MinAgo" => "دقیقه پیش",
    "time.1HrAgo" => "1 ساعت پیش",
    "time.HrsAgo" => "ساعت پیش",
    "time.1DayAgo" => "1 روز پیش",
    "time.DaysAgo" => "روز پیش",
    "time.1WeekAgo" => "1 هفته پیش",
    "time.WeeksAgo" => "هفته پیش",
    "time.1MonthAgo" => "1 ماه پیش",
    "time.MonthsAgo" => "ماه پیش",
    "time.1YearAgo" => "1 سال پیش",
    "time.YearsAgo" => "سال پیش",
    
    // Page bar
    "page.homepage" => "فهرست",
    "page.userlist" => "کاربران",
    "page.login" => "ورود",
    "page.signup" => "عضویت",
    "page.settings" => "تنظیمات",
    "page.newthread" => "موضوع جدید",
    "page.panel" => "پنل مدیریت",
    "page.panel.category" => "دسته‌بندی‌ها",
    "page.panel.user" => "کاربران",
    "page.panel.extensions" => "افزونه‌ها",
    "page.panel.auditlog" => "دفترچه گزارشات",
    "page.upgrade" => "ارتقادهنده",
    "page.userpanel" => "پنل کاربری",
    "page.userpanel.accountsettings" => "تنظیمات حساب",
    "page.userpanel.profilesettings" => "تنظیمات پروفایل",
    "page.search" => "جستجو",
    "page.useradmin" => "کاربر مدیر",

    // Upgrade
    "upgrade.Success" => "انجمن با موفقیت ارتقا یافت.",
    "upgrade.None" => "نسخه جدیدی برای ارتقا وجود ندارد!",
    
    // User panel
    "userpanel.AccountSettings" => "تنظیمات حساب کاربری",
    "userpanel.ProfileSettings" => "تنظیمات پروفایل",
    "userpanel.AvatarSettings" => "تنظیمات آواتار",
    "userpanel.Signature" => "امضاء",
    "userpanel.Bio" => "بیوگرافی",
    "userpanel.UpdateSignature" => "به‌روزرسانی امضاء",
    "userpanel.UpdateBio" => "به‌روزرسانی بیوگرافی",
    "userpanel.CurrentAvatar" => "آواتار فعلی",
    "userpanel.ChangeAvatar" => "تغییر آواتار",
    "userpanel.UploadAvatar" => "آپلود آواتار",
    "userpanel.RemoveAvatar" => "حذف آواتار",
    "userpanel.RemoveAvatarSuccess" => "آواتار با موفقیت حذف شد.",
    "userpanel.AvatarUploadSuccess" => "آواتار با موفقیت آپلود شد.",
    "userpanel.AvatarUploadsDisabled" => "با عرض پوزش، آپلود آواتار غیرفعال شده است.",
    "userpanel.NoAvatar" => "بدون آواتار.",
    "userpanel.WaitToUpload" => "شما قبلاً یک آواتار را اخیراً بارگذاری کرده اید. لطفا چند دقیقه صبر کنید و دوباره امتحان کنید.",

    // BBCode
    "BBCode.Bold" => "B",
    "BBCode.Italic" => "I",
    "BBCode.Underline" => "U",
    "BBCode.Strikethrough" => "S",
    "BBCode.Header" => "H",
    "BBCode.Link" => "آدرس وب",
    "BBCode.Image" => "تصویر",
    "BBCode.Spoiler" => "اسپویلر",
    "BBCode.Code" => "</>",
    
    // Userlist
    "userlist.Approve" => "تأیید",
);

?>
