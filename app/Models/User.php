<?php

// تحديد مساحة الاسم (Namespace) الخاص بالنماذج البرمجية لتنظيم الأكواد
namespace App\Models;

// استيراد كلاس مصنع المستخدمين لإنشاء بيانات وهمية عند الحاجة للاختبار
use Database\Factories\UserFactory;
// استيراد السمة Fillable لتحديد الحقول التي يمكن حفظها دفعة واحدة
use Illuminate\Database\Eloquent\Attributes\Fillable;
// استيراد السمة Hidden لتحديد الحقول الحساسة المخفية من الاستعلامات
use Illuminate\Database\Eloquent\Attributes\Hidden;
// استيراد سمة HasFactory لتسهيل التفاعل مع بيانات الاختبار والمصانع
use Illuminate\Database\Eloquent\Factories\HasFactory;
// استيراد كلاس Authenticatable لجعل نموذج المستخدم يدعم تسجيل الدخول والأمان
use Illuminate\Foundation\Auth\User as Authenticatable;
// استيراد سمة Notifiable لإرسال التنبيهات والرسائل البرمجية للمستخدمين
use Illuminate\Notifications\Notifiable;

// تحديد الحقول المسموح بكتابتها وحفظها بشكل مباشر (Mass Assignment) في جدول المستخدمين
#[Fillable(['name', 'email', 'password', 'role', 'profile_image', 'student_id', 'job_title', 'company', 'expertise', 'graduation_year', 'professor_id', 'title', 'department', 'phone'])]
// تحديد الحقول المخفية تلقائياً لمنع ظهورها عند تحويل البيانات لـ JSON (كالباسورد والتوكن)
#[Hidden(['password', 'remember_token'])]
// تعريف كلاس المستخدم الموروث من نظام المصادقة الأساسي في لارافل
class User extends Authenticatable
{
    // استخدام السمات المحددة للمصانع والإشعارات داخل الكلاس
    use HasFactory, Notifiable;

    /**
     * تعريف الحقول التي تحتاج إلى تحويل تلقائي لنوع البيانات (Casts) عند حفظها أو جلبها.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        // إرجاع مصفوفة التحويل لتحديد التواريخ وتشفير كلمة المرور تلقائياً
        return [
            // تحويل تاريخ التحقق من البريد إلى كائن وقت وتاريخ (Carbon datetime)
            'email_verified_at' => 'datetime',
            // تشفير وتجزئة كلمة المرور تلقائياً قبل حفظها بالاعتماد على خوارزمية Bcrypt
            'password' => 'hashed',
        ];
    }

    // دالة مساعدة للتحقق مما إذا كان المستخدم يمتلك صلاحية مدير النظام (Admin)
    public function isAdmin(): bool
    {
        // مقارنة حقل الصلاحية (role) بـ القيمة (admin)
        return $this->role === 'admin';
    }

    // دالة مساعدة للتحقق مما إذا كان المستخدم يمتلك صلاحية طالب (Student)
    public function isStudent(): bool
    {
        // مقارنة حقل الصلاحية (role) بـ القيمة (student)
        return $this->role === 'student';
    }

    // دالة مساعدة للتحقق مما إذا كان المستخدم يمتلك صلاحية خريج (Graduate)
    public function isGraduate(): bool
    {
        // مقارنة حقل الصلاحية (role) بـ القيمة (graduate)
        return $this->role === 'graduate';
    }

    // دالة مساعدة للتحقق مما إذا كان المستخدم يمتلك صلاحية أستاذ مشرف (Professor)
    public function isProfessor(): bool
    {
        // مقارنة حقل الصلاحية (role) بـ القيمة (professor)
        return $this->role === 'professor';
    }

    // علاقة المستخدم بالمشاريع (خاص بالخريج: يمكن للخريج أن يمتلك مشاريع تخرج متعددة)
    public function projects()
    {
        // علاقة One-to-Many: مستخدم واحد يمتلك عدة مشاريع تخرج بالربط مع graduate_id
        return $this->hasMany(Project::class, 'graduate_id');
    }

    // علاقة المستخدم بالمشاريع المشرف عليها (خاص بالأستاذ: يمكن للأستاذ الإشراف على مشاريع متعددة)
    public function supervisedProjects()
    {
        // علاقة One-to-Many: أستاذ واحد يشرف على عدة مشاريع بالربط مع supervisor_id
        return $this->hasMany(Project::class, 'supervisor_id');
    }

    // علاقة المستخدم بالأسئلة المطروحة بالمنتدى (خاص بالطالب: يمكن للطالب طرح أسئلة متعددة)
    public function questions()
    {
        // علاقة One-to-Many: طالب واحد يطرح عدة أسئلة بالربط مع student_id
        return $this->hasMany(Question::class, 'student_id');
    }

    // علاقة المستخدم بالإجابات المكتوبة بالمنتدى (يمكن للمستخدم كتابة إجابات متعددة)
    public function answers()
    {
        // علاقة One-to-Many: مستخدم واحد يكتب عدة إجابات بالربط مع user_id
        return $this->hasMany(Answer::class, 'user_id');
    }

    // علاقة المستخدم بالمشاريع المشارك فيها (علاقة Many-to-Many بين الخريجين والمشاريع عبر جدول وسيط)
    public function contributedProjects()
    {
        // علاقة Many-to-Many: يربط الخريج بالمشاريع عبر جدول project_user مع حفظ التواقيت الزمنية
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id')->withTimestamps();
    }
}
