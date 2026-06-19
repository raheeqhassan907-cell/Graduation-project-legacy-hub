@extends('layouts.app')

@title('تصفح مشاريع التخرج - مركز إرث لمشاريع التخرج')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>
            <span class="lang-ar">تصفح مشاريع التخرج</span><span class="lang-en">Browse Graduation Projects</span>
        </h2>
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('projects.create') }}" class="btn btn-primary">🎓 
                    <span class="lang-ar">رفع مشروع تخرج جديد</span><span class="lang-en">Upload New Project</span>
                </a>
            @endif
        @endauth
    </div>

    <!-- Filters Bar -->
    <form action="{{ route('projects.index') }}" method="GET" class="filters-bar glass-panel" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: end;">
        <div class="filter-item">
            <label for="search">
                <span class="lang-ar">البحث النصي</span><span class="lang-en">Search Text</span>
            </label>
            <input type="text" name="search" id="search" class="form-control" placeholder="اسم المشروع، كلمات دالة..." value="{{ request('search') }}">
        </div>

        <div class="filter-item">
            <label for="specialty">
                <span class="lang-ar">تخصص المشروع</span><span class="lang-en">Specialty</span>
            </label>
            <select name="specialty" id="specialty" class="form-control">
                <option value=""><span class="lang-ar">كل التخصصات</span><span class="lang-en">All Specialties</span></option>
                @foreach($specialties as $spec)
                    <option value="{{ $spec }}" {{ request('specialty') === $spec ? 'selected' : '' }}>{{ $spec }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-item">
            <label for="year">
                <span class="lang-ar">سنة المشروع</span><span class="lang-en">Year</span>
            </label>
            <select name="year" id="year" class="form-control">
                <option value=""><span class="lang-ar">كل السنوات</span><span class="lang-en">All Years</span></option>
                @foreach($years as $yr)
                    <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-item">
            <label for="supervisor_id">
                <span class="lang-ar">الأستاذ المشرف</span><span class="lang-en">Supervisor</span>
            </label>
            <select name="supervisor_id" id="supervisor_id" class="form-control">
                <option value=""><span class="lang-ar">كل المشرفين</span><span class="lang-en">All Supervisors</span></option>
                @foreach($supervisors as $prof)
                    <option value="{{ $prof->id }}" {{ request('supervisor_id') == $prof->id ? 'selected' : '' }}>{{ $prof->title }} {{ $prof->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-item">
            <label for="technology">
                <span class="lang-ar">التقنية المستخدمة</span><span class="lang-en">Technology</span>
            </label>
            <select name="technology" id="technology" class="form-control">
                <option value=""><span class="lang-ar">كل التقنيات</span><span class="lang-en">All Technologies</span></option>
                @foreach($technologies as $tech)
                    <option value="{{ $tech }}" {{ request('technology') === $tech ? 'selected' : '' }}>{{ $tech }}</option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 10px; grid-column: 1 / -1; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 8px 25px;">
                <span class="lang-ar">تطبيق الفلاتر</span><span class="lang-en">Apply Filters</span>
            </button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary" style="padding: 8px 25px; text-align: center; line-height: 2.2;">
                <span class="lang-ar">إعادة تعيين</span><span class="lang-en">Reset</span>
            </a>
        </div>
    </form>

    <!-- Projects Grid -->
    <div class="grid-3">
        @forelse($projects as $project)
            <div class="card glass-panel project-card">
                <h3>{{ $project->title }}</h3>
                
                <div class="project-meta">
                    <span class="badge badge-specialty">{{ $project->specialty }}</span>
                    <span class="badge">{{ $project->year }}</span>
                </div>

                <p class="project-desc">{{ $project->description }}</p>

                @if($project->technologies)
                    <div class="project-tags">
                        @foreach(explode(',', $project->technologies) as $tech)
                            <span class="badge">{{ trim($tech) }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="project-footer">
                    <div class="project-author">
                        <div>
                            @if($project->students->isNotEmpty())
                                <div style="font-size: 0.75rem; color: var(--text-muted);" class="lang-ar">الخريجون المشاركون:</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);" class="lang-en">Graduates:</div>
                                @foreach($project->students as $student)
                                    <div style="font-weight: bold; font-size: 0.85rem;">• {{ $student->name }}</div>
                                @endforeach
                            @elseif($project->graduate)
                                <div class="user-avatar-nav" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                    {{ mb_substr($project->graduate->name, 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight: bold;">{{ $project->graduate->name }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);" class="lang-ar">خريج</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);" class="lang-en">Graduate</div>
                                </div>
                            @else
                                <span style="color: var(--text-muted);" class="lang-ar">بواسطة النظام</span>
                                <span style="color: var(--text-muted);" class="lang-en">By System</span>
                            @endif
                        </div>
                    </div>

                    <div style="display: flex; gap: 8px; align-items: center;">
                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn-primary btn-sm">
                            <span class="lang-ar">عرض</span><span class="lang-en">View</span>
                        </a>
                        
                        @auth
                            @if(Auth::user()->isAdmin())
                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع نهائياً؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.4rem 0.6rem;">🗑️</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="glass-panel" style="grid-column: 1 / -1; padding: 50px; text-align: center; color: var(--text-secondary);">
                <span class="lang-ar">🔍 لم يتم العثور على أي مشاريع تخرج تطابق فلاتر البحث الحالية.</span>
                <span class="lang-en">🔍 No projects found matching the search filters.</span>
            </div>
        @endforelse
    </div>
@endsection
