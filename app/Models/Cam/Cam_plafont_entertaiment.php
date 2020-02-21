<?php
namespace App\Models\Cam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cam_plafont_entertaiment extends Model
{
    // protected $connection="cam"; 
    protected $table="cam_plafont_entertaiment";
    protected $primaryKey="id";
    use SoftDeletes;

    protected $fillable = [
        'insert_user',
        'update_user'
    ];
}