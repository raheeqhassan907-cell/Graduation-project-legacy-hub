<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
    public function register(Request $request)
    {

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:student',
            'student_id' => 'required|string|max:50',
            'student_department' => 'required|string|in:طب و جراحة,هندسة طبية,هندسة اتصالات,هندسة الكترونيات,هندسة ميكاترونكس,تقنية معلومات,ذكاء اصطناعي,هندسة برمجيات,دبلوم تقنية معلومات,ادارة اعمال,اقتصاد,وسائط الاعلام و الاتصال,دبلوم وسائط متعدده,علوم تمريض,علوم مختبرات,علاج طبيعي',
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

        $user = User::create($userData);

        Auth::login($user);

        return redirect()->route('projects.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('projects.index');
    }
}
