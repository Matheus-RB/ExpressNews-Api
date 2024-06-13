<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    $news = News::with('categorie', 'user')->get()->map(function ($item) {
      $imageData = null;
      $imagePath = storage_path('app/public/' . $item->main_image);
      if (file_exists($imagePath)) {
        $imageData = base64_encode(file_get_contents($imagePath));
      }
      $item->image_data = $imageData;

      return [
        'id' => $item->id,
        'title' => $item->title,
        'category' => $item->categorie->name,
        'author' => $item->user->name,
        'slug' => $item->slug,
        'main_image' => $item->image_data,
        'image_description' => $item->image_description,
        'introductory_paragraph' => $item->introductory_paragraph,
        'created_at' => $item->created_at,
        'updated_at' => $item->updated_at,
      ];
    });

    return $news;
  }
  public function store(Request $request)
  {
    $request->validate([
      'category_id' => 'required|exists:categories,id',
      'content' => 'required|string|min:100',
      'introductory_paragraph' => 'required|string|min:100|max:400',
      'image_description' => 'required|string|min:10|max:100',
      'title' => 'required|string|min:2',
      'main_image' => 'required|string',
    ]);

    $existingNews = News::where('title', $request->input('title'))
      ->orWhere('slug', Str::slug($request->input('title')))
      ->exists();
    if ($existingNews) {
      return response()->json(['message' => 'Já existe uma notícia com o mesmo título ou slug.'], 422);
    }

    $imageData = $request->input('main_image');
    list($type, $imageData) = explode(';', $imageData);
    list(, $imageData) = explode(',', $imageData);
    $imageData = base64_decode($imageData);

    $imageName = Str::slug($request->input('title')) . '.jpg';

    if (Storage::disk('public_imagens')->exists($imageName)) {
      return response()->json(['message' => 'Já existe uma notícia com a mesma imagem.'], 422);
    }

    try {
      Storage::disk('public_imagens')->put($imageName, $imageData);
    } catch (\Exception $e) {
      return response()->json(['message' => 'Erro ao salvar a imagem.', 'error' => $e->getMessage()], 500);
    }

    $imagePath = 'imagens/' . $imageName;

    try {
      $noticia = News::create([
        'category_id' => $request->input('category_id'),
        'content' => $request->input('content'),
        'user_id' => Auth::id(),
        'title' => $request->input('title'),
        'introductory_paragraph' => $request->input('introductory_paragraph'),
        'image_description' => $request->input('image_description'),
        'main_image' => $imagePath,
      ]);

      return response()->json(['message' => 'Notícia criada com sucesso.', 'data' => $noticia], 201);
    } catch (\Exception $e) {
      return response()->json(['message' => 'Erro ao criar a notícia.', 'error' => $e->getMessage()], 500);
    }
  }

  public function show(string $slug)
  {
    $new = $this->new::with('categorie:id,name', 'user')->where('slug', $slug)->firstOrFail();
    $imageData = null;
    $imagePath = storage_path('app/public/' . $new->main_image);
    if (file_exists($imagePath)) {
      $imageData = base64_encode(file_get_contents($imagePath));
    }
    $new->image_data = $imageData;
    return $new;
  }

  public function showId(string $id)
  {
    $new = News::findOrFail($id);
    $imageData = null;
    $imagePath = storage_path('app/public/' . $new->main_image);
    if (file_exists($imagePath)) {
      $imageData = base64_encode(file_get_contents($imagePath));
    }
    $new->image_data = $imageData;

    return $new;
  }

  public function update(Request $request, string $id)
  {
    $request->validate([
      'category_id' => 'required|exists:categories,id',
      'content' => 'required|string|min:100',
      'introductory_paragraph' => 'required|string|min:100|max:400',
      'image_description' => 'required|string|min:10|max:100',
      'title' => 'required|string|min:2',
      'main_image' => 'nullable|string',
    ]);

    try {
      $noticia = News::findOrFail($id);

      if ($request->has('main_image')) {
        list($type, $imageData) = explode(';', $request->input('main_image'));
        list(, $imageData) = explode(',', $imageData);
        $imageData = base64_decode($imageData);

        $imageName = Str::slug($request->input('title')) . '.jpg';

        Storage::disk('public_imagens')->put($imageName, $imageData);

        $noticia->main_image = 'imagens/' . $imageName;

        Storage::disk('public_imagens')->delete($noticia->getOriginal('main_image'));
      }

      $noticia->update([
        'category_id' => $request->input('category_id'),
        'content' => $request->input('content'),
        'title' => $request->input('title'),
        'introductory_paragraph' => $request->input('introductory_paragraph'),
        'image_description' => $request->input('image_description'),
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
      $noticia = News::findOrFail($id);

      $noticia->delete();

      return response()->json(['message' => 'Notícia excluída com sucesso'], 200);
    } catch (ModelNotFoundException $e) {
      return response()->json(['message' => 'Notícia não encontrada'], 404);
    } catch (\Exception $e) {
      return response()->json(['message' => 'Erro ao excluir a notícia', 'error' => $e->getMessage()], 500);
    }
  }
}
