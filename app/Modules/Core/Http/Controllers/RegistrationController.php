<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Actions\RegisterUser;
use App\Modules\Core\Http\Requests\RegisterRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('core::auth.register');
    }

    public function store(RegisterRequest $request, RegisterUser $registerUser): RedirectResponse
    {
        $registerUser->handle($request->validated('email'));

        return redirect()->route('verification.notice')
            ->with('status', 'Recibimos tu solicitud. Si la dirección puede recibir correo, te llegará un enlace para activar tu cuenta.');
    }
}
