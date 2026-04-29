<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
    h1 { color: #003366; font-size: 18px; margin-bottom: 4px; }
    .subtitle { color: #666; font-size: 11px; margin-bottom: 20px; }
    .summary { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .summary td { padding: 12px 16px; border: 1px solid #e0e0e0; text-align: center; }
    .summary .label { font-size: 10px; text-transform: uppercase; color: #999; display: block; margin-bottom: 4px; }
    .summary .value { font-size: 22px; font-weight: bold; color: #003366; }
    .pct { font-size: 11px; color: #666; margin-top: 2px; }
    .footer { margin-top: 30px; font-size: 10px; color: #999; text-align: right; }
</style>
</head>
<body>

<h1>Coverage Report — ChronoSync</h1>
<p class="subtitle">
    Period: {{ $dateFrom->format('M j, Y') }} – {{ $dateTo->format('M j, Y') }}
    &nbsp;|&nbsp; Generated: {{ now()->format('M j, Y g:i A') }}
</p>

<table class="summary">
    <tr>
        <td>
            <span class="label">Total Assignments</span>
            <div class="value">{{ $totalMeetings }}</div>
        </td>
        <td>
            <span class="label">Covered</span>
            <div class="value">{{ $assignedMeetings }}</div>
            <div class="pct">{{ $totalMeetings > 0 ? round(($assignedMeetings / $totalMeetings) * 100, 1) : 0 }}% confirmed / completed</div>
        </td>
        <td>
            <span class="label">Pending / Uncovered</span>
            <div class="value">{{ $unassignedMeetings }}</div>
        </td>
    </tr>
</table>

<p class="footer">ChronoSync &mdash; exported {{ now()->format('Y-m-d H:i') }}</p>
</body>
</html>
