<?php
namespace App\Models\Cam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cam_hobby extends Model
{
    // protected $connection="cam"; 
    protected $table="tbl_hobby";
    protected $primaryKey="id_hobby";

    use SoftDeletes;

    protected $fillable = [
        'insert_user',
        'update_user'
    ];
    public $timestamps = false;   
}