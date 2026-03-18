<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            return response()->json(['message' => 'Login realizado com sucesso', 'user' => $request->user()]);
        }
        return response()->json(['message' => 'Credenciais inválidas'], 401);
    }
}
