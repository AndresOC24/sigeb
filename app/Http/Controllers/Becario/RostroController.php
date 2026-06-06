<?php

namespace App\Http\Controllers\Becario;

use App\Http\Controllers\Controller;
use App\Models\Rostro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RostroController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'descriptor' => 'required|array|size:128',
            'descriptor.*' => 'numeric',
        ]);

        $user = Auth::user();

        if ($user->rostro) {
            return response()->json(['message' => 'Ya tienes un rostro registrado.'], 409);
        }

        Rostro::create([
            'user_id' => $user->id,
            'descriptor' => $data['descriptor'],
        ]);

        return response()->json(['message' => 'Rostro registrado correctamente.']);
    }
}