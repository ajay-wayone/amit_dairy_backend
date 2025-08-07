<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PolicyController extends Controller
{
    /**
     * Display a listing of policies
     */
    public function index()
    {
        $policies = Policy::orderBy('type')->get();
        $policyTypes = Policy::getTypes();
        
        return view('admin.policies.index', compact('policies', 'policyTypes'));
    }

    /**
     * Show the form for creating a new policy
     */
    public function create()
    {
        $policyTypes = Policy::getTypes();
        $existingTypes = Policy::pluck('type')->toArray();
        $availableTypes = array_diff_key($policyTypes, array_flip($existingTypes));
        
        return view('admin.policies.create', compact('availableTypes'));
    }

    /**
     * Store a newly created policy
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|unique:policies,type',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Policy::create([
            'type' => $request->type,
            'title' => $request->title,
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_active' => $request->has('is_active'),
            'last_updated_by' => Auth::id()
        ]);

        return redirect()->route('admin.policies.index')
            ->with('success', 'Policy created successfully!');
    }

    /**
     * Show the form for editing a policy
     */
    public function edit(Policy $policy)
    {
        $policyTypes = Policy::getTypes();
        
        return view('admin.policies.edit', compact('policy', 'policyTypes'));
    }

    /**
     * Update the specified policy
     */
    public function update(Request $request, Policy $policy)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $policy->update([
            'title' => $request->title,
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_active' => $request->has('is_active'),
            'last_updated_by' => Auth::id()
        ]);

        return redirect()->route('admin.policies.index')
            ->with('success', 'Policy updated successfully!');
    }

    /**
     * Toggle policy status
     */
    public function toggleStatus(Policy $policy)
    {
        $policy->update([
            'is_active' => !$policy->is_active,
            'last_updated_by' => Auth::id()
        ]);

        $status = $policy->is_active ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "Policy {$status} successfully!",
            'is_active' => $policy->is_active
        ]);
    }

    /**
     * Delete a policy
     */
    public function destroy(Policy $policy)
    {
        $policy->delete();
        
        return redirect()->route('admin.policies.index')
            ->with('success', 'Policy deleted successfully!');
    }

    /**
     * Show specific policy type
     */
    public function show($type)
    {
        $policy = Policy::where('type', $type)->firstOrFail();
        
        return view('admin.policies.show', compact('policy'));
    }

    /**
     * Get policy content for API
     */
    public function getPolicyContent($type)
    {
        $policy = Policy::where('type', $type)
            ->where('is_active', true)
            ->first();

        if (!$policy) {
            return response()->json([
                'success' => false,
                'message' => 'Policy not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'type' => $policy->type,
                'title' => $policy->title,
                'content' => $policy->content,
                'meta_title' => $policy->meta_title,
                'meta_description' => $policy->meta_description,
                'last_updated' => $policy->updated_at->format('Y-m-d H:i:s')
            ]
        ]);
    }
} 