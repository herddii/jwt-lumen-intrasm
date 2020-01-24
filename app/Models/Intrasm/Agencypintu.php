<?php
namespace App\Models\Intrasm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Agencypintu extends Model{
    protected $connection="intrasm";
    protected $table="db_m_agencypintu";
    protected $primaryKey="id_agcyptu";

    protected $hidden =[
        'insert_user',
        'update_user',
        'delete_user',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $appends = [
        'view_url',
        'delete_url'
    ];

    public function getDeleteUrlAttribute() {
        return route( 'agencypintu.delete', [ 'id' => $this->id_agcyptu ] );
    }
    
    public function getViewUrlAttribute() {
        return route( 'agencypintu.view', [ 'id' => $this->id_agcyptu ] );
    }
}