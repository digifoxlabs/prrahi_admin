<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\SalesPerson;
use App\Models\Distributor;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function salesLogin(Request $request)
    {
        $request->validate([
            'login_id' => 'required',
            'password' => 'required',
        ]);

        $user = SalesPerson::where('login_id', $request->login_id)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('sales-mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'role' => 'sales',
            'user' => $user,
        ]);
    }

    public function distributorLogin(Request $request)
    {
        $request->validate([
            'login_id' => 'required',
            'password' => 'required',
        ]);

        $user = Distributor::where('login_id', $request->login_id)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('distributor-mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'role' => 'distributor',
            'user' => $user,
        ]);
    }

        /**
     * Logout Sales (Mobile)
     */
    public function salesLogout(Request $request)
    {
        $user = auth('sales_api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Sanctum may return TransientToken (no delete method) for session auth.
        $token = $user->currentAccessToken();
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        } else {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => 'Sales user logged out successfully',
        ]);
    }

    /**
     * Logout Distributor (Mobile)
     */
    public function distributorLogout(Request $request)
    {
        $user = auth('distributor_api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Sanctum may return TransientToken (no delete method) for session auth.
        $token = $user->currentAccessToken();
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        } else {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => 'Distributor logged out successfully',
        ]);
    }
}
