<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetTypeRequest;
use App\Http\Requests\UpdateAssetTypeRequest;
use App\Http\Resources\AssetTypeResource;
use App\Models\AssetType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = AssetType::query();

        if (loginUser()->hasRole(ROLE_ADMIN)) {
            $query->where('user_id',loginUser()->id );
        }else{
            $user= User::find(loginUser()->id);
            $query->where('user_id',$user->created_by );
        }

        $assetTypes = $query->get();
        if ($assetTypes->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'status_code' => 404,
                'message' => 'No asset types found for this user.'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Asset types retrieved successfully.',
            'data' => AssetTypeResource::collection($assetTypes)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreAssetTypeRequest $request
     * @return
     */
    public function store(StoreAssetTypeRequest $request)
    {
        $validated = $request->validated();

        $assetType = AssetType::create($validated + ['user_id' => loginUser()->id]);
        if (!$assetType) {
            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'Failed to create asset type.'
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Asset type created successfully.',
            'data' => new AssetTypeResource($assetType)
        ], 201);
    }

    /**
     * Display the specified resource.
     * @param AssetType $assetType
     * @return
     */
    public function show(AssetType $assetType)
    {
        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Asset type created successfully.',
            'data' => new AssetTypeResource($assetType)
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateAssetTypeRequest $request
     * @param AssetType $assetType
     * @return
     */
    public function update(UpdateAssetTypeRequest $request, AssetType $assetType)
    {
        // Validate the incoming request
        $validated = $request->validated();

        // Update the asset type with the validated data
        $assetType->update($validated);
        if (!$assetType) {
            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'Failed to update asset type.'
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Asset type updated successfully.',
            'data' => new AssetTypeResource($assetType)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param AssetType $assetType
     * @return
     */
    public function destroy(AssetType $assetType)
    {
        if($assetType->projects->count() > 0){
            return response()->json([
                'status' => 'error',
                'status_code' => 422,
                'message' => 'You can not delete Asset type which is used in projects.',
            ], 422);
        }
        $assetType->delete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Asset type deleted successfully.',
        ], 200);
    }
}
