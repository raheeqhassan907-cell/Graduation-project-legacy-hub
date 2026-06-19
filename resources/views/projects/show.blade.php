@extends('layouts.app')

@title($project->title . ' - مركز إرث لمشاريع التخرج')

@section('content')
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('projects.index') }}" class="btn btn-secondary btn-sm">
            <span class="lang-ar">🔙 العودة للمشاريع</span><span class="lang-en">🔙 Back to Projects</span>
        </a>
    </div>

    <!-- Project Header Panel -->
    <div class="glass-panel project-header-show" style="margin-bottom: 30px; padding: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
            <div>
                <h1 style="font-size: 2.2rem; margin-bottom: 15px;">{{ $project->title }}</h1>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <span class="badge badge-specialty" style="font-size: 0.85rem; padding: 0.4rem 1rem;">{{ $project->specialty }}</span>
                    <span class="badge" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
                        <span class="lang-ar">السنة: {{ $project->year }}</span>
                        <span class="lang-en">Year: {{ $project->year }}</span>
                    </span>
                </div>
            </div>
            
            @if($project->file_url)
                <a href="{{ asset($project->file_url) }}" target="_blank" class="btn btn-primary" style="padding: 1rem 2rem; border-radius: 8px;">
                    <span class="lang-ar">📥 تنزيل كتاب المشروع (PDF)</span>
                    <span class="lang-en">📥 Download Project PDF</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="project-details" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Left Column: Description & Technologies -->
        <div class="glass-panel" style="padding: 40px; height: fit-content;">
            <h3 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                <span class="lang-ar">وصف المشروع</span><span class="lang-en">Project Description</span>
            </h3>
            <p style="color: var(--text-secondary); font-size: 1.1rem; line-height: 1.8; white-space: pre-line; margin-bottom: 30px;">
                {{ $project->description }}
            </p>

            @if($project->technologies)
                <h3 style="margin-bottom: 15px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                    <span class="lang-ar">التقنيات البرمجية والأدوات</span><span class="lang-en">Technologies & Tools</span>
                </h3>
                <div class="project-tags" style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @foreach(explode(',', $project->technologies) as $tech)
                        <span class="badge" style="font-size: 0.9rem; padding: 0.4rem 1rem; border-radius: 6px;">{{ trim($tech) }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right Column: Authors & Supervisors -->
        <div>
            <!-- Author Card -->
            <div class="glass-panel project-info-card" style="padding: 25px; margin-bottom: 25px;">
                <h3 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                    <span class="lang-ar">الخريجون المشاركون</span><span class="lang-en">Participating Graduates</span>
                </h3>
                
                @if($project->students->isNotEmpty())
                    @foreach($project->students as $student)
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; border-bottom: 1px dashed var(--border-color); padding-bottom: 15px; width: 100%;">
                            <div class="user-avatar-nav" style="width: 40px; height: 40px; font-size: 1rem; flex-shrink: 0;">
                                {{ mb_substr($student->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 style="font-size: 1rem; margin: 0;">{{ $student->name }}</h4>
                                <span class="badge badge-role graduate" style="margin-top: 3px; font-size: 0.75rem; padding: 2px 6px; display: inline-block;">
                                    <span class="lang-ar">خريج دفعة {{ $student->graduation_year }}</span>
                                    <span class="lang-en">Graduate Class of {{ $student->graduation_year }}</span>
                                </span>
                                @if($student->job_title)
                                    <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 4px;">
                                        {{ $student->job_title }} {{ $student->company ? '@ ' . $student->company : '' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @elseif($project->graduate)
                    <!-- Fallback to primary graduate contact -->
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <div class="user-avatar-nav" style="width: 50px; height: 50px; font-size: 1.2rem;">
                            {{ mb_substr($project->graduate->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 style="font-size: 1.1rem;">{{ $project->graduate->name }}</h4>
                            <span class="badge badge-role graduate" style="margin-top: 5px;">
                                <span class="lang-ar">خريج دفعة {{ $project->graduate->graduation_year }}</span>
                                <span class="lang-en">Graduate Class of {{ $project->graduate->graduation_year }}</span>
                            </span>
                        </div>
                    </div>
                    
                    <div class="project-info-item">
                        <div class="project-info-label"><span class="lang-ar">المسمى الوظيفي</span><span class="lang-en">Job Title</span></div>
                        <div class="project-info-value">{{ $project->graduate->job_title }}</div>
                    </div>
                    <div class="project-info-item">
                        <div class="project-info-label"><span class="lang-ar">الجهة الحالية</span><span class="lang-en">Company</span></div>
                        <div class="project-info-value">{{ $project->graduate->company }}</div>
                    </div>
                @else
                    <p style="color: var(--text-muted); text-align: center;" class="lang-ar">صاحب المشروع غير مسجل حالياً بالمنصة.</p>
                    <p style="color: var(--text-muted); text-align: center;" class="lang-en">No author registered for this project.</p>
                @endif
            </div>

            <!-- Supervisor Card -->
            @if($project->supervisor)
                <div class="glass-panel project-info-card" style="padding: 25px;">
                    <h3 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                        <span class="lang-ar">تحت إشراف</span><span class="lang-en">Supervised By</span>
                    </h3>
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <div class="user-avatar-nav" style="width: 50px; height: 50px; font-size: 1.2rem; background: var(--success);">
                            {{ mb_substr($project->supervisor->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 style="font-size: 1.1rem;">{{ $project->supervisor->title }} {{ $project->supervisor->name }}</h4>
                            <span class="badge badge-role professor" style="margin-top: 5px;">
                                <span class="lang-ar">عضو هيئة تدريس</span><span class="lang-en">Faculty Member</span>
                            </span>
                        </div>
                    </div>
                    
                    <div class="project-info-item">
                        <div class="project-info-label"><span class="lang-ar">القسم الدراسي</span><span class="lang-en">Academic Department</span></div>
                        <div class="project-info-value">{{ $project->supervisor->department }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
