@extends('layouts.app')

@title('التحقق من الكود - مركز إرث لمشاريع التخرج')

@section('content')
    <div class="auth-container glass-panel" style="max-width: 500px; margin: 0 auto; display: block; padding: 40px;">
        <h2 class="lang-ar" style="text-align: center; margin-bottom: 15px;">🔑 أدخل كود التحقق</h2>
        <h2 class="lang-en" style="text-align: center; margin-bottom: 15px;">🔑 Enter Verification Code</h2>
        
        <p class="lang-ar" style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 0.9rem; line-height: 1.5;">
            الرجاء إدخال الكود المكون من 6 أرقام المرسل إلى البريد الإلكتروني: <strong style="color: var(--accent);">{{ session('reset_email') ?? old('email') }}</strong>
        </p>
        <p class="lang-en" style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 0.9rem; line-height: 1.5;">
            Please enter the 6-digit code sent to: <strong style="color: var(--accent);">{{ session('reset_email') ?? old('email') }}</strong>
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

        <form action="{{ route('password.verify') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ session('reset_email') ?? old('email') }}">

            <div class="form-group">
                <label for="code">
                    <span class="lang-ar">كود التحقق (6 أرقام)</span>
                    <span class="lang-en">Verification Code (6 Digits)</span>
                </label>
                <input type="text" name="code" id="code" class="form-control" placeholder="123456" required maxlength="6" pattern="\d{6}" style="text-align: center; font-size: 1.5rem; letter-spacing: 5px;" value="{{ old('code') }}">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">
                <span class="lang-ar">التحقق من الكود</span>
                <span class="lang-en">Verify Code</span>
            </button>

            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ route('password.request') }}" class="lang-ar" style="font-size: 0.9rem; color: var(--text-secondary); font-weight: bold; text-decoration: none;">🔙 إرسال كود جديد</a>
                <a href="{{ route('password.request') }}" class="lang-en" style="font-size: 0.9rem; color: var(--text-secondary); font-weight: bold; text-decoration: none;">🔙 Send new code</a>
            </div>
        </form>
    </div>
@endsection
