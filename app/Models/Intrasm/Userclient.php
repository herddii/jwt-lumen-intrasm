<?php
namespace App\Models\Intrasm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Userclient extends Model
{
    protected $connection="intrasm";	
    protected $table="tbl_userclient";
    protected $primaryKey="id_client";

    use SoftDeletes;

    protected $appends = [
        'view_url',
        'delete_url'
    ];

    public function getDeleteUrlAttribute() {
        return route( 'userclient.delete', [ 'id' => $this->id_client ] );
    }
    
    public function getViewUrlAttribute() {
        return route( 'userclient.view', [ 'id' => $this->id_client ] );
    }
}
