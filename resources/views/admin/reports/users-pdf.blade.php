<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 24px 26px 28px 26px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #0f172a;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.45;
            background: #ffffff;
        }

        .hero {
            margin-bottom: 18px;
            padding: 20px 22px;
            border: 1px solid #cbd5e1;
            border-radius: 18px;
            background: #f8fafc;
        }

        .brand {
            display: inline-block;
            margin-bottom: 10px;
            padding: 6px 12px;
            border-radius: 999px;
            background: #{{ $accent }}22;
            color: #{{ $accent }};
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0 0 6px;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .subtitle {
            margin: 0;
            color: #475569;
            font-size: 11px;
        }

        .meta {
            margin-top: 12px;
            color: #64748b;
            font-size: 10px;
        }

        .cards {
            margin: 0 0 16px;
            font-size: 0;
        }

        .card {
            display: inline-block;
            width: 24%;
            margin-right: 1.333%;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #ffffff;
            vertical-align: top;
        }

        .card:last-child {
            margin-right: 0;
        }

        .card-label {
            margin-bottom: 6px;
            color: #64748b;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .card-value {
            color: #0f172a;
            font-size: 16px;
            font-weight: 800;
        }

        .filters {
            margin-bottom: 16px;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #f8fafc;
        }

        .filters-title {
            margin: 0 0 8px;
            color: #0f172a;
            font-size: 11px;
            font-weight: 800;
        }

        .filter-chip {
            display: inline-block;
            margin: 0 8px 8px 0;
            padding: 6px 10px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #dbeafe;
            color: #1e293b;
            font-size: 10px;
        }

        .filter-chip strong {
            color: #{{ $accent }};
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead {
            display: table-header-group;
        }

        th {
            padding: 10px 8px;
            border: 1px solid #cbd5e1;
            background: #{{ $accent }};
            color: #ffffff;
            font-size: 9px;
            font-weight: 800;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        td {
            padding: 9px 8px;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 10px;
            vertical-align: top;
            word-wrap: break-word;
            white-space: pre-line;
        }

        tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .footer-note {
            margin-top: 12px;
            color: #94a3b8;
            font-size: 9px;
            text-align: right;
        }
    </style>
</head>

<body>
    <section class="hero">
        <div class="brand">Schooli Report</div>
        <h1>{{ $title }}</h1>
        <p class="subtitle">{{ $subtitle }}</p>
        <div class="meta">Generated on {{ $generatedAt }}</div>
    </section>

    @if (!empty($cards))
        <section class="cards">
            @foreach ($cards as $card)
                <div class="card">
                    <div class="card-label">{{ $card['label'] }}</div>
                    <div class="card-value">{{ $card['value'] }}</div>
                </div>
            @endforeach
        </section>
    @endif

    <section class="filters">
        <p class="filters-title">Report Filters</p>
        @if (!empty($filters))
            @foreach ($filters as $filter)
                <span class="filter-chip"><strong>{{ $filter['label'] }}:</strong> {{ $filter['value'] }}</span>
            @endforeach
        @else
            <span class="filter-chip"><strong>Scope:</strong> All records included</span>
        @endif
    </section>

    <table>
        <thead>
            <tr>
                @foreach ($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-note">Prepared from the Schooli admin dashboard.</div>
</body>

</html>
