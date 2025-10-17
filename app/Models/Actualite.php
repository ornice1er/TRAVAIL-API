<?php

namespace App\Models;

use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Actualite extends Model
{
    use Filterable,HasFactory;

    private static $whiteListFilter = ['*'];


      protected $fillable=['title','sub_description','author','media_id','slug','description','photo','big_photo','category_id','link'];
 
    public static function getAllActualites(){
        return  Actualite::orderBy('id','DESC')->with('title')->paginate(10);
    }

    public function files()
    {
        return $this->hasMany(ActualitesFiles::class,'actualites_id');
    }
    public function filesPDF()
    {
        return $this->hasMany(ActualitesFiles::class,'actualites_id')->where('type',"pdf");
    }
   
    public function filesVideos()
    {
        return $this->hasMany(ActualitesFiles::class,'actualites_id')->where('type',"video");
    }
   
    public function filesImages()
    {
        return $this->hasMany(ActualitesFiles::class,'actualites_id')->where('type',"image");
    }
   

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

     public function media()
    {
        return $this->belongsTo(Media::class,'media_id');
    }
    
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            // $model->id = Str::uuid();
        });
    }
}
