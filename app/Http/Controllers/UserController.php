<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Mail\SignupFreelanceRequest;
use App\Mail\UnVerfiedFreelanceRequest;
use App\Mail\VerfiedFreelanceRequest;
use App\Models\User;
use App\Traits\UploadFile;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    use UploadFile;

    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $users = User::paginate(10);
        return UserResource::collection($users);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return UserResource
     */
    public function update(Request $request, User $user)
    {
        $current_user = auth()->user();
        // Company User
        if ($current_user->hasRole('company')) {
            request()->validate([
                'name' => 'string|between:2,100',
                'email' => 'string|email|max:100|unique:users',
                'password' => 'required|string|confirmed|min:6',
            ]);

            $user->update([
               'name' => $request->name,
               'email' => $request->email,
               'password' => bcrypt($request->password),
            ]);

            return new UserResource($user);
        }

        // Freelance User
        request()->validate([
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'document_freelance' => 'required|mimes:pdf|max:1000',
            'filter_video' => 'required|mimes:mp4,mov,ogg,qt|max:20000',
            'instagram_account' => 'required|string|min:2',
            'phone' => 'required|min:10|numeric',
        ]);

        $this->removeS3File($request->document_freelance, 'pdf');
        $this->removeS3File($request->filter_video, 'videos');

        $pdf_file = $this->storeToS3('pdf', $request->document_freelance);
        $video_file = $this->storeToS3('videos', $request->filter_video);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'document_freelance' => $this->getS3Url($pdf_file),
            'filter_video' => $this->getS3Url($video_file),
            'instagram_account' => $request->instagram_account,
            'phone' => $request->phone,
        ]);

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return UserResource
     * @throws Exception
     */
    public function destroy(User $user)
    {
        $user->delete();

        return new UserResource($user);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function toggleVerifiedUser(User $user)
    {
        $user->verified = !$user->verified;
        $user->save();

        if ($user->verified) {
            Mail::to($user->email)->queue(new VerfiedFreelanceRequest($user));
        } else {
            Mail::to($user->email)->queue(new UnVerfiedFreelanceRequest($user));
        }
        return response()->json($user);
    }
}
