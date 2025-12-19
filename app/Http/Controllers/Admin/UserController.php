<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\SubDepartment;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'department', 'subDepartment', 'designation'])
            ->latest()
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create', [
            'user' => null,
            'roles' => Role::all(),
            'departments' => Department::all(),
            'subDepartments' => SubDepartment::all(),
            'designations' => Designation::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|alpha_dash|unique:users,username|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'sub_department_id' => 'nullable|exists:sub_departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'gender' => 'nullable|in:male,female,other',
            'phone_no' => 'nullable|string|max:20',
            'district' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'department_id' => $validated['department_id'] ?? null,
            'sub_department_id' => $validated['sub_department_id'] ?? null,
            'designation_id' => $validated['designation_id'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'phone_no' => $validated['phone_no'] ?? null,
            'district' => $validated['district'] ?? null,
            'status' => $validated['status'],
            'profile_photo_path' => $profilePhotoPath,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.create', [
            'user' => $user,
            'roles' => Role::all(),
            'departments' => Department::all(),
            'subDepartments' => SubDepartment::all(),
            'designations' => Designation::all(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'sub_department_id' => 'nullable|exists:sub_departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'gender' => 'nullable|in:male,female,other',
            'phone_no' => 'nullable|string|max:20',
            'district' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'department_id' => $validated['department_id'] ?? null,
            'sub_department_id' => $validated['sub_department_id'] ?? null,
            'designation_id' => $validated['designation_id'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'phone_no' => $validated['phone_no'] ?? null,
            'district' => $validated['district'] ?? null,
            'status' => $validated['status'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
