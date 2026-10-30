<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@lang('Mindigo-auth::app.registration.complete_title') — {{ config('app.name') }}</title>
    @vite([
        'packages/Mindigo/Auth/src/resources/css/app.css',
        'packages/Mindigo/Auth/src/resources/js/app.js',
    ])
</head>
<body class="min-h-screen bg-gradient-to-br from-green-50 via-white to-emerald-100" style="font-family:Inter,'Segoe UI',Arial,sans-serif;">
<main class="min-h-screen flex items-center justify-center px-5 py-10">
    <section class="w-full max-w-lg rounded-3xl border border-green-100 bg-white p-7 sm:p-10 text-center shadow-xl">
        <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-green-100 text-3xl text-green-700" aria-hidden="true">✓</div>
        <p class="text-sm font-black uppercase tracking-widest text-green-700">LearnSphere</p>
        <h1 class="mt-2 text-3xl font-black text-gray-900">@lang('Mindigo-auth::app.registration.complete_title')</h1>
        <p class="mt-3 text-sm leading-6 text-gray-600">@lang('Mindigo-auth::app.registration.complete_description')</p>
        <div class="mt-7 rounded-2xl border-2 border-dashed border-green-300 bg-green-50 px-5 py-5">
            <p class="text-xs font-black uppercase tracking-wider text-green-800">@lang('Mindigo-auth::app.registration.your_id')</p>
            <p class="mt-2 select-all font-mono text-2xl font-black tracking-wide text-green-900" data-learn-sphere-id>{{ $learnSphereId }}</p>
        </div>
        <p class="mt-5 text-sm font-bold leading-6 text-amber-800">@lang('Mindigo-auth::app.registration.save_id_notice')</p>
        <p class="mt-2 text-sm text-gray-600">@lang('Mindigo-auth::app.registration.password_notice')</p>
        <a href="{{ route('login') }}" class="mt-7 inline-flex w-full items-center justify-center rounded-xl bg-green-600 px-5 py-3.5 text-sm font-black text-white shadow-[0_4px_0_#15803d] transition hover:bg-green-500">
            @lang('Mindigo-auth::app.registration.sign_in')
        </a>
    </section>
</main>
</body>
</html>
