<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $user->update(['ultimo_acceso' => now()]);

        return match ($user->tipo_usuario) {
            'admin'      => redirect()->route('home'),
            'instructor' => redirect()->route('instructor.mis-grupos'),
            default      => redirect()->route('home'),
        };
    }
}
