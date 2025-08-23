<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $newsletters = Newsletter::when($search, function ($query, $search) {
            return $query->where('email', 'like', "%{$search}%");
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]); // preserve search on pagination links

        return view('admin.newsletters.index', compact('newsletters', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'     => 'required|email|unique:newsletters,email',
            'name'      => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data                  = $request->all();
        $data['is_active']     = $request->has('is_active');
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
            'email'     => 'required|email|unique:newsletters,email,' . $newsletter->id,
            'name'      => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data              = $request->all();
        $data['is_active'] = $request->has('is_active');

        $newsletter->update($data);

        return redirect()->route('admin.newsletters.index')
            ->with('success', 'Newsletter subscription updated successfully.');
    }

   public function destroy(Newsletter $newsletter)
{
    $newsletter->delete();

    if (request()->ajax()) {
        // AJAX request => JSON response
        return response()->json([
            'success' => true,
            'message' => 'Newsletter subscription deleted successfully.',
        ]);
    } else {
        // Normal request => redirect with success message
        return redirect()->route('admin.newsletters.index')
            ->with('success', 'Newsletter subscription deleted successfully.');
    }
}


}
