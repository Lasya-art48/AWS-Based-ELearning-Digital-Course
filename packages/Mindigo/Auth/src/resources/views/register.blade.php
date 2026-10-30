<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@lang('Mindigo-auth::app.registration.title') — {{ config('app.name') }}</title>
    @vite([
        'packages/Mindigo/Auth/src/resources/css/app.css',
        'packages/Mindigo/Auth/src/resources/js/app.js',
    ])
</head>
<body class="min-h-screen bg-white" style="font-family:Inter,'Segoe UI',Arial,sans-serif;">
<div class="min-h-screen flex">
    <main class="w-full lg:w-1/2 flex flex-col min-h-screen bg-white">
        <div class="px-8 sm:px-10 py-5 border-b border-gray-100">
            <a href="/" class="flex items-center gap-2">
                <svg width="36" height="36" viewBox="0 0 200 220" fill="none" aria-hidden="true">
                    <circle cx="105" cy="145" r="90" fill="#22c55e" stroke="#14532d" stroke-width="3"/>
                    <ellipse cx="115" cy="185" rx="55" ry="38" fill="#86efac" stroke="#14532d" stroke-width="2"/>
                    <path d="M95 58 Q85 20 105 8 Q118 22 112 58" fill="#16a34a" stroke="#14532d" stroke-width="2.5"/>
                    <circle cx="82" cy="135" r="20" fill="white" stroke="#14532d" stroke-width="2"/>
                    <circle cx="86" cy="138" r="12" fill="#14532d"/>
                    <circle cx="128" cy="135" r="20" fill="white" stroke="#14532d" stroke-width="2"/>
                    <circle cx="132" cy="138" r="12" fill="#14532d"/>
                </svg>
                <span class="text-xl font-black text-green-600 tracking-tight">LearnSphere</span>
            </a>
        </div>

        <div class="flex-1 flex items-center justify-center px-6 sm:px-10 py-10">
            <div class="w-full max-w-md">
                <h1 class="text-3xl font-black text-gray-900 mb-2">@lang('Mindigo-auth::app.registration.title')</h1>
                <p class="text-gray-500 text-sm mb-7">@lang('Mindigo-auth::app.registration.subtitle')</p>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-4">
                    @csrf
                    <label class="flex flex-col gap-1.5">
                        <span class="text-sm font-black text-gray-700">@lang('Mindigo-auth::app.registration.name')</span>
                        <input name="name" type="text" value="{{ old('name') }}" autocomplete="name" maxlength="255" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-400"
                            @error('name') aria-invalid="true" @enderror>
                    </label>

                    <label class="flex flex-col gap-1.5">
                        <span class="text-sm font-black text-gray-700">@lang('Mindigo-auth::app.registration.email')</span>
                        <input name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-400"
                            @error('email') aria-invalid="true" @enderror>
                    </label>

                    <label class="flex flex-col gap-1.5">
                        <span class="text-sm font-black text-gray-700">@lang('Mindigo-auth::app.registration.role')</span>
                        <select name="role" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-400">
                            <option value="student" @selected(old('role', 'student') === 'student')>@lang('Mindigo-auth::app.registration.student')</option>
                            <option value="teacher" @selected(old('role') === 'teacher')>@lang('Mindigo-auth::app.registration.teacher')</option>
                        </select>
                    </label>

                    <label class="flex flex-col gap-1.5">
                        <span class="text-sm font-black text-gray-700">@lang('Mindigo-auth::app.registration.password')</span>
                        <input name="password" type="password" autocomplete="new-password" minlength="8" maxlength="255" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-400"
                            @error('password') aria-invalid="true" @enderror>
                    </label>

                    <label class="flex flex-col gap-1.5">
                        <span class="text-sm font-black text-gray-700">@lang('Mindigo-auth::app.registration.password_confirmation')</span>
                        <input name="password_confirmation" type="password" autocomplete="new-password" minlength="8" maxlength="255" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-400">
                    </label>

                    <button type="submit" class="mt-2 w-full bg-green-500 hover:bg-green-400 active:bg-green-600 text-white font-black text-sm py-3.5 rounded-xl shadow-[0_4px_0_#15803d] transition-all">
                        @lang('Mindigo-auth::app.registration.create_account')
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 mt-6">
                    @lang('Mindigo-auth::app.registration.already_account')
                    <a href="{{ route('login') }}" class="text-green-700 font-black hover:underline">@lang('Mindigo-auth::app.registration.sign_in')</a>
                </p>
            </div>
        </div>
    </main>

    <aside class="hidden lg:flex flex-1 items-center justify-center px-12 text-center"
        style="background:linear-gradient(160deg,#dcfce7 0%,#bbf7d0 30%,#4ade80 70%,#16a34a 100%)">
        <div class="max-w-md">
            <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-3xl bg-white shadow-2xl">
                <svg class="h-12 w-12 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M12 21V11"/>
                    <path d="M12 13C6 13 4 9 4 4c5 0 9 2 9 8"/>
                    <path d="M12 17c0-5 3-8 8-8 0 5-2 8-8 8"/>
                </svg>
            </div>
            <h2 class="text-3xl font-black text-green-950 mb-3">@lang('Mindigo-auth::app.registration.welcome_title')</h2>
            <p class="text-green-950/80 text-sm leading-relaxed">@lang('Mindigo-auth::app.registration.welcome_description')</p>
        </div>
    </aside>
</div>
</body>
</html>
