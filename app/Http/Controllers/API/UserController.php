<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use PasswordValidationRules;

    /**
     * @param Request $request
     * @return mixed
     */
    public function fetch(Request $request)
    {
        return ResponseFormatter::success($request->user(),'Data profile user berhasil diambil');
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            // cek email dan password
            $credentials = request(['email', 'password']);
            if(!Auth::attempt($credentials)) return ResponseFormatter::error([
                'message' => 'Unauthorized'
            ], 'Authentication Failed', 500);

            // cek password hash
            $user = User::where('email', $request->email)->first();
            if(!Hash::check($request->password, $user->password, [])) throw new Exception('Invalid Credentials');

            //membuat token
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Login success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }
    public function register(Request $request): JsonResponse
    {
            $validator =  Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules()
            ]);

            if($validator->fails()) {
                return ResponseFormatter::error(['error' => $validator->errors()], 'Registration Failed', 401);
            }else {
                User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'address' => $request->address,
                    'houseNumber' => $request->houseNumber,
                    'phoneNumber' => $request->phoneNumber,
                    'city' => $request->city,
                    'password' => Hash::make($request->password)
                ]);

                $user = User::where('email', $request->email)->first();
                $tokenResult = $user->createToken('authToken')->plainTextToken;
                return ResponseFormatter::success([
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user,
                    'message' => 'Registration success'
                ]);
            }
//        try {
//            $validator = $request->validate([
//                'name' => ['required', 'string', 'max:255'],
//                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
//                'password' => $this->passwordRules()
//            ]);
//
//            User::create([
//                'name' => $request->name,
//                'email' => $request->email,
//                'address' => $request->address,
//                'houseNumber' => $request->houseNumber,
//                'phoneNumber' => $request->phoneNumber,
//                'city' => $request->city,
//                'password' => Hash::make($request->password)
//            ]);
//
//            $user = User::where('email', $request->email)->first();
//            $tokenResult = $user->createToken('authToken')->plainTextToken;
//            return ResponseFormatter::success([
//                'access_token' => $tokenResult,
//                'token_type' => 'Bearer',
//                'user' => $user
//            ]);
//
//        }catch (Exception $error) {
//            return ResponseFormatter::error([
//                'message' => 'Something went wrong',
//                'error' => $validator->errors()
//            ], 'Authentication Failed', 500);
//        }
    }

    public function logout(Request $request): JsonResponse
    {
        //mengambil token dari user
        $token = $request->user()->currentAccessToken()->delete;
        return ResponseFormatter::success($token, 'Token revoked');
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->all();
        $user = Auth::user();
        $user->update($data);
        return ResponseFormatter::success($user, 'Profile Updated');
    }

    public function updatePhoto(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|max:2048'
        ]);

        if($validator->fails())
        {
            return ResponseFormatter::error(['error' => $validator->errors], 'Update foto fails', 401);
        }

        if ($request->file('file')) {

            $file = $request->file->store('assets/user', 'public');

            //store your file into database
            $user = Auth::user();
            $user->profile_photo_path = $file;
            $user->update();

            return ResponseFormatter::success([$file],'File successfully uploaded');
        }
    }
}
