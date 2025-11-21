<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ApiLog extends Model
{
protected $fillable = [
'user_id',
'endpoint',
'method',
'request_data',
'response_data',
'status_code',
'error_message',
'response_time',
];
protected $casts = [
'request_data' => 'array',
'response_data' => 'array',
'response_time' => 'decimal:3',
];
public function user(): BelongsTo
{
return $this->belongsTo(User::class);
}
}
