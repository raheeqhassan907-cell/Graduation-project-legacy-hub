@extends('layouts.app')

@title('تأكيد الحساب الجديد - مركز إرث لمشاريع التخرج')

@section('content')
    <div class="auth-container glass-panel" style="max-width: 500px; margin: 0 auto; display: block; padding: 40px;">
        <h2 class="lang-ar" style="text-align: center; margin-bottom: 15px;">📧 تأكيد البريد الإلكتروني</h2>
        <h2 class="lang-en" style="text-align: center; margin-bottom: 15px;">📧 Verify Email Address</h2>
        
        <p class="lang-ar" style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 0.9rem; line-height: 1.5;">
            أهلاً بك! لقد أرسلنا كود تحقق مكون من 6 أرقام إلى بريدك الإلكتروني: <strong style="color: var(--accent);">{{ session('reg_verification.user_data.email') }}</strong>. الرجاء إدخاله أدناه لإتمام إنشاء الحساب.
        </p>
        <p class="lang-en" style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 0.9rem; line-height: 1.5;">
            Welcome! We have sent a 6-digit verification code to: <strong style="color: var(--accent);">{{ session('reg_verification.user_data.email') }}</strong>. Please enter it below to complete your registration.
        </p>

        @if(session('dev_verification_code'))
            <div class="alert alert-warning" style="margin-bottom: 20px; border: 1px solid rgba(255, 193, 7, 0.3); background: rgba(255, 193, 7, 0.1); color: #ffc107; font-size: 0.9rem; text-align: center; padding: 15px; border-radius: 8px; line-height: 1.6;">
                <span class="lang-ar">⚠️ <strong>تنبيه المطور (بيئة محلية):</strong> تعذر إرسال البريد الإلكتروني بسبب انقطاع اتصال الإنترنت بالـ PHP. كود التحقق الخاص بك هو: <strong style="color: var(--accent); font-size: 1.2rem; text-decoration: underline; letter-spacing: 2px;">{{ session('dev_verification_code') }}</strong></span>
                <span class="lang-en">⚠️ <strong>Developer Notice (Local):</strong> SMTP connection failed. Your verification code is: <strong style="color: var(--accent); font-size: 1.2rem; text-decoration: underline; letter-spacing: 2px;">{{ session('dev_verification_code') }}</strong></span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <div>
                    @foreach($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('register.verify.submit') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="code">
                    <span class="lang-ar">كود التحقق (6 أرقام)</span>
                    <span class="lang-en">Verification Code (6 Digits)</span>
                </label>
                <input type="text" name="code" id="code" class="form-control" placeholder="123456" required maxlength="6" pattern="\d{6}" style="text-align: center; font-size: 1.5rem; letter-spacing: 5px;" value="{{ old('code') }}">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">
                <span class="lang-ar">تأكيد الحساب وإنشاء الحساب</span>
                <span class="lang-en">Verify & Create Account</span>
            </button>

            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ route('login') }}" class="lang-ar" style="font-size: 0.9rem; color: var(--text-secondary); font-weight: bold; text-decoration: none;">🔙 إلغاء والعودة للرئيسية</a>
                <a href="{{ route('login') }}" class="lang-en" style="font-size: 0.9rem; color: var(--text-secondary); font-weight: bold; text-decoration: none;">🔙 Cancel and return</a>
            </div>
        </form>
    </div>
@endsection
