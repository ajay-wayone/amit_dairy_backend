<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index()
    {
        $newsletters = Newsletter::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.newsletters.index', compact('newsletters'));
    }

    public function create()
    {
        return view('admin.newsletters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email',
            'name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['subscribed_at'] = now();

        Newsletter::create($data);

        return redirect()->route('admin.newsletters.index')
            ->with('success', 'Newsletter subscription added successfully.');
    }

    public function show(Newsletter $newsletter)
    {
        return view('admin.newsletters.show', compact('newsletter'));
    }

    public function edit(Newsletter $newsletter)
    {
        return view('admin.newsletters.edit', compact('newsletter'));
    }

    public function update(Request $request, Newsletter $newsletter)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email,' . $newsletter->id,
            'name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $newsletter->update($data);

        return redirect()->route('admin.newsletters.index')
            ->with('success', 'Newsletter subscription updated successfully.');
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();

        return redirect()->route('admin.newsletters.index')
            ->with('success', 'Newsletter subscription deleted successfully.');
    }

    public function toggleStatus(Newsletter $newsletter)
    {
        $newsletter->update(['is_active' => !$newsletter->is_active]);
        
        return response()->json([
            'success' => true,
            'message' => 'Newsletter status updated successfully.',
            'is_active' => $newsletter->is_active
        ]);
    }
}
