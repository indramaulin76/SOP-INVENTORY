@extends('layouts.auth')
@section('title', 'Login')

@section('content')
    <div class="auth-header">
        <div class="auth-logo">
            <img src="{{ asset('images/logo-sae.jpg') }}" alt="Sae Bakery Logo" class="logo-img">
        </div>
        <h1>Login</h1>
        <p>Masukkan kredensial Anda untuk melanjutkan</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-20">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success mb-20">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="form-group @error('email') error @enderror">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="input" value="{{ old('email') }}" placeholder="Masukkan email Anda" required>
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group @error('password') error @enderror">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="input" placeholder="Masukkan password Anda" required>
            @error('password')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
@endsection
