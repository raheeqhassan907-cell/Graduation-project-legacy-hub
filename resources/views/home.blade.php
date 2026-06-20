@extends('layouts.app')

@title('الرئيسية - GP Legacy Hub')

@section('content')
    <!-- Dashboard Top Banner Widget -->
    <div class="dashboard-banner glass-panel">
        <div class="dashboard-banner-text">
            <h2>استكشف، اسأل، وابنِ إرثك الأكاديمي 🚀</h2>
            <p>تصفح مشاريع التخرج السابقة حسب السنة والتخصص، واطرح أسئلتك على الخريجين والأساتذة للحصول على الدعم الأكاديمي والمهني.</p>
            <div class="dashboard-banner-actions">
                <a href="{{ route('projects.index') }}" class="btn btn-primary">📂 تصفح المشاريع</a>
                <a href="{{ route('forum.index') }}" class="btn btn-secondary" style="background: white; color: var(--primary);">💬 طرح سؤال</a>
            </div>
        </div>

        <div class="dashboard-banner-tags">
            <div class="banner-tag">
                <span style="color: var(--text-muted);">حالة الحساب</span>
                <span style="color: var(--primary); font-weight: 700;">
                    @auth
                        @if(Auth::user()->role === 'admin') مسؤول @elseif(Auth::user()->role === 'professor') أستاذ @elseif(Auth::user()->role === 'graduate') خريج @else طالب @endif
                    @else
                        زائر
                    @endauth
                </span>
            </div>
            <div class="banner-tag">
                <span style="color: var(--text-muted);">الأسئلة النشطة</span>
                <span style="color: var(--accent); font-weight: 700;">{{ $stats['total_students'] + 1 }}</span>
            </div>
            <div class="banner-tag">
                <span style="color: var(--text-muted);">المشاريع الكلية</span>
                <span style="color: var(--primary); font-weight: 700;">{{ $stats['total_projects'] }}</span>
            </div>
        </div>
    </div>

    @auth
    <!-- AI Forecasting & Predictions Layer -->
    <div class="glass-panel" style="padding: 30px; margin-bottom: 30px; position: relative; overflow: hidden; border: 1px solid rgba(30, 61, 115, 0.18);">
        <!-- Background decorative blur glow -->
        <div style="position: absolute; top: -50px; left: -50px; width: 150px; height: 150px; background: rgba(30, 61, 115, 0.12); filter: blur(40px); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -50px; right: -50px; width: 150px; height: 150px; background: rgba(214, 48, 49, 0.05); filter: blur(40px); border-radius: 50%;"></div>

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; border-bottom: 1px solid var(--border-glass); padding-bottom: 15px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 2.2rem; filter: drop-shadow(0 2px 8px rgba(30,61,115,0.2));">🔮</span>
                <div>
                    <h3 style="margin: 0; color: var(--primary); font-size: 1.3rem;" class="lang-ar">محرك التنبؤ والتحليل الذكي للمستقبل (AI Future Trends)</h3>
                    <h3 style="margin: 0; color: var(--primary); font-size: 1.3rem;" class="lang-en">AI Future Trends & Predictions Engine</h3>
                    <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: var(--text-secondary);" class="lang-ar">تنبأ بالاتجاهات المستقبلية للمشاريع والتقنيات بناءً على بيانات سوق العمل والكلية الحالية.</p>
                    <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: var(--text-secondary);" class="lang-en">Predict future graduation projects and tech stacks based on live market analysis.</p>
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="generateAIPredictions()" id="predict-btn" style="padding: 10px 20px; font-size: 0.9rem; border-radius: 30px; display: inline-flex; align-items: center; gap: 8px;">
                <span>🧠</span>
                <span class="lang-ar">توليد التوقعات الذكية للسنة القادمة</span>
                <span class="lang-en">Generate Future AI Predictions</span>
            </button>
        </div>

        <!-- Result Container -->
        <div id="ai-prediction-box" style="background: rgba(255,255,255,0.4); border: 1px solid rgba(30,61,115,0.06); border-radius: 12px; padding: 25px; min-height: 100px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
            <div id="ai-prediction-placeholder" style="text-align: center; color: var(--text-secondary);">
                <p class="lang-ar" style="margin: 0; font-size: 0.95rem;">اضغط على الزر أعلاه لتشغيل نموذج الذكاء الاصطناعي وتحليل توجهات سوق العمل للمشاريع القادمة.</p>
                <p class="lang-en" style="margin: 0; font-size: 0.95rem;">Click the button above to run the AI model and predict next year's graduation project trends.</p>
            </div>
            
            <div id="ai-prediction-loading" style="display: none; text-align: center; color: var(--primary); font-weight: bold; flex-direction: column; align-items: center; gap: 15px;">
                <div style="width: 40px; height: 40px; border: 4px solid rgba(30,61,115,0.1); border-top-color: var(--primary); border-radius: 50%; animation: spin 1s infinite linear;"></div>
                <div>
                    <span class="lang-ar">جاري استدعاء نموذج الذكاء الاصطناعي وتحليل قاعدة البيانات وسوق العمل...</span>
                    <span class="lang-en">Calling AI model and analyzing database & market trends...</span>
                </div>
            </div>

            <div id="ai-prediction-text" style="display: none; width: 100%; text-align: right; font-size: 0.95rem; line-height: 1.7; color: var(--text-primary);">
            </div>
        </div>
    </div>
    @endauth

    <!-- Dashboard Main Sections -->
    <div class="dashboard-sections">
        <!-- Left Section: Services -->
        <div>
            <div class="services-list-title">
                <h3>الخدمات الأكاديمية</h3>
                <a href="{{ route('projects.index') }}" style="color: var(--accent); font-weight: bold; font-size: 0.9rem;">عرض الكل &larr;</a>
            </div>

            <div class="services-grid">
                <!-- 1. Browse Projects -->
                <div class="card glass-panel service-card">
                    <div class="service-icon">📂</div>
                    <div class="service-info">
                        <h4><a href="{{ route('projects.index') }}" style="color: var(--primary); font-weight: 700;">تصفح المشاريع</a></h4>
                        <p>البحث وتصفح المشاريع حسب السنة، التخصص، والتقنيات البرمجية.</p>
                        <span class="badge badge-specialty" style="font-size: 0.65rem; padding: 2px 8px; margin-top: 5px;">موصى به</span>
                    </div>
                </div>

                <!-- 2. Smart Search -->
                <div class="card glass-panel service-card">
                    <div class="service-icon" style="color: #e67e22; background: rgba(230, 126, 34, 0.05);">🔍</div>
                    <div class="service-info">
                        <h4><a href="{{ route('projects.index') }}" style="color: var(--primary); font-weight: 700;">البحث الذكي</a></h4>
                        <p>البحث الفوري باستخدام الكلمات الدالة والوسوم والتقنيات البرمجية.</p>
                    </div>
                </div>

                <!-- 3. Ask a Question -->
                <div class="card glass-panel service-card">
                    <div class="service-icon" style="color: #9b59b6; background: rgba(155, 89, 182, 0.05);">💬</div>
                    <div class="service-info">
                        <h4><a href="{{ route('forum.index') }}" style="color: var(--primary); font-weight: 700;">طرح استفسار</a></h4>
                        <p>تلقي إجابات وتوجيهات مباشرة من الخريجين المتميزين وأعضاء هيئة التدريس.</p>
                    </div>
                </div>

                <!-- 4. Messages -->
                <div class="card glass-panel service-card">
                    <div class="service-icon" style="color: #3498db; background: rgba(52, 152, 219, 0.05);">✉️</div>
                    <div class="service-info">
                        <h4><a href="{{ route('forum.index') }}" style="color: var(--primary); font-weight: 700;">التواصل والرسائل</a></h4>
                        <p>النقاشات العامة والمشاركة في الردود والإرشادات الأكاديمية.</p>
                    </div>
                </div>

                <!-- 5. Upload Project -->
                <div class="card glass-panel service-card">
                    <div class="service-icon" style="color: #2ecc71; background: rgba(46, 204, 113, 0.05);">🎓</div>
                    <div class="service-info">
                        <h4>
                            @auth
                                @if(Auth::user()->isGraduate() || Auth::user()->isAdmin())
                                    <a href="{{ route('projects.create') }}" style="color: var(--primary); font-weight: 700;">نشر كتاب التخرج</a>
                                @else
                                    <span style="color: var(--text-muted);">نشر كتاب التخرج</span>
                                @endif
                            @else
                                <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700;">نشر كتاب التخرج</a>
                            @endauth
                        </h4>
                        <p>توثيق مشروع تخرجك ونشر الملف بصيغة PDF ليكون مرجعاً أكاديمياً للأجيال القادمة.</p>
                    </div>
                </div>

                <!-- 6. Admin Panel -->
                <div class="card glass-panel service-card">
                    <div class="service-icon" style="color: #e74c3c; background: rgba(231, 76, 60, 0.05);">⚙️</div>
                    <div class="service-info">
                        <h4>
                            @auth
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" style="color: var(--primary); font-weight: 700;">إدارة النظام</a>
                                @else
                                    <span style="color: var(--text-muted);">إدارة النظام (خاص بالمسؤول)</span>
                                @endif
                            @else
                                <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700;">إدارة النظام</a>
                            @endauth
                        </h4>
                        <p>تفعيل الحسابات، الإشراف الأكاديمي العام، والاطلاع على الإحصائيات الشاملة للمنصة.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Section: Activity & Tips -->
        <div>
            <div class="services-list-title">
                <h3>نشاطات وتوجيهات</h3>
            </div>

            <div class="card glass-panel activity-card">
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-item-info">
                            <h5>إجابات جديدة متاحة</h5>
                            <p>تفقد ردود الخريجين على أسئلتك المطروحة</p>
                        </div>
                        <div class="activity-item-num">2</div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-item-info">
                            <h5>المشاريع المميزة المقترحة</h5>
                            <p>بناءً على التخصصات النشطة حالياً</p>
                        </div>
                        <div class="activity-item-num blue">6</div>
                    </div>
                </div>

                <div class="menu-divider"></div>

                <div style="margin-top: 15px;">
                    <h5 style="margin-bottom: 5px; font-size: 0.85rem; color: var(--text-muted);">نصيحة البحث السريعة</h5>
                    <p style="font-size: 0.75rem; color: var(--text-secondary); line-height: 1.5;">
                        يمكنك استخدام التصفية المباشرة في شريط الفلاتر للبحث عن المشاريع التي تحتوي على تقنيات محددة مثل <b>Laravel</b> أو <b>Python</b> بسرعة وسهولة.
                    </p>
                </div>

                <div class="menu-divider"></div>

                <div style="margin-top: 15px;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: bold; margin-bottom: 8px;">
                        <span>اكتتمال إعداد الملف الشخصي</span>
                        <span style="color: var(--accent);">70%</span>
                    </div>
                    <div style="height: 6px; background: rgba(15, 42, 74, 0.05); border-radius: 3px; overflow: hidden;">
                        <div style="width: 70%; height: 100%; background: linear-gradient(90deg, var(--primary), var(--accent));"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        async function generateAIPredictions() {
            const placeholder = document.getElementById('ai-prediction-placeholder');
            const loading = document.getElementById('ai-prediction-loading');
            const textDiv = document.getElementById('ai-prediction-text');
            const box = document.getElementById('ai-prediction-box');
            const btn = document.getElementById('predict-btn');

            placeholder.style.display = 'none';
            textDiv.style.display = 'none';
            loading.style.display = 'flex';
            btn.disabled = true;

            try {
                const response = await fetch('/api/ai/predict-trends', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success) {
                    let text = data.prediction;
                    
                    // Simple Markdown-to-HTML parser helper
                    let html = text
                        .replace(/### (.*)/g, '<h4 style="color: var(--primary); margin-top: 20px; margin-bottom: 10px; font-weight: bold; border-bottom: 1px solid var(--border-glass); padding-bottom: 5px;">$1</h4>')
                        .replace(/#### (.*)/g, '<h5 style="color: var(--text-secondary); margin-top: 15px; margin-bottom: 8px; font-weight: bold;">$1</h5>')
                        .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
                        .replace(/\* (.*)/g, '<li style="margin-right: 20px; list-style-type: disc; margin-bottom: 5px;">$1</li>')
                        .replace(/\n/g, '<br>');

                    // Fix double br after list items
                    html = html.replace(/(<\/li>)<br>/g, '$1');

                    textDiv.innerHTML = html;
                    loading.style.display = 'none';
                    textDiv.style.display = 'block';
                    box.style.background = 'rgba(255, 255, 255, 0.85)';
                } else {
                    throw new Error("Failed to load predictions");
                }
            } catch (error) {
                console.error(error);
                loading.style.display = 'none';
                placeholder.style.display = 'block';
                alert("حدث خطأ أثناء الاتصال بالذكاء الاصطناعي. يرجى المحاولة مجدداً.");
            } finally {
                btn.disabled = false;
            }
        }
    </script>
@endsection
