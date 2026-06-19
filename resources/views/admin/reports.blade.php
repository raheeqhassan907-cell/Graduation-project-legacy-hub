@extends('layouts.app')

@title('التقارير - لوحة المسؤول')

@section('styles')
    <style>
        .report-section {
            margin-bottom: 40px;
        }
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        /* Print media styles to generate clean PDF print layouts */
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .sidebar, .topbar, .btn, .no-print {
                display: none !important;
            }
            .main-wrapper, .content-body {
                margin: 0 !important;
                padding: 0 !important;
                background: none !important;
                width: 100% !important;
            }
            .glass-panel {
                background: none !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                color: black !important;
            }
            table {
                border-collapse: collapse !important;
                color: black !important;
            }
            th, td {
                border: 1px solid #ddd !important;
                padding: 8px !important;
                color: black !important;
            }
            th {
                background-color: #f2f2f2 !important;
            }
            .report-header h3 {
                color: black !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="no-print" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2>📄 
                <span class="lang-ar">تقارير المنصة الأكاديمية</span><span class="lang-en">Academic Reports</span>
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                <span class="lang-ar">يمكنك هنا تصدير وطباعة تقارير شاملة عن الطلاب والأساتذة والمشاريع المسجلة.</span>
                <span class="lang-en">Export and print comprehensive reports about users, professors, and projects.</span>
            </p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary">
                <span class="lang-ar">🖨️ طباعة التقرير الحالي</span><span class="lang-en">🖨️ Print Report</span>
            </button>
        </div>
    </div>

    <!-- Stats Summary Section -->
    <div class="glass-panel report-section" style="padding: 25px;">
        <div class="report-header">
            <h3>📊 
                <span class="lang-ar">إحصائيات المنصة الإجمالية</span><span class="lang-en">General Platform Statistics</span>
            </h3>
            <span style="font-size: 0.85rem; color: var(--text-secondary);">{{ date('Y-m-d H:i') }}</span>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; text-align: center;">
            <div style="background: rgba(255, 255, 255, 0.02); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                <h4 style="font-size: 2rem; color: var(--accent); margin: 0;">{{ $stats['total_students'] }}</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 5px 0 0 0;" class="lang-ar">الطلاب</p>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 5px 0 0 0;" class="lang-en">Students</p>
            </div>
            <div style="background: rgba(255, 255, 255, 0.02); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                <h4 style="font-size: 2rem; color: var(--accent); margin: 0;">{{ $stats['total_graduates'] }}</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 5px 0 0 0;" class="lang-ar">الخريجين</p>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 5px 0 0 0;" class="lang-en">Graduates</p>
            </div>
            <div style="background: rgba(255, 255, 255, 0.02); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                <h4 style="font-size: 2rem; color: var(--accent); margin: 0;">{{ $stats['total_professors'] }}</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 5px 0 0 0;" class="lang-ar">المشرفين</p>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 5px 0 0 0;" class="lang-en">Supervisors</p>
            </div>
            <div style="background: rgba(255, 255, 255, 0.02); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                <h4 style="font-size: 2rem; color: var(--accent); margin: 0;">{{ $stats['total_projects'] }}</h4>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 5px 0 0 0;" class="lang-ar">المشاريع المرفوعة</p>
                <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 5px 0 0 0;" class="lang-en">Projects Uploaded</p>
            </div>
        </div>
    </div>

    <!-- Users Report Section -->
    <div class="glass-panel report-section" style="padding: 25px; overflow-x: auto;">
        <div class="report-header">
            <h3>👥 
                <span class="lang-ar">تقرير كشف المستخدمين والمنسوبين</span><span class="lang-en">Users Listing Report</span>
            </h3>
        </div>
        <table class="admin-table" style="width: 100%;">
            <thead>
                <tr>
                    <th><span class="lang-ar">الاسم الكامل</span><span class="lang-en">Name</span></th>
                    <th><span class="lang-ar">البريد الإلكتروني</span><span class="lang-en">Email</span></th>
                    <th><span class="lang-ar">رقم الهاتف</span><span class="lang-en">Phone</span></th>
                    <th><span class="lang-ar">نوع الحساب</span><span class="lang-en">Account Type</span></th>
                    <th><span class="lang-ar">الرقم التعريفي</span><span class="lang-en">ID Number</span></th>
                    <th><span class="lang-ar">التخصص / القسم</span><span class="lang-en">Department</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td style="font-weight: bold;">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
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
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Projects Report Section -->
    <div class="glass-panel report-section" style="padding: 25px; overflow-x: auto;">
        <div class="report-header">
            <h3>📁 
                <span class="lang-ar">تقرير كشف مشاريع التخرج المرفوعة</span><span class="lang-en">Graduation Projects Report</span>
            </h3>
        </div>
        <table class="admin-table" style="width: 100%;">
            <thead>
                <tr>
                    <th><span class="lang-ar">عنوان المشروع</span><span class="lang-en">Project Title</span></th>
                    <th><span class="lang-ar">سنة التقديم</span><span class="lang-en">Year</span></th>
                    <th><span class="lang-ar">التخصص / القسم</span><span class="lang-en">Specialty</span></th>
                    <th><span class="lang-ar">الخريجون المشاركون</span><span class="lang-en">Graduates</span></th>
                    <th><span class="lang-ar">الأستاذ المشرف</span><span class="lang-en">Supervisor</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $project)
                    <tr>
                        <td style="font-weight: bold;">{{ $project->title }}</td>
                        <td>{{ $project->year }}</td>
                        <td>{{ $project->specialty }}</td>
                        <td>
                            @if($project->students->isNotEmpty())
                                {{ implode(', ', $project->students->pluck('name')->toArray()) }}
                            @elseif($project->graduate)
                                {{ $project->graduate->name }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($project->supervisor)
                                {{ $project->supervisor->title }} {{ $project->supervisor->name }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
