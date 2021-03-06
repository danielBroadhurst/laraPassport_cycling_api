<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Http\Resources\User as ResourcesUser;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    $http = new \GuzzleHttp\Client;
    try {
      $response = $http->post(config('services.passport.login_endpoint'), [
        'form_params' => [
          'grant_type' => 'password',
          'client_id' => config('services.passport.client_id'),
          'client_secret' => config('services.passport.client_secret'),
          'username' => $request->email,
          'password' => $request->password
        ]
      ]);
      return json_decode($response->getBody(), true);
    } catch (\GuzzleHttp\Exception\BadResponseException $e) {
      if ($e->getCode() === 400) {
        return response()->json('Invalid Request. Please enter a username or a password.', $e->getCode());
      } else if ($e->getCode() === 401) {
        return response()->json('Your credentials are incorrect. Please try again.', $e->getCode());
      }
      return response()->json('Something went wrong on the server.', $e->getCode());
    }
  }

  public function register(Request $request)
  {
    $request->validate([
      'firstName' => 'required|string|max:255',
      'lastName' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string:min:6'
    ]);

    $user = User::create([
      'first_name' => $request->firstName,
      'last_name' => $request->lastName,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    if ($user) {
      return response()->json(new ResourcesUser($user), 201);
    } else {
      return response()->json('Something went wrong on the server.', 400);
    }
  }

  public function logout()
  {
    auth()->user()->tokens->each(function ($token, $key) {
      $token->delete();
    });

    return response()->json('Logged out successfully', 200);
  }

  public function deleteAccount(User $user)
  {
    $loggedUser = auth()->user();
    $user = User::where('id', $user->id)->first();
    if ($loggedUser->id === $user->id) {
      $user->delete();
    } else {
      $message = array(
        'message' => 'Unauthorised to delete that account.'
      );
      return response()->json($message, 400);
    }
    $message = array(
      'message' => 'Deleted account successfully.'
    );
    return response()->json($message, 200);
  }
}
