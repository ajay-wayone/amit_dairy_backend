<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gateway;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    /**
     * List all gateways.
     */
    public function index()
    {
        $gateways = Gateway::where('name', '!=', 'smtp')
            ->orderBy('type')->orderBy('display_name')->get();
        return view('admin.gateways.index', compact('gateways'));
    }

    /**
     * Show the edit form for a gateway.
     */
    public function edit($id)
    {
        $gateway = Gateway::findOrFail($id);
        $configData = $gateway->getConfigData();
        return view('admin.gateways.edit', compact('gateway', 'configData'));
    }

    /**
     * Update gateway credentials and mode.
     */
    public function update(Request $request, $id)
    {
        $gateway = Gateway::findOrFail($id);

        // Payment gateways (razorpay, stripe) use key/secret fields
        if (in_array($gateway->name, ['razorpay', 'stripe'])) {
            $request->validate([
                'test_key' => 'nullable|string|max:500',
                'test_secret' => 'nullable|string|max:500',
                'live_key' => 'nullable|string|max:500',
                'live_secret' => 'nullable|string|max:500',
            ]);

            $updateData = [
                'test_key'    => $request->test_key,
                'test_secret' => $request->test_secret,
                'live_key'    => $request->live_key,
                'live_secret' => $request->live_secret,
            ];

            if ($request->has('mode')) {
                $updateData['mode'] = $request->mode;
            }

            $gateway->update($updateData);
        }

        // UPI gateway uses config JSON
        elseif ($gateway->name === 'upi') {
            $request->validate([
                'upi_id'        => 'required|string|max:255',
                'merchant_name' => 'required|string|max:255',
            ]);

            $gateway->update([
                'config' => json_encode([
                    'upi_id'        => $request->upi_id,
                    'merchant_name' => $request->merchant_name,
                ]),
            ]);
        }

        return redirect()->route('admin.gateways.index')
            ->with('success', $gateway->display_name . ' settings updated successfully!');
    }

    /**
     * Toggle gateway mode between test and live (AJAX).
     */
    public function toggleMode($id)
    {
        $gateway = Gateway::findOrFail($id);
        $gateway->mode = $gateway->mode === 'test' ? 'live' : 'test';
        $gateway->save();

        return response()->json([
            'success' => true,
            'mode' => $gateway->mode,
            'message' => $gateway->display_name . ' switched to ' . strtoupper($gateway->mode) . ' mode.',
        ]);
    }

    /**
     * Toggle gateway active status (AJAX).
     */
    public function toggleStatus($id)
    {
        $gateway = Gateway::findOrFail($id);
        $gateway->active = !$gateway->active;
        $gateway->save();

        return response()->json([
            'success' => true,
            'active' => $gateway->active,
            'message' => $gateway->display_name . ($gateway->active ? ' activated.' : ' deactivated.'),
        ]);
    }
}
