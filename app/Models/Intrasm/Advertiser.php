<?php
namespace App\Models\Intrasm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Advertiser extends Model{
    
    protected $connection="intrasm";
    protected $table="db_m_advertiser";
    protected $primaryKey="id_adv";
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
        return route( 'advertiser.delete', [ 'id' => $this->id_adv ] );
    }
    public function getViewUrlAttribute() {
        return route( 'advertiser.view', [ 'id' => $this->id_adv ] );
    }
}