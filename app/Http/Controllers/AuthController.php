<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginRegister()
    {
        if (Auth::check()) {
            return redirect()->route('projects.index');
        }
        return view('auth.login-register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Convert Arabic numerals to English to prevent login failures from keyboard layout mismatch
        $credentials['password'] = $this->convertArabicNumbers($credentials['password']);
        $credentials['email'] = $this->convertArabicNumbers($credentials['email']);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('projects.index');
        }

        return back()->withErrors([
            'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        ])->onlyInput('email');
    }

    private function convertArabicNumbers($string)
    {
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        $string = str_replace($arabic, $english, $string);
        return str_replace($persian, $english, $string);
    }
    public function register(Request $request)
    {

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:student,professor',
            'student_id' => 'required_if:role,student|nullable|string|max:50',
            'student_department' => 'required_if:role,student|nullable|string|in:طب و جراحة,هندسة طبية,هندسة اتصالات,هندسة الكترونيات,هندسة ميكاترونكس,تقنية معلومات,ذكاء اصطناعي,هندسة برمجيات,دبلوم تقنية معلومات,ادارة اعمال,اقتصاد,وسائط الاعلام و الاتصال,دبلوم وسائط متعدده,علوم تمريض,علوم مختبرات,علاج طبيعي',
            'professor_id' => 'required_if:role,professor|nullable|string|max:50',
            'title' => 'required_if:role,professor|nullable|string|max:50',
            'professor_department' => 'required_if:role,professor|nullable|string|in:طب و جراحة,هندسة طبية,هندسة اتصالات,هندسة الكترونيات,هندسة ميكاترونكس,تقنية معلومات,ذكاء اصطناعي,هندسة برمجيات,دبلوم تقنية معلومات,ادارة اعمال,اقتصاد,وسائط الاعلام و الاتصال,دبلوم وسائط متعدده,علوم تمريض,علوم مختبرات,علاج طبيعي',
        ];

        $messages = [
            'required' => 'حقل :attribute مطلوب.',
            'string' => 'يجب أن يكون حقل :attribute نصاً.',
            'max' => 'حقل :attribute يجب ألا يتجاوز :max حرفاً.',
            'email' => 'يجب أن يكون البريد الإلكتروني عنواناً صالحاً.',
            'unique' => 'البريد الإلكتروني هذا مستخدم بالفعل.',
            'confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'min' => 'يجب أن تكون كلمة المرور على الأقل :min أحرف.',
            'integer' => 'يجب أن يكون حقل :attribute رقماً صحيحاً.',
            'in' => 'القيمة المحددة لحقل :attribute غير صالحة.',
            'graduation_year.min' => 'سنة التخرج يجب أن تكون :min أو أحدث.',
            'graduation_year.max' => 'سنة التخرج لا يمكن أن تتجاوز السنة الحالية.',
        ];

        $attributes = [
            'name' => 'الاسم الكامل',
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة المرور',
            'role' => 'نوع الحساب',
            'student_id' => 'الرقم الجامعي',
            'student_department' => 'القسم الدراسي / التخصص',
            'job_title' => 'المسمى الوظيفي',
            'company' => 'الشركة',
            'expertise' => 'مجالات الخبرة',
            'graduation_year' => 'سنة التخرج',
            'professor_id' => 'الرقم الوظيفي',
            'title' => 'اللقب الأكاديمي',
            'professor_department' => 'القسم الأكاديمي',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('active_tab', 'register');
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ];

        if ($request->role === 'student') {
            $userData['student_id'] = $request->student_id;
            $userData['department'] = $request->student_department;
        } elseif ($request->role === 'graduate') {
            $userData['job_title'] = $request->job_title;
            $userData['company'] = $request->company;
            $userData['expertise'] = $request->expertise;
            $userData['graduation_year'] = $request->graduation_year;
        } elseif ($request->role === 'professor') {
            $userData['professor_id'] = $request->professor_id;
            $userData['title'] = $request->title;
            $userData['department'] = $request->professor_department;
        }

        // Generate 6-digit verification code
        $code = rand(100000, 999999);

        // Store registration data in session temporarily
        session([
            'reg_verification' => [
                'user_data' => $userData,
                'code' => $code,
                'expires_at' => now()->addMinutes(15)->toIso8601String()
            ]
        ]);

        // Log the code locally for easy testing
        Log::info("كود التحقق لإنشاء حساب جديد للبريد {$request->email} هو: {$code}");

        try {
            Mail::raw("أهلاً بك،\n\nكود التحقق لإنشاء حسابك الجديد في منصة مركز إرث هو: {$code}\n\nهذا الكود صالح لمدة 15 دقيقة فقط.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('كود التحقق من البريد الإلكتروني - مركز إرث');
            });
        } catch (\Exception $e) {
            Log::error("فشل إرسال بريد التحقق من الحساب: " . $e->getMessage());
            
            // If in local development environment, flash the code to session so we can display it on the verify page for easy testing
            if (config('app.env') === 'local') {
                session()->flash('dev_verification_code', $code);
            }
        }

        return redirect()->route('register.verify.show')->with([
            'success' => 'تم إرسال كود التحقق إلى بريدك الإلكتروني بنجاح (يمكنك العثور عليه في مجلد البريد أو ملف log الخاص بالنظام للبيئة المحلية).'
        ]);
    }

    public function showRegisterVerifyForm()
    {
        if (!session('reg_verification')) {
            return redirect()->route('login');
        }
        return view('auth.verify-register');
    }

    public function verifyRegisterCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'حقل كود التحقق مطلوب.',
            'code.size' => 'كود التحقق يجب أن يتكون من 6 أرقام.',
        ]);

        $verification = session('reg_verification');

        if (!$verification) {
            return redirect()->route('login')->withErrors(['email' => 'انتهت صلاحية جلسة التسجيل، يرجى المحاولة مرة أخرى.']);
        }

        if (now()->greaterThan(\Illuminate\Support\Carbon::parse($verification['expires_at']))) {
            session()->forget('reg_verification');
            return redirect()->route('login')->withErrors(['email' => 'انتهت صلاحية كود التحقق (15 دقيقة)، يرجى المحاولة مرة أخرى.']);
        }

        if ($request->code !== (string)$verification['code']) {
            return back()->withErrors(['code' => 'كود التحقق غير صحيح.'])->withInput();
        }

        // Create the user in the database
        $user = User::create($verification['user_data']);

        // Log the user in
        Auth::login($user);

        // Clear the registration verification session
        session()->forget('reg_verification');

        return redirect()->route('projects.index')->with('success', 'تم إنشاء حسابك وتأكيده بنجاح! أهلاً بك.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('projects.index');
    }
}
