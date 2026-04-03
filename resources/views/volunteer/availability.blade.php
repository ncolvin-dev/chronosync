@extends('layouts.app')

@section('title', 'Availability - ChronoSync')

@section('extra-styles')
<style>
    .availability-header {
        margin-bottom: 2rem;
    }

    .availability-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .availability-subtitle {
        color: #666;
        font-size: 0.875rem;
    }

    .tabs-container {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .tab-button {
        padding: 0.75rem 1.5rem;
        background-color: #f0f0f0;
        color: #666;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        font-weight: 600;
        white-space: nowrap;
        transition: all 0.3s;
    }

    .tab-button:hover {
        background-color: #e0e0e0;
    }

    .tab-button.active {
        background-color: #0099cc;
        color: white;
    }

    .availability-grid {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .grid-header {
        display: grid;
        grid-template-columns: 80px repeat(7, 1fr);
        background-color: #f8f9fa;
        border-bottom: 2px solid #e0e0e0;
    }

    .grid-header-cell {
        padding: 1rem;
        font-weight: 600;
        color: #003366;
        text-align: center;
        font-size: 0.875rem;
        border-right: 1px solid #e0e0e0;
    }

    .grid-header-cell:last-child {
        border-right: none;
    }

    .grid-body {
        max-height: 500px;
        overflow-y: auto;
    }

    .grid-row {
        display: grid;
        grid-template-columns: 80px repeat(7, 1fr);
        border-bottom: 1px solid #e0e0e0;
    }

    .grid-row:last-child {
        border-bottom: none;
    }

    .grid-cell-time {
        padding: 1rem;
        background-color: #f8f9fa;
        font-weight: 600;
        color: #333;
        text-align: center;
        font-size: 0.85rem;
        border-right: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .grid-cell {
        padding: 0.75rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border-right: 1px solid #e0e0e0;
        transition: background-color 0.3s;
        position: relative;
    }

    .grid-cell:hover {
        background-color: #f0f8ff;
    }

    .grid-cell:last-child {
        border-right: none;
    }

    .availability-indicator {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        background-color: #d0d0d0;
        transition: all 0.3s;
    }

    .availability-indicator.available {
        background-color: #28a745;
    }

    .grid-cell.available .availability-indicator {
        background-color: #28a745;
    }

    .grid-cell.unavailable .availability-indicator {
        background-color: #d0d0d0;
    }

    .legend {
        display: flex;
        gap: 2rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .legend-box {
        width: 24px;
        height: 24px;
        border-radius: 4px;
    }

    .legend-box.available {
        background-color: #28a745;
    }

    .legend-box.unavailable {
        background-color: #d0d0d0;
    }

    .legend-text {
        color: #666;
        font-weight: 500;
    }

    .save-button-container {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-save {
        background-color: #0099cc;
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .btn-save:hover {
        background-color: #003366;
    }

    .btn-reset {
        background-color: #e0e0e0;
        color: #333;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .btn-reset:hover {
        background-color: #d0d0d0;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .loading-spinner {
        display: none;
        text-align: center;
        padding: 2rem;
    }

    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #0099cc;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .availability-title {
            font-size: 1.25rem;
        }

        .tabs-container {
            margin-bottom: 1.5rem;
        }

        .grid-header,
        .grid-row {
            grid-template-columns: 60px repeat(7, 1fr);
        }

        .grid-header-cell,
        .grid-cell-time {
            padding: 0.5rem;
            font-size: 0.75rem;
        }

        .grid-cell {
            padding: 0.5rem;
        }

        .availability-indicator {
            width: 20px;
            height: 20px;
        }

        .legend {
            gap: 1rem;
        }

        .save-button-container {
            flex-direction: column;
        }

        .btn-save,
        .btn-reset {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="container-md">
    <div class="row">
        <div class="col-12">
            <div class="availability-header">
                <h1 class="availability-title">
                    <i class="fas fa-calendar-check"></i> My Availability
                </h1>
                <p class="availability-subtitle">
                    Select the times you're available to volunteer. Click on a time slot to toggle your availability.
                </p>
            </div>

            <!-- Legend -->
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-box available"></div>
                    <span class="legend-text">Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box unavailable"></div>
                    <span class="legend-text">Not Available</span>
                </div>
            </div>

            <!-- Week Tabs -->
            <div class="tabs-container" id="weekTabs">
                <button class="tab-button active" data-week="1">Week 1</button>
                <button class="tab-button" data-week="2">Week 2</button>
                <button class="tab-button" data-week="3">Week 3</button>
                <button class="tab-button" data-week="4">Week 4</button>
                <button class="tab-button" data-week="5">Week 5</button>
            </div>

            <!-- Availability Grids -->
            <form method="POST" action="{{ route('availability.update', $volunteer) }}" id="availabilityForm">
                @csrf

                @for($week = 1; $week <= 5; $week++)
                    <div class="tab-content {{ $week === 1 ? 'active' : '' }}" id="week-{{ $week }}">
                        <div class="availability-grid">
                            <div class="grid-header">
                                <div class="grid-header-cell">Time</div>
                                <div class="grid-header-cell">Mon</div>
                                <div class="grid-header-cell">Tue</div>
                                <div class="grid-header-cell">Wed</div>
                                <div class="grid-header-cell">Thu</div>
                                <div class="grid-header-cell">Fri</div>
                                <div class="grid-header-cell">Sat</div>
                                <div class="grid-header-cell">Sun</div>
                            </div>

                            <div class="grid-body">
                                @for($hour = 8; $hour < 22; $hour++)
                                    @php
                                        $timeLabel = date('g A', strtotime("$hour:00"));
                                    @endphp
                                    <div class="grid-row">
                                        <div class="grid-cell-time">{{ $timeLabel }}</div>
                                        @for($day = 0; $day < 7; $day++)
                                            @php
                                                $cellId = "availability_week{$week}_day{$day}_hour{$hour}";
                                                $isAvailable = isset($slots["{$week}-{$day}-{$hour}"]);
                                            @endphp
                                            <div class="grid-cell {{ $isAvailable ? 'available' : 'unavailable' }}"
                                                 data-week="{{ $week }}"
                                                 data-day="{{ $day }}"
                                                 data-hour="{{ $hour }}"
                                                 onclick="toggleAvailability(this)">
                                                <input type="hidden"
                                                       name="availability[{{ $week }}][{{ $day }}][{{ $hour }}]"
                                                       class="availability-input"
                                                       value="{{ $isAvailable ? '1' : '0' }}">
                                                <div class="availability-indicator {{ $isAvailable ? 'available' : '' }}"></div>
                                            </div>
                                        @endfor
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endfor

                <!-- Save Button -->
                <div class="save-button-container">
                    <button type="button" class="btn-reset" onclick="resetAvailability()">
                        <i class="fas fa-redo"></i> Reset All
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Availability
                    </button>
                </div>
            </form>

            <!-- Loading Spinner -->
            <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner"></div>
                <p style="margin-top: 1rem; color: #666;">Saving your availability...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    // Tab switching
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const week = this.getAttribute('data-week');

            // Update active tab button
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Update active content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById('week-' + week).classList.add('active');
        });
    });

    // Toggle availability
    function toggleAvailability(element) {
        const input = element.querySelector('.availability-input');
        const indicator = element.querySelector('.availability-indicator');
        const isCurrentlyAvailable = input.value === '1';

        if (isCurrentlyAvailable) {
            input.value = '0';
            element.classList.remove('available');
            element.classList.add('unavailable');
            indicator.classList.remove('available');
        } else {
            input.value = '1';
            element.classList.add('available');
            element.classList.remove('unavailable');
            indicator.classList.add('available');
        }
    }

    // Reset availability
    function resetAvailability() {
        if (confirm('Are you sure you want to reset all availability to unavailable?')) {
            document.querySelectorAll('.grid-cell').forEach(cell => {
                const input = cell.querySelector('.availability-input');
                const indicator = cell.querySelector('.availability-indicator');

                input.value = '0';
                cell.classList.remove('available');
                cell.classList.add('unavailable');
                indicator.classList.remove('available');
            });
        }
    }

    // Form submission
    document.getElementById('availabilityForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const spinner = document.getElementById('loadingSpinner');
        const form = this;

        spinner.style.display = 'block';

        // Submit form via fetch
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => {
            spinner.style.display = 'none';
            if (response.ok) {
                // Show success message
                alert('Your availability has been saved successfully!');
                return response.json();
            } else {
                throw new Error('Failed to save availability');
            }
        })
        .catch(error => {
            spinner.style.display = 'none';
            alert('Error saving availability: ' + error.message);
        });
    });
</script>
@endsection
