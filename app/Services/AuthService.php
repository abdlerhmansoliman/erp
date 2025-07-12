<?php

namespace App\Services;

use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Create a new class instance.
     */
    protected $authRepository;
    public function __construct(AuthRepositoryInterface $authRepository)
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
            return response()->json(['message'=>'invalid email or password'],401);
        }
        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'user'=>$user,
            'token'=>$token,
        ]);
    }
    public function logout($user){
        $user->currentAccessToken()->delete();
        
    }
}
