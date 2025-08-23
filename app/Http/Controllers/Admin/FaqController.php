<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
  public function index(Request $request)
{
    $query = Faq::orderBy('sort_order');

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->where('question', 'like', "%{$search}%")
              ->orWhere('answer', 'like', "%{$search}%");
        });
    }

    $faqs = $query->paginate(10)->appends($request->only('search'));

    return view('admin.faqs.index', compact('faqs'));
}


    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        Faq::create($data);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ created successfully.');
    }

   
    public function edit(Faq $faq)
    {
        return view('admin.faqs.update', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
      
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $faq->update($data);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')
            ->with('success', 'FAQ deleted successfully.');
    }

    public function toggleStatus(Faq $faq)
    {
        $faq->update(['is_active' => !$faq->is_active]);
        
        return response()->json([
            'success' => true,
            'message' => 'FAQ status updated successfully.',
            'is_active' => $faq->is_active
        ]);
    }
}
