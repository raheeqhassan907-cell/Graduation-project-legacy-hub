@extends('layouts.app')

@title('لوحة تحكم مسؤول النظام - مركز إرث لمشاريع التخرج')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>
            <span class="lang-ar">لوحة تحكم المسؤول العام ⚙️</span><span class="lang-en">General Admin Dashboard ⚙️</span>
        </h2>
        <a href="{{ route('admin.users') }}" class="btn btn-primary">
            <span class="lang-ar">👥 إدارة جميع الأعضاء</span><span class="lang-en">👥 Manage All Users</span>
        </a>
    </div>

    <!-- Admin Stats Grid -->
    <div class="dashboard-grid">
        <div class="dashboard-stat-card glass-panel">
            <div class="stat-icon">👨‍🎓</div>
            <div>
                <div style="font-size: 1.8rem; font-weight: bold; font-family: 'Outfit';">{{ $stats['total_students'] }}</div>
                <div style="color: var(--text-secondary); font-size: 0.85rem;" class="lang-ar">الطلاب المسجلين</div>
                <div style="color: var(--text-secondary); font-size: 0.85rem;" class="lang-en">Registered Students</div>
            </div>
        </div>

        <div class="dashboard-stat-card glass-panel">
            <div class="stat-icon" style="color: #ff7675; background: rgba(232, 67, 147, 0.1);">👨‍💻</div>
            <div>
                <div style="font-size: 1.8rem; font-weight: bold; font-family: 'Outfit';">{{ $stats['total_graduates'] }}</div>
                <div style="color: var(--text-secondary); font-size: 0.85rem;" class="lang-ar">الخريجين المسجلين</div>
                <div style="color: var(--text-secondary); font-size: 0.85rem;" class="lang-en">Registered Graduates</div>
            </div>
        </div>

        <div class="dashboard-stat-card glass-panel">
            <div class="stat-icon" style="color: #55efc4; background: rgba(0, 184, 148, 0.1);">👨‍🏫</div>
            <div>
                <div style="font-size: 1.8rem; font-weight: bold; font-family: 'Outfit';">{{ $stats['total_professors'] }}</div>
                <div style="color: var(--text-secondary); font-size: 0.85rem;" class="lang-ar">أعضاء هيئة التدريس</div>
                <div style="color: var(--text-secondary); font-size: 0.85rem;" class="lang-en">Faculty Members</div>
            </div>
        </div>

        <div class="dashboard-stat-card glass-panel">
            <div class="stat-icon" style="color: #ffeaa7; background: rgba(253, 203, 110, 0.1);">📚</div>
            <div>
                <div style="font-size: 1.8rem; font-weight: bold; font-family: 'Outfit';">{{ $stats['total_projects'] }}</div>
                <div style="color: var(--text-secondary); font-size: 0.85rem;" class="lang-ar">مشاريع التخرج الكلية</div>
                <div style="color: var(--text-secondary); font-size: 0.85rem;" class="lang-en">Total Projects</div>
            </div>
        </div>
    </div>

    <!-- Details Section -->
    <div class="dashboard-layout">
        <!-- Project Specialties Breakdown -->
        <div class="glass-panel" style="padding: 30px;">
            <h3 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                <span class="lang-ar">توزيع المشاريع حسب التخصص</span><span class="lang-en">Projects by Specialty</span>
            </h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th><span class="lang-ar">التخصص</span><span class="lang-en">Specialty</span></th>
                        <th><span class="lang-ar">عدد المشاريع المرفوعة</span><span class="lang-en">Projects Count</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($specialtyStats as $stat)
                        <tr>
                            <td style="font-weight: bold;">{{ $stat->specialty }}</td>
                            <td style="font-family: 'Outfit'; font-size: 1.1rem; color: var(--primary);">{{ $stat->count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center; color: var(--text-muted);">
                                <span class="lang-ar">لا توجد إحصائيات للمشاريع حالياً.</span><span class="lang-en">No project statistics yet.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Latest Registrations & Uploads -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Latest Uploaded Projects -->
            <div class="glass-panel" style="padding: 25px;">
                <h3 style="margin-bottom: 15px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                    <span class="lang-ar">أحدث مشاريع التخرج المرفوعة</span><span class="lang-en">Latest Uploaded Projects</span>
                </h3>
                <ul style="list-style: none;">
                    @forelse($latestProjects as $project)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.03);">
                            <div>
                                <a href="{{ route('projects.show', $project->id) }}" style="font-weight: bold; hover: color(var(--primary));">{{ $project->title }}</a>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                    <span class="lang-ar">بواسطة: {{ $project->graduate ? $project->graduate->name : 'النظام' }} | سنة {{ $project->year }}</span>
                                    <span class="lang-en">By: {{ $project->graduate ? $project->graduate->name : 'System' }} | Year {{ $project->year }}</span>
                                </div>
                            </div>
                            <span class="badge badge-specialty">{{ $project->specialty }}</span>
                        </li>
                    @empty
                        <li style="color: var(--text-muted); text-align: center; padding: 15px;" class="lang-ar">لا توجد مشاريع مرفوعة.</li>
                        <li style="color: var(--text-muted); text-align: center; padding: 15px;" class="lang-en">No projects uploaded.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Latest Registered Users -->
            <div class="glass-panel" style="padding: 25px;">
                <h3 style="margin-bottom: 15px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                    <span class="lang-ar">أحدث الحسابات المسجلة بالمنصة</span><span class="lang-en">Latest User Registrations</span>
                </h3>
                <ul style="list-style: none;">
                    @forelse($latestUsers as $user)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.03);">
                            <div>
                                <span style="font-weight: bold;">{{ $user->name }}</span>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">{{ $user->email }}</div>
                            </div>
                            <span class="badge badge-role {{ $user->role }}">
                                @if($user->role === 'student')
                                    <span class="lang-ar">طالب</span><span class="lang-en">Student</span>
                                @elseif($user->role === 'graduate')
                                    <span class="lang-ar">خريج</span><span class="lang-en">Graduate</span>
                                @elseif($user->role === 'professor')
                                    <span class="lang-ar">أستاذ</span><span class="lang-en">Faculty</span>
                                @else
                                    <span class="lang-ar">مسؤول</span><span class="lang-en">Admin</span>
                                @endif
                            </span>
                        </li>
                    @empty
                        <li style="color: var(--text-muted); text-align: center; padding: 15px;" class="lang-ar">لا يوجد مستخدمون مسجلون.</li>
                        <li style="color: var(--text-muted); text-align: center; padding: 15px;" class="lang-en">No users registered yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
