<?php

namespace App\Http\Controllers;
use App\Mail\SignupCompanyRequest;
use App\Mail\SignupFreelanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Validator;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

  //      $freelance = User::create(array_merge(
//            $validator->validated(),
//            ['password' => bcrypt($request->password)]
//        ));
//
//        $freelance->attachRole('freelance');
//
//        Mail::to($freelance->email)->queue(new SignupFreelanceRequest($freelance));
//        return response()->json([
//            'message' => 'Freelance successfully registered',
//            'freelance' => $freelance
//        ],201);
    }

    public function saveImage(Request $request) {
        // Validate (size is in KB)
        $request->validate([
            'photo' => 'required|file|image|size:1024|dimensions:max_width=500,max_height=500',
        ]);

        // Read file contents...
        $contents = file_get_contents($request->photo->path());

        // ...or just move it somewhere else (eg: local `storage` directory or S3)
        $newPath = $request->photo->store('photos', 's3');
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
        return response()->json(auth()->user());
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
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

}
