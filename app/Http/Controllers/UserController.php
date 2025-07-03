<?php

namespace App\Http\Controllers;

use App\Models\AssetType;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\AdminUserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Auth;
use App\Models\AssignedUserProject;
use App\Http\Resources\UserDropdownResource;
use App\Http\Requests\DeleteUserRequest;
use App\Models\InvitationLog;
use App\Models\Project;
use Illuminate\Support\Str;
use App\Models\ProjectUserRole;
use App\Mail\ProjectInvitationMail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $input = $request->all();

        $entrepreneurIds = User::where('created_by', loginUser()->id)
            ->whereHas('roles', function ($q) {
                $q->where('name', ROLE_ENTREPRENEUR);
            })
            ->pluck('id');

        $users = User::query()
            ->where('created_by', loginUser()->id)
            ->orWhereHas('assignedUserProjects', function ($q2) use ($entrepreneurIds) {
                $q2->whereHas('project', function ($q3) use ($entrepreneurIds) {
                    $q3->whereIn('user_id', $entrepreneurIds)
                        ->orWhere('user_id', loginUser()->id);
                });
            })
            ->filterRecords($input)
            ->queryCrud($input);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Users retrieved successfully without project filter.',
            'data' => AdminUserResource::collection($users)
        ]);
    }


    public function indexWithoutProject(Request $request)
    {
        $input = $request->all();

        // Start with base query
        $query = User::query();

        // Get entrepreneurs created by current admin
        $entrepreneurIds = User::where('created_by', loginUser()->id)
            ->whereHas('roles', function ($q) {
                $q->where('name', ROLE_ENTREPRENEUR);
            })
            ->pluck('id');

        // Include entrepreneurs and users assigned to their projects
        $query->where(function ($q) use ($entrepreneurIds) {
            // Include entrepreneurs
            $q->whereIn('id', $entrepreneurIds)
                // Include users assigned to projects owned by these entrepreneurs
                ->orWhereHas('assignedUserProjects', function ($q2) use ($entrepreneurIds) {
                    $q2->whereHas('project', function ($q3) use ($entrepreneurIds) {
                        $q3->whereIn('user_id', $entrepreneurIds)
                            ->orwhere('user_id', loginUser()->id);
                    });
                });
        });

        // Eager load roles and filtered assignedUserProjects
        $query->with([
            'roles',
            'assignedUserProjects' => function ($q) use ($entrepreneurIds) {
                $q->whereHas('project', function ($q2) use ($entrepreneurIds) {
                    $q2->whereIn('user_id', $entrepreneurIds)
                        ->orwhere('user_id', loginUser()->id);;
                });
            }
        ]);

        // Apply Spatie role filter
        if (!empty($input['filters']['roles'])) {
            $query->whereHas('roles', function ($q) use ($input) {
                $q->where('name', $input['filters']['roles']);
            });
        }

        $users = $query
            ->filterRecords($input)
            ->queryCrud($input);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Users retrieved successfully without project filter.',
            'data' => AdminUserResource::collection($users)
        ]);
    }

    public function indexWithProject(Request $request)
    {
        $input = $request->all();
        $projectId = $input['filters']['project_id'] ?? null;

        // Start with base query
        $query = User::query();

        // Get entrepreneurs created by current admin
        $entrepreneurIds = User::where('created_by', loginUser()->id)
            ->whereHas('roles', function ($q) {
                $q->where('name', ROLE_ENTREPRENEUR);
            })
            ->pluck('id');

        if ($projectId) {
            // Verify the project belongs to one of our entrepreneurs
            $project = Project::where('id', $projectId)
                ->whereIn('user_id', $entrepreneurIds)
                ->first();

            if (!$project) {
                return response()->json([
                    'status' => 'error',
                    'status_code' => 403,
                    'message' => 'Unauthorized access to this project.',
                ], 403);
            }

            // Include project owner and users assigned to this project
            $query->where(function ($q) use ($projectId, $entrepreneurIds) {
                // Include entrepreneurs
                $q->whereIn('id', $entrepreneurIds)
                    // Include users assigned to this specific project
                    ->orWhereHas('assignedUserProjects', function ($q2) use ($projectId) {
                        $q2->where('project_id', $projectId);
                    });
            });
        } else {
            // Include entrepreneurs and users assigned to their projects
            $query->where(function ($q) use ($entrepreneurIds) {
                // Include entrepreneurs
                $q->whereIn('id', $entrepreneurIds)
                    // Include users assigned to projects owned by these entrepreneurs
                    ->orWhereHas('assignedUserProjects', function ($q2) use ($entrepreneurIds) {
                        $q2->whereHas('project', function ($q3) use ($entrepreneurIds) {
                            $q3->whereIn('user_id', $entrepreneurIds);
                        });
                    });
            });
        }

        // Eager load filtered assignedUserProjects
        $query->with([
            'assignedUserProjects' => function ($q) use ($entrepreneurIds) {
                $q->whereHas('project', function ($q2) use ($entrepreneurIds) {
                    $q2->whereIn('user_id', $entrepreneurIds);
                });
            }
        ]);

        // Apply Spatie role filter
        if (!empty($input['filters']['roles'])) {
            $query->whereHas('roles', function ($q) use ($input) {
                $q->where('name', $input['filters']['roles']);
            });
        }

        $users = $query
            ->filterRecords($input)
            ->queryCrud($input);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Users retrieved successfully for the specified project.',
            'data' => AdminUserResource::collection($users)
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
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $role = Role::query()->where('id', $validated['role_id'])->first();

        // Check if user exists by phone number
        $existingUser = User::where('phone_number', $validated['phone_number'])->first();


        if ($existingUser) {
            // If user exists and is an investor, assign projects
            if ( isset($validated['project_ids'])) {
                foreach ($validated['project_ids'] as $projectId) {
                    AssignedUserProject::updateOrCreate(
                        [
                            'user_id' => $existingUser->id,
                            'project_id' => $projectId,
                        ],
                        [
                            'invitation_status' => 'pending',
                            'role' => $role->name
                        ]
                    );
                }

                // Send SMS notification for existing user
                $projectNames = Project::whereIn('id', $validated['project_ids'])->pluck('name')->join(', ');
                $smsMessage = 'Dear ' . $existingUser->name . ', you have been invited to join the following projects: ' . $projectNames . '. Please log in to your account to accept or reject these invitations.';
                $this->sendSmsNotification($existingUser, $smsMessage);

                // Send email notification
//                if (!empty($existingUser->email)) {
//                    Mail::to($existingUser->email)->send(new ProjectInvitationMail($existingUser, $validated['project_ids']));
//                }

                // Log the invitation
                InvitationLog::create([
                    'user_id' => $existingUser->id,
                    'phone_number' => $existingUser->phone_number,
                    'message' => $smsMessage,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Projects assigned to existing user successfully',
                    'data' => new AdminUserResource($existingUser)
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'User already exists'
            ], 422);
        }

        // Create new user
        $user = new User();
        $user->name = $validated['name'];
        $user->phone_number = $validated['phone_number'];
        $user->country_code = $validated['country_code'];

        if ($role->name != "investor") {
            $user->created_by = Auth::id();
        }

        if ($request->hasFile('profile_photo')) {
            $user->profile_photo = $this->uploadProfilePhoto($request->file('profile_photo'));
        }

        $user->syncRoles($role);
        $user->save();

        // Assign projects if provided
        if (isset($validated['project_ids'])) {
            foreach ($validated['project_ids'] as $projectId) {
                AssignedUserProject::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'project_id' => $projectId,
                    ],
                    [
                        'invitation_status' => 'pending',
                        'role' => $role->name
                    ]
                );
            }
        }

        // Send SMS for new user with signup URL
        $signupUrl = config('app.vue_url') . 'signup?role=' . $role->name . '&country_code=' . $user->country_code . '&phone=' . $user->phone_number;
        $smsMessage = 'Dear ' . $user->name . ', welcome to our platform! Please complete your registration by visiting: ' . $signupUrl;

        $this->sendSmsNotification($user, $smsMessage);

        // Log the invitation
        InvitationLog::create([
            'user_id' => $user->id,
            'phone_number' => $user->country_code . $user->phone_number,
            'message' => $smsMessage,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => new AdminUserResource($user)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['projects', 'assignedUserProjects'])->find($id);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'User received successfully.',
            'data' => AdminUserResource::make($user)
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
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $role = Role::query()->where('id', $validated['role_id'])->first();

        // Update user details
        $user->name = $validated['name'];
        $user->phone_number = $validated['phone_number'];

        if ($request->hasFile('profile_photo')) {
            $user->profile_photo = $this->uploadProfilePhoto($request->file('profile_photo'));
        }

        $user->save();

        // Update project assignments
        if (isset($validated['project_ids'])) {
            // Delete existing assignments
            AssignedUserProject::where('user_id', $user->id)->delete();

            // Create new assignments
            foreach ($validated['project_ids'] as $projectId) {
                AssignedUserProject::create([
                    'user_id' => $user->id,
                    'project_id' => $projectId,
                    'invitation_status' => 'pending',
                    'role' => $role->name
                ]);
            }

            // Send SMS notification for project assignments
            $projectNames = Project::whereIn('id', $validated['project_ids'])->pluck('name')->join(', ');
            $smsMessage = 'Dear ' . $user->name . ', you have been invited to join the following projects: ' . $projectNames . '. Please log in to your account to accept or reject these invitations.';
            $this->sendSmsNotification($user, $smsMessage);

            // Send email notification if user has email
            if (!empty($user->email)) {
                Mail::to($user->email)->send(new ProjectInvitationMail($user, $validated['project_ids']));
            }

            // Log the invitation
            InvitationLog::create([
                'user_id' => $user->id,
                'phone_number' => $user->country_code . $user->phone_number,
                'message' => $smsMessage,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        //Reassign project to entrepreneur
        $projectsToReassign = $request->get('reassign_projects');
        if (sizeof($projectsToReassign) > 0) {
            $projects = Project::whereIn('id', $projectsToReassign)->get();

            foreach ($projects as $project) {
                AssetType::where('id', $project->asset_type_id)->update(['user_id' => $validated['user_id']]);
            }
            Project::whereIn('id', $projectsToReassign)->update(['user_id' => $validated['user_id']]);
        }

        $role = Role::query()->where('id', $validated['role_id'])->first();

        if (!blank($role)) {
            $user->syncRoles($role);
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => new AdminUserResource($user)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteUserRequest $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'User not found.',
            ]);
        }

        // If user is entrepreneur, reassign their projects
        if ($request->user_id) {
            $this->assignProjectToEntrepreneur($request->user_id, $id);
        }

        // Detach roles
        $user->roles()->detach();

        // Force delete (hard delete)
        $user->forceDelete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'User deleted permanently.',
        ]);
    }


    public function assignProjectToEntrepreneur($newEntrepreneurId, $entrepreneurId)
    {
        Project::where('user_id', $entrepreneurId)
            ->update([
                'user_id' => $newEntrepreneurId
            ]);
    }

    public function userRoles()
    {
        $roles = Role::whereNotIn('name', [ROLE_SUPER_ADMIN, ROLE_ADMIN])
            ->get();
        if ($roles->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'No roles found.'
            ], 404);
        }
        return RoleResource::collection($roles);
    }

    public function userDropdownByRoles(Request $request)
    {
        $role = ($request->role) ?? '';
        $userId = $request->user_id;

        $users = User::where('id', '!=', $userId)
            ->where('created_by', loginUser()->id)
            ->whereHas('roles', function ($rolesQuery) use ($role) {
                if ($role) {
                    $rolesQuery->where('name', $role);
                }
            })
            ->get();

        return UserDropdownResource::collection($users);
    }

    /**
     * Send SMS notification to user
     */
    private function sendSmsNotification($user, $message)
    {
        log::info('user', [$user, $message]);
        $response = Http::asForm()->post('https://rest.nexmo.com/sms/json', [
            'api_key'    => env('VONAGE_API_KEY'),
            'api_secret' => env('VONAGE_API_SECRET'),
            'to'         => $user->country_code . $user->phone_number,
            'from'       => env('VONAGE_FROM'),
            'text'       => $message
        ]);
        log::info('$response', [$response->body()]);


        if ($response->failed()) {
            throw new \Exception('Failed to send SMS.');
        }

        return true;
    }

    public function uploadProfilePhoto(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'User not found'
            ], 404);
        }
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                $oldPhotoPath = public_path('storage/' . $user->profile_photo);
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }
            $file = $request->file('profile_photo');

            $extension = $file->getClientOriginalExtension();

            // ✅ Generate filename like your image upload logic
            $filename = 'profile_' . now()->format('Ymd_His') . '_' . uniqid() . '.' . $extension;
            $path = 'profile_images/' . $filename;
            // Store the file
            $file->storeAs('profile_images', $filename, 'public');

            // Store new profile photo
            $user->profile_photo = $path;
            $user->save();

            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Profile photo uploaded successfully',
                'data' => [
                    'profile_photo_url' => asset(Storage::url($path)) ?? null
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'status_code' => 400,
            'message' => 'No profile photo uploaded'
        ], 400);
    }
}
