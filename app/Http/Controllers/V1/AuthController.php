<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Models\User;

class AuthController extends Controller
{
    /**
     * User Add
     *
     * This endpoint allows you to add a users to the database. It's a really useful endpoint,
     * <aside class="notice">Username should be unique :)</aside>
     * <aside class="notice">There are settings for default password</aside>
     *
     * @bodyParam username string required The username of the user.
     * @bodyParam name string required The name of the user.
     *
     * @response scenario="Success"{
     *  "id_user": 4,
     * }
     */
    public function userAdd(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'name' => 'required'
        ]);

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'password' => Hash::make(config('nobi.default_password'))
        ]);

        return response()->json([
            'id_user' => $user->id
        ]);
    }

    /**
     * User Authenticate
     *
     * This endpoint allows you to authenticate as user.
     * <aside class="notice">The default password are 'nobi'</aside>
     *
     * @bodyParam username string required The username of the user.
     * @bodyParam password string required The password of the user.
     *
     * @response scenario="Success"{
     *  "token": 3|lObhIwULaaDGXCkK86wVfb9Kmcgdyx7BG19VtrBY,
     * }
     */
    public function userAuthenticate(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'authenticate' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken($request->username)->plainTextToken
        ]);
    }
}
