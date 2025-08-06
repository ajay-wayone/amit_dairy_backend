<?php
namespace App\Http\Controllers\Policies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy;

class PolicyController extends Controller
{
    public function show($type)
    {
        $policy = Policy::firstOrCreate(['type' => $type], ['content' => '']);
        return view('admin.policies.form', compact('policy'));
    }

    public function update(Request $request, $type)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $policy = Policy::updateOrCreate(
            ['type' => $type],
            ['content' => $request->input('content')]
        );

        return redirect()->back()->with('success', ucfirst($type).' policy updated successfully!');
    }
}