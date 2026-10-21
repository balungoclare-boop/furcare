<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $reportTitle }} - {{ $clinicName }}</title>
    <style>
        :root {
            --primary: #3B3281;
            --accent: #02BF95;
            --text: #0f172a;
            --muted: #475569;
            --border: #e2e8f0;
            --bg-soft: #f8fafc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            font-family: "Plus Jakarta Sans", "Segoe UI", Arial, sans-serif;
            color: var(--text);
            background: #ffffff;
        }

        .report-shell {
            max-width: 900px;
            margin: 0 auto;
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(120deg, var(--primary), #3B3281 55%, var(--accent));
            color: #ffffff;
            padding: 24px;
        }

        .header .clinic {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.03em;
        }

        .header .title {
            margin: 8px 0 0;
            font-size: 18px;
            font-weight: 700;
        }

        .meta {
            margin-top: 8px;
            font-size: 13px;
            opacity: 0.95;
        }

        .content {
            padding: 24px;
            display: grid;
            gap: 20px;
        }

        .section-title {
            margin: 0 0 10px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .stat {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 14px;
            background: var(--bg-soft);
        }

        .stat .label {
            margin: 0;
            font-size: 12px;
            color: var(--muted);
        }

        .stat .value {
            margin: 4px 0 0;
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
        }

        .cases {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px 12px;
            font-size: 13px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--muted);
            background: #f1f5f9;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .footer {
            border-top: 1px solid var(--border);
            background: #ffffff;
            padding: 14px 24px 20px;
            font-size: 12px;
            color: var(--muted);
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .no-print {
            margin: 12px auto 0;
            max-width: 900px;
            display: flex;
            justify-content: flex-end;
        }

        .no-print button {
            border: 0;
            border-radius: 10px;
            padding: 10px 14px;
            background: var(--primary);
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
        }

        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        @media print {
            body {
                padding: 0;
            }

            .report-shell {
                border: 0;
                border-radius: 0;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <main class="report-shell">
        <header class="header">
            <p class="clinic">{{ $clinicName }}</p>
            <p class="title">{{ $reportTitle }}</p>
            <p class="meta">
                Reporting Period: {{ $periodLabel }}
            </p>
        </header>

        <section class="content">
            <div>
                <h2 class="section-title">Summary</h2>
                <div class="stats">
                    <article class="stat">
                        <p class="label">Total Consultations</p>
                        <p class="value">{{ number_format($summary['totalConsultations']) }}</p>
                    </article>
                    <article class="stat">
                        <p class="label">New Patients</p>
                        <p class="value">{{ number_format($summary['newPatients']) }}</p>
                    </article>
                    <article class="stat">
                        <p class="label">Returning Patients</p>
                        <p class="value">{{ number_format($summary['returningPatients']) }}</p>
                    </article>
                    <article class="stat">
                        <p class="label">Services Rendered</p>
                        <p class="value">{{ number_format($summary['servicesRendered']) }}</p>
                    </article>
                </div>
            </div>

            <div>
                <h2 class="section-title">Common Cases</h2>
                <div class="cases">
                    <table>
                        <thead>
                            <tr>
                                <th>Case</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commonCases as $case)
                                <tr>
                                    <td>{{ $case->case_name }}</td>
                                    <td>{{ number_format($case->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">No consultation records found for the selected period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <footer class="footer">
            <span>Prepared by: {{ $preparedByName }} ({{ $preparedByRole }})</span>
            <span>Date generated: {{ $generatedAt->format('m/d/Y h:i A') }}</span>
        </footer>
    </main>

    <div class="no-print">
        <button type="button" onclick="window.print()">Print</button>
    </div>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
