<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['title', 'description', 'specialty', 'technologies', 'file_url', 'year', 'graduate_id', 'supervisor_id'])]
class Project extends Model
{
    use HasFactory;

    public function graduate()
    {
        return $this->belongsTo(User::class, 'graduate_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')->withTimestamps();
    }
}
