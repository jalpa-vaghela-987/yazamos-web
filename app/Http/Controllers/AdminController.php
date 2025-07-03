<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRequest;
use App\Http\Requests\TenantRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\TenantResource;
use App\Mail\WelcomeMail;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Project;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $admins = User::role('admin', 'api')->queryCrud($input);
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Admins retrieved successfully.',
            'data' => AdminResource::collection($admins)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request)
    {
        $validatedData = $request->validated();

        try {
            if ($request->hasFile('profile_photo')) {
                $profile = $request->file('profile_photo');

                // Step 1: Generate file name and path
                $filename = 'profile_' . now()->format('Ymd_His') . '_' . uniqid() . '.' . $profile->getClientOriginalExtension();
                $path = 'profile_images/' . $filename;

                // Step 2: Save path into data array (before file is stored)
                $validatedData['profile_photo'] = $path;

                // Step 3: Store file using that exact path
                $profile->storeAs('profile_images', $filename, 'public');

                Log::info('Profile photo path prepared and stored', ['path' => $path]);
            }

            // Create company first
            $company = Company::create([
                'name' => $validatedData['company_name']
            ]);

            $admin = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'company_name' => $validatedData['company_name'],
                'country_code' => $validatedData['country_code'],
                'phone_number' => $validatedData['phone_number'],
                'profile_photo' => $validatedData['profile_photo'] ?? null,
                'created_by' => Auth::id(),
            ]);


            $role = Role::findByName('admin', 'api');
            if ($role) {
                $admin->assignRole($role);
                Log::info('Admin role assigned to user', ['user_id' => $admin->id]);
            } else {
                Log::error('Admin role not found');
            }

            Mail::to($admin->email)->send(new WelcomeMail($admin->email, "admin"));

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Company Admin created successfully.',
                'data' => AdminResource::make($admin)
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating admin', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'Failed to create admin.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = User::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Tenant not found'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Company Admin retrieved successfully.',
            'data' => TenantResource::make($admin)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $request, string $id)
    {
        //        $this->authorize('updateTenant', User::class);

        $admin = User::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Admin not found'
            ]);
        }

        $validatedData = $request->validated();
        if ($request->hasFile('profile_photo')) {
            // If the project already has an image, delete the old one
            if ($admin->profile_photo) {
                Storage::delete($admin->profile_photo);
            }

            // Store the new image and update the validated data
            $path = $request->file('profile_photo')->store('profile_images', 'public');
            $validatedData['profile_photo'] = $path;
        }

        $admin->update($validatedData);

        $role = Role::findByName('admin', 'api');

        $admin->syncRoles([$role]);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Company Admin updated successfully.',
            'data' => AdminResource::make($admin)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //        $this->authorize('deleteTenant', User::class);

        $admin = User::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Tenant not found'
            ]);
        }

        $admin->roles()->detach();
        $admin->permissions()->detach();

        $admin->forceDelete();


        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Company Admin deleted successfully.',
        ]);
    }
}
