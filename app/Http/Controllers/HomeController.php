<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Auth;
use Firebase\JWT\JWT;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("auth");
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("home");
    }

    public function generateZendesktoken()
    {
        $user = Auth::user();
        $carbon = \Carbon\Carbon::now()->timestamp;

        $payload = [
            "name" => $user->name,
            "email" => $user->email,
            "iat" => $carbon,
            "external_id" => "user-" . $user->id,
        ];

        $token = JWT::encode($payload, "7EAD23D4B97270A42FC02EDEFADE37FE91C111F102B8001A68EE0EB9DC7E15EB");
        return response()->json($token);
    }
}
