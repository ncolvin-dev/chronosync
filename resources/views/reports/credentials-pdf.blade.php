<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
    h1 { color: #003366; font-size: 18px; margin-bottom: 4px; }
    .subtitle { color: #666; font-size: 11px; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th { background-color: #f0f4f8; color: #003366; font-size: 11px; text-transform: uppercase;
         padding: 8px 10px; text-align: left; border-bottom: 2px solid #cdd; }
    td { padding: 8px 10px; border-bottom: 1px solid #e0e0e0; }
    tr:nth-child(even) td { background-color: #f9f9f9; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: bold; }
    .expiring-soon { background-color: #fff3cd; color: #856404; }
    .expired       { background-color: #f8d7da; color: #721c24; }
    .empty { text-align: center; color: #999; padding: 20px; }
    .footer { margin-top: 30px; font-size: 10px; color: #999; text-align: right; }
</style>
</head>
<body>

<h1>Expiring Credentials — ChronoSync</h1>
<p class="subtitle">
    Approved credentials expiring within 30 days &nbsp;|&nbsp; Generated: {{ now()->format('M j, Y g:i A') }}
</p>

@if($credentials->isEmpty())
    <p class="empty">No credentials expiring within the next 30 days.</p>
@else
<table>
    <thead>
        <tr>
            <th>Volunteer</th>
            <th>Credential Type</th>
            <th>Expiration Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($credentials as $cred)
            @php
                $isExpired = $cred->expiration_date->isPast();
                $badgeClass = $isExpired ? 'expired' : 'expiring-soon';
                $badgeLabel = $isExpired ? 'Expired' : 'Expiring Soon';
            @endphp
            <tr>
                <td>{{ $cred->volunteer?->first_name }} {{ $cred->volunteer?->last_name }}</td>
                <td>{{ $cred->credentialType?->name ?? '—' }}</td>
                <td>{{ $cred->expiration_date->format('M j, Y') }}</td>
                <td><span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif

<p class="footer">ChronoSync &mdash; exported {{ now()->format('Y-m-d H:i') }}</p>
</body>
</html>
