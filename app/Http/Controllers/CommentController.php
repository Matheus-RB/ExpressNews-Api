<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function index()
    {
        $comments = $this->comment->all();
        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'news_id' => 'required|exists:news,id',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        if(Auth::id() != $request->user_id) {
            return response()->json(['message' => 'O autor não é a mesma pessoa que está fazendo a requisição.'], 403);
        }

        $comment = $this->comment->create($request->all());
        return response()->json($comment, 201);
    }

    public function show(string $id)
    {
        $comment = $this->comment->find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comentario não encontrado.'], 404);
        }

        return response()->json($comment);
    }

    public function update(Request $request, string $id)
    {
        $comment = $this->comment->find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comentário não encontrado.'], 404);
        }

        if (Auth::id() == $comment->user_id) {
            $request->validate([
                'content' => 'required|string',
            ]);

            $comment->update([
                'content' => $request->input('content'),
            ]);

            return response()->json($comment);
        } else {
            return response()->json(['message' => 'Você não tem permissão para atualizar as informações deste comentário'], 403);
        }
    }


    public function destroy(string $id)
    {
        $comment = $this->comment->find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comentario não encontrado.'], 404);
        }

        if (Auth::id() == $comment->user_id) {
            $comment->delete();

            return response()->json(['message' => 'Comentario excluído com sucesso.']);
        } else {
            return response()->json(['message' => 'Você não tem permissão para excluir este comentário'], 403);
        }
    }

    public function like($id)
    {
        $comment = $this->comment->find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comentário não encontrado.'], 404);
        }

        $existingLike = $comment->likes()->where('user_id', Auth::id())->first();

        if ($existingLike) {
            $existingLike->delete();
            $message = 'Like removido com sucesso.';
        } else {
            $comment->likes()->create(['user_id' => Auth::id()]);
            $message = 'Like adicionado com sucesso.';
        }

        return response()->json(['message' => $message]);
    }
}
