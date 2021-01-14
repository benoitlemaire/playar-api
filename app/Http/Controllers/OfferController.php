<?php

namespace App\Http\Controllers;

use App\Http\Resources\OfferResource;
use App\Models\Offer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Traits\UploadFile;

class OfferController extends Controller
{
    use UploadFile;

    /**
     * Create a new OfferController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return OfferResource::collection(Offer::all());
    }

    /**
     * Create a new resource
     *
     * @return OfferResource
     */
    public function create(Request $request)
    {
        request()->validate([
            'title' => 'required|string|between:5,100',
            'description' => 'required|string|between:100,5000',
            'company_logo' => 'mimes:jpeg,png,|max:1500'
        ]);

        $logo_path = $this->storeToS3('offers', $request->company_logo);

        $offer = Offer::updateOrCreate([
            'title' => $request->title,
            'description' => $request->description,
            'company_logo' => $this->getS3Url($logo_path),
            'user_id' => auth()->user()['id'],
        ]);

        return new OfferResource($offer);
    }

    /**
     * Display the specified resource.
     *
     * @param Offer $offer
     * @return OfferResource
     */
    public function show(Offer $offer)
    {
        return new OfferResource($offer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Offer $offer
     * @return OfferResource
     */
    public function update(Request $request, Offer $offer)
    {

        if ($request->company_logo) {
            $this->removeS3File($offer->company_logo, 'offers');
            $logo_path = $this->storeToS3('offers', $request->company_logo);

            $offer->update([
                'title' => $request->title,
                'description' => $request->description,
                'company_logo' => $this->getS3Url($logo_path),
                'user_id' => auth()->user()['id'],
            ]);
        }

        $offer->update([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->user()['id'],
        ]);

        return new OfferResource($offer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Offer $offer
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Offer $offer)
    {
        $this->removeS3File($offer->company_logo, 'offers');
        $offer->delete();

        return response()->json([],200);
    }
}
