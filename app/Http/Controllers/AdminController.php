<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح بدخول لوحة التحكم.');
        }

        // Compute statistics
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_graduates' => User::where('role', 'graduate')->count(),
            'total_professors' => User::where('role', 'professor')->count(),
            'total_projects' => Project::count(),
            'total_questions' => Question::count(),
        ];

        // Group projects by specialty
        $specialtyStats = Project::selectRaw('specialty, count(*) as count')
            ->groupBy('specialty')
            ->orderByDesc('count')
            ->get();

        // Get latest projects and users
        $latestProjects = Project::with('graduate')->latest()->take(5)->get();
        $latestUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'specialtyStats', 'latestProjects', 'latestUsers'));
    }

    public function usersIndex()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح بدخول هذه الصفحة.');
        }

        $users = User::orderBy('role')->orderBy('name')->get();
        return view('admin.users', compact('users'));
    }

    public function destroyUser($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح بدخول هذه الصفحة.');
        }

        if (Auth::id() == $id) {
            return back()->withErrors(['error' => 'لا يمكنك حذف حسابك الحالي.']);
        }

        $user = User::findOrFail($id);
        
        // Delete uploaded files if graduate
        if ($user->isGraduate()) {
            foreach ($user->projects as $project) {
                if ($project->file_url && file_exists(public_path($project->file_url))) {
                    unlink(public_path($project->file_url));
                }
            }
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'تم حذف المستخدم وجميع بياناته بنجاح.');
    }

    public function storeUser(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح بدخول هذه الصفحة.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:student,graduate,professor,admin',
        ];

        if ($request->role === 'student') {
            $rules['student_id'] = 'required|string|max:50';
            $rules['student_department'] = 'required|string|max:100';
        } elseif ($request->role === 'graduate') {
            $rules['job_title'] = 'required|string|max:100';
            $rules['company'] = 'required|string|max:100';
            $rules['expertise'] = 'required|string|max:255';
            $rules['graduation_year'] = 'required|integer|min:1990|max:' . date('Y');
        } elseif ($request->role === 'professor') {
            $rules['professor_id'] = 'required|string|max:50';
            $rules['title'] = 'required|string|max:50';
            $rules['professor_department'] = 'required|string|max:100';
        }

        $messages = [
            'required' => 'حقل :attribute مطلوب.',
            'string' => 'يجب أن يكون حقل :attribute نصاً.',
            'max' => 'حقل :attribute يجب ألا يتجاوز :max حرفاً.',
            'email' => 'يجب أن يكون البريد الإلكتروني عنواناً صالحاً.',
            'unique' => 'البريد الإلكتروني هذا مستخدم بالفعل.',
            'min' => 'يجب أن تكون كلمة المرور على الأقل :min أحرف.',
            'integer' => 'يجب أن يكون حقل :attribute رقماً صحيحاً.',
            'in' => 'القيمة المحددة لحقل :attribute غير صالحة.',
            'graduation_year.min' => 'سنة التخرج يجب أن تكون :min أو أحدث.',
            'graduation_year.max' => 'سنة التخرج لا يمكن أن تتجاوز السنة الحالية.',
        ];

        $attributes = [
            'name' => 'الاسم الكامل',
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة المرور',
            'role' => 'نوع الحساب',
            'student_id' => 'الرقم الجامعي',
            'student_department' => 'القسم الدراسي / التخصص',
            'job_title' => 'المسمى الوظيفي',
            'company' => 'الشركة',
            'expertise' => 'مجالات الخبرة',
            'graduation_year' => 'سنة التخرج',
            'professor_id' => 'الرقم الوظيفي',
            'title' => 'اللقب الأكاديمي',
            'professor_department' => 'القسم الأكاديمي',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('show_add_modal', true);
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
        ];

        if ($request->role === 'student') {
            $userData['student_id'] = $request->student_id;
            $userData['department'] = $request->student_department;
        } elseif ($request->role === 'graduate') {
            $userData['job_title'] = $request->job_title;
            $userData['company'] = $request->company;
            $userData['expertise'] = $request->expertise;
            $userData['graduation_year'] = $request->graduation_year;
        } elseif ($request->role === 'professor') {
            $userData['professor_id'] = $request->professor_id;
            $userData['title'] = $request->title;
            $userData['department'] = $request->professor_department;
        }

        User::create($userData);

        return redirect()->route('admin.users')->with('success', 'تم إنشاء الحساب بنجاح.');
    }

    public function projectsIndex()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح بدخول هذه الصفحة.');
        }

        $projects = Project::with(['graduate', 'supervisor', 'students'])->latest()->get();
        return view('admin.projects', compact('projects'));
    }

    public function destroyProject($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح بدخول هذه الصفحة.');
        }

        $project = Project::findOrFail($id);

        if ($project->file_url && file_exists(public_path($project->file_url))) {
            unlink(public_path($project->file_url));
        }

        $project->delete();

        return redirect()->route('admin.projects')->with('success', 'تم حذف المشروع بنجاح.');
    }

    public function questionsIndex()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح بدخول هذه الصفحة.');
        }

        $questions = Question::with(['student', 'answers.user'])->latest()->get();
        return view('admin.questions', compact('questions'));
    }

    public function reportsIndex()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح بدخول هذه الصفحة.');
        }

        $users = User::orderBy('role')->get();
        $projects = Project::with(['graduate', 'supervisor', 'students'])->latest()->get();
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_graduates' => User::where('role', 'graduate')->count(),
            'total_professors' => User::where('role', 'professor')->count(),
            'total_projects' => Project::count(),
            'total_questions' => Question::count(),
        ];

        return view('admin.reports', compact('users', 'projects', 'stats'));
    }

    public function analyticsIndex()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'غير مصرح بدخول هذه الصفحة.');
        }

        // 1. Projects distribution by year
        $yearsDist = Project::selectRaw('year, count(*) as count')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // 2. Active departments
        $deptsDist = Project::selectRaw('specialty, count(*) as count')
            ->groupBy('specialty')
            ->orderByDesc('count')
            ->get();

        // 3. Active supervisors
        $supervisors = User::where('role', 'professor')
            ->withCount('supervisedProjects')
            ->orderByDesc('supervised_projects_count')
            ->get();

        // 4. Most used technologies
        $techs = Project::pluck('technologies')
            ->filter()
            ->flatMap(function($item) {
                return array_map('trim', explode(',', $item));
            })
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(10);

        return view('admin.analytics', compact('yearsDist', 'deptsDist', 'supervisors', 'techs'));
    }

    public function settingsIndex()
    {
        return view('admin.settings');
    }

    public function profileShow()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $completion = 0;
        
        if (!empty($user->name)) $completion += 15;
        if (!empty($user->email)) $completion += 15;
        if (!empty($user->phone)) $completion += 15;
        if (!empty($user->profile_image)) $completion += 15;
        
        if ($user->role === 'student') {
            $studentFields = 0;
            if (!empty($user->student_id)) $studentFields += 20;
            if (!empty($user->department)) $studentFields += 20;
            $completion += $studentFields;
        } elseif ($user->role === 'graduate') {
            $gradFields = 0;
            if (!empty($user->job_title)) $gradFields += 10;
            if (!empty($user->company)) $gradFields += 10;
            if (!empty($user->expertise)) $gradFields += 10;
            if (!empty($user->graduation_year)) $gradFields += 10;
            $completion += $gradFields;
        } elseif ($user->role === 'professor') {
            $profFields = 0;
            if (!empty($user->professor_id)) $profFields += 20;
            if (!empty($user->title)) $profFields += 10;
            if (!empty($user->department)) $profFields += 10;
            $completion += $profFields;
        } else {
            // Admin gets 40% automatically for system fields
            $completion += 40;
        }

        return view('profile.show', compact('user', 'completion'));
    }

    public function profileUpdate(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Specific fields based on role
        if ($user->role === 'student') {
            $rules['student_id'] = 'required|string|max:50';
            $rules['department'] = 'required|string|max:100';
        } elseif ($user->role === 'graduate') {
            $rules['job_title'] = 'required|string|max:100';
            $rules['company'] = 'required|string|max:100';
            $rules['expertise'] = 'required|string|max:255';
            $rules['graduation_year'] = 'required|integer|min:1990|max:' . date('Y');
        } elseif ($user->role === 'professor') {
            $rules['professor_id'] = 'required|string|max:50';
            $rules['title'] = 'required|string|max:50';
            $rules['department'] = 'required|string|max:100';
        }

        $request->validate($rules);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($user->role === 'student') {
            $user->student_id = $request->student_id;
            $user->department = $request->department;
        } elseif ($user->role === 'graduate') {
            $user->job_title = $request->job_title;
            $user->company = $request->company;
            $user->expertise = $request->expertise;
            $user->graduation_year = $request->graduation_year;
        } elseif ($user->role === 'professor') {
            $user->professor_id = $request->professor_id;
            $user->title = $request->title;
            $user->department = $request->department;
        }

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Ensure public/uploads/profiles directory exists
            if (!file_exists(public_path('uploads/profiles'))) {
                mkdir(public_path('uploads/profiles'), 0777, true);
            }
            
            $file->move(public_path('uploads/profiles'), $fileName);
            $user->profile_image = 'uploads/profiles/' . $fileName;
        }

        $user->save();

        return redirect()->route('profile.show')->with('success', 'تم تحديث الملف الشخصي بنجاح!');
    }
}
