@extends('layouts.app')

@section('title', 'Volunteers - ChronoSync')

@section('extra-styles')
<style>
    .volunteers-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .volunteers-title {
        color: #003366;
        font-weight: 700;
        font-size: 1.5rem;
    }

    .search-and-filter {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .filter-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 1.5rem;
    }

    .filter-controls {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
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

    /* Volunteers Table -->
    .volunteers-table-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow-x: auto;
    }

    .volunteers-table {
        width: 100%;
        border-collapse: collapse;
    }

    .volunteers-table th {
        background-color: #f8f9fa;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #003366;
        border-bottom: 2px solid #e0e0e0;
    }

    .volunteers-table td {
        padding: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .volunteers-table tr:hover {
        background-color: #f8f9fa;
    }

    .volunteer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #0099cc;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }

    .volunteer-name-cell {
        display: flex;
        align-items: center;
    }

    .volunteer-name {
        font-weight: 600;
        color: #003366;
    }

    .volunteer-email {
        font-size: 0.85rem;
        color: #999;
        display: block;
    }

    .clean-time {
        font-weight: 600;
        color: #333;
    }

    .status-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background-color: #e2e3e5;
        color: #383d41;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .credentials-cell {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .credential-badge {
        background-color: #d1ecf1;
        color: #0c5460;
        padding: 0.3rem 0.6rem;
        border-radius: 0.5rem;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .credential-badge.pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .credential-badge.expired {
        background-color: #f8d7da;
        color: #721c24;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-small {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .btn-view {
        background-color: #0099cc;
        color: white;
    }

    .btn-view:hover {
        background-color: #003366;
    }

    .btn-edit {
        background-color: #28a745;
        color: white;
    }

    .btn-edit:hover {
        background-color: #218838;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e0e0e0;
    }

    .pagination button,
    .pagination a {
        padding: 0.5rem 0.75rem;
        background-color: #f0f0f0;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s;
        color: #666;
        text-decoration: none;
        font-weight: 600;
    }

    .pagination button:hover,
    .pagination a:hover {
        background-color: #e0e0e0;
    }

    .pagination .active {
        background-color: #0099cc;
        color: white;
        border-color: #0099cc;
    }

    .pagination .disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #d0d0d0;
        margin-bottom: 1rem;
    }

    .empty-state-title {
        color: #666;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .empty-state-text {
        color: #999;
        font-size: 0.875rem;
    }

    @media (max-width: 1024px) {
        .volunteers-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .filter-controls {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-small {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .volunteers-title {
            font-size: 1.25rem;
        }

        .volunteers-table th,
        .volunteers-table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.85rem;
        }

        .volunteer-avatar {
            width: 36px;
            height: 36px;
        }

        .volunteer-name {
            font-size: 0.95rem;
        }

        .volunteer-email {
            display: none;
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
            <div class="volunteers-header">
                <h1 class="volunteers-title">
                    <i class="fas fa-users"></i> Volunteer Management
                </h1>
            </div>

            <!-- Search and Filters -->
            <div class="search-and-filter">
                <div class="filter-title">Search & Filter</div>

                <div class="filter-controls">
                    <div class="form-group">
                        <label for="search" class="form-label">Search by Name or Email</label>
                        <input
                            type="text"
                            class="form-control"
                            id="search"
                            placeholder="Enter name or email..."
                        >
                    </div>

                    <div class="form-group">
                        <label for="clean-time-filter" class="form-label">Clean Time</label>
                        <select class="form-select" id="clean-time-filter">
                            <option value="">All</option>
                            <option value="0-1">0-1 years</option>
                            <option value="1-2">1-2 years</option>
                            <option value="2-5">2-5 years</option>
                            <option value="5+">5+ years</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="credential-filter" class="form-label">Credential Status</label>
                        <select class="form-select" id="credential-filter">
                            <option value="">All</option>
                            <option value="approved">All Approved</option>
                            <option value="pending">Pending Approval</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="availability-filter" class="form-label">Availability</label>
                        <select class="form-select" id="availability-filter">
                            <option value="">All</option>
                            <option value="available">Available This Week</option>
                            <option value="unavailable">Not Available</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status-filter" class="form-label">Status</label>
                        <select class="form-select" id="status-filter">
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <button class="btn-filter" onclick="applyFilters()">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <button class="btn-reset" onclick="resetFilters()">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Volunteers Table -->
            <div class="volunteers-table-container">
                <table class="volunteers-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Clean Time</th>
                            <th>Gender</th>
                            <th>Phone</th>
                            <th>Credentials</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Volunteer 1 -->
                        <tr>
                            <td>
                                <div class="volunteer-name-cell">
                                    <div class="volunteer-avatar">AJ</div>
                                    <div>
                                        <div class="volunteer-name">Alex Johnson</div>
                                        <span class="volunteer-email">alex.johnson@email.com</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="clean-time">4 years 6 months</span></td>
                            <td>Male</td>
                            <td>(555) 123-4567</td>
                            <td>
                                <div class="credentials-cell">
                                    <span class="credential-badge">Background</span>
                                    <span class="credential-badge">Reference</span>
                                </div>
                            </td>
                            <td><span class="status-badge status-active"><i class="fas fa-check"></i> Active</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-view">View</button>
                                    <button class="btn-small btn-edit">Edit</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Volunteer 2 -->
                        <tr>
                            <td>
                                <div class="volunteer-name-cell">
                                    <div class="volunteer-avatar">MD</div>
                                    <div>
                                        <div class="volunteer-name">Morgan Davis</div>
                                        <span class="volunteer-email">morgan.davis@email.com</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="clean-time">3 years 2 months</span></td>
                            <td>Female</td>
                            <td>(555) 234-5678</td>
                            <td>
                                <div class="credentials-cell">
                                    <span class="credential-badge">Background</span>
                                    <span class="credential-badge pending">Reference (Pending)</span>
                                </div>
                            </td>
                            <td><span class="status-badge status-active"><i class="fas fa-check"></i> Active</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-view">View</button>
                                    <button class="btn-small btn-edit">Edit</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Volunteer 3 -->
                        <tr>
                            <td>
                                <div class="volunteer-name-cell">
                                    <div class="volunteer-avatar">JT</div>
                                    <div>
                                        <div class="volunteer-name">Jordan Taylor</div>
                                        <span class="volunteer-email">jordan.taylor@email.com</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="clean-time">2 years 8 months</span></td>
                            <td>Non-Binary</td>
                            <td>(555) 345-6789</td>
                            <td>
                                <div class="credentials-cell">
                                    <span class="credential-badge">Background</span>
                                    <span class="credential-badge">Reference</span>
                                    <span class="credential-badge">Training</span>
                                </div>
                            </td>
                            <td><span class="status-badge status-active"><i class="fas fa-check"></i> Active</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-view">View</button>
                                    <button class="btn-small btn-edit">Edit</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Volunteer 4 -->
                        <tr>
                            <td>
                                <div class="volunteer-name-cell">
                                    <div class="volunteer-avatar">CM</div>
                                    <div>
                                        <div class="volunteer-name">Casey Miller</div>
                                        <span class="volunteer-email">casey.miller@email.com</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="clean-time">5 years 1 month</span></td>
                            <td>Female</td>
                            <td>(555) 456-7890</td>
                            <td>
                                <div class="credentials-cell">
                                    <span class="credential-badge">Background</span>
                                </div>
                            </td>
                            <td><span class="status-badge status-active"><i class="fas fa-check"></i> Active</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-view">View</button>
                                    <button class="btn-small btn-edit">Edit</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Volunteer 5 -->
                        <tr>
                            <td>
                                <div class="volunteer-name-cell">
                                    <div class="volunteer-avatar">RT</div>
                                    <div>
                                        <div class="volunteer-name">Riley Thompson</div>
                                        <span class="volunteer-email">riley.thompson@email.com</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="clean-time">2 years 4 months</span></td>
                            <td>Male</td>
                            <td>(555) 567-8901</td>
                            <td>
                                <div class="credentials-cell">
                                    <span class="credential-badge">Background</span>
                                    <span class="credential-badge">Reference</span>
                                </div>
                            </td>
                            <td><span class="status-badge status-inactive"><i class="fas fa-times"></i> Inactive</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-view">View</button>
                                    <button class="btn-small btn-edit">Edit</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Volunteer 6 -->
                        <tr>
                            <td>
                                <div class="volunteer-name-cell">
                                    <div class="volunteer-avatar">SA</div>
                                    <div>
                                        <div class="volunteer-name">Sam Anderson</div>
                                        <span class="volunteer-email">sam.anderson@email.com</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="clean-time">1 year 11 months</span></td>
                            <td>Male</td>
                            <td>(555) 678-9012</td>
                            <td>
                                <div class="credentials-cell">
                                    <span class="credential-badge expired">Background (Expired)</span>
                                </div>
                            </td>
                            <td><span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-view">View</button>
                                    <button class="btn-small btn-edit">Edit</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Volunteer 7 -->
                        <tr>
                            <td>
                                <div class="volunteer-name-cell">
                                    <div class="volunteer-avatar">TJ</div>
                                    <div>
                                        <div class="volunteer-name">Taylor Jackson</div>
                                        <span class="volunteer-email">taylor.jackson@email.com</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="clean-time">3 years 7 months</span></td>
                            <td>Female</td>
                            <td>(555) 789-0123</td>
                            <td>
                                <div class="credentials-cell">
                                    <span class="credential-badge">Background</span>
                                    <span class="credential-badge">Reference</span>
                                </div>
                            </td>
                            <td><span class="status-badge status-active"><i class="fas fa-check"></i> Active</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-small btn-view">View</button>
                                    <button class="btn-small btn-edit">Edit</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="volunteers-table-container" style="box-shadow: none; padding: 0;">
                <div class="pagination">
                    <button class="disabled">&laquo; Previous</button>
                    <button class="active">1</button>
                    <button>2</button>
                    <button>3</button>
                    <button>Next &raquo;</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    function applyFilters() {
        const search = document.getElementById('search').value;
        const cleanTime = document.getElementById('clean-time-filter').value;
        const credential = document.getElementById('credential-filter').value;
        const availability = document.getElementById('availability-filter').value;
        const status = document.getElementById('status-filter').value;

        alert('Applying filters: ' + [search, cleanTime, credential, availability, status].filter(f => f).join(', '));
        // In a real app, this would filter the table or submit a form
    }

    function resetFilters() {
        document.getElementById('search').value = '';
        document.getElementById('clean-time-filter').value = '';
        document.getElementById('credential-filter').value = '';
        document.getElementById('availability-filter').value = '';
        document.getElementById('status-filter').value = '';
    }

    // Pagination
    document.querySelectorAll('.pagination button').forEach(button => {
        if (!button.classList.contains('disabled')) {
            button.addEventListener('click', function() {
                if (this.textContent !== 'Previous »' && this.textContent !== 'Next »') {
                    document.querySelectorAll('.pagination button').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        }
    });
</script>
@endsection
