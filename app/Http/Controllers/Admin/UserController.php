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
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function data(): JsonResponse
    {
        $query = User::query()
            ->with(['role:id,name', 'department:id,name', 'subDepartment:id,name', 'designation:id,title'])
            ->select('users.*');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('profile_photo', function (User $user): string {
                return sprintf(
                    '<img src="%s" alt="%s" class="rounded-circle border" width="42" height="42" style="object-fit:cover;">',
                    e($user->profile_photo_url),
                    e($user->name)
                );
            })
            ->addColumn('role_name', fn (User $user): string => $user->role?->name ?? 'N/A')
            ->addColumn('department_name', fn (User $user): string => $user->department?->name ?? 'N/A')
            ->addColumn('sub_department_name', fn (User $user): string => $user->subDepartment?->name ?? 'N/A')
            ->addColumn('designation_title', fn (User $user): string => $user->designation?->title ?? 'N/A')
            ->editColumn('status', function (User $user): string {
                $class = $user->status === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';

                return sprintf(
                    '<span class="badge rounded-pill %s">%s</span>',
                    $class,
                    e(ucfirst($user->status ?? 'unknown'))
                );
            })
            ->editColumn('gender', fn (User $user): string => $user->gender ? ucfirst($user->gender) : 'N/A')
            ->editColumn('qualification', fn (User $user): string => $user->qualification ? e(Str::limit($user->qualification, 60)) : 'N/A')
            ->editColumn('area_of_expertise', fn (User $user): string => $user->area_of_expertise ? e(Str::limit($user->area_of_expertise, 60)) : 'N/A')
            ->editColumn('procurement_support', fn (User $user): string => $user->procurement_support ? e(Str::limit($user->procurement_support, 60)) : 'N/A')
            ->editColumn('previous_experience', fn (User $user): string => $user->previous_experience ? e(Str::limit($user->previous_experience, 60)) : 'N/A')
            ->editColumn('dob', fn (User $user): string => optional($user->dob)->format('d M Y') ?? 'N/A')
            ->editColumn('date_of_joining', fn (User $user): string => optional($user->date_of_joining)->format('d M Y') ?? 'N/A')
            ->editColumn('email_verified_at', fn (User $user): string => optional($user->email_verified_at)?->format('d M Y, h:i A') ?? 'Not verified')
            ->editColumn('created_at', fn (User $user): string => optional($user->created_at)?->format('d M Y, h:i A') ?? 'N/A')
            ->editColumn('updated_at', fn (User $user): string => optional($user->updated_at)?->format('d M Y, h:i A') ?? 'N/A')
            ->addColumn('actions', function (User $user): string {
                $profileUrl = route('admin.profile.edit', $user->id);
                $editUrl = route('admin.users.edit', $user);
                $deleteUrl = route('admin.users.destroy', $user);
                $token = csrf_token();

                return <<<HTML
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{$profileUrl}" class="btn btn-sm btn-outline-info" title="Edit profile">
                            <i class="fas fa-id-card"></i>
                        </a>
                        <a href="{$editUrl}" class="btn btn-sm btn-outline-primary" title="Edit user">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form action="{$deleteUrl}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                            <input type="hidden" name="_token" value="{$token}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete user">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                HTML;
            })
            ->filterColumn('role_name', function ($query, $keyword): void {
                $query->whereHas('role', fn ($roleQuery) => $roleQuery->where('name', 'like', "%{$keyword}%"));
            })
            ->filterColumn('department_name', function ($query, $keyword): void {
                $query->whereHas('department', fn ($departmentQuery) => $departmentQuery->where('name', 'like', "%{$keyword}%"));
            })
            ->filterColumn('sub_department_name', function ($query, $keyword): void {
                $query->whereHas('subDepartment', fn ($subDepartmentQuery) => $subDepartmentQuery->where('name', 'like', "%{$keyword}%"));
            })
            ->filterColumn('designation_title', function ($query, $keyword): void {
                $query->whereHas('designation', fn ($designationQuery) => $designationQuery->where('title', 'like', "%{$keyword}%"));
            })
            ->orderColumn('role_name', function ($query, $order): void {
                $query->orderBy(
                    Role::select('name')
                        ->whereColumn('roles.id', 'users.role_id')
                        ->limit(1),
                    $order
                );
            })
            ->rawColumns(['profile_photo', 'status', 'actions'])
            ->toJson();
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
            'dob' => 'nullable|date',
            'date_of_joining' => 'nullable|date',
            'qualification' => 'nullable|string',
            'total_work_experience' => 'nullable|string|max:50',
            'area_of_expertise' => 'nullable|string',
            'procurement_support' => 'nullable|string',
            'research_publication_citation' => 'nullable|string|max:100',
            'previous_experience' => 'nullable|string',
        ]);

        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 's3');
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
            'dob' => $validated['dob'] ?? null,
            'date_of_joining' => $validated['date_of_joining'] ?? null,
            'qualification' => $validated['qualification'] ?? null,
            'total_work_experience' => $validated['total_work_experience'] ?? null,
            'area_of_expertise' => $validated['area_of_expertise'] ?? null,
            'procurement_support' => $validated['procurement_support'] ?? null,
            'research_publication_citation' => $validated['research_publication_citation'] ?? null,
            'previous_experience' => $validated['previous_experience'] ?? null,
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

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->updateUser(
            $user,
            $request->validated(),
            $request->file('profile_photo')
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
    
}
