<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TaskUrl extends Model
{
protected $fillable = [
'task_id',
'url',
'status',
'error_message',
'indexed_at',
];
protected $casts = [
'indexed_at' => 'datetime',
];
public function task(): BelongsTo
{
return $this->belongsTo(Task::class);
}
}
