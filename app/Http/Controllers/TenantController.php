<?php

namespace App\Http\Controllers;

use App\Http\Requests\TenantRequest;
use App\Http\Resources\TenantResource;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//        $this->authorize('viewTenant', User::class);

        $tenants = User::role('tenant','api')->get();
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Tenants retrieved successfully.',
            'data' => TenantResource::collection($tenants)
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
    public function store(TenantRequest $request)
    {
//        $this->authorize('createTenant', User::class);

        $validatedData = $request->validated();

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_images', 'public');
            $validatedData['profile_photo'] = $path;
        }

        $tenant = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'phone_number' => $validatedData['phone_number'],
            'profile_photo' =>  $validatedData['profile_photo'],
            'created_by' => Auth::id(),
        ]);


        $role = Role::findByName('tenant', 'api');

        $permissions = Permission::whereIn('id', $validatedData['permission'])->get();
        $tenant->syncPermissions($permissions);
        $tenant->assignRole($role);

        Mail::to($tenant->email)->send(new WelcomeMail($tenant->email,$role->name));

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Tenants created successfully.',
            'data' => TenantResource::make($tenant)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tenant = User::find($id);

        if (!$tenant) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Tenant not found']);
        }

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Tenants retrieved successfully.',
            'data' => TenantResource::make($tenant)
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
    public function update(TenantRequest $request, string $id)
    {
//        $this->authorize('updateTenant', User::class);

        $tenant = User::find($id);

        if (!$tenant) {
            return response()->json(['status' => 'error',
                'status_code' => 404,
                'message' => 'Tenant not found']);
        }

        $validatedData = $request->validated();
        if ($request->hasFile('profile_photo')) {
            // If the project already has an image, delete the old one
            if ($tenant->profile_photo) {
                Storage::delete($tenant->profile_photo);
            }

            // Store the new image and update the validated data
            $path = $request->file('profile_photo')->store('profile_images', 'public');
            $validatedData['profile_photo'] = $path;
        }

        $tenant->update($validatedData);

        $role = Role::findByName('tenant', 'api');

        $permissions = Permission::whereIn('id', $validatedData['permission'])->get();
        $tenant->syncPermissions($permissions);
        $tenant->syncRoles([$role]);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Tenants updated successfully.',
            'data' => TenantResource::make($tenant)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
//        $this->authorize('deleteTenant', User::class);

        $tenant = User::find($id);

        if (!$tenant) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Tenant not found']);
        }

        $tenant->roles()->detach();
        $tenant->permissions()->detach();

        $tenant->delete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Tenants deleted successfully.',
        ]);
    }
}
