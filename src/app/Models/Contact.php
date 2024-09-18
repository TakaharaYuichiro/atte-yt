<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'tel_middle',
        'tel_bottom',
        'address',
        'building',
        'category_id',
        'detail'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'        => 'datetime:Y-m-d H:i:s',    
        'updated_at'        => 'datetime:Y-m-d H:i:s',    
    ];

    public function category(){    
        return $this->belongsTo(Category::class);
    }


    public function getName(){
        return $this->last_name . '　' . $this->first_name;
    }

    public function scopeCategorySearch($query, $category_id){
        if (!empty($category_id)) {
            $query->where('category_id', $category_id);
        }
    }

    public function scopeKeywordSearch($query, $keyword_expression){
        if (!empty($keyword_expression)) {
            $expression_s = mb_convert_kana($keyword_expression, 's'); // 全角スペースを半角スペースに変換
            $keywords = explode(' ', $expression_s);
            
            foreach($keywords as $keyword){
                $query->where(function ($query) use($keyword) {
                    $query->Where('last_name', 'like', '%' . $keyword . '%')
                        ->orWhere('first_name', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%');
                });   
            }

            
        }
    }

    public function scopeGenderSearch($query, $gender){
        if (!empty($gender)) {
            $query->where('gender', $gender);
        }
    }

    public function scopeDateSearch($query, $date){    
        if (!empty($date)) {
            $query->whereDate('created_at', $date);
        }
    }
}
