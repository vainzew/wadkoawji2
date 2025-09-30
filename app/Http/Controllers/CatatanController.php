<?php

namespace App\Http\Controllers;

use App\Models\Catatan;
use Illuminate\Http\Request;

class CatatanController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->level == 1) {
            // Admin: see all notes
            $notes = Catatan::orderByDesc('created_at')->get();
        } else {
            // Kasir: only own notes
            $notes = Catatan::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->get();
        }

        return view('catatan.index', compact('notes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        $note = Catatan::create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'content' => $data['content'] ?? '',
            'color' => $data['color'] ?? '#FDE68A',
        ]);

        return response()->json(['success' => true, 'note' => $note], 201);
    }

    public function update(Request $request, Catatan $catatan)
    {
        $user = auth()->user();
        if (!($user->level == 1 || $catatan->user_id == $user->id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        $catatan->update([
            'title' => $data['title'],
            'content' => $data['content'] ?? '',
            'color' => $data['color'] ?? $catatan->color,
        ]);

        return response()->json(['success' => true, 'note' => $catatan]);
    }

    public function destroy(Catatan $catatan)
    {
        $user = auth()->user();
        if (!($user->level == 1 || $catatan->user_id == $user->id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $catatan->delete();

        return response()->json(['success' => true]);
    }

    public function updateOrder(Request $request)
    {
        $data = $request->validate([
            'note_ids' => 'required|array',
            'note_ids.*' => 'exists:catatans,id',
        ]);

        $user = auth()->user();
        
        foreach ($data['note_ids'] as $index => $noteId) {
            $note = Catatan::find($noteId);
            
            // Check if user has permission to update this note
            if ($user->level == 1 || $note->user_id == $user->id) {
                $note->order = $index;
                $note->save();
            }
        }

        return response()->json(['success' => true]);
    }
}
