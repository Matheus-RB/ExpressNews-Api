<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewController extends Controller
{
    protected $user;
    public News $new;

    public function __construct(News $new)
    {
        $this->new = $new;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            if (Auth::user()->role !== 'admin') {
                abort(403, 'Acesso não autorizado');
            }

            return $next($request);
        })->only(['store', 'update', 'destroy']);
    }

    public function index()
    {
        $news = News::with('categorie')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'category' => $item->categorie->name,
                'slug' => $item->slug,
                'created_at' => $item->created_at
            ];
        });

        return $news;
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',//|min:150
            'category_id' => 'required|exists:categories,id'
        ]);

        try {
            $noticia = $this->new::create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'category_id' => $request->input('category_id'),
                'user_id' => Auth::id(),
            ]);

            return response()->json(['message' => 'Notícia criada com sucesso.', 'data' => $noticia], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Categoria ou usuário não encontrado.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar a notícia.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(string $slug)
    {
      $new = $this->new::with('categorie:id,name')->where('slug', $slug)->firstOrFail();
      return $new;
    }

    public function showId(string $id)
    {
      $new = $this->new->findOrFail($id);
      return $new;
    }

    public function update(Request $request, string $id)
    {
        // Validar os dados da requisição
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        try {
            // Buscar a notícia pelo ID
            $noticia = News::findOrFail($id);

            // Atualizar os dados da notícia
            $noticia->update([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'category_id' => $request->input('category_id'),
            ]);

            return response()->json(['message' => 'Notícia atualizada com sucesso', 'data' => $noticia], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Notícia não encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar a notícia', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            // Buscar a notícia pelo ID
            $noticia = News::findOrFail($id);

            // Deletar a notícia
            $noticia->delete();

            return response()->json(['message' => 'Notícia excluída com sucesso'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Notícia não encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao excluir a notícia', 'error' => $e->getMessage()], 500);
        }
    }
}
