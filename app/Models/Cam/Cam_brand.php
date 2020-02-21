<?php
namespace App\Models\Cam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cam_brand extends Model
{
    // protected $connection="cam"; 
    protected $table="cam_brand";
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

    public function Brand(){
        return $this->belongsTo('App\Models\Intrasm\Brand','id_brand')
            ->select(['id_brand', 'nama_brand']);
    }    
}