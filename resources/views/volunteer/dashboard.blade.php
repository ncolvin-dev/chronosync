@extends('layouts.app')
@section('title', 'My Dashboard - ChronoSync')
@section('content')
<div class="container-fluid py-4">
    <h2 style="color:#003366; font-weight:700;">
        <i class="fas fa-hand-holding-heart"></i> Welcome back!
    </h2>
    <p class="text-muted mb-4">Here's your volunteer overview.</p>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header" style="background:linear-gradient(135deg,#003366,#0099cc);color:white;">
                    <i class="fas fa-calendar-check"></i> My Upcoming Assignments
                </div>
                <div class="card-body">
                    <p class="text-muted">No upcoming assignments at this time.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header" style="background:linear-gradient(135deg,#003366,#0099cc);color:white;">
                    <i class="fas fa-user"></i> My Profile
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <a href="{{ route('volunteer.profile') }}" class="btn btn-sm btn-outline-primary mt-2">
                        View / Edit Profile
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header" style="background:linear-gradient(135deg,#003366,#0099cc);color:white;">
                    <i class="fas fa-clock"></i> My Availability
                </div>
                <div class="card-body">
                    <a href="{{ route('volunteer.availability') }}" class="btn btn-sm btn-outline-primary">
                        Manage Availability
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header" style="background:linear-gradient(135deg,#003366,#0099cc);color:white;">
                    <i class="fas fa-history"></i> Assignment History
                </div>
                <div class="card-body">
                    <p class="text-muted">No past assignments on record.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
