<?php

namespace App\Http\Controllers;

use App\Http\Resources\OfferResource;
use App\Http\Resources\UserResource;
use App\Models\Offer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Traits\UploadFile;

/**
 * Class OfferController
 * @package App\Http\Controllers
 */
class OfferController extends Controller
{
    use UploadFile;

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $offers = Offer::paginate(10);
        return OfferResource::collection($offers);
    }

    /**
     * @param Offer $offer
     * @return UserResource
     */
    public function apply(Offer $offer)
    {
        $user = auth()->user();

        if ($user->applies->contains($offer)) {
            $user->applies()->detach($offer->id);

            return new UserResource($user->load('applies'));
        }

        $user->applies()->attach($offer->id);

        return new UserResource($user->load('applies'));
    }

    /**
     * Create a new resource
     *
     * @param Request $request
     * @return OfferResource
     */
    public function create(Request $request)
    {
        request()->validate([
            'title' => 'required|string|between:5,100',
            'description' => 'required|string|between:100,5000',
            'company_logo' => 'mimes:jpeg,png,|max:1500'
        ]);

        if ($request->company_logo) {
            $logo_path = $this->storeToS3('offers', $request->company_logo);
        }

        $offer = Offer::create([
            'title' => $request->title,
            'author' => auth()->user()->name,
            'description' => $request->description,
            'company_logo' => $request->company_logo ? $this->getS3Url($logo_path) : null,
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
        return new OfferResource($offer->load('apply'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Offer $offer
     * @return OfferResource
     */
    public function update(Request $request, Offer $offer)
    {
        $this->removeS3File($offer->company_logo, 'offers');

        if ($request->company_logo) {
            $logo_path = $this->storeToS3('offers', $request->company_logo);
        }

        $offer->update([
            'title' => $request->title,
            'author' => auth()->user()->name,
            'description' => $request->description,
            'company_logo' => $request->company_logo ? $this->getS3Url($logo_path) : null,
            'user_id' => auth()->user()['id'],
        ]);

        return new OfferResource($offer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Offer $offer
     * @return OfferResource
     * @throws Exception
     */
    public function destroy(Offer $offer)
    {
        if ($offer->company_logo) {
            $this->removeS3File($offer->company_logo, 'offers');
        }

        $offer->delete();

        return new OfferResource($offer);
    }
}
