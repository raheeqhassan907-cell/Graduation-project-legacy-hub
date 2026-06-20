@extends('layouts.app')

@title('تسجيل الدخول والانضمام - مركز إرث لمشاريع التخرج')

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
    <div class="auth-container glass-panel">
        <!-- Sidebar Info -->
        <div class="auth-sidebar">
            <h2 class="lang-ar" style="margin-bottom: 20px; font-weight: 900;">مرحباً بك في إرث 👋</h2>
            <h2 class="lang-en" style="margin-bottom: 20px; font-weight: 900;">Welcome to Erth 👋</h2>
            
            <p class="lang-ar" style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 20px; line-height: 1.6;">
                بانضمامك إلينا، تساهم في بناء أكبر قاعدة بيانات للمشاريع الأكاديمية والبحثية، وتصبح جزءاً من مجتمع تفاعلي يهدف لتبادل المعرفة والتوجيه المهني.
            </p>
            <p class="lang-en" style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 20px; line-height: 1.6;">
                By joining us, you contribute to building the largest database of academic and research projects, becoming part of an interactive community aimed at knowledge exchange and professional guidance.
            </p>
            
            <div style="font-size: 5rem; text-align: center; margin-top: 20px;">🚀</div>
        </div>

        <!-- Forms Wrapper -->
        <div class="auth-form-wrapper">
            <!-- Language Switcher -->
            <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleLanguage()" style="font-size: 0.8rem; padding: 4px 10px; border-radius: 6px;">
                    <span class="lang-ar">🌐 English</span>
                    <span class="lang-en">🌐 العربية</span>
                </button>
            </div>

            <!-- Tabs -->
            <div class="auth-tabs">
                <div id="tab-login" class="auth-tab {{ session('active_tab') !== 'register' ? 'active' : '' }}" onclick="switchTab('login')">
                    <span class="lang-ar">تسجيل الدخول</span>
                    <span class="lang-en">Login</span>
                </div>
                <div id="tab-register" class="auth-tab {{ session('active_tab') === 'register' ? 'active' : '' }}" onclick="switchTab('register')">
                    <span class="lang-ar">إنشاء حساب جديد</span>
                    <span class="lang-en">Register</span>
                </div>
            </div>

            <!-- Validation Errors for Tab -->
            @if($errors->any() && (session('active_tab') === 'register' || session('active_tab') === 'login'))
                <div class="alert alert-danger" style="margin-bottom: 20px;">
                    <div>
                        @foreach($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form id="form-login-el" action="{{ route('login') }}" method="POST" style="display: {{ session('active_tab') !== 'register' ? 'block' : 'none' }};">
                @csrf
                <div class="form-group">
                    <label for="login-email">
                        <span class="lang-ar">البريد الإلكتروني</span>
                        <span class="lang-en">Email Address</span>
                    </label>
                    <input type="email" name="email" id="login-email" class="form-control" required value="{{ old('email') }}">
                </div>
                
                <div class="form-group">
                    <label for="login-password">
                        <span class="lang-ar">كلمة المرور</span>
                        <span class="lang-en">Password</span>
                    </label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="login-password" class="form-control" required style="padding-left: 40px; padding-right: 40px;">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('login-password')">👁️</button>
                    </div>
                    <div style="text-align: left; margin-top: 8px;">
                        <a href="{{ route('password.request') }}" class="lang-ar" style="font-size: 0.85rem; color: var(--accent); font-weight: bold; text-decoration: none;">نسيت كلمة المرور؟</a>
                        <a href="{{ route('password.request') }}" class="lang-en" style="font-size: 0.85rem; color: var(--accent); font-weight: bold; text-decoration: none;">Forgot Password?</a>
                    </div>
                </div>
                
                <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="remember" id="remember" style="cursor: pointer;">
                    <label for="remember" style="margin: 0; cursor: pointer; user-select: none;">
                        <span class="lang-ar">تذكرني في هذا الجهاز</span>
                        <span class="lang-en">Remember me</span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">
                    <span class="lang-ar">دخول</span>
                    <span class="lang-en">Login</span>
                </button>
            </form>

            <!-- Register Form -->
            <form id="form-register-el" action="{{ route('register') }}" method="POST" style="display: {{ session('active_tab') === 'register' ? 'block' : 'none' }};">
                @csrf
                <div class="form-group">
                    <label for="reg-name">
                        <span class="lang-ar">الاسم الكامل</span>
                        <span class="lang-en">Full Name</span>
                    </label>
                    <input type="text" name="name" id="reg-name" class="form-control" required value="{{ old('name') }}">
                </div>

                <div class="form-group">
                    <label for="reg-email">
                        <span class="lang-ar">البريد الإلكتروني</span>
                        <span class="lang-en">Email Address</span>
                    </label>
                    <input type="email" name="email" id="reg-email" class="form-control" required value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label for="reg-password">
                        <span class="lang-ar">كلمة المرور</span>
                        <span class="lang-en">Password</span>
                    </label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="reg-password" class="form-control" required style="padding-left: 40px; padding-right: 40px;">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('reg-password')">👁️</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg-password-conf">
                        <span class="lang-ar">تأكيد كلمة المرور</span>
                        <span class="lang-en">Confirm Password</span>
                    </label>
                    <div class="password-wrapper">
                        <input type="password" name="password_confirmation" id="reg-password-conf" class="form-control" required style="padding-left: 40px; padding-right: 40px;">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('reg-password-conf')">👁️</button>
                    </div>
                </div>

                <!-- Role Selector -->
                <div class="form-group">
                    <label for="reg-role">
                        <span class="lang-ar">نوع الحساب *</span>
                        <span class="lang-en">Account Type *</span>
                    </label>
                    <select name="role" id="reg-role" class="form-control" onchange="toggleRoleFields()" required>
                        <option value="student" {{ old('role') === 'student' || !old('role') ? 'selected' : '' }}>طالب | Student</option>
                        <option value="professor" {{ old('role') === 'professor' ? 'selected' : '' }}>أستاذ مشرف | Professor/Supervisor</option>
                    </select>
                </div>

                <!-- Student Fields -->
                <div id="student-fields-group" style="display: block;">
                    <div class="form-group">
                        <label for="student_id">
                            <span class="lang-ar">الرقم الجامعي *</span>
                            <span class="lang-en">Student ID *</span>
                        </label>
                        <input type="text" name="student_id" id="student_id" class="form-control" value="{{ old('student_id') }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="student_dept">
                            <span class="lang-ar">القسم الدراسي / التخصص *</span>
                            <span class="lang-en">Department / Specialty *</span>
                        </label>
                        <select name="student_department" id="student_dept" class="form-control">
                            <option value="" disabled {{ !old('student_department') ? 'selected' : '' }}>
                                اختر القسم الدراسي... | Select Department...
                            </option>
                            <option value="طب و جراحة" {{ old('student_department') === 'طب و جراحة' ? 'selected' : '' }}>طب و جراحة | Medicine & Surgery</option>
                            <option value="هندسة طبية" {{ old('student_department') === 'هندسة طبية' ? 'selected' : '' }}>هندسة طبية | Biomedical Engineering</option>
                            <option value="هندسة اتصالات" {{ old('student_department') === 'هندسة اتصالات' ? 'selected' : '' }}>هندسة اتصالات | Telecommunications Engineering</option>
                            <option value="هندسة الكترونيات" {{ old('student_department') === 'هندسة الكترونيات' ? 'selected' : '' }}>هندسة الكترونيات | Electronics Engineering</option>
                            <option value="هندسة ميكاترونكس" {{ old('student_department') === 'هندسة ميكاترونكس' ? 'selected' : '' }}>هندسة ميكاترونكس | Mechatronics Engineering</option>
                            <option value="تقنية معلومات" {{ old('student_department') === 'تقنية معلومات' ? 'selected' : '' }}>تقنية معلومات | Information Technology</option>
                            <option value="ذكاء اصطناعي" {{ old('student_department') === 'ذكاء اصطناعي' ? 'selected' : '' }}>ذكاء اصطناعي | Artificial Intelligence</option>
                            <option value="هندسة برمجيات" {{ old('student_department') === 'هندسة برمجيات' ? 'selected' : '' }}>هندسة برمجيات | Software Engineering</option>
                            <option value="دبلوم تقنية معلومات" {{ old('student_department') === 'دبلوم تقنية معلومات' ? 'selected' : '' }}>دبلوم تقنية معلومات | IT Diploma</option>
                            <option value="ادارة اعمال" {{ old('student_department') === 'ادارة اعمال' ? 'selected' : '' }}>ادارة اعمال | Business Administration</option>
                            <option value="اقتصاد" {{ old('student_department') === 'اقتصاد' ? 'selected' : '' }}>اقتصاد | Economics</option>
                            <option value="وسائط الاعلام و الاتصال" {{ old('student_department') === 'وسائط الاعلام و الاتصال' ? 'selected' : '' }}>وسائط الاعلام و الاتصال | Media & Communication</option>
                            <option value="دبلوم وسائط متعدده" {{ old('student_department') === 'دبلوم وسائط متعدده' ? 'selected' : '' }}>دبلوم وسائط متعدده | Multimedia Diploma</option>
                            <option value="علوم تمريض" {{ old('student_department') === 'علوم تمريض' ? 'selected' : '' }}>علوم تمريض | Nursing Sciences</option>
                            <option value="علوم مختبرات" {{ old('student_department') === 'علوم مختبرات' ? 'selected' : '' }}>علوم مختبرات | Laboratory Sciences</option>
                            <option value="علاج طبيعي" {{ old('student_department') === 'علاج طبيعي' ? 'selected' : '' }}>علاج طبيعي | Physical Therapy</option>
                        </select>
                    </div>
                </div>

                <!-- Professor Fields -->
                <div id="professor-fields-group" style="display: none;">
                    <div class="form-group">
                        <label for="professor_id">
                            <span class="lang-ar">الرقم الوظيفي *</span>
                            <span class="lang-en">Professor / Staff ID *</span>
                        </label>
                        <input type="text" name="professor_id" id="professor_id" class="form-control" value="{{ old('professor_id') }}">
                    </div>

                    <div class="form-group">
                        <label for="title">
                            <span class="lang-ar">اللقب الأكاديمي * (مثال: د.، أ.د.، أستاذ مشارك)</span>
                            <span class="lang-en">Academic Title * (e.g. Dr., Prof.)</span>
                        </label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="د. / Dr.">
                    </div>
                    
                    <div class="form-group">
                        <label for="professor_dept">
                            <span class="lang-ar">القسم الأكاديمي *</span>
                            <span class="lang-en">Academic Department *</span>
                        </label>
                        <select name="professor_department" id="professor_dept" class="form-control">
                            <option value="" disabled {{ !old('professor_department') ? 'selected' : '' }}>
                                اختر القسم الأكاديمي... | Select Department...
                            </option>
                            <option value="طب و جراحة" {{ old('professor_department') === 'طب و جراحة' ? 'selected' : '' }}>طب و جراحة | Medicine & Surgery</option>
                            <option value="هندسة طبية" {{ old('professor_department') === 'هندسة طبية' ? 'selected' : '' }}>هندسة طبية | Biomedical Engineering</option>
                            <option value="هندسة اتصالات" {{ old('professor_department') === 'هندسة اتصالات' ? 'selected' : '' }}>هندسة اتصالات | Telecommunications Engineering</option>
                            <option value="هندسة الكترونيات" {{ old('professor_department') === 'هندسة الكترونيات' ? 'selected' : '' }}>هندسة الكترونيات | Electronics Engineering</option>
                            <option value="هندسة ميكاترونكس" {{ old('professor_department') === 'هندسة ميكاترونكس' ? 'selected' : '' }}>هندسة ميكاترونكس | Mechatronics Engineering</option>
                            <option value="تقنية معلومات" {{ old('professor_department') === 'تقنية معلومات' ? 'selected' : '' }}>تقنية معلومات | Information Technology</option>
                            <option value="ذكاء اصطناعي" {{ old('professor_department') === 'ذكاء اصطناعي' ? 'selected' : '' }}>ذكاء اصطناعي | Artificial Intelligence</option>
                            <option value="هندسة برمجيات" {{ old('professor_department') === 'هندسة برمجيات' ? 'selected' : '' }}>هندسة برمجيات | Software Engineering</option>
                            <option value="دبلوم تقنية معلومات" {{ old('professor_department') === 'دبلوم تقنية معلومات' ? 'selected' : '' }}>دبلوم تقنية معلومات | IT Diploma</option>
                            <option value="ادارة اعمال" {{ old('professor_department') === 'ادارة اعمال' ? 'selected' : '' }}>ادارة اعمال | Business Administration</option>
                            <option value="اقتصاد" {{ old('professor_department') === 'اقتصاد' ? 'selected' : '' }}>اقتصاد | Economics</option>
                            <option value="وسائط الاعلام و الاتصال" {{ old('professor_department') === 'وسائط الاعلام و الاتصال' ? 'selected' : '' }}>وسائط الاعلام و الاتصال | Media & Communication</option>
                            <option value="دبلوم وسائط متعدده" {{ old('professor_department') === 'دبلوم وسائط متعدده' ? 'selected' : '' }}>دبلوم وسائط متعدده | Multimedia Diploma</option>
                            <option value="علوم تمريض" {{ old('professor_department') === 'علوم تمريض' ? 'selected' : '' }}>علوم تمريض | Nursing Sciences</option>
                            <option value="علوم مختبرات" {{ old('professor_department') === 'علوم مختبرات' ? 'selected' : '' }}>علوم مختبرات | Laboratory Sciences</option>
                            <option value="علاج طبيعي" {{ old('professor_department') === 'علاج طبيعي' ? 'selected' : '' }}>علاج طبيعي | Physical Therapy</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;" id="reg-submit-btn">
                    <span class="lang-ar">إنشاء حساب</span>
                    <span class="lang-en">Register Account</span>
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function toggleLanguage() {
            const currentLang = localStorage.getItem('lang') || 'ar';
            const newLang = currentLang === 'ar' ? 'en' : 'ar';
            localStorage.setItem('lang', newLang);
            window.location.reload();
        }

        function togglePasswordVisibility(fieldId) {
            const passwordField = document.getElementById(fieldId);
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }

        function switchTab(tab) {
            const loginTab = document.getElementById('tab-login');
            const regTab = document.getElementById('tab-register');
            const loginForm = document.getElementById('form-login-el');
            const regForm = document.getElementById('form-register-el');

            if (tab === 'login') {
                loginTab.classList.add('active');
                regTab.classList.remove('active');
                loginForm.style.display = 'block';
                regForm.style.display = 'none';
            } else {
                loginTab.classList.remove('active');
                regTab.classList.add('active');
                loginForm.style.display = 'none';
                regForm.style.display = 'block';
            }
        }

        function toggleRoleFields() {
            const role = document.getElementById('reg-role').value;
            const studentFields = document.getElementById('student-fields-group');
            const professorFields = document.getElementById('professor-fields-group');

            const studentIdInput = document.getElementById('student_id');
            const studentDeptSelect = document.getElementById('student_dept');
            
            const professorIdInput = document.getElementById('professor_id');
            const titleInput = document.getElementById('title');
            const professorDeptSelect = document.getElementById('professor_dept');

            if (role === 'student') {
                studentFields.style.display = 'block';
                professorFields.style.display = 'none';

                studentIdInput.required = true;
                studentDeptSelect.required = true;

                professorIdInput.required = false;
                titleInput.required = false;
                professorDeptSelect.required = false;
            } else if (role === 'professor') {
                studentFields.style.display = 'none';
                professorFields.style.display = 'block';

                studentIdInput.required = false;
                studentDeptSelect.required = false;

                professorIdInput.required = true;
                titleInput.required = true;
                professorDeptSelect.required = true;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleRoleFields();
        });
    </script>
@endsection
