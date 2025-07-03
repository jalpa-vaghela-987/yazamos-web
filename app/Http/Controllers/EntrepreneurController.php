<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntrepreneurRequest;
use App\Http\Resources\EntrepreneurResource;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\SuccessResource;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EntrepreneurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entrepreneurs = User::role('entrepreneur', 'api')->get();
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Entrepreneurs retrieved successfully.',
            'data' => EntrepreneurResource::collection($entrepreneurs)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EntrepreneurRequest $request)
    {
        // Validate the data
        $validatedData = $request->validated();

        // Handle the profile photo upload if provided
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public'); // Store in 'profile_photos' folder in public disk
            $validatedData['profile_photo'] = $profilePhotoPath; // Save the file path to the validated data
        }


        // Create the entrepreneur user record
        $entrepreneur = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'phone_number' => $validatedData['phone_number'],
            'created_by' => Auth::id(),
            'profile_photo' => $validatedData['profile_photo'] ?? null, // Save the profile photo path in the database
        ]);

        // Assign the 'entrepreneur' role
        $role = Role::findByName('entrepreneur', 'api');

        // Assign the permissions based on the validated data
        $permissions = Permission::whereIn('id', $validatedData['permission'])->get();
        $entrepreneur->syncPermissions($permissions);
        $entrepreneur->assignRole($role);

        Mail::to($entrepreneur->email)->send(new WelcomeMail($entrepreneur->email,$role->name));


        // Return success response
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Entrepreneur created successfully.',
            'data' => EntrepreneurResource::make($entrepreneur)
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $entrepreneur = User::find($id);

        if (!$entrepreneur) {
            return response()->json(
                [
                    'status' => 'error',
                    'status_code' => 404,
                    'message' => 'Entrepreneur not found'
                ]
            );
        }
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Entrepreneurs retrieved successfully.',
            'data' => EntrepreneurResource::make($entrepreneur)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EntrepreneurRequest $request, string $id)
    {
        $entrepreneur = User::find($id);

        if (!$entrepreneur) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Entrepreneur not found'
            ]);
        }

        $validatedData = $request->validated();
        // Check if the request contains a new profile photo
        if ($request->hasFile('profile_photo')) {
            // Delete the old photo if it exists
            if ($entrepreneur->profile_photo) {
                Storage::delete($entrepreneur->profile_photo);
            }

            // Store the new profile photo
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $validatedData['profile_photo'] = $profilePhotoPath;
        }

        // Update the entrepreneur's details
        $entrepreneur->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'phone_number' => $validatedData['phone_number'],
            'profile_photo' => $validatedData['profile_photo'] ?? $entrepreneur->profile_photo, // Update if profile photo is new
        ]);

        // Sync the permissions and roles
        $role = Role::findByName('entrepreneur', 'api');
        $permissions = Permission::whereIn('id', $validatedData['permission'])->get();
        $entrepreneur->syncPermissions($permissions);
        $entrepreneur->syncRoles([$role]);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Entrepreneur updated successfully.',
            'data' => EntrepreneurResource::make($entrepreneur)
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $entrepreneur = User::find($id);

        if (!$entrepreneur) {
            return response()->json(
                [
                    'status' => 'error',
                    'status_code' => 404,
                    'message' => 'Entrepreneur not found'
                ]
            );
        }

        $entrepreneur->roles()->detach();
        $entrepreneur->permissions()->detach();

        $entrepreneur->delete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Entrepreneur deleted successfully.',
        ]);
    }

    public function getAllPermission()
    {

        $permissions = Permission::where('guard_name', 'api')->get();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Permissions retrieved successfully.',
            'data' => PermissionResource::collection($permissions)
        ]);
    }
}
