<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class WalletTransaction extends Model
{
    use Sortable;

    protected $fillable = [
        'user_id',
        'type', // deposit, withdraw, send, receive, add
        'amount',
        'status', // pending, succeeded, failed
        'related_user_id', // for send/receive
        'description',
        'admin_commission', // new field for commission
        'transaction_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function relatedUser()
    {
        return $this->belongsTo(User::class, 'related_user_id');
    }
} 