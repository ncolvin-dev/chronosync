@extends('layouts.app')
@section('title', 'Access Denied')
@section('content')
<div style="text-align:center; padding: 4rem;">
    <h2 style="color:#003366;"><i class="fas fa-lock"></i> Access Denied</h2>
    <p style="color:#666;">You don't have permission to view this page.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">Back to Dashboard</a>
</div>
@endsection
