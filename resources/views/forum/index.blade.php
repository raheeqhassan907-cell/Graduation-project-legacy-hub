@extends('layouts.app')

@title('مجتمع الأسئلة والإرشاد - مركز إرث لمشاريع التخرج')

@section('content')
    <div class="forum-header">
        <div>
            <h2>مجتمع الأسئلة والإرشاد المهني</h2>
            <p style="color: var(--text-secondary); margin-top: 5px;">
                مساحة مخصصة للطلاب لطرح استفساراتهم حول الأفكار الأكاديمية والمشاريع، وللخريجين والأساتذة لتقديم النصح والإجابات.
            </p>
        </div>
    </div>

    <!-- Ask a Question Section (Students Only) -->
    @auth
        @if(Auth::user()->isStudent())
            <div class="glass-panel" style="padding: 30px; margin-bottom: 40px;">
                <h3 style="margin-bottom: 15px;"> طرح سؤال جديد </h3>
                <form action="{{ route('forum.question.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <textarea name="content" class="form-control" rows="3" placeholder="اكتب سؤالك هنا بوضوح (مثال: ما هي أفضل معمارية لتطبيق توصيل طلبات؟ أو كيف أختار موضوع مشروع التخرج؟)" required>{{ old('content') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">نشر السؤال 🚀</button>
                </form>
            </div>
        @else
            <div class="glass-panel" style="padding: 20px 30px; margin-bottom: 40px; border-color: rgba(9, 132, 227, 0.2); background: rgba(9, 132, 227, 0.05); color: #74b9ff; font-weight: 500;">
                📢 الأسئلة متاحة للطلاب فقط. يمكنك كخريج أو أستاذ الإجابة على استفسارات الطلاب المدرجة أدناه.
            </div>
        @endif
    @else
        <div class="glass-panel" style="padding: 20px 30px; margin-bottom: 40px; border-color: rgba(108, 92, 231, 0.2); background: rgba(108, 92, 231, 0.05); text-align: center;">
            🔑 <a href="{{ route('login') }}" style="color: var(--primary); font-weight: bold; text-decoration: underline;">سجل دخولك كطالب</a> لطرح الأسئلة، أو كـ <span style="font-weight: bold;">خريج/أستاذ</span> للإجابة على استفسارات زملائك.
        </div>
    @endauth

    <!-- Questions Feed -->
    <h3 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-glass); padding-bottom: 10px;">أحدث الأسئلة المطروحة</h3>
    
    @forelse($questions as $question)
        <div class="glass-panel forum-thread">
            <!-- Question Header -->
            <div class="thread-user">
                <div class="thread-avatar">
                    {{ mb_substr($question->student->name, 0, 1) }}
                </div>
                <div class="thread-user-info" style="flex-grow: 1;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <h4>{{ $question->student->name }}</h4>
                        <span class="badge badge-role student">طالب ({{ $question->student->department }})</span>
                    </div>
                    <span>نُشر {{ $question->created_at->diffForHumans() }}</span>
                </div>
                
                @auth
                    @if(Auth::user()->isAdmin() || Auth::id() === $question->student_id)
                        <form action="{{ route('forum.question.destroy', $question->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال نهائياً؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.3rem 0.6rem;">حذف السؤال</button>
                        </form>
                    @endif
                @endauth
            </div>

            <!-- Question Content -->
            <div class="thread-content">{{ $question->content }}</div>

            <!-- Answers List -->
            <div class="answers-section">
                <div class="answers-title">الإجابات المتاحة ({{ $question->answers->count() }})</div>
                
                @foreach($question->answers as $answer)
                    <div class="answer-item">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="user-avatar-nav" style="width: 30px; height: 30px; font-size: 0.85rem; {{ $answer->user->isProfessor() ? 'background: var(--success);' : '' }}">
                                    {{ mb_substr($answer->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <span style="font-weight: bold; font-size: 0.9rem;">{{ $answer->user->name }}</span>
                                    <span class="badge badge-role {{ $answer->user->role }}" style="font-size: 0.7rem; padding: 0.15rem 0.5rem; margin-right: 5px;">
                                        @if($answer->user->isProfessor())
                                            {{ $answer->user->title }} (مشرف)
                                        @else
                                            خريج ({{ $answer->user->job_title }} في {{ $answer->user->company }})
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $answer->created_at->diffForHumans() }}</span>
                                
                                @auth
                                    @if(Auth::user()->isAdmin() || Auth::id() === $answer->user_id)
                                        <form action="{{ route('forum.answer.destroy', $answer->id) }}" method="POST" onsubmit="return confirm('هل تريد حذف هذه الإجابة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: none; border: none; cursor: pointer; color: var(--danger); font-size: 0.8rem;">حذف</button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                        <p style="color: var(--text-secondary); font-size: 0.95rem; white-space: pre-line;">{{ $answer->content }}</p>
                    </div>
                @endforeach

                <!-- Add Answer Form (Graduates and Professors Only) -->
                @auth
                    @if(Auth::user()->isGraduate() || Auth::user()->isProfessor())
                        <form action="{{ route('forum.answer.store', $question->id) }}" method="POST" class="answer-form">
                            @csrf
                            <div style="display: flex; gap: 10px; align-items: flex-end;">
                                <div style="flex-grow: 1;">
                                    <textarea name="content" class="form-control" rows="2" placeholder="اكتب إجابتك أو نصيحتك المهنية للزميل..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.75rem 1.5rem; border-radius: 10px; height: fit-content;">إرسال الإجابة</button>
                            </div>
                        </form>
                    @endif
                @else
                    <p style="font-size: 0.85rem; color: var(--text-muted); text-align: center; margin-top: 15px;">
                        🔒 يجب <a href="{{ route('login') }}" style="color: var(--primary); text-decoration: underline;">تسجيل الدخول</a> لتتمكن من إضافة إجابة.
                    </p>
                @endauth
            </div>
        </div>
    @empty
        <div class="glass-panel" style="padding: 50px; text-align: center; color: var(--text-secondary);">
            💡 لا توجد أسئلة مطروحة حالياً. كن أول من يطرح استفساره!
        </div>
    @endforelse
@endsection
