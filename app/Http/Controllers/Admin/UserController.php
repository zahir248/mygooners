<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('services');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Verification filter
        if ($request->filled('verified')) {
            $query->where('is_verified', $request->verified);
        }

        // Order by latest first
        $query->orderBy('created_at', 'desc');

        // Get paginated results
        $users = $query->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::withCount('services')->findOrFail($id);
        $userServices = $user->services()->latest()->get();

        return view('admin.users.show', compact('user', 'userServices'));
    }

    public function verify($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_verified' => true]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berjaya disahkan!');
    }

    public function suspend($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'suspended']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berjaya digantung!');
    }

    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berjaya diaktifkan!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deletion of super admin or current user
        if ($user->role === 'super_admin' || $user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete this user.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berjaya dipadam!');
    }

    public function create()
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('admin.users.index')->with('error', 'Only super admin can create users.');
        }
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('admin.users.index')->with('error', 'Only super admin can create users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:user,admin,super_admin,writer',
            'password' => 'required|string|min:8|confirmed',
            'is_verified' => 'nullable|boolean',
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->password = bcrypt($validated['password']);
        $user->is_verified = $request->has('is_verified');
        $user->status = 'active'; // Automatically set to active
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berjaya dicipta!');
    }
} 