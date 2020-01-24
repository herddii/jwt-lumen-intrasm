<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Category extends Model{
    
    protected $connection="intrasm";
    protected $table="category";

    protected $appends = [
        'view_url',
        'delete_url'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getDeleteUrlAttribute() {
        return route( 'category.delete', [ 'id' => $this->id ] );
    }

    public function getViewUrlAttribute() {
        return route( 'category.view', [ 'id' => $this->id ] );
    }

    public function setCategoryNameAttribute($value){
        $this->attributes['category_name']=ucfirst($value);
    }
}