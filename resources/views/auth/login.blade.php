<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <div class="login-brand">
                <span class="brand-mark" style="width:46px;height:46px;border-radius:14px">@include('partials.icon', ['name' => 'wallet', 'size' => 24])</span>
            </div>
            <h1>Selamat Datang!</h1>
            <p class="login-subtitle">Masuk untuk mengelola keuangan SILKA</p>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    @include('partials.icon', ['name' => 'x', 'size' => 18])
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="login-field">
                        @include('partials.icon', ['name' => 'user', 'size' => 18])
                        <input type="email" class="form-control @error('email') input-error @enderror"
                               id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com"
                               required autofocus autocomplete="username">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="login-field">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input type="password" class="form-control @error('password') input-error @enderror"
                               id="password" name="password" placeholder="••••••••"
                               required autocomplete="current-password">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="padding:12px 16px;font-size:14.5px">Masuk</button>
            </form>
        </div>
    </div>
</body>
</html>