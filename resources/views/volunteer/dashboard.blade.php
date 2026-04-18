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
                    @if($upcomingAssignments->isEmpty())
                        <p class="text-muted">No upcoming assignments at this time.</p>
                    @else
                        <ul class="list-unstyled mb-0">
                            @foreach($upcomingAssignments as $assignment)
                            <li class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="fw-bold">{{ $assignment->meeting?->facility?->facility_name ?? 'Unknown Facility' }}</div>
                                <div class="text-muted small">{{ $assignment->assignment_date->format('D, M j, Y') }}</div>
                                @if($assignment->meeting?->meeting_time)
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($assignment->meeting->meeting_time)->format('g:i A') }}</div>
                                @endif
                                <span class="badge mt-1 {{ $assignment->status === 'confirmed' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $assignment->status === 'confirmed' ? 'Confirmed' : 'Pending Response' }}
                                </span>
                            </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('volunteer.assignments') }}" class="btn btn-sm btn-outline-primary mt-2">View All</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header" style="background:linear-gradient(135deg,#003366,#0099cc);color:white;">
                    <i class="fas fa-user"></i> My Profile
                </div>
                <div class="card-body">
                    @if($volunteer)
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-muted small">Name</div>
                            <div class="fw-bold">{{ $volunteer->full_name }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Email</div>
                            <div>{{ $volunteer->email }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Phone</div>
                            <div>{{ $volunteer->phone }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Gender</div>
                            <div>{{ ucfirst($volunteer->gender) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Clean Date</div>
                            <div>{{ $volunteer->clean_date?->format('M j, Y') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Clean Time</div>
                            @php $ct = $volunteer->cleanTime() @endphp
                            <div>{{ $ct['years'] }}y {{ $ct['months'] }}m</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Neighborhood</div>
                            <div>{{ $volunteer->neighborhood ?? '—' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Probation Status</div>
                            <div>
                                @if($volunteer->isOnProbation())
                                    <span class="badge bg-warning text-dark">On Probation</span>
                                @else
                                    <span class="badge bg-success">Clear</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    <a href="{{ route('volunteer.profile') }}" class="btn btn-sm btn-outline-primary">
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
                    @if($totalAvailableSlots === 0)
                        <p class="text-muted">No availability set yet.</p>
                    @else
                        @php
                            $dayNames = [0=>'Mon',1=>'Tue',2=>'Wed',3=>'Thu',4=>'Fri',5=>'Sat',6=>'Sun'];
                        @endphp
                        <div class="mb-3">
                            <div class="text-muted small mb-1">Available days</div>
                            <div class="d-flex gap-1 flex-wrap">
                                @foreach($dayNames as $num => $label)
                                    @if($availabilityByDay->has($num))
                                        <span class="badge bg-primary">{{ $label }}</span>
                                    @else
                                        <span class="badge bg-light text-muted border">{{ $label }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small mb-1">Hours per day</div>
                            @foreach($availabilityByDay as $dayNum => $hours)
                                <div class="d-flex align-items-center mb-1 small">
                                    <span style="width:2.5rem; font-weight:600;">{{ $dayNames[$dayNum] }}</span>
                                    <span class="text-muted">
                                        {{ \Carbon\Carbon::createFromTime($hours->first())->format('g A') }}
                                        –
                                        {{ \Carbon\Carbon::createFromTime($hours->last() + 1)->format('g A') }}
                                        ({{ $hours->count() }}h)
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-muted small mb-3">{{ $totalAvailableSlots }} total slot{{ $totalAvailableSlots !== 1 ? 's' : '' }} across all weeks</div>
                    @endif
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
                    @if($pastAssignments->isEmpty())
                        <p class="text-muted">No past assignments on record.</p>
                    @else
                        <ul class="list-unstyled mb-0">
                            @foreach($pastAssignments as $assignment)
                            <li class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="fw-bold">{{ $assignment->meeting?->facility?->facility_name ?? 'Unknown Facility' }}</div>
                                <div class="text-muted small">{{ $assignment->assignment_date->format('D, M j, Y') }}</div>
                                <span class="badge mt-1 {{ $assignment->status === 'confirmed' ? 'bg-success' : ($assignment->status === 'declined' ? 'bg-danger' : 'bg-secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                </span>
                            </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('volunteer.assignments') }}" class="btn btn-sm btn-outline-primary mt-2">View All</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
