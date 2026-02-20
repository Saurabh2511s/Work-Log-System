<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    protected $fillable = ['user_id','work_date','project_id','description','minutes'];

    protected $casts = [
        'work_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
