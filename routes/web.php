<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\AdminController;
use App\Models\Project;
use App\Models\User;

// Landing Page (Home)
Route::get('/', function () {
    $stats = [
        'total_students' => User::where('role', 'student')->count(),
        'total_graduates' => User::where('role', 'graduate')->count(),
        'total_projects' => Project::count(),
    ];
    $featuredProjects = Project::with('graduate')->latest()->take(3)->get();
    return view('home', compact('stats', 'featuredProjects'));
})->name('home');

// Custom Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginRegister'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/register/verify', [AuthController::class, 'showRegisterVerifyForm'])->name('register.verify.show');
Route::post('/register/verify', [AuthController::class, 'verifyRegisterCode'])->name('register.verify.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes (Forgot Password)
use App\Http\Controllers\ForgotPasswordController;
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetCode'])->name('password.email');
Route::get('/forgot-password/verify', [ForgotPasswordController::class, 'showCodeForm'])->name('password.code');
Route::post('/forgot-password/verify', [ForgotPasswordController::class, 'verifyCode'])->name('password.verify');
Route::get('/forgot-password/reset', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// Projects Routes
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');

// Forum Q&A Routes
Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::post('/forum/question', [ForumController::class, 'storeQuestion'])->name('forum.question.store');
Route::post('/forum/question/{questionId}/answer', [ForumController::class, 'storeAnswer'])->name('forum.answer.store');
Route::delete('/forum/question/{id}', [ForumController::class, 'destroyQuestion'])->name('forum.question.destroy');
Route::delete('/forum/answer/{id}', [ForumController::class, 'destroyAnswer'])->name('forum.answer.destroy');

// Profile & Settings Routes (Accessible by all logged in users)
Route::get('/settings', [AdminController::class, 'settingsIndex'])->name('settings.index');
Route::get('/profile', [AdminController::class, 'profileShow'])->name('profile.show');
Route::post('/profile', [AdminController::class, 'profileUpdate'])->name('profile.update');

// Admin Dashboard Routes
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/admin/users', [AdminController::class, 'usersIndex'])->name('admin.users');
Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
Route::get('/admin/projects', [AdminController::class, 'projectsIndex'])->name('admin.projects');
Route::delete('/admin/projects/{id}', [AdminController::class, 'destroyProject'])->name('admin.projects.destroy');
Route::get('/admin/questions', [AdminController::class, 'questionsIndex'])->name('admin.questions');
Route::get('/admin/reports', [AdminController::class, 'reportsIndex'])->name('admin.reports');
Route::get('/admin/analytics', [AdminController::class, 'analyticsIndex'])->name('admin.analytics');
Route::get('/admin/settings', [AdminController::class, 'settingsIndex'])->name('admin.settings');

// Chatbot Route
use App\Http\Controllers\ChatbotController;
Route::post('/chatbot/query', [ChatbotController::class, 'query'])->name('chatbot.query');
Route::post('/api/ai/predict-trends', [ChatbotController::class, 'predictTrends'])->name('ai.predict-trends');

