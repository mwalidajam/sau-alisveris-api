<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserSections;
use App\Http\Requests\User\{CreateUser, UpdateUser};
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super-admin');
    }

    public function index()
    {
        try {
            $users = User::all();
            return response()->json([
                'status' => 'success',
                'users' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function create(CreateUser $request)
    {
        try {
            $validated = $request->validated();
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
            ]);
            $user->syncSections($validated['sections']);
            return response()->json([
                'status' => 'success',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function update(UpdateUser $request, User $user)
    {
        try {
            $validated = $request->validated();
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);
            $user->syncSections($validated['sections']);
            return response()->json([
                'status' => 'success',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function show(User $user)
    {
        try {
            // get user roles and permissions
            $user->roles;
            $user->permissions;
            $user->sections;
            return response()->json([
                'status' => 'success',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function roles()
    {
        try {
            $roles = Role::all()->pluck('name');
            return response()->json([
                'status' => 'success',
                'roles' => $roles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function permissions()
    {
        try {
            $permissions = Permission::all()->pluck('name');
            return response()->json([
                'status' => 'success',
                'permissions' => $permissions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function rolesWithPermissions()
    {
        try {
            $roles = Role::with('permissions')->get();
            return response()->json([
                'status' => 'success',
                'roles' => $roles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateUserPermissionsAndRoles(Request $request, User $user)
    {
        try {
            $user->syncRoles($request->roles);
            $user->syncPermissions($request->permissions);
            return response()->json([
                'status' => 'success',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getMe(Request $request)
    {
        try {
            $user = $request->user();
            $user->roles;
            $user->permissions;
            return response()->json([
                'status' => 'success',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
