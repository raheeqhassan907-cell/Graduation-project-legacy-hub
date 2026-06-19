@extends('layouts.app')

@title('الملف الشخصي - مركز إرث لمشاريع التخرج')

@section('styles')
    <style>
        .profile-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
        }
        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }
        .profile-sidebar-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 30px 20px;
        }
        .profile-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(30, 61, 115, 0.08);
            color: var(--primary);
            font-size: 3.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 15px;
            border: 4px solid var(--border-glass);
            background-size: cover;
            background-position: center;
        }
        /* SVG Circle progress styles */
        .completion-widget {
            margin-top: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .circular-progress {
            position: relative;
            height: 100px;
            width: 100px;
            border-radius: 50%;
            background: conic-gradient(var(--accent) calc({{ $completion }} * 3.6deg), #e2e8f0 0deg);
        }
        .circular-progress::before {
            content: "";
            position: absolute;
            height: 84px;
            width: 84px;
            border-radius: 50%;
            background-color: var(--bg-secondary);
        }
        .progress-value {
            position: relative;
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary);
        }
    </style>
@endsection

@section('content')
    <div class="profile-container">
        <!-- Sidebar Column (Picture, Role, Completion) -->
        <div class="glass-panel profile-sidebar-card">
            <div class="profile-avatar-large" style="{{ $user->profile_image ? 'background-image: url(' . asset($user->profile_image) . '); color: transparent;' : '' }}">
                @if(!$user->profile_image)
                    {{ mb_substr($user->name, 0, 1) }}
                @endif
            </div>
            
            <h3 style="margin-bottom: 5px;">{{ $user->name }}</h3>
            <span class="badge badge-role {{ $user->role }}" style="font-size: 0.8rem; padding: 4px 12px; border-radius: 6px;">
                @if($user->role === 'admin')
                    <span class="lang-ar">مسؤول النظام</span><span class="lang-en">Admin</span>
                @elseif($user->role === 'professor')
                    <span class="lang-ar">أستاذ مشرف</span><span class="lang-en">Supervisor</span>
                @elseif($user->role === 'graduate')
                    <span class="lang-ar">خريج</span><span class="lang-en">Graduate</span>
                @else
                    <span class="lang-ar">طالب</span><span class="lang-en">Student</span>
                @endif
            </span>

            <!-- Completion Widget -->
            <div class="completion-widget">
                <span class="lang-ar" style="font-size: 0.85rem; color: var(--text-secondary); font-weight: bold;">نسبة إكمال الملف</span>
                <span class="lang-en" style="font-size: 0.85rem; color: var(--text-secondary); font-weight: bold;">Profile Completion</span>
                
                <div class="circular-progress">
                    <span class="progress-value">{{ $completion }}%</span>
                </div>
            </div>
        </div>

        <!-- Main Form Column (Editing Details) -->
        <div class="glass-panel" style="padding: 40px;">
            <h3 style="margin-bottom: 25px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                📝 <span class="lang-ar">تعديل بيانات الحساب</span><span class="lang-en">Edit Profile Information</span>
            </h3>

            @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 20px;">
                    <div>
                        @foreach($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="name">
                            <span class="lang-ar">الاسم الكامل *</span><span class="lang-en">Full Name *</span>
                        </label>
                        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $user->name) }}">
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <span class="lang-ar">البريد الإلكتروني *</span><span class="lang-en">Email Address *</span>
                        </label>
                        <input type="email" name="email" id="email" class="form-control" required value="{{ old('email', $user->email) }}">
                    </div>

                    <div class="form-group">
                        <label for="phone">
                            <span class="lang-ar">رقم الهاتف</span><span class="lang-en">Phone Number</span>
                        </label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="مثال: 0912345678" value="{{ old('phone', $user->phone) }}">
                    </div>

                    <div class="form-group">
                        <label for="profile_image">
                            <span class="lang-ar">الصورة الشخصية</span><span class="lang-en">Profile Picture</span>
                        </label>
                        <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*" style="padding: 5px 10px;">
                    </div>
                </div>

                <!-- Role specific fields -->
                @if($user->isStudent())
                    <h4 style="margin: 25px 0 15px 0; color: var(--accent);">
                        👨‍🎓 <span class="lang-ar">بيانات الطالب الدراسية</span><span class="lang-en">Student Academic Details</span>
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="student_id">
                                <span class="lang-ar">الرقم الجامعي *</span><span class="lang-en">Student ID *</span>
                            </label>
                            <input type="text" name="student_id" id="student_id" class="form-control" required value="{{ old('student_id', $user->student_id) }}">
                        </div>
                        <div class="form-group">
                            <label for="department">
                                <span class="lang-ar">القسم / التخصص *</span><span class="lang-en">Department / Specialty *</span>
                            </label>
                            <input type="text" name="department" id="department" class="form-control" required value="{{ old('department', $user->department) }}">
                        </div>
                    </div>
                @elseif($user->isGraduate())
                    <h4 style="margin: 25px 0 15px 0; color: var(--accent);">
                        👨‍💻 <span class="lang-ar">البيانات المهنية والتخرج</span><span class="lang-en">Graduate Professional Details</span>
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="graduation_year">
                                <span class="lang-ar">سنة التخرج *</span><span class="lang-en">Graduation Year *</span>
                            </label>
                            <input type="number" name="graduation_year" id="graduation_year" class="form-control" required value="{{ old('graduation_year', $user->graduation_year) }}">
                        </div>
                        <div class="form-group">
                            <label for="job_title">
                                <span class="lang-ar">المسمى الوظيفي *</span><span class="lang-en">Job Title *</span>
                            </label>
                            <input type="text" name="job_title" id="job_title" class="form-control" required value="{{ old('job_title', $user->job_title) }}">
                        </div>
                        <div class="form-group">
                            <label for="company">
                                <span class="lang-ar">الشركة الحالية *</span><span class="lang-en">Company *</span>
                            </label>
                            <input type="text" name="company" id="company" class="form-control" required value="{{ old('company', $user->company) }}">
                        </div>
                        <div class="form-group">
                            <label for="expertise">
                                <span class="lang-ar">مجالات الخبرة *</span><span class="lang-en">Expertise *</span>
                            </label>
                            <input type="text" name="expertise" id="expertise" class="form-control" required value="{{ old('expertise', $user->expertise) }}">
                        </div>
                    </div>
                @elseif($user->isProfessor())
                    <h4 style="margin: 25px 0 15px 0; color: var(--accent);">
                        👨‍🏫 <span class="lang-ar">البيانات الأكاديمية والوظيفة</span><span class="lang-en">Supervisor Academic Details</span>
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="professor_id">
                                <span class="lang-ar">الرقم الوظيفي *</span><span class="lang-en">Employee ID *</span>
                            </label>
                            <input type="text" name="professor_id" id="professor_id" class="form-control" required value="{{ old('professor_id', $user->professor_id) }}">
                        </div>
                        <div class="form-group">
                            <label for="title">
                                <span class="lang-ar">اللقب الأكاديمي *</span><span class="lang-en">Academic Title *</span>
                            </label>
                            <input type="text" name="title" id="title" class="form-control" required placeholder="مثال: دكتور، أستاذ مشارك" value="{{ old('title', $user->title) }}">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="department">
                                <span class="lang-ar">القسم الأكاديمي *</span><span class="lang-en">Academic Department *</span>
                            </label>
                            <input type="text" name="department" id="department" class="form-control" required value="{{ old('department', $user->department) }}">
                        </div>
                    </div>
                @endif

                <button type="submit" class="btn btn-primary" style="margin-top: 30px; padding: 10px 30px;">
                    <span class="lang-ar">حفظ التغييرات</span><span class="lang-en">Save Profile Changes</span>
                </button>
            </form>
        </div>
    </div>
@endsection
