<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Setting extends Model
{
    use Sluggable;

    protected $fillable = ['title','slug','config_value','manager','field_type','is_delete'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::updating(function($model)
        {
            $model->yamlParse();
        });
        static::saved(function($model)
        {
            $model->yamlParse();
        });
        static::updated(function($model)
        {
            $model->yamlParse();
        });
        static::deleted(function($model)
        {
            $model->yamlParse();
        });
    }

    public function sluggable() :array
    {
        return [
            'slug' => [
                'source' => 'customslug',
                // 'source' => Str::upper('title'),
                'unique' => true,
                'separator' => '_',
                'onUpdate' => true,
            ]
        ];
    }
 public function getCustomslugAttribute()
    {
        if(isset($this->slug)){
            $str = strtoupper($this->slug);
        }
        else{
            $str =  strtoupper($this->title);
        }
        return $str;
    }


    protected function yamlParse()
    {
        $settings = DB::table('settings')->whereNull('manager')->orWhere('manager','general')->orWhere('manager','social')->orWhere('manager','options')->orWhere('manager','smtp')->orWhere('manager','theme_images')->pluck('config_value','slug')->toArray();

        $listYaml = Yaml::dump($settings, 4, 60);
        Storage::disk('configuration')->put('settings.yml', $listYaml);
    }
}
