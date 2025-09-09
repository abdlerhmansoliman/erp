<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Hash;
use Google_Client;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * Create a new class instance.
     */
    protected $authRepository;
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository=$authRepository;
    }
    public function register(array $data){
        $user=$this->authRepository->create($data);
        $user->assignRole('employee');
        $token=$user->createToken('auth_token')->plainTextToken;
        return  response()->json([
            'user'=>$user,
            'token'=>$token
        ]);
    }
    public function login(array $data){
        $user=$this->authRepository->findByEmail($data['email']);
        if(!$user || ! Hash::check($data['password'],$user->password)){
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }
        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'message' => 'Login successful'
        ]);
    }
    public function logout($user){
        $user->currentAccessToken()->delete();
        
    }
    public function loginWithGoogle(string $idToken){
        $client=new Google_Client(['client_id'=>env('GOOGLE_CLIENT_ID')]);

        $payload=$client->verifyIdToken($idToken);

        if(! $payload){
            return null;
        }
        $user=$this->authRepository->firstOrCreateByEmail(
            $payload['email'],
            [
                'name' => $payload['name'] ?? 'No Name',
                'password'=>bcrypt(Str::random(16))
            ]
            );
            $token=$user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token
            ];
    }

}
