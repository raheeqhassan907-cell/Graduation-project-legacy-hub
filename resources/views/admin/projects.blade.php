@extends('layouts.app')

@title('إدارة المشاريع - لوحة المسؤول')

@section('content')
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin-top: 15px;">📁 
                <span class="lang-ar">إدارة مشاريع التخرج</span><span class="lang-en">Manage Graduation Projects</span>
            </h2>
        </div>
        <div>
            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
                <span class="lang-ar">🎓 رفع مشروع جديد</span><span class="lang-en">🎓 Upload New Project</span>
            </a>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="glass-panel" style="padding: 30px; overflow-x: auto;">
        <table class="admin-table" style="width: 100%;">
            <thead>
                <tr>
                    <th><span class="lang-ar">عنوان المشروع</span><span class="lang-en">Project Title</span></th>
                    <th><span class="lang-ar">الخريجون المشاركون</span><span class="lang-en">Graduates</span></th>
                    <th><span class="lang-ar">التخصص</span><span class="lang-en">Specialty</span></th>
                    <th><span class="lang-ar">سنة المشروع</span><span class="lang-en">Year</span></th>
                    <th><span class="lang-ar">المشرف</span><span class="lang-en">Supervisor</span></th>
                    <th style="text-align: center;"><span class="lang-ar">العمليات</span><span class="lang-en">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                    <tr>
                        <td style="font-weight: bold;">
                            <a href="{{ route('projects.show', $project->id) }}" style="color: var(--primary); text-decoration: none;">
                                {{ $project->title }}
                            </a>
                        </td>
                        <td>
                            @if($project->students->isNotEmpty())
                                @foreach($project->students as $student)
                                    <div style="font-size: 0.85rem; font-weight: bold;">• {{ $student->name }}</div>
                                @endforeach
                            @elseif($project->graduate)
                                {{ $project->graduate->name }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-specialty" style="background: rgba(255, 255, 255, 0.08); font-size: 0.8rem; padding: 3px 8px; border-radius: 4px;">
                                {{ $project->specialty }}
                            </span>
                        </td>
                        <td>{{ $project->year }}</td>
                        <td>
                            @if($project->supervisor)
                                {{ $project->supervisor->title }} {{ $project->supervisor->name }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ asset($project->file_url) }}" class="btn btn-secondary btn-sm" target="_blank" style="margin-left: 5px;">
                                <span class="lang-ar">📥 تحميل PDF</span><span class="lang-en">📥 PDF</span>
                            </a>
                            <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('⚠️ هل أنت متأكد من حذف هذا المشروع نهائياً؟ لا يمكن التراجع عن هذا الإجراء.');" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <span class="lang-ar">حذف</span><span class="lang-en">Delete</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 40px;">
                            <span class="lang-ar">لا توجد مشاريع مرفوعة حالياً.</span><span class="lang-en">No projects uploaded yet.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
