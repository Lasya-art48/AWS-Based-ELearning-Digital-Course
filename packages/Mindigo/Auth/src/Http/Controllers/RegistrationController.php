<?php

namespace Mindigo\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mindigo\Auth\Http\Requests\RegistrationRequest;
use Mindigo\Auth\Services\RegistrationService;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly RegistrationService $service
    ) {}

    public function index()
    {
        return view('Mindigo-auth::register');
    }

    public function store(RegistrationRequest $request)
    {
        $user = $this->service->register($request->validated());
        $request->session()->put('registered_learn_sphere_id', $user->learn_sphere_id);

        return redirect()->route('register.complete');
    }

    public function complete(Request $request)
    {
        $learnSphereId = $request->session()->pull('registered_learn_sphere_id');

        if (! is_string($learnSphereId)) {
            return redirect()->route('register');
        }

        return view('Mindigo-auth::registered', compact('learnSphereId'));
    }
}
