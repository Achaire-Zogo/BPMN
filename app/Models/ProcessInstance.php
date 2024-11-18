<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProcessInstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'bpmn_diagram_id',
        'status',
        'current_task',
        'data',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function diagram()
    {
        return $this->belongsTo(BPMNDiagram::class, 'bpmn_diagram_id');
    }
}
