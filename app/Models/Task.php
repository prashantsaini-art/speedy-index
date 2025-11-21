<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Task extends Model
{
protected $fillable = [
'user_id',
'title',
'search_engine',
'task_type',
'external_task_id',
'status',
'total_urls',
'indexed_count',
'pending_count',
'error_count',
'metadata',
];
protected $casts = [
'metadata' => 'array',
];
public function user(): BelongsTo
{
return $this->belongsTo(User::class);
}
public function urls(): HasMany
{
return $this->hasMany(TaskUrl::class);
}
// Scopes
public function scopePending($query)
{
return $query->where('status', 'pending');
}
public function scopeCompleted($query)
{
return $query->where('status', 'completed');
}
}
