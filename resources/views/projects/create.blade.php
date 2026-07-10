@extends('layouts.app')

@title('رفع مشروع تخرج جديد - مركز إرث لمشاريع التخرج')

@section('styles')
    <style>
        .tech-badge.selected {
            background: rgba(214, 48, 49, 0.12) !important;
            border-color: var(--accent) !important;
            color: var(--accent) !important;
        }
        .selected-tag {
            background: var(--primary);
            border: 1px solid var(--border-color);
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: bold;
        }
        .remove-tag-btn {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 1rem;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .remove-tag-btn:hover {
            color: var(--danger);
        }
    </style>
@endsection

@section('content')
    <div class="glass-panel form-card" style="max-width: 700px; margin: 0 auto; padding: 40px;">
        <h2 style="margin-bottom: 10px; text-align: center;">🎓 
            <span class="lang-ar">رفع مشروع تخرج جديد</span><span class="lang-en">Upload Graduation Project</span>
        </h2>
        <p style="color: var(--text-secondary); text-align: center; margin-bottom: 30px; font-size: 0.95rem;">
            <span class="lang-ar">املأ البيانات أدناه لتوثيق ونشر مشروع التخرج. تأكد من إرفاق الملف بصيغة PDF.</span>
            <span class="lang-en">Fill in the details below to document and publish the graduation project. PDF file only.</span>
        </p>

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <div>
                    @foreach($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" onsubmit="syncTechnologiesBeforeSubmit()">
            @csrf

            <div class="form-group">
                <label for="title">
                    <span class="lang-ar">عنوان مشروع التخرج *</span><span class="lang-en">Project Title *</span>
                </label>
                <input type="text" name="title" id="title" class="form-control" placeholder="مثال: نظام إدارة الهوية الرقمية باستخدام البلوكشين" required value="{{ old('title') }}">
            </div>

            <!-- Student Names & IDs Input (Up to 5) -->
            <div class="form-group">
                <label>
                    <span class="lang-ar">الخريجون المشاركون في المشروع (من 1 إلى 5 طلاب مع الرقم الجامعي) *</span>
                    <span class="lang-en">Participating Graduates (1 to 5 Students with ID) *</span>
                </label>
                <div id="student-names-container">
                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="text" name="student_names[]" class="form-control" placeholder="اسم الخريج الأول" required style="flex: 2;" value="{{ old('student_names.0') }}">
                        <input type="text" name="student_ids[]" class="form-control" placeholder="الرقم الجامعي" required style="flex: 1;" value="{{ old('student_ids.0') }}">
                        <button type="button" class="btn btn-secondary" onclick="addStudentField()" style="padding: 5px 15px; font-size: 1.1rem;">➕</button>
                    </div>
                    @if(old('student_names'))
                        @foreach(old('student_names') as $index => $oldName)
                            @if($index > 0)
                                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                                    <input type="text" name="student_names[]" class="form-control" placeholder="اسم الخريج" required style="flex: 2;" value="{{ $oldName }}">
                                    <input type="text" name="student_ids[]" class="form-control" placeholder="الرقم الجامعي" required style="flex: 1;" value="{{ old('student_ids.'.$index) }}">
                                    <button type="button" class="btn btn-danger" onclick="removeStudentField(this)" style="padding: 5px 15px;">&times;</button>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="specialty">
                        <span class="lang-ar">تخصص المشروع *</span><span class="lang-en">Project Specialty *</span>
                    </label>
                    <input type="text" name="specialty" id="specialty" class="form-control" placeholder="مثال: تقنية معلومات، هندسة برمجيات" required value="{{ old('specialty') }}">
                </div>

                <div class="form-group">
                    <label for="year">
                        <span class="lang-ar">سنة المشروع *</span><span class="lang-en">Project Year *</span>
                    </label>
                    <input type="number" name="year" id="year" class="form-control" min="1990" max="{{ date('Y') + 1 }}" required value="{{ old('year', date('Y')) }}">
                </div>
            </div>

            <!-- Selectable Technologies tag combobox -->
            <div class="form-group">
                <label>
                    <span class="lang-ar">التقنيات المستخدمة (اختر من القائمة أو أضف يدوياً)</span>
                    <span class="lang-en">Technologies Used (Select from list or add custom)</span>
                </label>
                <!-- Popular selectable tags -->
                <div id="popular-techs" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; margin-top: 5px;">
                    @foreach(['Laravel', 'React', 'Vue.js', 'Python', 'MySQL', 'SQLite', 'Flutter', 'Django', 'Node.js', 'Bootstrap', 'TailwindCSS'] as $tech)
                        <span class="tech-badge" id="badge-{{ $tech }}" onclick="toggleTechTag('{{ $tech }}')" style="cursor: pointer; background: rgba(255,255,255,0.04); border: 1px solid var(--border-color); padding: 5px 12px; border-radius: 8px; font-size: 0.85rem; user-select: none; transition: all 0.2s;">
                            {{ $tech }}
                        </span>
                    @endforeach
                </div>
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" id="tech-input" class="form-control" placeholder="اكتب اسم تقنية أخرى ثم اضغط إضافة" onkeydown="handleTechInputKeydown(event)">
                    <button type="button" onclick="addCustomTech()" class="btn btn-secondary" style="padding: 5px 15px;">
                        <span class="lang-ar">إضافة</span><span class="lang-en">Add</span>
                    </button>
                </div>
                
                <!-- Hidden input to store comma-separated technologies -->
                <input type="hidden" name="technologies" id="technologies-hidden" value="{{ old('technologies') }}">
                
                <!-- Selected tags wrapper -->
                <div id="selected-techs-wrapper" style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;"></div>
            </div>

            <div class="form-group">
                <label for="supervisor_id">
                    <span class="lang-ar">الأستاذ المشرف * (إجباري)</span><span class="lang-en">Supervisor * (Mandatory)</span>
                </label>
                <select name="supervisor_id" id="supervisor_id" class="form-control" required>
                    <option value="">
                        <span class="lang-ar">-- اختر الأستاذ المشرف --</span><span class="lang-en">-- Select Supervisor --</span>
                    </option>
                    @foreach($professors as $prof)
                        <option value="{{ $prof->id }}" {{ old('supervisor_id') == $prof->id ? 'selected' : '' }}>
                            {{ $prof->title }} {{ $prof->name }} ({{ $prof->department }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="grade">
                    <span class="lang-ar">تقدير المشروع *</span><span class="lang-en">Project Grade *</span>
                </label>
                <select name="grade" id="grade" class="form-control" required>
                    <option value="" disabled {{ !old('grade') ? 'selected' : '' }}>
                        اختر التقدير... | Select Grade...
                    </option>
                    @foreach(['A+', 'A', 'B+', 'B', 'C+', 'C'] as $g)
                        <option value="{{ $g }}" {{ old('grade') === $g ? 'selected' : '' }}>
                            {{ $g }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="description">
                    <span class="lang-ar">وصف تفصيلي للمشروع *</span><span class="lang-en">Detailed Description *</span>
                </label>
                <textarea name="description" id="description" class="form-control" rows="6" placeholder="اكتب نبذة شاملة عن فكرة المشروع، الأهداف، وأهم النتائج التي تم التوصل إليها..." required>{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="file">
                    <span class="lang-ar">كتاب المشروع أو البحث (صيغة PDF فقط) *</span><span class="lang-en">Project Document (PDF only) *</span>
                </label>
                <input type="file" name="file" id="file" class="form-control" accept=".pdf" required style="padding: 0.5rem 1rem;">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">الحجم الأقصى المسموح به: 20 ميجابايت.</p>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <span class="lang-ar">نشر المشروع</span><span class="lang-en">Publish Project</span>
                </button>
                <a href="{{ route('projects.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center; line-height: 2.2;">
                    <span class="lang-ar">إلغاء</span><span class="lang-en">Cancel</span>
                </a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        // Array to store selected technology tags
        let selectedTechs = [];

        window.addEventListener('DOMContentLoaded', () => {
            // Load existing tags if validation failed
            const existingTags = document.getElementById('technologies-hidden').value;
            if (existingTags) {
                selectedTechs = existingTags.split(',').map(t => t.trim()).filter(t => t.length > 0);
                renderTechTags();
            }
        });

        function addStudentField() {
            const container = document.getElementById('student-names-container');
            const currentFields = container.querySelectorAll('input[name="student_names[]"]').length;
            
            if (currentFields >= 5) {
                alert('يمكنك إضافة 5 خريجين كحد أقصى للمشروع الواحد.');
                return;
            }

            const div = document.createElement('div');
            div.style.display = 'flex';
            div.style.gap = '10px';
            div.style.marginBottom = '10px';
            div.innerHTML = `
                <input type="text" name="student_names[]" class="form-control" placeholder="اسم الخريج" required style="flex: 2;">
                <input type="text" name="student_ids[]" class="form-control" placeholder="الرقم الجامعي" required style="flex: 1;">
                <button type="button" class="btn btn-danger" onclick="removeStudentField(this)" style="padding: 5px 15px; font-size: 1rem;">&times;</button>
            `;
            container.appendChild(div);
        }

        function removeStudentField(btn) {
            btn.parentElement.remove();
        }

        function toggleTechTag(tech) {
            const index = selectedTechs.indexOf(tech);
            if (index > -1) {
                selectedTechs.splice(index, 1);
            } else {
                selectedTechs.push(tech);
            }
            renderTechTags();
        }

        function handleTechInputKeydown(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addCustomTech();
            }
        }

        function addCustomTech() {
            const input = document.getElementById('tech-input');
            const tech = input.value.trim();
            if (tech && !selectedTechs.includes(tech)) {
                selectedTechs.push(tech);
                renderTechTags();
            }
            input.value = '';
        }

        function removeTechTag(tech) {
            const index = selectedTechs.indexOf(tech);
            if (index > -1) {
                selectedTechs.splice(index, 1);
                renderTechTags();
            }
        }

        function renderTechTags() {
            const wrapper = document.getElementById('selected-techs-wrapper');
            wrapper.innerHTML = '';

            // Reset all popular tags highlight
            document.querySelectorAll('.tech-badge').forEach(badge => {
                badge.classList.remove('selected');
            });

            selectedTechs.forEach(tech => {
                // Highlight corresponding badge if it exists in popular list
                const badge = document.getElementById('badge-' + tech);
                if (badge) {
                    badge.classList.add('selected');
                }

                // Add to selected list wrapper
                const tagSpan = document.createElement('span');
                tagSpan.className = 'selected-tag';
                tagSpan.innerHTML = `
                    <span>${tech}</span>
                    <button type="button" class="remove-tag-btn" onclick="removeTechTag('${tech}')">&times;</button>
                `;
                wrapper.appendChild(tagSpan);
            });

            // Sync with hidden input
            document.getElementById('technologies-hidden').value = selectedTechs.join(', ');
        }

        function syncTechnologiesBeforeSubmit() {
            document.getElementById('technologies-hidden').value = selectedTechs.join(', ');
        }
    </script>
@endsection
