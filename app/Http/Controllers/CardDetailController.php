<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCardDetailRequest;
use App\Http\Requests\UpdateCardDetailRequest;
use App\Models\CardDetail;
use App\Http\Resources\CardDetailResource;
use Carbon\Carbon;

class CardDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cardDetails = CardDetail::where('user_id', loginUser()->id)->get();
        // if ($cardDetails->isEmpty()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'status_code' => 404,
        //         'message' => 'No card details found.',
        //     ], 404);
        // }
        return CardDetailResource::collection($cardDetails);
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
    public function store(StoreCardDetailRequest $request)
    {
        $year = substr(Carbon::now()->year, 0, 2) . $request->expiry_year;

        $cardDetail = CardDetail::create(
            [
                'user_id' => loginUser()->id,
                'expiry_year' => $year,
            ] + $request->validated()
        );
        if (!$cardDetail) {
            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => 'Failed to create card detail.',
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Card detail created successfully.',
            'data' => CardDetailResource::make($cardDetail)
        ]);
    }

    /**
     * Display the user card details.
     */
    public function show(CardDetail $cardDetail)
    {
        return CardDetailResource::make($cardDetail);
    }

    /**
     * Update the user card detail.
     */
    public function update(UpdateCardDetailRequest $request, CardDetail $cardDetail)
    {
        $year = substr(Carbon::now()->year, 0, 2) . $request->expiry_year;
        
        $request = $request->validated();

        $request['expiry_year'] = $year;

        $cardDetail->fill($request)->save();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Card detail updated successfully.',
            'data' => CardDetailResource::make($cardDetail)
        ]);
    }

    public function setActiveCardDetail(CardDetail $cardDetail) {
        //first update user all card is active = 0 
        CardDetail::where('user_id', loginuser()->id)
                    ->update([
                        'is_active' => 0
                    ]);
        

        //set active card details
        $cardDetail->update([
                        'is_active' => 1
                    ]);
                
        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Card detail updated successfully.',
            'data' => CardDetailResource::make($cardDetail)
        ]);
    }
}
