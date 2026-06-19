@extends('layouts.app')

@title('إدارة المستخدمين - لوحة المسؤول')

@section('styles')
    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .modal-card {
            background: #111d43;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            width: 90%;
            max-width: 650px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
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
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2>👥 
                <span class="lang-ar">إدارة جميع الأعضاء</span><span class="lang-en">User Accounts Management</span>
            </h2>
        </div>
        <div>
            <button onclick="showAddUserModal()" class="btn btn-primary">
                <span class="lang-ar">➕ إضافة حساب جديد</span><span class="lang-en">➕ Add New Account</span>
            </button>
        </div>
    </div>

    <!-- Error Alert in Modal Redirects -->
    @if($errors->any() && session('show_add_modal'))
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <div>
                @foreach($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Add User Modal Form -->
    <div id="add-user-modal" class="modal-overlay" style="display: {{ session('show_add_modal') ? 'flex' : 'none' }};">
        <div class="modal-card glass-panel">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-glass); padding-bottom: 15px;">
                <h3 style="margin: 0; color: var(--primary);">
                    <span class="lang-ar">➕ إضافة حساب عضو جديد</span><span class="lang-en">➕ Add New User Account</span>
                </h3>
                <button onclick="hideAddUserModal()" style="background: none; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="name">
                            <span class="lang-ar">الاسم الكامل *</span><span class="lang-en">Full Name *</span>
                        </label>
                        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <span class="lang-ar">البريد الإلكتروني *</span><span class="lang-en">Email Address *</span>
                        </label>
                        <input type="email" name="email" id="email" class="form-control" required value="{{ old('email') }}">
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <span class="lang-ar">كلمة المرور *</span><span class="lang-en">Password *</span>
                        </label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="form-control" required style="padding-left: 40px; padding-right: 40px;">
                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password')">👁️</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">
                            <span class="lang-ar">رقم الهاتف</span><span class="lang-en">Phone Number</span>
                        </label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label for="role">
                            <span class="lang-ar">نوع الحساب (الصلاحية) *</span><span class="lang-en">Account Role *</span>
                        </label>
                        <select name="role" id="role" class="form-control" onchange="toggleModalRoleFields(this.value)" required>
                            <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>طالب | Student</option>
                            <option value="graduate" {{ old('role') === 'graduate' ? 'selected' : '' }}>خريج | Graduate</option>
                            <option value="professor" {{ old('role') === 'professor' ? 'selected' : '' }}>أستاذ مشرف | Supervisor</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>مسؤول نظام | Admin</option>
                        </select>
                    </div>
                </div>

                <!-- Student Fields -->
                <div id="modal-fields-student" class="modal-role-fields" style="display: block; margin-top: 15px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="student_id">
                                <span class="lang-ar">الرقم الجامعي *</span><span class="lang-en">Student ID *</span>
                            </label>
                            <input type="text" name="student_id" id="student_id" class="form-control" value="{{ old('student_id') }}">
                        </div>
                        <div class="form-group">
                            <label for="student_dept">
                                <span class="lang-ar">القسم / التخصص *</span><span class="lang-en">Department *</span>
                            </label>
                            <input type="text" name="student_department" id="student_dept" class="form-control" value="{{ old('student_department') }}">
                        </div>
                    </div>
                </div>

                <!-- Graduate Fields -->
                <div id="modal-fields-graduate" class="modal-role-fields" style="display: none; margin-top: 15px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="graduation_year">
                                <span class="lang-ar">سنة التخرج *</span><span class="lang-en">Graduation Year *</span>
                            </label>
                            <input type="number" name="graduation_year" id="graduation_year" class="form-control" value="{{ old('graduation_year') }}">
                        </div>
                        <div class="form-group">
                            <label for="job_title">
                                <span class="lang-ar">المسمى الوظيفي *</span><span class="lang-en">Job Title *</span>
                            </label>
                            <input type="text" name="job_title" id="job_title" class="form-control" value="{{ old('job_title') }}">
                        </div>
                        <div class="form-group">
                            <label for="company">
                                <span class="lang-ar">الشركة الحالية *</span><span class="lang-en">Company *</span>
                            </label>
                            <input type="text" name="company" id="company" class="form-control" value="{{ old('company') }}">
                        </div>
                        <div class="form-group">
                            <label for="expertise">
                                <span class="lang-ar">مجالات الخبرة *</span><span class="lang-en">Expertise *</span>
                            </label>
                            <input type="text" name="expertise" id="expertise" class="form-control" value="{{ old('expertise') }}">
                        </div>
                    </div>
                </div>

                <!-- Professor Fields -->
                <div id="modal-fields-professor" class="modal-role-fields" style="display: none; margin-top: 15px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="professor_id">
                                <span class="lang-ar">الرقم الوظيفي *</span><span class="lang-en">Employee ID *</span>
                            </label>
                            <input type="text" name="professor_id" id="professor_id" class="form-control" value="{{ old('professor_id') }}">
                        </div>
                        <div class="form-group">
                            <label for="title">
                                <span class="lang-ar">اللقب الأكاديمي *</span><span class="lang-en">Academic Title *</span>
                            </label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="مثال: دكتور" value="{{ old('title') }}">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="professor_dept">
                                <span class="lang-ar">القسم الأكاديمي *</span><span class="lang-en">Academic Department *</span>
                            </label>
                            <input type="text" name="professor_department" id="professor_dept" class="form-control" value="{{ old('professor_department') }}">
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <span class="lang-ar">إنشاء الحساب</span><span class="lang-en">Create Account</span>
                    </button>
                    <button type="button" onclick="hideAddUserModal()" class="btn btn-secondary" style="flex: 1;">
                        <span class="lang-ar">إلغاء</span><span class="lang-en">Cancel</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="glass-panel" style="padding: 30px; overflow-x: auto;">
        <table class="admin-table" style="width: 100%;">
            <thead>
                <tr>
                    <th><span class="lang-ar">الاسم الكامل</span><span class="lang-en">Full Name</span></th>
                    <th><span class="lang-ar">البريد الإلكتروني</span><span class="lang-en">Email Address</span></th>
                    <th><span class="lang-ar">نوع الحساب</span><span class="lang-en">Role</span></th>
                    <th><span class="lang-ar">الرقم الجامعي / الوظيفي</span><span class="lang-en">ID Number</span></th>
                    <th><span class="lang-ar">القسم / التخصص</span><span class="lang-en">Department / Specialty</span></th>
                    <th style="text-align: center;"><span class="lang-ar">العمليات</span><span class="lang-en">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td style="font-weight: bold; display: flex; align-items: center; gap: 10px;">
                            <div class="user-avatar-nav" style="width: 32px; height: 32px; font-size: 0.9rem; {{ $user->isProfessor() ? 'background: var(--success);' : '' }} {{ $user->isAdmin() ? 'background: var(--accent);' : '' }}">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            {{ $user->name }}
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge badge-role {{ $user->role }}">
                                @if($user->role === 'student')
                                    <span class="lang-ar">طالب</span><span class="lang-en">Student</span>
                                @elseif($user->role === 'graduate')
                                    <span class="lang-ar">خريج</span><span class="lang-en">Graduate</span>
                                @elseif($user->role === 'professor')
                                    <span class="lang-ar">أستاذ مشرف</span><span class="lang-en">Supervisor</span>
                                @else
                                    <span class="lang-ar">مسؤول</span><span class="lang-en">Admin</span>
                                @endif
                            </span>
                        </td>
                        <td>
                            @if($user->isStudent())
                                {{ $user->student_id }}
                            @elseif($user->isProfessor())
                                {{ $user->professor_id }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $user->department ?? '-' }}</td>
                        <td style="text-align: center;">
                            @if(Auth::id() != $user->id)
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('⚠️ تحذير: حذف هذا المستخدم سيؤدي لحذف جميع مساهماته ومحاضراته وأسئلته وإجاباته نهائياً! هل أنت متأكد؟');" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <span class="lang-ar">حذف الحساب</span><span class="lang-en">Delete</span>
                                    </button>
                                </form>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.85rem;" class="lang-ar">حسابك الحالي</span>
                                <span style="color: var(--text-muted); font-size: 0.85rem;" class="lang-en">Your account</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <script>
        function showAddUserModal() {
            document.getElementById('add-user-modal').style.display = 'flex';
        }

        function hideAddUserModal() {
            document.getElementById('add-user-modal').style.display = 'none';
        }

        function togglePasswordVisibility(fieldId) {
            const passwordField = document.getElementById(fieldId);
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }

        function toggleModalRoleFields(role) {
            const fields = document.querySelectorAll('.modal-role-fields');
            fields.forEach(f => f.style.display = 'none');

            if (role === 'student') {
                document.getElementById('modal-fields-student').style.display = 'block';
            } else if (role === 'graduate') {
                document.getElementById('modal-fields-graduate').style.display = 'block';
            } else if (role === 'professor') {
                document.getElementById('modal-fields-professor').style.display = 'block';
            }
        }

        // Initialize modal role fields based on default/old selection
        window.addEventListener('DOMContentLoaded', () => {
            const defaultRole = document.getElementById('role').value;
            toggleModalRoleFields(defaultRole);
        });
    </script>
@endsection
