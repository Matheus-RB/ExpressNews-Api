<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function index()
    {
        $categories = $this->category->all();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            $request->validate([
                'name' => 'required|string|max:15'
            ]);

            $category = $this->category->create($request->all());

            return response()->json($category, 201);
        } else {
            return response()->json(['message' => 'Você não tem permissão para criar uma categoria'], 403);
        }
    }

    public function show(string $id)
    {
        $category = $this->category->find($id);

        if(!$category) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        if (Auth::user()->role === 'admin') {
            return response()->json($category);
        } else {
            return response()->json(['message' => 'Você não tem permissão para visualizar esta categoria'], 403);
        }
    }

    public function update(Request $request, string $id)
    {
        $category = $this->category->find($id);

        if (!$category) {
            return response()->json(['message' => 'Categoria não encontrada.'], 404);
        }

        if (Auth::user()->role === 'admin') {
            $request->validate([
                'name' => 'required|string|max:15'
            ]);

            $category->update($request->all());

            return response()->json($category);
        } else {
            return response()->json(['message' => 'Você não tem permissão para atualizar as informações desta categoria'], 403);
        }
    }

    public function destroy(string $id)
    {
        $category = $this->category->find($id);

        if (!$category) {
            return response()->json(['message' => 'Categoria não encontrada.'], 404);
        }

        if (Auth::user()->role === 'admin') {
            $category->delete();

            return response()->json(['message' => 'Categoria excluída com sucesso.']);
        } else {
            return response()->json(['message' => 'Você não tem permissão para excluir esta categoria'], 403);
        }
    }
}
