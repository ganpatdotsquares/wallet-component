<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Kyslik\ColumnSortable\Sortable;

class EmailTemplate extends Model
{
    use HasFactory,sluggable,Sortable;

    protected $fillable = [
        'name','subject','description', 'status','slug','created_at',
    ];

    protected $sortable = [
        'name',
        'subject',
        'description',
        'status',
        'slug',
        'created_at',
        'updated_at',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'dbfield' => 'slug',
                'source' => 'name',
                'replacement' => '-',
                'update' => true,
            ]
        ];
    }

    public function scopeFilter($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%');
                $query->orWhere('subject', 'LIKE', '%' . $keyword . '%');
            });
        }
        return $query;
    }

}