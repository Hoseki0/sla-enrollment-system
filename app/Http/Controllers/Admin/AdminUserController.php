<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $this->requireSuperAdmin();
        $admins = Admin::orderBy('name')->get();
        return view('admin.users.index', compact('admins'));
    }

    public function create()
    {
        $this->requireSuperAdmin();
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $this->requireSuperAdmin();

        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:admins,email',
            'role'     => 'required|in:super_admin,registrar,cashier,teacher',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Admin::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        ActivityLog::recordAdmin('admin.created', "Created admin account: {$admin->name} ({$admin->role_label})", $admin);

        return redirect()->route('admin.users.index')
            ->with('success', "Admin account for {$admin->name} created successfully.");
    }

    public function edit(Admin $user)
    {
        $this->requireSuperAdmin();
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, Admin $user)
    {
        $this->requireSuperAdmin();

        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', Rule::unique('admins', 'email')->ignore($user->id)],
            'role'     => 'required|in:super_admin,registrar,cashier,teacher',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->role  = $data['role'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        ActivityLog::recordAdmin('admin.updated', "Updated admin account: {$user->name}", $user);

        return redirect()->route('admin.users.index')
            ->with('success', "Admin account updated.");
    }

    public function destroy(Admin $user)
    {
        $this->requireSuperAdmin();

        if ($user->id === Auth::guard('admin')->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::recordAdmin('admin.deleted', "Deleted admin account: {$name}");

        return redirect()->route('admin.users.index')
            ->with('success', "Admin account for {$name} deleted.");
    }

    private function requireSuperAdmin(): void
    {
        if (!Auth::guard('admin')->user()?->isSuperAdmin()) {
            abort(403, 'Only super administrators can manage admin accounts.');
        }
    }
}
