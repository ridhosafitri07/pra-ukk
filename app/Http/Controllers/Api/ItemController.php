<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // GET /api/items (hanya milik user login)
    public function index(Request $request)
    {
        return Item::where('user_id', $request->user()->id)
                   ->latest()->get();
    }

    // POST /api/items
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required','string','max:150'],
            'description' => ['nullable','string'],
        ]);

        $item = Item::create([
            'user_id'     => $request->user()->id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        return response()->json($item, 201);
    }

    // GET /api/items/{id}
    public function show(Request $request, Item $item)
    {
        $this->authorizeOwner($request->user()->id, $item);
        return $item;
    }

    // PUT /api/items/{id}
    public function update(Request $request, Item $item)
    {
        $this->authorizeOwner($request->user()->id, $item);

        $data = $request->validate([
            'title'       => ['sometimes','required','string','max:150'],
            'description' => ['nullable','string'],
        ]);

        $item->update($data);

        return response()->json($item);
    }

    // DELETE /api/items/{id}
    public function destroy(Request $request, Item $item)
    {
        $this->authorizeOwner($request->user()->id, $item);
        $item->delete();

        return response()->json(['message' => 'Deleted']);
    }

    private function authorizeOwner(int $userId, Item $item): void
    {
        abort_if($item->user_id !== $userId, 403, 'Forbidden');
    }
}
