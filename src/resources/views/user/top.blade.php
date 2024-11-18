<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
<div class="min-h-screen flex justify-center items-center bg-slate-300">
    <div class="p-4 flex justify-between items-center rounded-3xl bg-white">
        {{-- ロゴエリア --}}
        <div class="w-64 mr-5">
            <x-application-logo/>
        </div>
        {{-- 説明文エリア --}}
        <div class="mb-4">
            {{-- タイトル --}}
            <h1 class="mb-5 text-8xl text-slate-800 font-bold">EasyMemo</h1>
            {{-- サービスの説明 --}}
            <div class="mb-7 text-center text-slate-600 font-semibold leading-7">
                <p>趣味、日々のちょっとしたことを、簡単にメモするサービスです。</p>
                <p>ログインして使用してください。</p>
            </div>
            {{-- ログインエリア --}}
            <div class="flex justify-around text-center">
                {{-- ユーザーログイン --}}
                @if (Route::has('user.login'))
                    <a href="{{ route('user.login') }}"
                       class="btn w-44 py-2 mr-2 font-semibold rounded-lg bg-sky-900 hover:bg-sky-800">
                        ユーザーログイン
                    </a>
                @endif
                {{-- 管理者ログイン --}}
                @if (Route::has('admin.login'))
                    <a href="{{ route('admin.login') }}"
                       class="btn w-44 py-2 font-semibold rounded-lg bg-rose-900 hover:bg-rose-800">
                        管理者ログイン
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
</body>

</html>
