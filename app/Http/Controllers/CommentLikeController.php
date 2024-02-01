<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CommentLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CommentLikeController extends Controller
{
    public function likeComment(Request $request, $commentId)
    {
        $request->validate([
            'type' => ['required', Rule::in(['like', 'dislike'])],
            'comment_id' => ['required', 'exists:comments,id'],
            'user_id' => ['required', 'exists:users,id'],
            'type' => Rule::unique('comment_likes')->where(function ($query) use ($commentId) {
                return $query->where('comment_id', $commentId)
                    ->where('user_id', Auth::id());
            }),
        ]);

        // Se a validação passar, pode prosseguir com a criação do like/deslike
        $like = CommentLike::create([
            'comment_id' => $commentId,
            'user_id' => Auth::id(),
            'type' => $request->input('type'),
        ]);

        // Outras lógicas necessárias...

        return response()->json(['message' => 'Like/Dislike criado com sucesso.']);
    }
}
