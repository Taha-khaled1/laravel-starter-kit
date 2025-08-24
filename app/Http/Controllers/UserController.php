<?php

namespace App\Http\Controllers;

use App\Imports\NewUserImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        // For DataTables AJAX request - keep for backward compatibility but will be eventually removed
        if ($request->ajax()) {
            $columns = [
                1 => 'id',
                2 => 'name',
                3 => 'phone',
                4 => 'type',
                5 => 'identity_id',
                6 => 'nationality',
                7 => 'email',
            ];

            $search = $request->input('search.value');

            $query = User::query()
                ->when($search, function ($query, $search) {
                    return $query->where('id', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%")
                        ->orWhere('identity_id', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });

            $totalData = $query->count();
            $totalFiltered = $totalData;

            $users = $query->orderBy($columns[$request->input('order.0.column')], $request->input('order.0.dir'))
                ->offset($request->input('start'))
                ->limit($request->input('length'))
                ->get();

            $data = $users->map(function ($user, $index) use ($request) {
                return [
                    'id' => $user->id,
                    'fake_id' => $request->input('start') + $index + 1,
                    'name' => $user->name,
                    'identity_id' => $user->identity_id,
                    'nationality' => $user->country->name_ar ?? '',
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'type' => $user->type,
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => intval($totalData),
                'recordsFiltered' => intval($totalFiltered),
                'data' => $data,
            ]);
        }

        // For regular page load with server-side pagination
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'desc');
        
        $query = User::query()->with('country');
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%")
                  ->orWhere('identity_id', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply sorting
        $query->orderBy($sort, $order);
        
        // Get paginated results
        $users = $query->paginate($perPage);
        
        // Get statistics data
        $userCount = User::count();
        $notVerified = User::whereNull('email_verified_at')->count();
        $usersUnique = User::all()->unique(['phone']);
        $userDuplicates = User::count() - $usersUnique->count();

        return view('content.shared.users.index', [
            'users' => $users,
            'totalUser' => $userCount,
            'verified' => 0,
            'notVerified' => $notVerified,
            'userDuplicates' => $userDuplicates,
            'search' => $search,
            'sort' => $sort,
            'order' => $order,
            'perPage' => $perPage
        ]);
    }

    /**
     * Import users from Excel file.
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv'
            ]);

            Excel::import(new NewUserImport, $request->file('file'));

            return back()->with('success', 'تم استيراد البيانات بنجاح والان يتم العمل عليها في الخلفيه!');
        } catch (\Exception $e) {
            return back()->with('warning', 'حدث خطأ أثناء استيراد البيانات: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('content.shared.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $request->id,
            'phone' => 'nullable|string|max:20',
            'identity_id' => 'nullable|string|max:255',
            'type' => 'required|string',
            'password' => $request->id ? 'nullable|string|min:6' : 'required|string|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'type' => $request->type,
            'phone' => $request->phone,
            'identity_id' => $request->identity_id,
            'status' => $request->has('status') ? 1 : 0,
            'age' => $request->age,
            'gender' => $request->gender,
            'name_en' => $request->name, // Set name_en equal to name as it's required
        ];

        // Only hash password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($request->id) {
                $user = User::find($request->id);
                if ($user && $user->image) {
                    Storage::disk('public')->delete($user->image);
                }
            }
            $image = $request->file('image');
            $data['image'] = $image->store('users', 'public');
        }

        $user = User::updateOrCreate(['id' => $request->id], $data);

        if ($request->ajax()) {
            return response()->json($user, $request->id ? 200 : 201);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User ' . ($request->id ? 'updated' : 'created') . ' successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('content.shared.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        if (request()->ajax()) {
            return response()->json($user);
        }

        return view('content.shared.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        // Redirect to store method as it handles both create and update
        return $this->store($request);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        try {
            // Delete user image if exists
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            $user->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return redirect()->route('admin.users.index')
                ->with('warning', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Export users data in different formats.
     */
    public function export(Request $request)
    {
        $format = $request->format ?? 'csv';
        $search = $request->search;
        $sort = $request->sort ?? 'id';
        $order = $request->order ?? 'desc';
        
        $query = User::query()->with('country');
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%")
                  ->orWhere('identity_id', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply sorting
        $query->orderBy($sort, $order);
        
        $users = $query->get();
        
        return Excel::download(new UsersExport($users), 'users.' . $format);
    }
}
