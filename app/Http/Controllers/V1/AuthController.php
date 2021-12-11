<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
     * @response {
     *  "id_user": 4,
     * }
     */
    public function userAdd(Request $request)
    {
        $input = $request->validate([
            'username' => 'required|unique:users,username',
            'name' => 'required'
        ]);

        $user = User::create([
            'username' => $input['username'],
            'name' => $input['name'],
            'password' => config('nobi.default_password')
        ]);

        return response()->json(['id_user' => $user->id]);
    }
}
