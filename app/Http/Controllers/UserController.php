<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    
    //Api rest Login
    public function login (Request $request){
        $jwtAuth = new \App\Helpers\JwtAuth();
        //Recibir datos por post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
            //Validar esos datos
        $validate = \Validator::make($params_array, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
            if($validate->fails()){
                //La validación ha fallado
                $signup = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha podido loguear ',
                    'errors' => $validate->errors()
                );
            } else{
                //Cifrar la contraseña
                $pwd = hash('sha256', $params->password);
                //Devolver token
                $signup = $jwtAuth->signup($params->email, $pwd);
                if(!empty($params->gettoken)){
                    $signup = $jwtAuth->signup($params->email, $pwd, true);
                }
            }
        
    
    
        return response()->json($signup, 200);
    }
}
