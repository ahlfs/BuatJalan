<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inbox extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'sender',
        'avatar',
        'avatar_bg',
        'subject',
        'content',
        'unread',
        'workspace_id',
        'invitation_status',
    ];

    protected $casts = [
        'unread' => 'boolean',
    ];

    /**
     * Get the user who owns this inbox item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the workspace associated with this inbox item (e.g. for invitations).
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
