<?php 
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Sopport\Facades\BD;
use App\Models\User;

class JwtAuth {

    public $key;

    public function __construct()
    {
        $this->key = 'key_MaterTech_2022';
    }

    public function signup($email, $pass, $getToken = null) {
        // Verificar existencia del usuario
        $user = User::where([
            'user_email' => $email,
            'user_pass' => $pass
        ])->first();

        // Comporbar si son correctar
        $signup = false;

        if ( is_object($user) ) {
            $signup = true;
        }

        // Generar el token 
        if ( $signup ) {
            $token = array(
                'sub' => $user->user_id,
                'email' => $user->user_email,
                'name' => $user->user_name,
                'iat' => time(),
                'exp' => time() + ( 7 * 24 * 60 * 60 )
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decode = JWT::decode($jwt, $this->key, ['HS256']);
            if ( is_null($getToken) ) {
                $data = $jwt;
            } else {
                $data = $decode;
            }
        } else {
            $data = array(
                'status' => 'error',
                'message' => 'Incorrect Login'
            );
        }
        // Devolver los datos decodificacion o el token
        return $data;
    }

    public function checkToken($jwt, $getIdentity = false){
        $auth = false;

        try{
            $jwt = str_replace('"', '', $jwt);
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        }catch(\UnexpectedValueException $e){
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }
        
        if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }

        if($getIdentity){
          return $decoded;
        }

        return $auth;

    }
}