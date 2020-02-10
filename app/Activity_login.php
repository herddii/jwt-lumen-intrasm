<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Activity_login extends Model
{
    protected $connection="intrasm";
    protected $table="activity_login";
    protected $primaryKey="id";

    protected $hidden =[
        'insert_user',
        'update_user',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $appends = [
        'view_url',
        'delete_url'
    ];
}