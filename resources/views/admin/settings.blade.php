@extends('layouts.app')

@title('الإعدادات - مركز إرث لمشاريع التخرج')

@section('content')
    <div class="glass-panel" style="padding: 40px; max-width: 650px; margin: 0 auto;">
        <h2 style="margin-bottom: 25px; text-align: center;">⚙️ 
            <span class="lang-ar">إعدادات المنصة</span><span class="lang-en">Platform Settings</span>
        </h2>

        <!-- UI Settings Section -->
        <div style="margin-bottom: 35px;">
            <h3 style="border-bottom: 1px solid var(--border-color); padding-bottom: 8px; margin-bottom: 20px;">
                🎨 <span class="lang-ar">تخصيص المظهر</span><span class="lang-en">Appearance Settings</span>
            </h3>

            <!-- Language Toggle -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: bold; margin-bottom: 10px; display: block;">
                    🌐 <span class="lang-ar">لغة الواجهة</span><span class="lang-en">Interface Language</span>
                </label>
                <div style="display: flex; gap: 15px;">
                    <button type="button" id="btn-lang-ar" onclick="setLanguage('ar')" class="btn btn-secondary" style="flex: 1; padding: 10px;">العربية (RTL)</button>
                    <button type="button" id="btn-lang-en" onclick="setLanguage('en')" class="btn btn-secondary" style="flex: 1; padding: 10px;">English (LTR)</button>
                </div>
            </div>

            <!-- Font Size Selector -->
            <div class="form-group">
                <label style="font-weight: bold; margin-bottom: 10px; display: block;">
                    🔎 <span class="lang-ar">حجم الخط</span><span class="lang-en">Font Size</span>
                </label>
                <div style="display: flex; gap: 10px;">
                    <button type="button" id="btn-fs-small" onclick="setFontSize('small')" class="btn btn-secondary" style="flex: 1; padding: 10px;">
                        <span class="lang-ar">صغير</span><span class="lang-en">Small</span>
                    </button>
                    <button type="button" id="btn-fs-medium" onclick="setFontSize('medium')" class="btn btn-secondary" style="flex: 1; padding: 10px;">
                        <span class="lang-ar">متوسط</span><span class="lang-en">Medium</span>
                    </button>
                    <button type="button" id="btn-fs-large" onclick="setFontSize('large')" class="btn btn-secondary" style="flex: 1; padding: 10px;">
                        <span class="lang-ar">كبير</span><span class="lang-en">Large</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- About Platform / Developer Section -->
        <div class="glass-panel" style="background: rgba(255, 255, 255, 0.03); border: 1px dashed var(--border-color); padding: 25px; border-radius: 12px; margin-top: 30px;">
            <h3 style="margin-bottom: 15px; text-align: center; color: var(--accent);">
                ℹ️ <span class="lang-ar">حول المنصة</span><span class="lang-en">About the Platform</span>
            </h3>
            <p class="lang-ar" style="font-size: 0.95rem; text-align: center; line-height: 1.6; color: var(--text-secondary); margin-bottom: 20px;">
                <strong>مركز إرث لمشاريع التخرج (GP Legacy Hub)</strong> هو أرشيف أكاديمي متطور يهدف إلى حفظ وتوثيق وعرض أفضل مشاريع التخرج الأكاديمية والبحثية، وإتاحة مجتمع تفاعلي بين الطلاب، الخريجين، وأعضاء هيئة التدريس لتبادل المعرفة والإشراف الأكاديمي المباشر.
            </p>
            <p class="lang-en" style="font-size: 0.95rem; text-align: center; line-height: 1.6; color: var(--text-secondary); margin-bottom: 20px;">
                <strong>Erth Graduation Projects Center (GP Legacy Hub)</strong> is an advanced academic archive that aims to preserve, document, and display the best graduation and research projects, providing an interactive forum for students, graduates, and supervisors to share knowledge and academic guidance.
            </p>

            <div style="border-top: 1px solid var(--border-color); padding-top: 15px; font-size: 0.9rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-weight: bold; color: var(--text-secondary);">
                        <span class="lang-ar">اسم المطور:</span><span class="lang-en">Developer:</span>
                    </span>
                    <span style="color: var(--text-primary); font-weight: bold;">Raheeq Hassan</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-weight: bold; color: var(--text-secondary);">
                        <span class="lang-ar">البريد الإلكتروني:</span><span class="lang-en">Developer Email:</span>
                    </span>
                    <span style="color: var(--accent); font-weight: bold;">raheegohassan@gmail.com</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Set active button states on page load
        window.addEventListener('DOMContentLoaded', () => {
            const currentLang = localStorage.getItem('lang') || 'ar';
            const currentFontSize = localStorage.getItem('font-size') || 'medium';

            // Highlight language button
            if (currentLang === 'ar') {
                document.getElementById('btn-lang-ar').classList.add('btn-primary');
                document.getElementById('btn-lang-ar').classList.remove('btn-secondary');
            } else {
                document.getElementById('btn-lang-en').classList.add('btn-primary');
                document.getElementById('btn-lang-en').classList.remove('btn-secondary');
            }

            // Highlight font size button
            const fsBtn = document.getElementById('btn-fs-' + currentFontSize);
            if (fsBtn) {
                fsBtn.classList.add('btn-primary');
                fsBtn.classList.remove('btn-secondary');
            }
        });

        function setLanguage(lang) {
            localStorage.setItem('lang', lang);
            document.documentElement.setAttribute('lang', lang);
            if (lang === 'en') {
                document.documentElement.setAttribute('dir', 'ltr');
                document.documentElement.classList.add('lang-en');
            } else {
                document.documentElement.setAttribute('dir', 'rtl');
                document.documentElement.classList.remove('lang-en');
            }
            window.location.reload();
        }

        function setFontSize(size) {
            localStorage.setItem('font-size', size);
            
            // Remove previous classes
            document.documentElement.classList.remove('font-size-small', 'font-size-medium', 'font-size-large');
            
            // Add new class
            document.documentElement.classList.add('font-size-' + size);
            window.location.reload();
        }
    </script>
@endsection
