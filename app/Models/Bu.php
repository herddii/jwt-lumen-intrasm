<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bu extends Model
{
	protected $connection="intrasm";
    protected $table="tbl_bu";
    protected $primaryKey="ID_BU";
    protected $appends = [
        'view_url',
        'delete_url'
    ];

     public function getDeleteUrlAttribute() {
        return route( 'get_bu.delete', [ 'id' => $this->ID_BU ] );
    }
    public function getViewUrlAttribute() {
        return route( 'get_bu.view', [ 'id' => $this->ID_BU ] );
    }
}
