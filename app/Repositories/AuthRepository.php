<?php 

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;


class AuthRepository implements AuthRepositoryInterface
{
public function findByEmail(string $email)
{
    return User::where('email',$email)->first();
}

public function create (array $data){
return User::create($data);
}  

public function delete(int $id)
{
    return User::destroy($id);       // or throw an exception if preferred

}
}