@extends('layouts.app')

@section('title', 'Login - Banking System')

@section('content')
<div class="card" style="max-width:420px;margin:60px auto">
    <h1>Sign in</h1>
    <p class="sub">Use the demo account: <code>demo@bank.test</code> / <code>password</code></p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
        </div>

        <button type="submit" class="btn">Login</button>
    </form>
</div>
@endsection
