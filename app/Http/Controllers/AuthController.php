<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PharIo\Manifest\Email;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use \stdClass;

class AuthController extends Controller
{
    public function register (Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=> 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password'=> 'required|string|min:8'
        ]);

        if($validator ->fails()){
            return response()->json($validator->errors());
        }
        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password)
        ]);
        $token = $user -> createToken('auth_token')->plainTextToken;

        return response()
        ->json(['data'=>$user,'acces_token'=>$token,'token_type'=>'Bearer',]);

    }

    public function login(Request $request){
        if(!Auth::attempt($request->only('email','password'))){
            return response()
            ->json(['message'=>'Unauthorized'],401);
        }
        $user = User::where('email',$request['email'])->firstOrFail();
        $token =$user->createToken('auth_token')->plainTextToken;

        return response()
        ->json([
            'message'=>'hi'.$user->name,
            'accesToken'=>$token,
            'token_type'=> 'Bearer',
            'user'=>$user
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'you have successfully logged out and was sucessfully deleted'
        ];
    }
    public function update(request $request){
        $usuario = User::findOrFail(request($request->id));
        $usuario ->usuario = $request->name;
        $usuario -> email = $request->email;
        $usuario -> password = $request->password;

        $usuario ->save();
        return $usuario;
    }
}
