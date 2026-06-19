@extends('layouts.app')

@title('نسيت كلمة المرور - مركز إرث لمشاريع التخرج')

@section('content')
    <div class="auth-container glass-panel" style="max-width: 500px; margin: 0 auto; display: block; padding: 40px;">
        <h2 class="lang-ar" style="text-align: center; margin-bottom: 15px;">🔒 نسيت كلمة المرور</h2>
        <h2 class="lang-en" style="text-align: center; margin-bottom: 15px;">🔒 Forgot Password</h2>
        
        <p class="lang-ar" style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 0.9rem; line-height: 1.5;">
            أدخل بريدك الإلكتروني المسجل في النظام، وسنقوم بإرسال رمز تحقق مكون من 6 أرقام لإعادة تعيين كلمة المرور.
        </p>
        <p class="lang-en" style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 0.9rem; line-height: 1.5;">
            Enter your registered email address, and we will send you a 6-digit verification code to reset your password.
        </p>

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <div>
                    @foreach($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">
                    <span class="lang-ar">البريد الإلكتروني</span>
                    <span class="lang-en">Email Address</span>
                </label>
                <input type="email" name="email" id="email" class="form-control" required value="{{ old('email') }}" placeholder="example@erth.com">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">
                <span class="lang-ar">إرسال كود التحقق</span>
                <span class="lang-en">Send Verification Code</span>
            </button>

            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ route('login') }}" class="lang-ar" style="font-size: 0.9rem; color: var(--text-secondary); font-weight: bold; text-decoration: none;">🔙 العودة لصفحة الدخول</a>
                <a href="{{ route('login') }}" class="lang-en" style="font-size: 0.9rem; color: var(--text-secondary); font-weight: bold; text-decoration: none;">🔙 Back to Login</a>
            </div>
        </form>
    </div>
@endsection
