<?php
namespace App\Models\Cam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cam_userclient_infomaster extends Model
{
    // protected $connection="cam"; 
    protected $table="tbl_userclient_infomaster";
    protected $primaryKey="id_description";

    use SoftDeletes;

    protected $fillable = [
        'insert_user',
        'update_user'
    ];
    public $timestamps = false;   
}