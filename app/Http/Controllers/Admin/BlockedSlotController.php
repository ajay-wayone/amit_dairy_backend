<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedSlot;
use Illuminate\Http\Request;

class BlockedSlotController extends Controller
{
    // Slots list
    public function index()
    {
        $blockedSlots = BlockedSlot::orderBy('blocked_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('admin.block.index', compact('blockedSlots'));
    }

    // Store new slot
    public function store(Request $request)
    {
        $data = $request->validate([
            'blocked_date' => 'nullable|date',
            'time_select'  => 'nullable|string',
            'custom_start_hour'   => 'nullable|integer',
            'custom_start_minute' => 'nullable|integer',
            'custom_start_ampm'   => 'nullable|string',
            'custom_end_hour'     => 'nullable|integer',
            'custom_end_minute'   => 'nullable|integer',
            'custom_end_ampm'     => 'nullable|string',
        ]);

        $start_time = $end_time = null;

        // Custom time selected
        if ($data['time_select'] === 'custom') {
            if(isset(
                $data['custom_start_hour'], $data['custom_start_minute'], $data['custom_start_ampm'],
                $data['custom_end_hour'], $data['custom_end_minute'], $data['custom_end_ampm']
            )) {

                $sh = $data['custom_start_hour'];
                $sm = $data['custom_start_minute'];
                $sampm = $data['custom_start_ampm'];

                $eh = $data['custom_end_hour'];
                $em = $data['custom_end_minute'];
                $eampm = $data['custom_end_ampm'];

                // Convert to 24-hour format
                $sh24 = ($sampm === 'PM' && $sh != 12) ? $sh + 12 : (($sampm === 'AM' && $sh == 12) ? 0 : $sh);
                $eh24 = ($eampm === 'PM' && $eh != 12) ? $eh + 12 : (($eampm === 'AM' && $eh == 12) ? 0 : $eh);

                $start_time = sprintf('%02d:%02d:00', $sh24, $sm);
                $end_time   = sprintf('%02d:%02d:00', $eh24, $em);
            }
        } 
        // Predefined time slot
        elseif (strpos($data['time_select'], '-') !== false) {
            list($start_time, $end_time) = explode('-', $data['time_select']);
            $start_time = trim($start_time) . ':00';
            $end_time   = trim($end_time) . ':00';
        }

        // Prevent duplicate slot
        if ($data['blocked_date'] && $start_time && $end_time) {
            $exists = BlockedSlot::where('blocked_date', $data['blocked_date'])
                ->where('start_time', $start_time)
                ->where('end_time', $end_time)
                ->exists();

            if ($exists) {
                return back()->with('error', 'This slot is already blocked.');
            }
        }

        // Create slot
        BlockedSlot::create([
            'blocked_date' => $data['blocked_date'] ?? null,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ]);

        return redirect()->route('block.index')->with('success', 'Slot added successfully.');
    }

    // Delete slot
    public function destroy(BlockedSlot $blockedSlot)
    {
        $blockedSlot->delete();
        return redirect()->route('block.index')->with('success', 'Slot deleted successfully.');
    }
}
