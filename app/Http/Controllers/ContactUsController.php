<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use Illuminate\Http\Request;


class ContactUsController extends Controller
{
  /**
   * Display a listing of the messages.
   */
  public function index(Request $request)
  {
    $search = $request->input('search');
    $perPage = $request->input('per_page', 10);
    $sort = $request->input('sort', 'created_at');
    $order = $request->input('order', 'desc');

    $query = ContactUs::query();

    // Apply search filter if provided
    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('name', 'LIKE', "%{$search}%")
          ->orWhere('email', 'LIKE', "%{$search}%")
          ->orWhere('subject', 'LIKE', "%{$search}%")
          ->orWhere('message', 'LIKE', "%{$search}%")
          ->orWhere('phone', 'LIKE', "%{$search}%");
      });
    }

    // Apply sorting
    $query->orderBy($sort, $order);

    // Get paginated results
    $messages = $query->paginate($perPage);

    // Get statistics
    $totalMessages = ContactUs::count();
    $todayMessages = ContactUs::whereDate('created_at', now()->toDateString())->count();
    $lastWeekMessages = ContactUs::whereBetween('created_at', [now()->subDays(7), now()])->count();

    return view('content.shared.contact-us.index', [
      'messages' => $messages,
      'totalMessages' => $totalMessages,
      'todayMessages' => $todayMessages,
      'lastWeekMessages' => $lastWeekMessages,
      'search' => $search,
      'sort' => $sort,
      'order' => $order,
      'perPage' => $perPage
    ]);
  }

  /**
   * Display the specified message.
   */
  public function show(ContactUs $contactMessage)
  {
    return view('content.shared.contact-us.show', compact('contactMessage'));
  }

  /**
   * Show the form for editing the specified message.
   */
  public function edit(ContactUs $contactMessage)
  {
    if (request()->ajax()) {
      return response()->json($contactMessage);
    }

    return view('content.shared.contact-us.edit', compact('contactMessage'));
  }

  /**
   * Update the specified message in storage.
   */
  public function update(Request $request, ContactUs $contactMessage)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'nullable|email|max:255',
      'phone' => 'nullable|string|max:20',
      'subject' => 'nullable|string|max:255',
      'message' => 'required|string',
    ]);

    $contactMessage->update($request->all());

    if ($request->ajax()) {
      return response()->json($contactMessage);
    }

    return redirect()->route('admin.contact-us.index')
      ->with('success', 'Message updated successfully.');
  }
}
