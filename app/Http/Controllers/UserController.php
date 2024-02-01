<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        $users = $this->user->all();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'avatar' => 'required',
        ]);
        $hashedPassword = Hash::make($request->input('password'));

        $request->merge(['password' => $hashedPassword]);

        $user = $this->user->create($request->all());
        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = $this->user->find($id);

        if(!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        return response()->json($user);
    }

    public function update(Request $request, string $id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        if (Auth::id() == $user->id) {
            $request->validate([
                'name' => 'string|max:255',
                'email' => 'email|unique:users,email,' . $user->id,
                'avatar' => 'string'
            ]);

            $user->update($request->all());

            return response()->json($user);
        } else {
            return response()->json(['message' => 'Você não tem permissão para atualizar as informações deste usuário'], 403);
        }
    }

    public function destroy(string $id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        if (Auth::id() == $user->id || Auth::user()->role === 'admin') {
            $user->delete();

            return response()->json(['message' => 'Usuário excluído com sucesso.']);
        } else {
            return response()->json(['message' => 'Você não tem permissão para excluir este usuário'], 403);
        }
    }
}
