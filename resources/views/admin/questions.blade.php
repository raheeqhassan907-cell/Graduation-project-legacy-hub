@extends('layouts.app')

@title('الأسئلة والإجابات - لوحة المسؤول')

@section('content')
    <div style="margin-bottom: 2rem;">
        <h2>💬 
            <span class="lang-ar">الرقابة على الأسئلة والمجتمع</span><span class="lang-en">Q&A Moderation</span>
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            <span class="lang-ar">يمكنك هنا مراجعة كافة الأسئلة المطروحة وإجاباتها من قِبل الأعضاء وحذف المساهمات المخالفة.</span>
            <span class="lang-en">Here you can monitor all questions and answers in the forum and moderate content.</span>
        </p>
    </div>

    @forelse($questions as $question)
        <div class="glass-panel" style="padding: 25px; margin-bottom: 25px;">
            <!-- Question Card -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 15px; margin-bottom: 15px;">
                <div>
                    <span class="badge badge-role student" style="margin-bottom: 8px; display: inline-block;">
                        <span class="lang-ar">السائل:</span><span class="lang-en">Asked by:</span> 
                        {{ $question->student->name }} ({{ $question->student->department }})
                    </span>
                    <h3 style="font-size: 1.15rem; color: var(--text-primary);">{{ $question->content }}</h3>
                    <span style="font-size: 0.75rem; color: var(--text-muted);">
                        {{ $question->created_at->diffForHumans() }}
                    </span>
                </div>
                <div>
                    <form action="{{ route('forum.question.destroy', $question->id) }}" method="POST" onsubmit="return confirm('⚠️ هل أنت متأكد من حذف هذا السؤال وكل إجاباته؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 10px;">
                            <span class="lang-ar">حذف السؤال</span><span class="lang-en">Delete Question</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Answers List -->
            <div style="padding-right: 20px; border-right: 2px solid var(--border-color); margin-top: 15px;">
                <h4 style="margin-bottom: 15px; font-size: 0.95rem; color: var(--text-secondary);">
                    📥 <span class="lang-ar">الإجابات المرفقة ({{ $question->answers->count() }}):</span>
                    <span class="lang-en">Answers ({{ $question->answers->count() }}):</span>
                </h4>

                @forelse($question->answers as $answer)
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; background: rgba(255, 255, 255, 0.02); padding: 12px 15px; border-radius: 8px; margin-bottom: 10px;">
                        <div>
                            <span class="badge badge-role {{ $answer->user->role }}" style="font-size: 0.75rem; padding: 2px 8px; margin-bottom: 5px; display: inline-block;">
                                <span class="lang-ar">المجيب:</span><span class="lang-en">Answered by:</span>
                                {{ $answer->user->role === 'professor' ? $answer->user->title : '' }} {{ $answer->user->name }}
                            </span>
                            <p style="font-size: 0.95rem; line-height: 1.5; color: var(--text-secondary); white-space: pre-line; margin: 5px 0;">{{ $answer->content }}</p>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ $answer->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div>
                            <form action="{{ route('forum.answer.destroy', $answer->id) }}" method="POST" onsubmit="return confirm('⚠️ هل أنت متأكد من حذف هذه الإجابة؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" style="padding: 2px 6px; font-size: 0.75rem;">
                                    <span class="lang-ar">حذف الإجابة</span><span class="lang-en">Delete Answer</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p style="color: var(--text-muted); font-size: 0.85rem;" class="lang-ar">لا توجد إجابات على هذا السؤال بعد.</p>
                    <p style="color: var(--text-muted); font-size: 0.85rem;" class="lang-en">No answers on this question yet.</p>
                @endforelse
            </div>
        </div>
    @empty
        <div class="glass-panel" style="padding: 40px; text-align: center; color: var(--text-secondary);">
            <span class="lang-ar">لا توجد أسئلة مطروحة في المنتدى حالياً.</span><span class="lang-en">No questions in the forum yet.</span>
        </div>
    @endforelse
@endsection
