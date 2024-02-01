<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategorieController extends Controller
{
    public Categorie $categorie;

    public function __construct(Categorie $categorie)
    {
        $this->categorie = $categorie;
    }

    public function index()
    {
        $categories = $this->categorie->all();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            $request->validate([
                'name' => 'required|string|max:15'
            ]);

            $categorie = $this->categorie->create($request->all());

            return response()->json($categorie, 201);
        } else {
            return response()->json(['message' => 'Você não tem permissão para criar uma categoria'], 403);
        }
    }

    public function show(string $id)
    {
        $categorie = $this->categorie->find($id);

        if(!$categorie) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        if (Auth::user()->role === 'admin') {
            return response()->json($categorie);
        } else {
            return response()->json(['message' => 'Você não tem permissão para visualizar esta categoria'], 403);
        }
    }

    public function update(Request $request, string $id)
    {
        $categorie = $this->categorie->find($id);

        if (!$categorie) {
            return response()->json(['message' => 'Categoria não encontrada.'], 404);
        }

        if (Auth::user()->role === 'admin') {
            $request->validate([
                'name' => 'required|string|max:15'
            ]);

            $categorie->update($request->all());

            return response()->json($categorie);
        } else {
            return response()->json(['message' => 'Você não tem permissão para atualizar as informações desta categoria'], 403);
        }
    }

    public function destroy(string $id)
    {
        $categorie = $this->categorie->find($id);

        if (!$categorie) {
            return response()->json(['message' => 'Categoria não encontrada.'], 404);
        }

        if (Auth::user()->role === 'admin') {
            $categorie->delete();

            return response()->json(['message' => 'Categoria excluída com sucesso.']);
        } else {
            return response()->json(['message' => 'Você não tem permissão para excluir esta categoria'], 403);
        }
    }
}
