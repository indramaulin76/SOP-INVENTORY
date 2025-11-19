<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        // Check if user has permission (admin or superadmin)
        $currentUser = auth()->user();
        
        if ($currentUser->role === 'employee') {
            abort(403, 'Unauthorized access. Only admin and pimpinan can manage users.');
        }

        // Filter users based on role
        if ($currentUser->role === 'admin') {
            // Admin can only see karyawan (employee)
            $users = User::where('role', 'employee')->orderBy('name')->get();
        } else {
            // Pimpinan (superadmin) can see all users
            $users = User::orderBy('role')->orderBy('name')->get();
        }

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $currentUser = auth()->user();
        
        if ($currentUser->role === 'employee') {
            abort(403, 'Unauthorized access. Only admin and pimpinan can create users.');
        }

        return view('users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();
        
        if ($currentUser->role === 'employee') {
            abort(403, 'Unauthorized access. Only admin and pimpinan can create users.');
        }

        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:employee,admin,superadmin',
        ];

        // Role restrictions based on current user
        if ($currentUser->role === 'admin') {
            // Admin can only create employee (karyawan)
            $rules['role'] = 'required|in:employee';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // Create user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a user
     */
    public function edit(User $user)
    {
        $currentUser = auth()->user();
        
        if ($currentUser->role === 'employee') {
            abort(403, 'Unauthorized access. Only admin and pimpinan can edit users.');
        }

        // Admin can only edit employee (karyawan)
        if ($currentUser->role === 'admin' && $user->role !== 'employee') {
            abort(403, 'Unauthorized access. Admin can only edit employee users.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();
        
        if ($currentUser->role === 'employee') {
            abort(403, 'Unauthorized access. Only admin and pimpinan can update users.');
        }

        // Admin can only update employee (karyawan)
        if ($currentUser->role === 'admin' && $user->role !== 'employee') {
            abort(403, 'Unauthorized access. Admin can only update employee users.');
        }

        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ];

        // Role restrictions based on current user
        if ($currentUser->role === 'admin') {
            // Admin can only set role to employee
            $rules['role'] = 'required|in:employee';
        } else {
            // Pimpinan (superadmin) can set any role
            $rules['role'] = 'required|in:employee,admin,superadmin';
        }

        // Password is optional
        if ($request->filled('password')) {
            $rules['password'] = 'min:6|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // Update user
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $currentUser = auth()->user();
        
        if ($currentUser->role === 'employee') {
            abort(403, 'Unauthorized access. Only admin and pimpinan can delete users.');
        }

        // Prevent deleting yourself
        if ($currentUser->id === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Admin restrictions
        if ($currentUser->role === 'admin') {
            // Admin can only delete employee (karyawan)
            if ($user->role !== 'employee') {
                abort(403, 'Unauthorized access. Admin can only delete employee users.');
            }
        }
        // Pimpinan (superadmin) can delete anyone (no restrictions)

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}

