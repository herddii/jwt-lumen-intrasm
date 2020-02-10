<?php
namespace App\Http\Controllers;
use Validator;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller as BaseController;
class AuthController extends BaseController 
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }
    /**
     * Create a new token.
     * 
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt(User $user) {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->ID_USER, // Subject of the token 
            'USER_ID' => $user->USER_ID,
            'ID_USER' => $user->ID_USER,
            'USERNAME' => $user->username,
            'NAME' => $user->name,
            'USER_NAME' => $user->USER_NAME,
            'GENDER' => $user->GENDER,
            'IMAGES' => $user->IMAGES,
            'POSITION' => $user->POSITION,
            'ID_SECTION' => $user->ID_SECTION,
            'ID_DEPARTMENT' => $user->ID_DEPARTEMENT,
            'ID_BU' => $user->ID_BU,
            'PHONE' => $user->phone_number,
            'birthdate' => $user->birth_date,
            'iat' => time(), // Time when JWT was issued. 
            // 'exp' => time() + 60*60*60*24*7 // Expiration time
        ];
        
        // As you can see we are passing `JWT_SECRET` as the second parameter that will 
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    } 
    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     * 
     * @param  \App\User   $user 
     * @return mixed
     */
    public function authenticate(Request $request, User $user) {
        $this->validate($this->request, [
            'email'     => 'required',
            'password'  => 'required'
        ]);
        // Find the user by email

        // $mail = '@mncgroup.com';
        $name=$this->request->input('email');

        if (strpos($request->input('email'), '@') !== false) {
           $pisah = explode("@",$name);
           $user = $pisah[0];
        } else {
           $user = $name;
        }

        // $name = $request->input('email');
        // $user = preg_replace("/\(|\)|$mail/",'',$name);

        $user=User::where('username','like','%'.$user.'%')
            ->where('ACTIVE',1)
            ->first();

        if (!$user) {
            // You wil probably have some sort of helpers or whatever
            // to make sure that you have the same response format for
            // differents kind of responses. But let's return the 
            // below respose for now.
            return response()->json([
                'error' => 'Email does not exist.'
            ], 400);
        }
        // Verify the password and generate the token
        if (Hash::check($this->request->input('password'), $user->password_mobile)) {
            
            try {

                $userget = user($this->jwt($user));
                $posisi= $userget->POSITION;
                $userid = $userget->USER_ID;            
                $useridbu = $userget->ID_BU;            

                $var=new \App\Activity_login;
                $var->email=$userid;
                $var->id_bu=$useridbu;
                $var->insert_user=$userid;
                $var->device=$this->request->input('device');
                $var->save(); 

                return response()->json([
                    'token' => $this->jwt($user)
                ], 200);
            }catch(\Exception $e){
                return response(array('data'=>'Error at Beckend'));
            }
        }
        // Bad Request response
        return response()->json([
            'error' => 'Email or password is wrong.'
        ], 400);
    }

    public function me(Request $request){
        $g = user($request->get('token'));
        return response()->json([
            'data' => $g
        ]);  
    }
}