<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DbColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'db_schema_id',
        'name',
        'type',
        'nullable',
        'desc',
    ];

    public function schema(): BelongsTo
    {
        return $this->belongsTo(DbSchema::class, 'db_schema_id');
    }
}
