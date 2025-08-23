<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;
use Illuminate\Http\Request;

class ContactController extends Controller
{
   public function index(Request $request)
{
    $query = ContactEnquiry::query();

    // Agar search parameter aaya hai
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('message', 'like', "%{$search}%");
        });
    }

    $contacts = $query->orderBy('created_at', 'desc')->paginate(15);

    return view('admin.contacts.index', compact('contacts'));
}


    public function show(ContactEnquiry $contact)
    {
        // Mark as read if status is new
        if ($contact->status === 'new') {
            $contact->update([
                'status' => 'read',
                'read_at' => now()
            ]);
        }
        
        return view('admin.contacts.show', compact('contact'));
    }

    public function edit(ContactEnquiry $contact)
    {
        return view('admin.contacts.edit', compact('contact'));
    }

    public function update(Request $request, ContactEnquiry $contact)
    {
        $request->validate([
            'status' => 'required|in:new,read,replied,closed',
            'admin_notes' => 'nullable|string',
        ]);

        $data = $request->all();
        
        if ($request->status === 'replied' && $contact->status !== 'replied') {
            $data['replied_at'] = now();
        }

        $contact->update($data);

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact enquiry updated successfully.');
    }

    public function destroy(ContactEnquiry $contact)
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact enquiry deleted successfully.');
    }

    public function updateStatus(Request $request, ContactEnquiry $contact)
    {
        $request->validate([
            'status' => 'required|in:new,read,replied,closed',
        ]);

        $data = ['status' => $request->status];
        
        if ($request->status === 'read' && $contact->status === 'new') {
            $data['read_at'] = now();
        } elseif ($request->status === 'replied' && $contact->status !== 'replied') {
            $data['replied_at'] = now();
        }

        $contact->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Contact enquiry status updated successfully.',
            'status' => $contact->status
        ]);
    }
}
