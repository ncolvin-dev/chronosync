@extends('layouts.app')

@section('title', 'Matching - ChronoSync')

@section('extra-styles')
<style>
    .matching-header {
        margin-bottom: 2rem;
    }

    .matching-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .filter-section {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .filter-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 1rem;
    }

    .filter-controls {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.875rem;
    }

    .form-control,
    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0099cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 153, 204, 0.25);
        outline: none;
    }

    .filter-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-filter {
        padding: 0.75rem 1.5rem;
        background-color: #0099cc;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-filter:hover {
        background-color: #003366;
    }

    .btn-reset {
        padding: 0.75rem 1.5rem;
        background-color: #e0e0e0;
        color: #333;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-reset:hover {
        background-color: #d0d0d0;
    }

    /* Meetings Table */
    .meetings-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .meetings-table {
        width: 100%;
        border-collapse: collapse;
    }

    .meetings-table th {
        background-color: #f8f9fa;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #003366;
        border-bottom: 2px solid #e0e0e0;
    }

    .meetings-table td {
        padding: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .meetings-table tr:hover {
        background-color: #f8f9fa;
    }

    .meeting-row {
        display: flex;
        gap: 2rem;
        padding: 1.5rem;
    }

    .meeting-info {
        flex: 1;
        min-width: 300px;
    }

    .meeting-info-row {
        display: flex;
        gap: 2rem;
        margin-bottom: 1rem;
    }

    .meeting-info-item {
        flex: 1;
    }

    .meeting-info-label {
        font-size: 0.75rem;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .meeting-info-value {
        color: #333;
        font-weight: 500;
    }

    .meeting-status-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-filled {
        background-color: #d4edda;
        color: #155724;
    }

    .status-candidates {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-unfilled {
        background-color: #f8d7da;
        color: #721c24;
    }

    .volunteers-section {
        flex: 1;
        min-width: 300px;
    }

    .volunteers-title {
        font-weight: 600;
        color: #003366;
        margin-bottom: 0.75rem;
    }

    .volunteer-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .volunteer-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.8rem;
        background-color: #f0f0f0;
        border-radius: 9999px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .volunteer-chip:hover {
        background-color: #e0e0e0;
    }

    .volunteer-chip.assigned {
        background-color: #d4edda;
        color: #155724;
    }

    .chip-score {
        background-color: #0099cc;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .volunteer-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-assign {
        padding: 0.5rem 1rem;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .btn-assign:hover {
        background-color: #218838;
    }

    .btn-change {
        padding: 0.5rem 1rem;
        background-color: #ffc107;
        color: #333;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .btn-change:hover {
        background-color: #e0a800;
        color: white;
    }

    .btn-override {
        padding: 0.5rem 1rem;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .btn-override:hover {
        background-color: #c82333;
    }

    .no-candidates {
        color: #999;
        font-size: 0.9rem;
        font-style: italic;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #d0d0d0;
        margin-bottom: 1rem;
    }

    .empty-state-text {
        color: #666;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 1024px) {
        .meeting-row {
            flex-direction: column;
            gap: 1rem;
        }

        .meeting-info-row {
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-controls {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .matching-title {
            font-size: 1.25rem;
        }

        .filter-actions {
            flex-direction: column;
        }

        .btn-filter,
        .btn-reset {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="matching-header">
                <h1 class="matching-title">
                    <i class="fas fa-link"></i> Volunteer Matching
                </h1>
                <p style="color: #666; font-size: 0.9rem;">
                    View upcoming meetings and match available volunteers based on availability, experience, and facility requirements.
                </p>
            </div>

            <!-- Filters -->
            <div class="filter-section">
                <div class="filter-title">Filter Meetings</div>

                <div class="filter-controls">
                    <div class="form-group">
                        <label for="facility-filter" class="form-label">Facility</label>
                        <select class="form-select" id="facility-filter">
                            <option value="">All Facilities</option>
                            <option value="1">Harmony House</option>
                            <option value="2">New Path Center</option>
                            <option value="3">Sunrise Community</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date-from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date-from">
                    </div>

                    <div class="form-group">
                        <label for="date-to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date-to">
                    </div>

                    <div class="form-group">
                        <label for="buffer" class="form-label">Buffer (minutes)</label>
                        <input type="number" class="form-control" id="buffer" value="0" min="0">
                    </div>
                </div>

                <div class="filter-actions">
                    <button class="btn-filter">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <button class="btn-reset">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Meetings List -->
            <div class="meetings-container">
                <!-- Meeting Row 1 -->
                <div class="meeting-row">
                    <div class="meeting-info">
                        <div class="meeting-info-row">
                            <div class="meeting-info-item">
                                <div class="meeting-info-label">Facility</div>
                                <div class="meeting-info-value">Harmony House Treatment Center</div>
                            </div>
                            <div class="meeting-info-item">
                                <div class="meeting-info-label">Date & Time</div>
                                <div class="meeting-info-value">Tuesday, April 2, 2026 at 6:30 PM</div>
                            </div>
                        </div>
                        <div style="margin-top: 0.75rem;">
                            <span class="meeting-status-badge status-candidates">
                                <i class="fas fa-users"></i> Candidates Available
                            </span>
                        </div>
                    </div>

                    <div class="volunteers-section">
                        <div class="volunteers-title">Available Candidates</div>
                        <div class="volunteer-chips">
                            <div class="volunteer-chip">
                                <span>Alex Johnson</span>
                                <div class="chip-score">95</div>
                            </div>
                            <div class="volunteer-chip">
                                <span>Morgan Davis</span>
                                <div class="chip-score">87</div>
                            </div>
                            <div class="volunteer-chip">
                                <span>Jordan Taylor</span>
                                <div class="chip-score">72</div>
                            </div>
                        </div>

                        <div class="volunteer-actions">
                            <button class="btn-assign">
                                <i class="fas fa-check"></i> Assign Top Match
                            </button>
                            <button class="btn-change">
                                <i class="fas fa-exchange-alt"></i> Choose Other
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Meeting Row 2 -->
                <div class="meeting-row">
                    <div class="meeting-info">
                        <div class="meeting-info-row">
                            <div class="meeting-info-item">
                                <div class="meeting-info-label">Facility</div>
                                <div class="meeting-info-value">New Path Recovery Center</div>
                            </div>
                            <div class="meeting-info-item">
                                <div class="meeting-info-label">Date & Time</div>
                                <div class="meeting-info-value">Wednesday, April 3, 2026 at 7:00 PM</div>
                            </div>
                        </div>
                        <div style="margin-top: 0.75rem;">
                            <span class="meeting-status-badge status-filled">
                                <i class="fas fa-check-circle"></i> Assigned
                            </span>
                        </div>
                    </div>

                    <div class="volunteers-section">
                        <div class="volunteers-title">Current Assignment</div>
                        <div class="volunteer-chips">
                            <div class="volunteer-chip assigned">
                                <span>Casey Miller</span>
                                <div class="chip-score" style="background-color: #28a745;">✓</div>
                            </div>
                        </div>

                        <div class="volunteer-actions">
                            <button class="btn-change">
                                <i class="fas fa-exchange-alt"></i> Change Assignment
                            </button>
                            <button class="btn-override">
                                <i class="fas fa-ban"></i> Override
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Meeting Row 3 -->
                <div class="meeting-row">
                    <div class="meeting-info">
                        <div class="meeting-info-row">
                            <div class="meeting-info-item">
                                <div class="meeting-info-label">Facility</div>
                                <div class="meeting-info-value">Sunrise Community Center</div>
                            </div>
                            <div class="meeting-info-item">
                                <div class="meeting-info-label">Date & Time</div>
                                <div class="meeting-info-value">Thursday, April 4, 2026 at 5:00 PM</div>
                            </div>
                        </div>
                        <div style="margin-top: 0.75rem;">
                            <span class="meeting-status-badge status-unfilled">
                                <i class="fas fa-exclamation-circle"></i> No Candidates
                            </span>
                        </div>
                    </div>

                    <div class="volunteers-section">
                        <div class="volunteers-title">Available Candidates</div>
                        <div class="no-candidates">
                            No volunteers available for this meeting based on current criteria.
                        </div>

                        <div class="volunteer-actions">
                            <button class="btn-override">
                                <i class="fas fa-ban"></i> Override Requirements
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Meeting Row 4 -->
                <div class="meeting-row">
                    <div class="meeting-info">
                        <div class="meeting-info-row">
                            <div class="meeting-info-item">
                                <div class="meeting-info-label">Facility</div>
                                <div class="meeting-info-value">Recovery Plus</div>
                            </div>
                            <div class="meeting-info-item">
                                <div class="meeting-info-label">Date & Time</div>
                                <div class="meeting-info-value">Friday, April 5, 2026 at 6:00 PM</div>
                            </div>
                        </div>
                        <div style="margin-top: 0.75rem;">
                            <span class="meeting-status-badge status-candidates">
                                <i class="fas fa-users"></i> Candidates Available
                            </span>
                        </div>
                    </div>

                    <div class="volunteers-section">
                        <div class="volunteers-title">Available Candidates</div>
                        <div class="volunteer-chips">
                            <div class="volunteer-chip">
                                <span>Riley Thompson</span>
                                <div class="chip-score">91</div>
                            </div>
                            <div class="volunteer-chip">
                                <span>Sam Anderson</span>
                                <div class="chip-score">78</div>
                            </div>
                        </div>

                        <div class="volunteer-actions">
                            <button class="btn-assign">
                                <i class="fas fa-check"></i> Assign Top Match
                            </button>
                            <button class="btn-change">
                                <i class="fas fa-exchange-alt"></i> Choose Other
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    // Filter and assignment actions
    document.querySelectorAll('.btn-assign').forEach(btn => {
        btn.addEventListener('click', function() {
            alert('Volunteer assigned successfully!');
        });
    });

    document.querySelectorAll('.btn-change').forEach(btn => {
        btn.addEventListener('click', function() {
            alert('Opening volunteer selection...');
        });
    });

    document.querySelectorAll('.btn-override').forEach(btn => {
        btn.addEventListener('click', function() {
            alert('Opening override options...');
        });
    });

    document.querySelector('.btn-filter').addEventListener('click', function() {
        alert('Applying filters...');
    });

    document.querySelector('.btn-reset').addEventListener('click', function() {
        document.getElementById('facility-filter').value = '';
        document.getElementById('date-from').value = '';
        document.getElementById('date-to').value = '';
        document.getElementById('buffer').value = '0';
    });
</script>
@endsection
