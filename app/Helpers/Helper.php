<?php
use Firebase\JWT\JWT;

function user($value){
	$token = $value;
    $users = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
   	return $users;
}