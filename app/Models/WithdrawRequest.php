<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class WithdrawRequest extends Model
{
    use Sortable;
    
    protected $fillable = [
        'user_id',
        'amount',
        'method',
        'method_details',
        'status', // pending, approved, declined, cancelled
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
} 