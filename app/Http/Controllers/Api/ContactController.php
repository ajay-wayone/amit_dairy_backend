<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Notifications\UserNotification;

class ContactController extends Controller
{
    /**
     * Submit contact enquiry
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'required|string|max:20',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|min:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    // 'errors' => $validator->errors(),
                ], 422);
            }

            $contact = ContactEnquiry::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'subject' => $request->subject,
                'message' => $request->message,
                'status' => 'unread',
            ]);

            // Notify all admins about new contact enquiry
            $this->notifyAdmins($contact);

            return response()->json([
                'status' => true,
                'message' => 'Contact enquiry submitted successfully. We will get back to you soon.',
                'data' => [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'subject' => $contact->subject,
                    'submitted_at' => $contact->created_at
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's contact enquiries (Protected)
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $query = ContactEnquiry::where('email', $user->email);

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSortFields = ['subject', 'status', 'created_at'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'created_at';
            }

            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 10);
            $enquiries = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Contact enquiries retrieved successfully',
                'data' => [
                    'enquiries' => $enquiries->items(),
                    'pagination' => [
                        'current_page' => $enquiries->currentPage(),
                        'last_page' => $enquiries->lastPage(),
                        'per_page' => $enquiries->perPage(),
                        'total' => $enquiries->total(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get contact enquiry details (Protected)
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();

            $enquiry = ContactEnquiry::where('id', $id)
                ->where('email', $user->email)
                ->first();

            if (!$enquiry) {
                return response()->json([
                    'status' => false,
                    'message' => 'Contact enquiry not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Contact enquiry details retrieved successfully',
                'data' => $enquiry
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Notify admin users about new contact enquiry
     */
    protected function notifyAdmins(ContactEnquiry $contact)
    {
        try {
            $admins = User::where('role', 'admin')->where('is_active', true)->get();

            foreach ($admins as $admin) {
                $admin->notify(new UserNotification(
                    'New Contact Enquiry',
                    'A new contact enquiry from ' . $contact->name . ' has been submitted.',
                    ['contact_id' => $contact->id, 'type' => 'contact_enquiry']
                ));
            }

        } catch (\Exception $e) {
            \Log::error('Contact notification failed: ' . $e->getMessage());
        }
    }
}
