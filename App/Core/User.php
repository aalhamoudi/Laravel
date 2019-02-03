<?php
namespace App\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Laravel\Lumen\Auth\Authorizable as Authorizable;
//use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;


class LumenUser extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $fillable = ['name', 'email'];

    protected $hidden = ['password'];

    public function can($ability, $arguments = []) {}
}


class LaravelUser extends LumenUser implements CanResetPasswordContract
{
    use CanResetPassword, MustVerifyEmail;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
}
