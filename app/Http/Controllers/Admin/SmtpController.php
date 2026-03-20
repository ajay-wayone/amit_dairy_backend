<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gateway;
use Illuminate\Http\Request;

class SmtpController extends Controller
{
    /**
     * Show the SMTP configuration form.
     */
    public function edit()
    {
        $gateway = Gateway::where('name', 'smtp')->first();
        $configData = $gateway ? $gateway->getConfigData() : [];

        return view('admin.smtp.edit', compact('gateway', 'configData'));
    }

    /**
     * Update SMTP configuration.
     */
    public function update(Request $request)
    {
        $request->validate([
            'smtp_host'       => 'required|string|max:255',
            'smtp_port'       => 'required|integer',
            'smtp_username'   => 'required|string|max:255',
            'smtp_password'   => 'required|string|max:255',
            'smtp_from_email' => 'required|email|max:255',
            'smtp_from_name'  => 'required|string|max:255',
            'smtp_encryption' => 'required|in:tls,ssl,none',
        ]);

        $gateway = Gateway::firstOrCreate(
            ['name' => 'smtp'],
            [
                'display_name' => 'SMTP Email',
                'type'         => 'email',
                'mode'         => 'live',
                'active'       => true,
            ]
        );

        $gateway->update([
            'config' => json_encode([
                'host'       => $request->smtp_host,
                'port'       => (int)$request->smtp_port,
                'username'   => $request->smtp_username,
                'password'   => $request->smtp_password,
                'from_email' => $request->smtp_from_email,
                'from_name'  => $request->smtp_from_name,
                'encryption' => $request->smtp_encryption,
            ]),
        ]);

        return redirect()->route('admin.smtp.edit')
            ->with('success', 'SMTP settings updated successfully!');
    }

    /**
     * Toggle SMTP active status (AJAX).
     */
    public function toggleStatus()
    {
        $gateway = Gateway::where('name', 'smtp')->first();
        if ($gateway) {
            $gateway->active = !$gateway->active;
            $gateway->save();

            return response()->json([
                'success' => true,
                'active'  => $gateway->active,
                'message' => 'SMTP ' . ($gateway->active ? 'enabled.' : 'disabled.'),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'SMTP not configured.']);
    }
}
