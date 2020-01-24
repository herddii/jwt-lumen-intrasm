<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Transaksi extends Model{
    
    protected $connection="intrasm";
    protected $table="transaksi";

    protected $appends = [
        'view_url',
        'delete_url'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getDeleteUrlAttribute() {
        return route( 'transaksi.delete', [ 'id' => $this->id ] );
    }

    public function getViewUrlAttribute() {
        return route( 'transaksi.view', [ 'id' => $this->id ] );
    }

    public function cabang(){
        return $this->belongsTo('App\Models\Cabang','cabang_id');
    }
}