@extends('layouts.app')

@title('تحليل المشاريع - لوحة المسؤول')

@section('styles')
    <style>
        .analytics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }
        @media (max-width: 900px) {
            .analytics-grid {
                grid-template-columns: 1fr;
            }
        }
        .analytics-card {
            padding: 30px;
        }
        
        /* Pure CSS Bar Chart style */
        .chart-container {
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            height: 220px;
            padding-top: 20px;
            border-bottom: 2px solid var(--border-color);
            border-right: 2px solid var(--border-color);
            margin-top: 15px;
        }
        .chart-bar-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            height: 100%;
            justify-content: flex-end;
        }
        .chart-bar {
            width: 32px;
            background: linear-gradient(180deg, var(--accent) 0%, var(--primary) 100%);
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
            position: relative;
            transition: height 0.5s ease-in-out;
            min-height: 5px;
        }
        .chart-bar-value {
            position: absolute;
            top: -22px;
            font-size: 0.8rem;
            font-weight: bold;
            color: var(--primary);
        }
        .chart-label {
            margin-top: 10px;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        /* Progress lists */
        .progress-list-item {
            margin-bottom: 18px;
        }
        .progress-list-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            margin-bottom: 5px;
            color: var(--text-secondary);
        }
        .progress-bar-bg {
            background: rgba(30, 90, 160, 0.08);
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
        }
        .progress-bar-fill {
            background: var(--primary);
            height: 100%;
            border-radius: 5px;
        }

        /* Tech cloud list */
        .tech-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        .tech-cloud-item {
            background: rgba(30, 90, 160, 0.05);
            border: 1px solid var(--border-glass);
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tech-count-badge {
            background: rgba(214, 48, 49, 0.08);
            color: var(--accent);
            font-size: 0.75rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid rgba(214, 48, 49, 0.15);
        }
    </style>
@endsection

@section('content')
    <div style="margin-bottom: 2.5rem;">
        <h2>📈 
            <span class="lang-ar">تحليلات وإحصائيات المشاريع</span><span class="lang-en">Project Analytics & Statistics</span>
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            <span class="lang-ar">عرض تحليلي تفصيلي لتوزيع المشاريع الأكاديمية على السنوات والأقسام، والتقنيات ونشاطات المشرفين.</span>
            <span class="lang-en">Detailed analytical charts showing project distributions across years, departments, supervisors, and technologies.</span>
        </p>
    </div>

    <div class="analytics-grid">
        <!-- 1. Years Distribution Chart -->
        <div class="glass-panel analytics-card">
            <h3>📅 
                <span class="lang-ar">توزيع المشاريع على السنوات</span><span class="lang-en">Projects Distribution by Year</span>
            </h3>
            
            <div class="chart-container">
                @php
                    $maxYearCount = $yearsDist->max('count') ?: 1;
                @endphp
                @forelse($yearsDist as $yd)
                    @php
                        $heightPercent = ($yd->count / $maxYearCount) * 100;
                    @endphp
                    <div class="chart-bar-wrapper">
                        <div class="chart-bar" style="height: {{ $heightPercent }}%;">
                            <span class="chart-bar-value">{{ $yd->count }}</span>
                        </div>
                        <div class="chart-label">{{ $yd->year }}</div>
                    </div>
                @empty
                    <div style="color: var(--text-muted); padding: 50px 0;" class="lang-ar">لا توجد مشاريع إحصائية كافية</div>
                    <div style="color: var(--text-muted); padding: 50px 0;" class="lang-en">No statistical data available</div>
                @endforelse
            </div>
        </div>

        <!-- 2. Most Active Departments -->
        <div class="glass-panel analytics-card">
            <h3>🏢 
                <span class="lang-ar">أكثر الأقسام نشاطاً في المشاريع</span><span class="lang-en">Most Active Departments</span>
            </h3>
            
            <div style="margin-top: 20px;">
                @php
                    $maxDeptCount = $deptsDist->max('count') ?: 1;
                @endphp
                @forelse($deptsDist as $dd)
                    @php
                        $fillPercent = ($dd->count / $maxDeptCount) * 100;
                    @endphp
                    <div class="progress-list-item">
                        <div class="progress-list-header">
                            <span style="font-weight: bold; color: var(--primary);">{{ $dd->specialty }}</span>
                            <span style="color: var(--text-secondary);">{{ $dd->count }} <span class="lang-ar">مشروع</span><span class="lang-en">projects</span></span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ $fillPercent }}%;"></div>
                        </div>
                    </div>
                @empty
                    <p style="color: var(--text-muted);" class="lang-ar">لا توجد مشاريع حالياً.</p>
                    <p style="color: var(--text-muted);" class="lang-en">No projects available.</p>
                @endforelse
            </div>
        </div>

        <!-- 3. Active Supervisors Leaderboard -->
        <div class="glass-panel analytics-card">
            <h3>👨‍🏫 
                <span class="lang-ar">أكثر المشرفين نشاطاً (عدد الإشرافات)</span><span class="lang-en">Most Active Supervisors</span>
            </h3>
            
            <div style="margin-top: 20px;">
                @php
                    $maxSuperCount = $supervisors->max('supervised_projects_count') ?: 1;
                @endphp
                @forelse($supervisors as $sup)
                    @php
                        $fillPercent = ($sup->supervised_projects_count / $maxSuperCount) * 100;
                    @endphp
                    <div class="progress-list-item">
                        <div class="progress-list-header">
                            <span style="font-weight: bold; color: var(--primary);">{{ $sup->title }} {{ $sup->name }} ({{ $sup->department }})</span>
                            <span style="font-weight: bold; color: var(--primary);">{{ $sup->supervised_projects_count }}</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ $fillPercent }}%; background: var(--success);"></div>
                        </div>
                    </div>
                @empty
                    <p style="color: var(--text-muted);" class="lang-ar">لا يوجد مشرفين نشطين حالياً.</p>
                    <p style="color: var(--text-muted);" class="lang-en">No active supervisors yet.</p>
                @endforelse
            </div>
        </div>

        <!-- 4. Most Used Technologies -->
        <div class="glass-panel analytics-card">
            <h3>💻 
                <span class="lang-ar">التقنيات الأكثر استخداماً وتكراراً</span><span class="lang-en">Most Used Technologies</span>
            </h3>
            
            <div class="tech-cloud">
                @forelse($techs as $tech => $count)
                    <div class="tech-cloud-item">
                        <span>{{ $tech }}</span>
                        <span class="tech-count-badge">{{ $count }}</span>
                    </div>
                @empty
                    <p style="color: var(--text-muted); padding: 30px 0;" class="lang-ar">لم يتم رصد تقنيات مستخدمة بعد.</p>
                    <p style="color: var(--text-muted); padding: 30px 0;" class="lang-en">No technologies recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
