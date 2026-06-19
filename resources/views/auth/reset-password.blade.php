@extends('layouts.app')

@title('إعادة تعيين كلمة المرور - مركز إرث لمشاريع التخرج')

@section('styles')
    <style>
        .password-wrapper {
            position: relative;
        }
        .password-toggle-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 10px;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1.1rem;
            user-select: none;
            padding: 0 5px;
            display: flex;
            align-items: center;
        }
        html[lang="en"] .password-toggle-btn {
            right: 10px;
            left: auto;
        }
    </style>
@endsection

@section('content')
    <div class="auth-container glass-panel" style="max-width: 500px; margin: 0 auto; display: block; padding: 40px;">
        <h2 class="lang-ar" style="text-align: center; margin-bottom: 15px;">🔄 كلمة المرور الجديدة</h2>
        <h2 class="lang-en" style="text-align: center; margin-bottom: 15px;">🔄 New Password</h2>
        
        <p class="lang-ar" style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 0.9rem; line-height: 1.5;">
            قم بإنشاء كلمة مرور جديدة قوية وآمنة لحسابك.
        </p>
        <p class="lang-en" style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 0.9rem; line-height: 1.5;">
            Create a new strong and secure password for your account.
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

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ session('reset_email') ?? old('email') }}">
            <input type="hidden" name="code" value="{{ session('reset_code') ?? old('code') }}">

            <div class="form-group">
                <label for="password">
                    <span class="lang-ar">كلمة المرور الجديدة</span>
                    <span class="lang-en">New Password</span>
                </label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" class="form-control" required style="padding-left: 40px; padding-right: 40px;">
                    <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password')">👁️</button>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">
                    <span class="lang-ar">تأكيد كلمة المرور الجديدة</span>
                    <span class="lang-en">Confirm New Password</span>
                </label>
                <div class="password-wrapper">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required style="padding-left: 40px; padding-right: 40px;">
                    <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password_confirmation')">👁️</button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 25px;">
                <span class="lang-ar">حفظ كلمة المرور الجديدة</span>
                <span class="lang-en">Save New Password</span>
            </button>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        function togglePasswordVisibility(fieldId) {
            const passwordField = document.getElementById(fieldId);
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }
    </script>
@endsection
