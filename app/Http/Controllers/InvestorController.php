<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvestorRequest;
use App\Http\Resources\InvestorResource;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InvestorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //        $this->authorize('viewInvestor', User::class);

        $investors = User::role('investor', 'api')->get();
        if ($investors->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'No investors found.'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Investor retrieved successfully.',
            'data' => InvestorResource::collection($investors)
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
    public function store(InvestorRequest $request)
    {
        //        $this->authorize('createInvestor', User::class);

        $validatedData = $request->validated();

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_images', 'public');
            $validatedData['profile_photo'] = $path;
        }

        $investor = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'address' => $validatedData['address'],
            'profile_photo' =>  $validatedData['profile_photo'],
            'phone_number' => $validatedData['phone_number'],
            'created_by' => Auth::id(),
        ]);
        

        $role = Role::findByName('investor', 'api');
        if (!$role) {
            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'Role not found.'
            ], 500);
        }
        $permissions = Permission::whereIn('id', $validatedData['permission'])->get();
        if ($permissions->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'Permissions not found.'
            ], 500);
        }
        $investor->syncPermissions($permissions);
        $investor->assignRole($role);

        Mail::to($investor->email)->send(new WelcomeMail($investor->email, $role->name));

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Investor created successfully.',
            'data' => InvestorResource::make($investor)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $investor = User::find($id);

        if (!$investor) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Investor not found'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Investor retrieved successfully.',
            'data' => InvestorResource::make($investor)
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
    public function update(InvestorRequest $request, string $id)
    {

        //        $this->authorize('updateInvestor', User::class);

        $investor = User::find($id);

        if (!$investor) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Investor not found'
            ]);
        }

        $validatedData = $request->validated();

        if ($request->hasFile('profile_photo')) {
            // If the project already has an image, delete the old one
            if ($investor->profile_photo) {
                Storage::delete($investor->profile_photo);
            }

            // Store the new image and update the validated data
            $path = $request->file('profile_photo')->store('profile_images', 'public');
            $validatedData['profile_photo'] = $path;
        }


        $investor->update($validatedData);

        $role = Role::findByName('investor', 'api');
        if (!$role) {
            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'Role not found.'
            ], 500);
        }
        $permissions = Permission::whereIn('id', $validatedData['permission'])->get();
        $investor->syncPermissions($permissions);
        $investor->syncRoles([$role]);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Investor updated successfully.',
            'data' => InvestorResource::make($investor)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //        $this->authorize('deleteInvestor', User::class);

        $investor = User::find($id);
        
        if (!$investor) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'Investor not found'
            ]);
        }

        $investor->roles()->detach();
        $investor->permissions()->detach();

        $investor->delete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Investor deleted successfully.',
        ]);
    }
}
