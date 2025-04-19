@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>You are logged in!</h2>

        <!-- Logout Form -->
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>
@endsection
