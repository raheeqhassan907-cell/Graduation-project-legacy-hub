<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['graduate', 'supervisor', 'students']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('technologies', 'like', "%{$search}%")
                  ->orWhere('specialty', 'like', "%{$search}%")
                  ->orWhere('year', 'like', "%{$search}%")
                  ->orWhereHas('supervisor', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Direct filters
        if ($request->filled('specialty')) {
            $query->where('specialty', $request->specialty);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('supervisor_id')) {
            $query->where('supervisor_id', $request->supervisor_id);
        }

        if ($request->filled('technology')) {
            $query->where('technologies', 'like', "%{$request->technology}%");
        }

        $projects = $query->latest()->get();

        // Get distinct data for filtering UI
        $specialties = Project::distinct()->pluck('specialty')->filter()->values();
        $years = Project::distinct()->pluck('year')->filter()->sortDesc()->values();
        $supervisors = User::where('role', 'professor')->orderBy('name')->get();
        $technologies = Project::pluck('technologies')->filter()->flatMap(function($item) {
            return array_map('trim', explode(',', $item));
        })->unique()->filter()->values();

        return view('projects.index', compact('projects', 'specialties', 'years', 'supervisors', 'technologies'));
    }

    public function show($id)
    {
        $project = Project::with(['graduate', 'supervisor', 'students'])->findOrFail($id);
        return view('projects.show', compact('project'));
    }

    public function create()
    {
        // Only admins can upload
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح لك برفع المشاريع. هذه الصلاحية للمسؤول فقط.');
        }

        $professors = User::where('role', 'professor')->get();
        return view('projects.create', compact('professors'));
    }

    public function store(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح لك برفع المشاريع. هذه الصلاحية للمسؤول فقط.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'specialty' => 'required|string|max:100',
            'technologies' => 'nullable|string|max:255',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'supervisor_id' => 'required|exists:users,id', // MUST define supervisor
            'file' => 'required|file|mimes:pdf|max:20480', // PDF up to 20MB
            'student_names' => 'required|array|min:1|max:5',
            'student_names.*' => 'required|string|max:255',
        ], [
            'supervisor_id.required' => 'يجب اختيار الأستاذ المشرف على المشروع.',
            'supervisor_id.exists' => 'المشرف المختار غير موجود.',
            'file.required' => 'يجب رفع كتاب المشروع (ملف PDF).',
            'file.mimes' => 'يجب أن يكون الملف بصيغة PDF فقط.',
            'file.max' => 'حجم الملف يجب ألا يتجاوز 20 ميجابايت.',
            'student_names.required' => 'يجب إدخال اسم خريج واحد على الأقل.',
            'student_names.min' => 'يجب إدخال اسم خريج واحد على الأقل.',
            'student_names.max' => 'يمكن إدخال 5 خريجين كحد أقصى للمشروع الواحد.',
            'student_names.*.required' => 'اسم الطالب لا يمكن أن يكون فارغاً.',
        ]);

        $studentIds = [];
        foreach ($request->student_names as $name) {
            $name = trim($name);
            if (empty($name)) continue;

            // Check if user already exists
            $user = User::where('name', $name)->first();
            if (!$user) {
                // Generate clean unique email
                $email = $this->generateUniqueEmail($name);
                
                // Auto create account
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt('password'), // default password
                    'role' => 'graduate',
                    'department' => $request->specialty,
                    'graduation_year' => $request->year,
                ]);
            }
            $studentIds[] = $user->id;
        }

        $project = new Project();
        $project->title = $request->title;
        $project->description = $request->description;
        $project->specialty = $request->specialty;
        $project->technologies = $request->technologies;
        $project->year = $request->year;
        $project->supervisor_id = $request->supervisor_id;
        $project->graduate_id = !empty($studentIds) ? $studentIds[0] : null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/projects'), $fileName);
            $project->file_url = 'uploads/projects/' . $fileName;
        }

        $project->save();

        // Sync pivot table relationships
        $project->students()->sync($studentIds);

        return redirect()->route('projects.index')->with('success', 'تم رفع مشروع التخرج بنجاح وإنشاء الحسابات التلقائية للخريجين المشاركين!');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        // Only admin can delete now
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح لك بحذف هذا المشروع.');
        }

        // Delete file
        if ($project->file_url && file_exists(public_path($project->file_url))) {
            unlink(public_path($project->file_url));
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'تم حذف المشروع بنجاح.');
    }

    private function generateUniqueEmail($arabicName) {
        $charMap = [
            'أ' => 'a', 'إ' => 'a', 'آ' => 'a', 'ا' => 'a',
            'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'j',
            'ح' => 'h', 'خ' => 'kh', 'د' => 'd', 'ذ' => 'dh',
            'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'sh',
            'ص' => 's', 'ض' => 'd', 'ط' => 't', 'ظ' => 'z',
            'ع' => 'a', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'q',
            'ك' => 'k', 'ل' => 'l', 'م' => 'm', 'ن' => 'n',
            'ه' => 'h', 'و' => 'w', 'ي' => 'y', 'ى' => 'a',
            'ة' => 'h', 'ئ' => 'y', 'ء' => 'a', 'ؤ' => 'w',
            'لا' => 'la', ' ' => '.'
        ];
        
        $clean = strtr($arabicName, $charMap);
        $clean = preg_replace('/[^a-zA-Z0-9\.]/', '', $clean);
        $clean = strtolower($clean);
        
        if (empty($clean)) {
            $clean = 'graduate';
        }
        
        $email = $clean . '@erth.com';
        
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $clean . $counter . '@erth.com';
            $counter++;
        }
        
        return $email;
    }
}
