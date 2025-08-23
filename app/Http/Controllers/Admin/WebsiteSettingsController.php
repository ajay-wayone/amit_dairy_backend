<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebsiteSettingsController extends Controller
{
    public function edit()
    {
        // Fetch first settings row or create default
        $settings = WebsiteSetting::first();
        return view('admin.contact-details.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'email'        => 'required|email|max:255',
            'address'      => 'required|string|max:255',
            'facebook'     => 'nullable|url',
            'instagram'    => 'nullable|url',
            'twitter'      => 'nullable|url',
            'youtube'      => 'nullable|url',
            'about_us'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $settings = WebsiteSetting::first();
        if (! $settings) {
            $settings = new WebsiteSetting();
        }

        $settings->fill($request->only([
            'company_name',
            'phone',
            'email',
            'address',
            'facebook',
            'instagram',
            'twitter',
            'youtube',
            'about_us',
        ]));

        $settings->save();

        return redirect()->back()->with('success', 'Website settings updated successfully.');
    }
}
