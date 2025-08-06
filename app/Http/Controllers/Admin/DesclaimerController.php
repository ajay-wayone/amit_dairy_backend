<?php

namespace App\Http\Controllers\admin;
use App\Models\Disclaimer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DesclaimerController extends Controller
{
    public function index(Request $request)
    {
       
   return view('admin.policies.disclaimer');
}
 public function edit(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            
        ]);

        $data = $request->all();


        return redirect()->route('admin.policies.disclaimer')
            ->with('success', ' created successfully.');
    }

}