<?php

namespace App\Http\Controllers;

use App\Models\Subcomment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubcommentController extends Controller
{
    public Subcomment $subcomment;

    public function __construct(Subcomment $subcomment)
    {
        $this->subcomment = $subcomment;
    }

    public function index()
    {
        $subcomment = $this->subcomment->all();
        return response()->json($subcomment);
    }

    public function store(Request $request)
    {
        $request->validate([
            'comment_id' => 'required|exists:comments,id',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        if (Auth::id() != $request->user_id) {
            return response()->json(['message' => 'O autor não é a mesma pessoa que está fazendo a requisição.'], 403);
        }

        $subcomment = $this->subcomment->create($request->all());
        return response()->json($subcomment, 201);
    }

    public function show(string $id)
    {
        $subcomment = $this->subcomment->find($id);

        if (!$subcomment) {
            return response()->json(['message' => 'Comentario não encontrado.'], 404);
        }

        return response()->json($subcomment);
    }

    public function update(Request $request, string $id)
    {
        $subcomment = $this->subcomment->find($id);

        if (!$subcomment) {
            return response()->json(['message' => 'Comentário não encontrado.'], 404);
        }

        if (Auth::id() == $subcomment->user_id) {
            $request->validate([
                'content' => 'required|string',
            ]);

            $subcomment->update([
                'content' => $request->input('content'),
            ]);

            return response()->json($subcomment);
        } else {
            return response()->json(['message' => 'Você não tem permissão para atualizar as informações deste comentário'], 403);
        }
    }

    public function destroy(string $id)
    {
        $subcomment = $this->subcomment->find($id);

        if (!$subcomment) {
            return response()->json(['message' => 'Comentário não encontrado.'], 404);
        }

        if (Auth::id() == $subcomment->user_id) {
            $subcomment->delete();

            return response()->json(['message' => 'Comentário excluído com sucesso.']);
        } else {
            return response()->json(['message' => 'Você não tem permissão para excluir este Comentário'], 403);
        }
    }

    public function like($id)
    {
        $subcomment = $this->subcomment->find($id);

        if (!$subcomment) {
            return response()->json(['message' => 'Comentário não encontrado.'], 404);
        }

        $existingLike = $subcomment->likes()->where('user_id', Auth::id())->first();

        if ($existingLike) {
            $existingLike->delete();
            $message = 'Like removido com sucesso.';
        } else {
            $subcomment->likes()->create(['user_id' => Auth::id()]);
            $message = 'Like adicionado com sucesso.';
        }

        return response()->json(['message' => $message]);
    }
}
