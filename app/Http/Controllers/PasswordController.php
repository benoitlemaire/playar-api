<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetRequest;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    public function create(Request $request)
    {
        request()->validate([
            'email' => 'required|string|email|exists:users'
        ]);

        $user = User::where('email',$request->email)->firstOrFail();

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
                [
                    'email' => $user->email,
                    'token' => Str::random(60),
                ]
        );
        if ($user && $passwordReset){
            Mail::to($user->email)->queue(new PasswordResetRequest($passwordReset->token,$user));
            return response()->json([
                'message' => 'We have e-mailed your password reset link!'
            ],200);
        }
    }

    public function reset(Request $request)
    {
        request()->validate([
            'password' => 'required|string|confirmed',
            'token' => 'required|exists:password_resets'
        ]);

        $passwordReset = PasswordReset::where('token', $request->token)->firstOrFail();

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()){
            $passwordReset->delete();
            return response()->json([
                'message' => 'The token has expired. Send a new password request'
            ], 403);
        }

        $user = User::where('email', $passwordReset->email)->firstOrFail();
        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();

        return response()->json([
            'message' => 'Password changed'
        ],200);
    }
}
