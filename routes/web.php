<?php

// استيراد كلاس إدارة المسارات (Route) من إطار العمل لارافل
use Illuminate\Support\Facades\Route;
// استيراد متحكم عمليات تسجيل الدخول والتسجيل (AuthController)
use App\Http\Controllers\AuthController;
// استيراد متحكم مشاريع التخرج وعمليات الرفع والفلترة (ProjectController)
use App\Http\Controllers\ProjectController;
// استيراد متحكم منتدى الاستشارات الأكاديمية (ForumController)
use App\Http\Controllers\ForumController;
// استيراد متحكم لوحة تحكم المسؤول والإعدادات (AdminController)
use App\Http\Controllers\AdminController;
// استيراد نموذج مشروع التخرج (Project Model) للتفاعل مع جدول المشاريع
use App\Models\Project;
// استيراد نموذج المستخدم (User Model) للتفاعل مع جدول المستخدمين
use App\Models\User;

// مسار الصفحة الرئيسية للمنصة (Landing Page) باستخدام طريقة GET
Route::get('/', function () {
    // مصفوفة لتخزين إحصائيات المنصة الحية من قاعدة البيانات
    $stats = [
        // حساب إجمالي الطلاب المسجلين بالمنصة بفلترة الدور (student)
        'total_students' => User::where('role', 'student')->count(),
        // حساب إجمالي الخريجين المسجلين بالمنصة بفلترة الدور (graduate)
        'total_graduates' => User::where('role', 'graduate')->count(),
        // حساب إجمالي عدد مشاريع التخرج المرفوعة في المنصة بالكامل
        'total_projects' => Project::count(),
    ];
    // جلب آخر 3 مشاريع تخرج تم رفعها مع تحميل بيانات الطلاب المشتركين لتسريع الأداء
    $featuredProjects = Project::with('graduate')->latest()->take(3)->get();
    // إرجاع واجهة الصفحة الرئيسية (home.blade.php) ممرراً مصفوفة الإحصائيات والمشاريع
    return view('home', compact('stats', 'featuredProjects'));
    // تسمية المسار باسم (home) لاستدعائه برمجياً في الواجهات والأكواد
})->name('home');

