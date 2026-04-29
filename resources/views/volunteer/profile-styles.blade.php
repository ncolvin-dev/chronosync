<style>
    .profile-header {
        background: linear-gradient(135deg, #003366 0%, #0099cc 100%);
        color: white;
        padding: 3rem 2rem;
        border-radius: 0.75rem;
        margin-bottom: 2rem;
        text-align: center;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 1rem;
        font-weight: 700;
        border: 3px solid white;
    }

    .profile-name {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .profile-email {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 1rem;
    }

    .status-badges {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .badge-info {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .collapsible-section {
        margin-bottom: 2rem;
    }

    .collapsible-header {
        background-color: #f8f9fa;
        padding: 1.25rem;
        border-radius: 0.5rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        user-select: none;
        transition: background-color 0.3s;
        border: 1px solid #e0e0e0;
    }

    .collapsible-header:hover { background-color: #e9ecef; }

    .collapsible-header.collapsed { border-radius: 0.5rem 0.5rem 0 0; }

    .collapsible-header-title {
        font-weight: 600;
        color: #003366;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .collapsible-header-icon {
        color: #0099cc;
        transition: transform 0.3s;
    }

    .collapsible-header.collapsed .collapsible-header-icon {
        transform: rotate(-90deg);
    }

    .collapsible-content {
        display: none;
        border: 1px solid #e0e0e0;
        border-top: none;
        padding: 1.5rem;
        background-color: white;
        border-radius: 0 0 0.5rem 0.5rem;
    }

    .collapsible-content.show { display: block; }

    .info-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 1.5rem;
    }

    .info-row.full { grid-template-columns: 1fr; }

    .info-item {
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 1rem;
    }

    .info-label {
        font-weight: 600;
        color: #666;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .info-value {
        color: #333;
        font-size: 1rem;
    }

    .edit-form { display: none; }
    .edit-form.show { display: block; }

    .form-group { margin-bottom: 1.5rem; }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control,
    .form-select {
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        padding: 0.75rem;
        width: 100%;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0099cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 153, 204, 0.25);
    }

    .button-group {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .button-group button {
        flex: 1;
        padding: 0.75rem;
        font-weight: 600;
        border-radius: 0.5rem;
        cursor: pointer;
    }

    .btn-primary { background-color: #0099cc; color: white; border: none; }
    .btn-primary:hover { background-color: #003366; }

    .btn-secondary { background-color: #e0e0e0; color: #333; border: none; }
    .btn-secondary:hover { background-color: #d0d0d0; }

    .audit-trail {
        background-color: #f8f9fa;
        border-left: 4px solid #0099cc;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-top: 1rem;
        font-size: 0.875rem;
    }

    .audit-item { margin-bottom: 0.75rem; color: #666; }
    .audit-item:last-child { margin-bottom: 0; }
    .audit-date { font-weight: 600; color: #333; }

    .edit-button-inline {
        background-color: transparent;
        color: #0099cc;
        border: 1px solid #0099cc;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .edit-button-inline:hover { background-color: #0099cc; color: white; }

    /* ── Dark Mode ── */
    html.dark .collapsible-header { background-color: #1a2235; border-color: #2a3a50; }
    html.dark .collapsible-header:hover { background-color: #223044; }
    html.dark .collapsible-header-title { color: #93c5fd; }
    html.dark .collapsible-content { background-color: #141d2e; border-color: #2a3a50; }
    html.dark .info-label { color: #64748b; }
    html.dark .info-value { color: #e2e8f0; }
    html.dark .info-item { border-bottom-color: #2a3a50; }
    html.dark .form-label { color: #cbd5e1; }
    html.dark .form-control,
    html.dark .form-select { background-color: #1a2235; border-color: #2a3a50; color: #e2e8f0; }
    html.dark .btn-secondary { background-color: #2a3a50; color: #cbd5e1; }
    html.dark .btn-secondary:hover { background-color: #3a4a60; }
    html.dark .edit-button-inline { border-color: #38bdf8; color: #38bdf8; }
    html.dark .edit-button-inline:hover { background-color: #38bdf8; color: #0f172a; }
    html.dark .audit-trail { background-color: #1a2235; border-left-color: #0099cc; }
    html.dark .audit-trail > div { color: #e2e8f0 !important; }
    html.dark .audit-date { color: #93c5fd !important; }
    html.dark .audit-item { color: #94a3b8 !important; }
    html.dark .collapsible-content [style*="border-bottom: 1px solid #e0e0e0"] { border-bottom-color: #2a3a50 !important; }
    html.dark .availability-subtitle { color: #94a3b8; }

    @media (max-width: 768px) {
        .profile-header { padding: 2rem 1rem; }
        .status-badges { flex-direction: column; }
        .info-row { grid-template-columns: 1fr; gap: 0; }
        .button-group { flex-direction: column; }
    }
</style>
