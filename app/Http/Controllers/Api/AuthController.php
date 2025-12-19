<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuthToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Login with username/email + password
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::with(['role', 'department', 'subDepartment', 'designation'])
            ->where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // ❌ Block inactive/disabled users
        if (!$user->status) {
            return response()->json(['message' => 'Your account is inactive, please contact admin'], 403);
        }

        // Invalidate old tokens
        AuthToken::where('user_id', $user->id)->update(['is_valid' => false]);

        // Generate token
        $token = hash('sha256', Str::random(60) . $user->id . now());

        $authToken = AuthToken::create([
            'user_id'      => $user->id,
            'token'        => $token,
            'generated_at' => Carbon::now(),
            'valid_until'  => Carbon::now()->addDays(7),
            'is_valid'     => true,
        ]);

        return response()->json([
            'message' => 'Login successful',
            'status'  => true,
            'user'    => $this->formatUser($user),
            'token'   => $authToken->token,
            'expires' => $authToken->valid_until,
        ]);
    }

    /**
     * Logout (invalidate token)
     */
    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if ($token) {
            AuthToken::where('token', $token)->update(['is_valid' => false]);
        }

        return response()->json([
            'message' => 'Logged out successfully',
            'status'  => true
        ]);
    }

    /**
     * Get current logged-in user (via token)
     */
    public function me(Request $request)
    {
        $token = $request->bearerToken();

        $authToken = AuthToken::with('user.role', 'user.department', 'user.subDepartment', 'user.designation')
            ->where('token', $token)
            ->where('is_valid', true)
            ->where('valid_until', '>=', Carbon::now())
            ->first();

        if (!$authToken) {
            return response()->json([
                'message' => 'Invalid or expired token',
                'status'  => false
            ], 401);
        }

        return response()->json([
            'status' => true,
            'user'   => $this->formatUser($authToken->user),
        ]);
    }

    /**
     * Format user for API response
     */
    private function formatUser(User $user): array
    {
        return [
            'id'               => $user->id,
            'name'             => $user->name,
            'username'         => $user->username,
            'email'            => $user->email,
            'phone_no'         => $user->phone_no,
            'gender'           => $user->gender,

            // ✅ Return status as true/false
            'status'           => (bool) $user->status,

            'district'         => $user->district,
            'profile_photo'    => $user->profile_photo_url,

            // Reference fields with both ID and Name
            'role' => [
                'id'   => $user->role_id,
                'name' => optional($user->role)->name,
            ],
            'department' => [
                'id'   => $user->department_id,
                'name' => optional($user->department)->name,
            ],
            'sub_department' => [
                'id'   => $user->sub_department_id,
                'name' => optional($user->subDepartment)->name,
            ],
            'designation' => [
                'id'   => $user->designation_id,
                'name' => optional($user->designation)->name,
            ],

            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
