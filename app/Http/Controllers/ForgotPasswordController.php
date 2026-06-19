<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'email.exists' => 'هذا البريد الإلكتروني غير مسجل لدينا.',
        ]);

        $code = rand(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $code,
                'created_at' => now()
            ]
        );

        // Log the code locally for easy retrieval during testing/local development
        Log::info("كود إعادة تعيين كلمة المرور للحساب {$request->email} هو: {$code}");

        try {
            Mail::raw("أهلاً بك،\n\nكود إعادة تعيين كلمة المرور الخاص بك في منصة مركز إرث هو: {$code}\n\nهذا الكود صالح لمدة 15 دقيقة فقط.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('كود إعادة تعيين كلمة المرور - مركز إرث');
            });
        } catch (\Exception $e) {
            Log::error("فشل إرسال بريد إعادة تعيين كلمة المرور: " . $e->getMessage());
        }

        return redirect()->route('password.code')->with([
            'reset_email' => $request->email,
            'success' => 'تم إرسال كود التحقق إلى بريدك الإلكتروني بنجاح (يمكنك العثور عليه في مجلد البريد أو ملف log الخاص بالنظام للبيئة المحلية).'
        ]);
    }

    public function showCodeForm()
    {
        if (!session('reset_email') && !old('email')) {
            return redirect()->route('password.request');
        }
        return view('auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'حقل كود التحقق مطلوب.',
            'code.size' => 'كود التحقق يجب أن يتكون من 6 أرقام.',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$reset || now()->diffInMinutes($reset->created_at) > 15) {
            return back()->withErrors(['code' => 'كود التحقق غير صحيح أو انتهت صلاحيته.'])->withInput();
        }

        return redirect()->route('password.reset')->with([
            'reset_email' => $request->email,
            'reset_code' => $request->code
        ]);
    }

    public function showResetForm()
    {
        if (!session('reset_email') || !session('reset_code')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.min' => 'يجب ألا تقل كلمة المرور عن 6 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        // Double check token validity
        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$reset || now()->diffInMinutes($reset->created_at) > 15) {
            return redirect()->route('password.request')->withErrors(['email' => 'انتهت صلاحية جلسة إعادة التعيين. يرجى المحاولة مجدداً.']);
        }

        // Update password
        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'تمت إعادة تعيين كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول بها.');
    }
}
