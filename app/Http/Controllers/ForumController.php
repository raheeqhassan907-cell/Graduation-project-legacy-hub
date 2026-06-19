<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index()
    {
        $questions = Question::with(['student', 'answers.user'])->latest()->get();
        return view('forum.index', compact('questions'));
    }

    public function storeQuestion(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isStudent()) {
            abort(403, 'الطلاب فقط هم من يستطيعون طرح الأسئلة.');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $question = new Question();
        $question->student_id = Auth::id();
        $question->content = $request->content;
        $question->save();

        return redirect()->route('forum.index')->with('success', 'تم نشر سؤالك بنجاح! بانتظار إجابة الخريجين والأساتذة.');
    }

    public function storeAnswer(Request $request, $questionId)
    {
        if (!Auth::check() || (!Auth::user()->isGraduate() && !Auth::user()->isProfessor())) {
            abort(403, 'الخريجون والأساتذة فقط هم من يستطيعون الإجابة.');
        }

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $question = Question::findOrFail($questionId);

        $answer = new Answer();
        $answer->question_id = $question->id;
        $answer->user_id = Auth::id();
        $answer->content = $request->content;
        $answer->save();

        return redirect()->route('forum.index')->with('success', 'تم إضافة إجابتك بنجاح.');
    }

    public function destroyQuestion($id)
    {
        $question = Question::findOrFail($id);

        if (!Auth::check() || (!Auth::user()->isAdmin() && Auth::id() !== $question->student_id)) {
            abort(403, 'غير مصرح لك بحذف هذا السؤال.');
        }

        $question->delete();

        return redirect()->route('forum.index')->with('success', 'تم حذف السؤال بنجاح.');
    }

    public function destroyAnswer($id)
    {
        $answer = Answer::findOrFail($id);

        if (!Auth::check() || (!Auth::user()->isAdmin() && Auth::id() !== $answer->user_id)) {
            abort(403, 'غير مصرح لك بحذف هذه الإجابة.');
        }

        $answer->delete();

        return redirect()->route('forum.index')->with('success', 'تم حذف الإجابة بنجاح.');
    }
}
