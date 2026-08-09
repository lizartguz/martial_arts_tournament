<?php

namespace App\Http\Controllers\Api\v1;
#namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class PasswordResetApiController extends Controller
{
   
    public function test(Request $request){
        return response()->json(['message' => __('aaa')], 200);
    }
    
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'response' => false,
                'message' => 'Ingresa un correo electronico valido.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'response' => true,
                'message' => 'Si el correo esta registrado, enviaremos un enlace para restablecer la contrasena.',
            ], 200);
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'response' => true,
                'message' => 'Te enviamos un enlace para restablecer tu contrasena. Revisa tu correo.',
            ], 200);
        }

        return response()->json([
            'response' => false,
            'message' => __($status),
        ], 500);
    }

    // Restablecer la contraseña
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
           return response()->json(['errors' => $validator->errors()], 422);
            
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)], 200);
        }

        return response()->json(['message' => __($status)], 500);
    }
}