// مسار عرض صفحة تسجيل الدخول وإنشاء الحساب (GET)
Route::get('/login', [AuthController::class, 'showLoginRegister'])->name('login');
// مسار معالجة طلب تسجيل الدخول الفعلي وإرسال البيانات (POST)
Route::post('/login', [AuthController::class, 'login']);
// مسار معالجة طلب تسجيل الحساب الجديد المبدئي وإرسال البيانات (POST)
Route::post('/register', [AuthController::class, 'register'])->name('register');
// مسار عرض صفحة إدخال كود تأكيد البريد الإلكتروني المكون من 6 أرقام (GET)
Route::get('/register/verify', [AuthController::class, 'showRegisterVerifyForm'])->name('register.verify.show');
// مسار استقبال ومعالجة كود تأكيد الحساب وإنشاء الحساب الفعلي (POST)
Route::post('/register/verify', [AuthController::class, 'verifyRegisterCode'])->name('register.verify.submit');
// مسار معالجة تسجيل الخروج وإنهاء جلسة المستخدم الحالية (POST)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// استيراد متحكم استعادة كلمة المرور المفقودة (ForgotPasswordController)
use App\Http\Controllers\ForgotPasswordController;
// مسار عرض صفحة طلب استعادة كلمة المرور وإدخال البريد (GET)
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
// مسار إرسال كود استعادة كلمة المرور للبريد الإلكتروني المدخل (POST)
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetCode'])->name('password.email');
// مسار عرض صفحة إدخال كود التحقق لاستعادة كلمة المرور (GET)
Route::get('/forgot-password/verify', [ForgotPasswordController::class, 'showCodeForm'])->name('password.code');
// مسار معالجة ومطابقة كود استعادة كلمة المرور للتأكد من صحته (POST)
Route::post('/forgot-password/verify', [ForgotPasswordController::class, 'verifyCode'])->name('password.verify');
// مسار عرض نموذج تعيين كلمة المرور الجديدة بعد مطابقة كود التحقق (GET)
Route::get('/forgot-password/reset', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
// مسار معالجة وحفظ كلمة المرور الجديدة وتحديثها في قاعدة البيانات (POST)
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// مسار تصفح واستعراض مشاريع التخرج بالكامل وفلترتها (GET)
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
// مسار عرض صفحة رفع مشروع تخرج جديد المخصصة للمسؤول (GET)
Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
// مسار حفظ بيانات مشروع التخرج المرفوع والتحقق من الملفات أونلاين (POST)
Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
// مسار تصفح وعرض تفاصيل مشروع تخرج محدد بناءً على معرّف المشروع (GET)
Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
// مسار حذف مشروع تخرج معين من قاعدة البيانات بناءً على معرّفه (DELETE)
Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');

// مسار استعراض أسئلة وأجوبة منتدى الاستشارات الأكاديمية (GET)
Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
// مسار إرسال وحفظ سؤال أكاديمي جديد في منتدى المنصة (POST)
Route::post('/forum/question', [ForumController::class, 'storeQuestion'])->name('forum.question.store');
// مسار إرسال وحفظ إجابة أكاديمية على سؤال معين بالمنتدى (POST)
Route::post('/forum/question/{questionId}/answer', [ForumController::class, 'storeAnswer'])->name('forum.answer.store');
// مسار حذف سؤال معين بالكامل من المنتدى بواسطة المشرف أو المسؤول (DELETE)
Route::delete('/forum/question/{id}', [ForumController::class, 'destroyQuestion'])->name('forum.question.destroy');
// مسار حذف إجابة معينة من المنتدى بواسطة المشرف أو المسؤول (DELETE)
Route::delete('/forum/answer/{id}', [ForumController::class, 'destroyAnswer'])->name('forum.answer.destroy');

// مسار تصفح صفحة الإعدادات وتخصيص حجم الخط لجميع الأعضاء (GET)
Route::get('/settings', [AdminController::class, 'settingsIndex'])->name('settings.index');
// مسار عرض الملف الشخصي للعضو المسجل واستعراض بياناته (GET)
Route::get('/profile', [AdminController::class, 'profileShow'])->name('profile.show');
// مسار تحديث وحفظ بيانات الملف الشخصي وكلمات المرور للمستخدم (POST)
Route::post('/profile', [AdminController::class, 'profileUpdate'])->name('profile.update');

// مسار لوحة تحكم المسؤول الرئيسية لعرض إحصائيات الكلية والأعضاء (GET)
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
// مسار استعراض وإدارة جميع مستخدمي المنصة وتعديلهم للمسؤول (GET)
Route::get('/admin/users', [AdminController::class, 'usersIndex'])->name('admin.users');
// مسار إنشاء حساب مستخدم جديد يدوياً من قبل المسؤول باللوحة (POST)
Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
// مسار حذف مستخدم بالكامل من لوحة التحكم وإلغاء حسابه (DELETE)
Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
// مسار استعراض قائمة مشاريع التخرج بالكامل داخل لوحة التحكم (GET)
Route::get('/admin/projects', [AdminController::class, 'projectsIndex'])->name('admin.projects');
// مسار حذف مشروع تخرج معين من لوحة تحكم المسؤول وإزالته (DELETE)
Route::delete('/admin/projects/{id}', [AdminController::class, 'destroyProject'])->name('admin.projects.destroy');
// مسار استعراض الأسئلة المطروحة وإدارتها من لوحة المسؤول (GET)
Route::get('/admin/questions', [AdminController::class, 'questionsIndex'])->name('admin.questions');
// مسار استعراض تقارير المنصة وحالة النظام من لوحة المسؤول (GET)
Route::get('/admin/reports', [AdminController::class, 'reportsIndex'])->name('admin.reports');
// مسار استعراض التحليلات المتقدمة وتوزيع التخصصات ونسب المشاريع (GET)
Route::get('/admin/analytics', [AdminController::class, 'analyticsIndex'])->name('admin.analytics');
// مسار إعدادات المنصة المتقدمة المتاحة للمسؤول في لوحة التحكم (GET)
Route::get('/admin/settings', [AdminController::class, 'settingsIndex'])->name('admin.settings');

// استيراد متحكم الشات بوت الأكاديمي والتحليل الذكي (ChatbotController)
use App\Http\Controllers\ChatbotController;
// مسار استقبال واستعلام محادثات الشات بوت الأكاديمي ومعالجتها (POST)
Route::post('/chatbot/query', [ChatbotController::class, 'query'])->name('chatbot.query');
// مسار التنبؤ الذكي وتحليل اتجاهات مشاريع التخرج المستقبلية بالكلية (POST)
Route::post('/api/ai/predict-trends', [ChatbotController::class, 'predictTrends'])->name('ai.predict-trends');
