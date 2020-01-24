<?php
namespace App\Models\Intrasm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Brand extends Model
{
    protected $connection="intrasm"; 
    protected $table="db_m_brand";
    protected $primaryKey="id_brand";
    //const CREATED_AT ="INSERT_DATE";
    //const UPDATED_AT ="UPDATE_DATE";
    use SoftDeletes;
    //protected $dates = ['DELETED_AT'];
    protected $fillable = [
        'insert_user',
        'update_user'
    ];
    public $timestamps = false;
    protected $appends = [
        'view_url',
        'delete_url'
    ];
    public function getDeleteUrlAttribute() {
        return route( 'brand.delete', [ 'id' => $this->id_brand ] );
    }
    public function getViewUrlAttribute() {
        return route( 'brand.view', [ 'id' => $this->id_brand ] );
    }
}