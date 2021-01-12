<?php

namespace App\Http\Controllers;
use App\Http\Resources\UserResource;
use App\Mail\SignupCompanyRequest;
use App\Mail\SignupFreelanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Validator;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'registerFreelance']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a Company.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerCompany(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $company = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        $company->attachRole('company');

        Mail::to($company->email)->queue(new SignupCompanyRequest($company));
        return response()->json([
            'message' => 'Company creation account successful',
            'company' => $company
        ],201);
    }

    /**
     * Register a Freelance.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerFreelance(Request $request) {
        request()->validate([
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'document_freelance' => 'required|mimes:pdf|max:1000',
            'instagram_account' => 'required|string|min:2',
            'filter_video' => 'required|mimes:mp4,mov,ogg,qt | max:20000',
            'phone' => 'required|min:10|numeric',
        ]);

        $pdf_file = Storage::disk('s3')->put('pdf', $request->document_freelance, 'public' );
        $video_file = Storage::disk('s3')->put('videos', $request->filter_video, 'public' );

        $freelance = User::create([
                   'email' => $request->email,
                   'password' => bcrypt($request->password),
                   'name' => $request->name,
                   'phone' => $request->phone,
                   'document_freelance' => Storage::disk('s3')->url($pdf_file),
                   'filter_video' => Storage::disk('s3')->url($video_file),
                   'instagram_account' => $request->instagram_account
               ]);

        $freelance->attachRole('freelance');

        Mail::to($freelance->email)->queue(new SignupFreelanceRequest($freelance));
        return response()->json([
            'message' => 'Freelance successfully registered',
            'freelance' => $freelance
        ],201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me() {
        return new UserResource(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 43800,
            'user' => auth()->user()
        ]);
    }

}
