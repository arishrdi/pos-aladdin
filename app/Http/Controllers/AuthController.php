<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(
 *     title="Auth API",
 *     version="1.0.0",
 *     description="API untuk manajemen autentikasi dan pengguna"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */

class AuthController extends Controller
{
    use ApiResponse;
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Login pengguna",
     *     description="Mendapatkan token akses",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/User"),
     *                 @OA\Property(property="token", type="string", example="1|abcdefghijklmnopqrstuvwxyz")
     *             ),
     *             @OA\Property(property="message", type="string", example="Login success")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->errorResponse('Email or password is incorrect');
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Revoke all existing tokens
        // $user->tokens()->delete();

        $data = [
            'user' => $user->load(['outlet', 'lastShift']),
            'token' => $user->createToken('auth_token')->plainTextToken
        ];

        return $this->successResponse($data, 'Login success');
    }

    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if (Auth::attempt($credentials, $request->remember)) {
    //         $user = Auth::user();
    //         $token = $user->createToken('auth_token')->plainTextToken;
            
    //         // Check if user has a valid role
    //         $validRoles = ['admin', 'manajer', 'kasir'];
            
    //         if (!in_array($user->role, $validRoles)) {
    //             Auth::logout();
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Anda tidak memiliki akses yang diperlukan',
    //             ], 403);
    //         }
            
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Login berhasil',
    //             'data' => [
    //                 'token' => $token,
    //                 'user' => $user,
    //                 'role' => $user->role,
    //                 'outlet' => $user->outlet,
    //                 'shift' => $user->lastShift,
    //             ]
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Email atau password salah',
    //     ], 401);
    // }

    /**
     * Get authenticated user info
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function me(Request $request)
    {
        try {

            $user = $request->user()->load(['outlet', 'lastShift']);

            return $this->successResponse($user, 'User profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user profile: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Logout user (revoke token)
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Successfully logged out');
    }

    /**
     * Register user
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,kasir,supervisor',
            'outlet_id' => 'nullable|exists:outlets,id',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);

        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'outlet_id' => $request->outlet_id,
            ]);

            if ($request->outlet_id) {
                Shift::create([
                    'user_id' => $user->id,
                    'outlet_id' => $request->outlet_id,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                ]);
            }
            DB::commit();
            return $this->successResponse($user, 'User created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
           return $this->errorResponse('Terjadi kesalahan saat menyimpan data. Silakan periksa kembali input Anda.');
        }
    }

    public function getAllUsers($outletId)
    {
        try {
            $users = User::with('outlet', 'lastShift')->where('outlet_id', $outletId)->get();
            return $this->successResponse($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|in:admin,kasir,supervisor',
            'outlet_id' => 'nullable|exists:outlets,id',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);

        try {
            DB::beginTransaction();
            if ($request->filled('password')) {
                $request->merge(['password' => Hash::make($request->password)]);
            }

            $user->update($request->all());
            $user->shifts()->update([
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ]);
            DB::commit();
            return $this->successResponse($user, 'User updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
            'password' => 'nullable|string|min:6',
        ]);

        try {
            if ($request->has('password')) {
                $request->merge(['password' => Hash::make($request->password)]);
            }

            $request->user()->update($request->all());
            return $this->successResponse($request->user(), 'Profile updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return $this->successResponse(null, 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function validateToken(Request $request)
    {
        return $this->successResponse($request->user(), 'Token is valid');
    }
}
