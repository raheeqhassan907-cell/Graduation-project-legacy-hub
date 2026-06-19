<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GP Legacy Hub - مركز إرث')</title>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <!-- Inline settings loader to prevent screen flashing -->
    <script>
        (function() {
            const fontSize = localStorage.getItem('font-size') || 'medium';
            document.documentElement.classList.add('font-size-' + fontSize);
            
            const lang = localStorage.getItem('lang') || 'ar';
            document.documentElement.setAttribute('lang', lang);
            if (lang === 'en') {
                document.documentElement.setAttribute('dir', 'ltr');
                document.documentElement.classList.add('lang-en');
            } else {
                document.documentElement.setAttribute('dir', 'rtl');
                document.documentElement.classList.remove('lang-en');
            }
        })();
    </script>

    <style>
        /* Multi-language client-side display logic */
        html[lang="en"] .lang-ar { display: none !important; }
        html[lang="ar"] .lang-en { display: none !important; }
        html:not([lang="en"]) .lang-en { display: none !important; }
        
        /* Font size utility classes */
        .font-size-small { font-size: 14px !important; }
        .font-size-medium { font-size: 16px !important; }
        .font-size-large { font-size: 18px !important; }
    </style>
    
    @yield('styles')
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar Navigation (Right side for RTL layout) -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="GP Legacy Hub Logo">
                </a>
            </div>
            
            <ul class="sidebar-menu">
                @guest
                    <!-- Guest Sidebar -->
                    <li>
                        <a href="{{ route('home') }}" class="{{ Route::is('home') ? 'active' : '' }}">
                            <span class="menu-icon">🏠</span> 
                            <span class="lang-ar">الرئيسية</span><span class="lang-en">Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('projects.index') }}" class="{{ Route::is('projects.index') || Route::is('projects.show') ? 'active' : '' }}">
                            <span class="menu-icon">📂</span> 
                            <span class="lang-ar">تصفح المشاريع</span><span class="lang-en">Browse Projects</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('forum.index') }}" class="{{ Route::is('forum.index') ? 'active' : '' }}">
                            <span class="menu-icon">💬</span> 
                            <span class="lang-ar">مجتمع الأسئلة</span><span class="lang-en">Q&A Forum</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('settings.index') }}" class="{{ Route::is('settings.index') ? 'active' : '' }}">
                            <span class="menu-icon">⚙️</span> 
                            <span class="lang-ar">الإعدادات</span><span class="lang-en">Settings</span>
                        </a>
                    </li>
                @else
                    @if(Auth::user()->isAdmin())
                        <!-- Admin Sidebar -->
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="{{ Route::is('admin.dashboard') ? 'active' : '' }}">
                                <span class="menu-icon">📊</span> 
                                <span class="lang-ar">الرئيسية</span><span class="lang-en">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users') }}" class="{{ Route::is('admin.users') ? 'active' : '' }}">
                                <span class="menu-icon">👥</span> 
                                <span class="lang-ar">المستخدمين</span><span class="lang-en">Users</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.projects') }}" class="{{ Route::is('admin.projects') ? 'active' : '' }}">
                                <span class="menu-icon">📁</span> 
                                <span class="lang-ar">المشاريع</span><span class="lang-en">Projects</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('projects.create') }}" class="{{ Route::is('projects.create') ? 'active' : '' }}">
                                <span class="menu-icon">🎓</span> 
                                <span class="lang-ar">رفع مشروع</span><span class="lang-en">Upload Project</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.questions') }}" class="{{ Route::is('admin.questions') ? 'active' : '' }}">
                                <span class="menu-icon">💬</span> 
                                <span class="lang-ar">الأسئلة</span><span class="lang-en">Questions</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.reports') }}" class="{{ Route::is('admin.reports') ? 'active' : '' }}">
                                <span class="menu-icon">📄</span> 
                                <span class="lang-ar">التقارير</span><span class="lang-en">Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.analytics') }}" class="{{ Route::is('admin.analytics') ? 'active' : '' }}">
                                <span class="menu-icon">📈</span> 
                                <span class="lang-ar">تحليل المشاريع</span><span class="lang-en">Project Analytics</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings') }}" class="{{ Route::is('admin.settings') ? 'active' : '' }}">
                                <span class="menu-icon">⚙️</span> 
                                <span class="lang-ar">الإعدادات</span><span class="lang-en">Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.show') }}" class="{{ Route::is('profile.show') ? 'active' : '' }}">
                                <span class="menu-icon">👤</span> 
                                <span class="lang-ar">الملف الشخصي</span><span class="lang-en">Profile</span>
                            </a>
                        </li>
                    @elseif(Auth::user()->isGraduate())
                        <!-- Graduate Sidebar -->
                        <li>
                            <a href="{{ route('home') }}" class="{{ Route::is('home') ? 'active' : '' }}">
                                <span class="menu-icon">🏠</span> 
                                <span class="lang-ar">الرئيسية</span><span class="lang-en">Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('projects.index') }}" class="{{ Route::is('projects.index') || Route::is('projects.show') ? 'active' : '' }}">
                                <span class="menu-icon">📂</span> 
                                <span class="lang-ar">المشاريع</span><span class="lang-en">Projects</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('forum.index') }}" class="{{ Route::is('forum.index') ? 'active' : '' }}">
                                <span class="menu-icon">💬</span> 
                                <span class="lang-ar">الإجابة على الأسئلة</span><span class="lang-en">Answer Questions</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index') }}" class="{{ Route::is('settings.index') ? 'active' : '' }}">
                                <span class="menu-icon">⚙️</span> 
                                <span class="lang-ar">الإعدادات</span><span class="lang-en">Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.show') }}" class="{{ Route::is('profile.show') ? 'active' : '' }}">
                                <span class="menu-icon">👤</span> 
                                <span class="lang-ar">الملف الشخصي</span><span class="lang-en">Profile</span>
                            </a>
                        </li>
                    @elseif(Auth::user()->isProfessor())
                        <!-- Professor Sidebar -->
                        <li>
                            <a href="{{ route('home') }}" class="{{ Route::is('home') ? 'active' : '' }}">
                                <span class="menu-icon">🏠</span> 
                                <span class="lang-ar">الرئيسية</span><span class="lang-en">Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('projects.index') }}" class="{{ Route::is('projects.index') || Route::is('projects.show') ? 'active' : '' }}">
                                <span class="menu-icon">📂</span> 
                                <span class="lang-ar">المشاريع</span><span class="lang-en">Projects</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('forum.index') }}" class="{{ Route::is('forum.index') ? 'active' : '' }}">
                                <span class="menu-icon">💬</span> 
                                <span class="lang-ar">الإجابة على الأسئلة</span><span class="lang-en">Answer Questions</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index') }}" class="{{ Route::is('settings.index') ? 'active' : '' }}">
                                <span class="menu-icon">⚙️</span> 
                                <span class="lang-ar">الإعدادات</span><span class="lang-en">Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.show') }}" class="{{ Route::is('profile.show') ? 'active' : '' }}">
                                <span class="menu-icon">👤</span> 
                                <span class="lang-ar">الملف الشخصي</span><span class="lang-en">Profile</span>
                            </a>
                        </li>
                    @else
                        <!-- Student Sidebar -->
                        <li>
                            <a href="{{ route('home') }}" class="{{ Route::is('home') ? 'active' : '' }}">
                                <span class="menu-icon">🏠</span> 
                                <span class="lang-ar">الرئيسية</span><span class="lang-en">Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('projects.index') }}" class="{{ Route::is('projects.index') || Route::is('projects.show') ? 'active' : '' }}">
                                <span class="menu-icon">📂</span> 
                                <span class="lang-ar">المشاريع</span><span class="lang-en">Projects</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('forum.index') }}" class="{{ Route::is('forum.index') ? 'active' : '' }}">
                                <span class="menu-icon">💬</span> 
                                <span class="lang-ar">الأسئلة</span><span class="lang-en">Questions</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.index') }}" class="{{ Route::is('settings.index') ? 'active' : '' }}">
                                <span class="menu-icon">⚙️</span> 
                                <span class="lang-ar">الإعدادات</span><span class="lang-en">Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.show') }}" class="{{ Route::is('profile.show') ? 'active' : '' }}">
                                <span class="menu-icon">👤</span> 
                                <span class="lang-ar">الملف الشخصي</span><span class="lang-en">Profile</span>
                            </a>
                        </li>
                    @endif
                @endif

                @auth
                    <li class="menu-divider"></li>
                    <li class="user-info-item">
                        <div class="user-avatar" style="text-transform: uppercase;">
                            {{ mb_substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="user-details">
                            <span class="user-name">{{ Auth::user()->name }}</span>
                            <span class="badge badge-role {{ Auth::user()->role }}" style="font-size: 0.7rem; padding: 1px 6px; border-radius: 4px; margin-top: 3px;">
                                @if(Auth::user()->role === 'admin')
                                    <span class="lang-ar">مسؤول</span><span class="lang-en">Admin</span>
                                @elseif(Auth::user()->role === 'professor')
                                    <span class="lang-ar">أستاذ مشرف</span><span class="lang-en">Supervisor</span>
                                @elseif(Auth::user()->role === 'graduate')
                                    <span class="lang-ar">خريج</span><span class="lang-en">Graduate</span>
                                @else
                                    <span class="lang-ar">طالب</span><span class="lang-en">Student</span>
                                @endif
                            </span>
                        </div>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-link">
                            <span class="menu-icon">🚪</span> 
                            <span class="lang-ar">تسجيل الخروج</span><span class="lang-en">Logout</span>
                        </a>
                    </li>
                @else
                    <li class="menu-divider"></li>
                    <li>
                        <a href="{{ route('login') }}" class="login-link {{ Route::is('login') ? 'active' : '' }}">
                            <span class="menu-icon">🔑</span> 
                            <span class="lang-ar">دخول / تسجيل</span><span class="lang-en">Login / Register</span>
                        </a>
                    </li>
                @endauth
            </ul>
            
            <div class="sidebar-footer">
                &copy; {{ date('Y') }} Legacy Hub
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="main-wrapper">
            <!-- Top Bar -->
            <header class="topbar">
                <div class="welcome-text">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <h3 class="lang-ar">مرحباً بك مجدداً، {{ Auth::user()->name }} ⚙️</h3>
                            <h3 class="lang-en">Welcome back, {{ Auth::user()->name }} ⚙️</h3>
                            <p class="lang-ar">أنت في لوحة تحكم مدير المنصة. يمكنك مراقبة الإحصائيات وإدارة الحسابات والمشاريع.</p>
                            <p class="lang-en">You are in the admin dashboard. Monitor stats, manage users and projects.</p>
                        @elseif(Auth::user()->isProfessor())
                            <h3 class="lang-ar">أهلاً بك، د. {{ Auth::user()->name }} 👨‍🏫</h3>
                            <h3 class="lang-en">Welcome, Dr. {{ Auth::user()->name }} 👨‍🏫</h3>
                            <p class="lang-ar">أنت في لوحة الأستاذ المشرف. يمكنك تصفح المشاريع وتقديم الدعم والإجابة على الطلاب.</p>
                            <p class="lang-en">You are in the supervisor panel. Browse projects, answer questions, guide students.</p>
                        @elseif(Auth::user()->isGraduate())
                            <h3 class="lang-ar">أهلاً بك، {{ Auth::user()->name }} 👨‍💻</h3>
                            <h3 class="lang-en">Welcome, {{ Auth::user()->name }} 👨‍💻</h3>
                            <p class="lang-ar">أنت في لوحة الخريجين. يمكنك تصفح المشاريع والمشاركة في مجتمع الأسئلة.</p>
                            <p class="lang-en">You are in the graduate panel. Browse projects and help students in the forum.</p>
                        @else
                            <h3 class="lang-ar">أهلاً بك، {{ Auth::user()->name }} 👨‍🎓</h3>
                            <h3 class="lang-en">Welcome, {{ Auth::user()->name }} 👨‍🎓</h3>
                            <p class="lang-ar">أنت في لوحة الطلاب. يمكنك البحث في الأرشيف الأكاديمي وطرح الأسئلة ومتابعة الأجوبة.</p>
                            <p class="lang-en">You are in the student panel. Search academic archives, ask questions, get answers.</p>
                        @endif
                    @else
                        <h3 class="lang-ar">أهلاً بك في Legacy Hub 🎓</h3>
                        <h3 class="lang-en">Welcome to Legacy Hub 🎓</h3>
                        <p class="lang-ar">المنصة الأكاديمية الأولى لتصفح وتوثيق مشاريع التخرج المتميزة.</p>
                        <p class="lang-en">The primary academic platform to browse and document outstanding graduation projects.</p>
                    @endauth
                </div>
                
                <div class="topbar-actions">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                                <span class="lang-ar">🎓 رفع مشروع جديد</span><span class="lang-en">🎓 Upload New Project</span>
                            </a>
                        @endif
                    @endauth
                </div>
            </header>

            <div class="content-body">
                <!-- Success Alert -->
                @if(session('success'))
                    <div class="alert alert-success glass-panel">
                        <span>✅</span>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                <!-- Error Alert -->
                @if($errors->any() && !session('active_tab') && !session('show_add_modal'))
                    <div class="alert alert-danger glass-panel">
                        <span>⚠️</span>
                        <div>
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Chatbot Widget -->
    <div class="chatbot-container">
        <!-- Floating Toggle Button -->
        <button type="button" class="chatbot-toggle" id="chatbot-toggle-btn" onclick="toggleChatbot()">
            <span class="chat-icon">💬</span>
            <span class="chat-text lang-ar">مساعد إرث الذكي</span>
            <span class="chat-text lang-en">Erth AI Assistant</span>
        </button>

        <!-- Chat Window -->
        <div class="chatbot-window glass-panel" id="chatbot-window">
            <div class="chatbot-header">
                <div class="chatbot-header-info">
                    <span class="chatbot-avatar">🤖</span>
                    <div>
                        <h4 style="color: #ffffff; margin: 0; font-size: 0.95rem;" class="lang-ar">مساعد إرث الذكي</h4>
                        <h4 style="color: #ffffff; margin: 0; font-size: 0.95rem;" class="lang-en">Erth AI Assistant</h4>
                        <span style="color: #2ecc71; font-size: 0.75rem; display: flex; align-items: center; gap: 4px;">
                            <span style="width: 8px; height: 8px; background-color: #2ecc71; border-radius: 50%; display: inline-block;"></span>
                            <span class="lang-ar">متصل الآن</span><span class="lang-en">Online</span>
                        </span>
                    </div>
                </div>
                <button type="button" class="chatbot-close" onclick="toggleChatbot()">✖</button>
            </div>

            <!-- Chat Messages -->
            <div class="chatbot-messages" id="chatbot-messages">
                <div class="chatbot-msg bot">
                    <div class="msg-bubble">
                        <span class="lang-ar">أهلاً بك في منصة **مركز إرث لمشاريع التخرج**! 👋 أنا مساعدك الذكي. يمكنك سؤالي عن المشاريع الحالية، التحليلات، التنبؤات التقنية، أو طريقة رفع المشاريع. كيف أساعدك اليوم؟</span>
                        <span class="lang-en">Welcome to **Erth Graduation Projects Center**! 👋 I am your smart assistant. You can ask me about current projects, analytics, future predictions, or how to upload projects. How can I help you today?</span>
                    </div>
                </div>
            </div>

            <!-- Chat Input -->
            <div class="chatbot-input-area">
                <input type="text" id="chatbot-input" class="form-control" placeholder="اكتب سؤالك هنا..." onkeypress="handleChatKey(event)">
                <button type="button" class="btn btn-primary" onclick="sendChatQuery()" style="padding: 10px 15px; border-radius: 10px;">
                    <span>✈️</span>
                </button>
            </div>
        </div>
    </div>

    @yield('scripts')

    <script>
        function toggleChatbot() {
            const windowEl = document.getElementById('chatbot-window');
            windowEl.classList.toggle('active');
            if (windowEl.classList.contains('active')) {
                document.getElementById('chatbot-input').focus();
            }
        }

        function handleChatKey(event) {
            if (event.key === 'Enter') {
                sendChatQuery();
            }
        }

        function sendChatQuery() {
            const inputEl = document.getElementById('chatbot-input');
            const query = inputEl.value.trim();
            if (!query) return;

            inputEl.value = '';
            appendChatMessage(query, 'user');

            // Show typing indicator
            const messagesEl = document.getElementById('chatbot-messages');
            const typingEl = document.createElement('div');
            typingEl.className = 'chatbot-msg bot typing-indicator-msg';
            typingEl.innerHTML = '<div class="msg-bubble"><span class="typing-dots"><span>.</span><span>.</span><span>.</span></span></div>';
            messagesEl.appendChild(typingEl);
            messagesEl.scrollTop = messagesEl.scrollHeight;

            fetch('{{ route("chatbot.query") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: query })
            })
            .then(res => res.json())
            .then(data => {
                // Remove typing indicator
                typingEl.remove();
                
                let reply = data.reply || 'عذراً، حدث خطأ أثناء الاتصال.';
                appendChatMessage(reply, 'bot');
            })
            .catch(err => {
                typingEl.remove();
                appendChatMessage('عذراً، حدث خطأ في النظام. يرجى المحاولة مرة أخرى.', 'bot');
            });
        }

        function formatChatText(text) {
            // Simple markdown parsing for chat UI
            let formatted = text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`([^`]+)`/g, '<code style="background: rgba(30,90,160,0.08); padding: 2px 5px; border-radius: 4px; color: var(--primary); font-family: monospace;">$1</code>')
                .replace(/\n/g, '<br>')
                .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" style="color: var(--primary); font-weight: bold; text-decoration: underline;">$1</a>');
            return formatted;
        }

        function appendChatMessage(text, sender) {
            const messagesEl = document.getElementById('chatbot-messages');
            const msgEl = document.createElement('div');
            msgEl.className = 'chatbot-msg ' + sender;
            
            const bubbleEl = document.createElement('div');
            bubbleEl.className = 'msg-bubble';
            bubbleEl.innerHTML = sender === 'bot' ? formatChatText(text) : escapeHtml(text);
            
            msgEl.appendChild(bubbleEl);
            messagesEl.appendChild(msgEl);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;");
        }
    </script>
</body>
</html>
