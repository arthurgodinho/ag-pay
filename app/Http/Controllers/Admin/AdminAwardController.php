<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Award;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminAwardController extends Controller
{
    public function index()
    {
        $awards = Award::orderBy('goal_amount', 'asc')->get();
        return view('admin.awards.index', compact('awards'));
    }

    public function create()
    {
        return view('admin.awards.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $data = $request->only(['title', 'description', 'goal_amount']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('awards', 'public');
            $data['image_url'] = $path;
        }

        Award::create($data);

        return redirect()->route('admin.awards.index')->with('success', 'Prêmio criado com sucesso!');
    }

    public function edit(Award $award)
    {
        return view('admin.awards.edit', compact('award'));
    }

    public function update(Request $request, Award $award)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $data = $request->only(['title', 'description', 'goal_amount']);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($award->image_url) {
                Storage::disk('public')->delete($award->image_url);
            }
            $path = $request->file('image')->store('awards', 'public');
            $data['image_url'] = $path;
        }

        $award->update($data);

        return redirect()->route('admin.awards.index')->with('success', 'Prêmio atualizado com sucesso!');
    }

    public function destroy(Award $award)
    {
        if ($award->image_url) {
            Storage::disk('public')->delete($award->image_url);
        }
        $award->delete();

        return redirect()->route('admin.awards.index')->with('success', 'Prêmio removido com sucesso!');
    }
}
