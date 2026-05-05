<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            color: #0d6efd;
            margin: 0;
        }
        .header p {
            margin: 4px 0 0;
            color: #666;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        thead tr {
            background-color: #0d6efd;
            color: white;
        }
        th, td {
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .expiring { background-color: #fff3cd; }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #999;
            font-size: 9px;
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ITAM TechCorp — Conformité des Licences</h1>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }} — RF-26</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Logiciel</th>
                <th>Version</th>
                <th>Statut</th>
                <th style="text-align:center">Total</th>
                <th style="text-align:center">Utilisés</th>
                <th style="text-align:center">Disponibles</th>
                <th>Expiration</th>
                <th style="text-align:right">Coût (MAD)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($licenses as $license)
            <tr class="{{ $license->isExpiringSoon() ? 'expiring' : '' }}">
                <td>{{ $license->software->product->name ?? '—' }}</td>
                <td>{{ $license->software->version ?? '—' }}</td>
                <td>{{ $license->status }}</td>
                <td style="text-align:center">
                    {{ $license->seats_total }}
                </td>
                <td style="text-align:center">
                    {{ $license->seats_used }}
                </td>
                <td style="text-align:center">
                    {{ $license->seats_available }}
                </td>
                <td>
                    {{ $license->expiry_date?->format('d/m/Y') ?? '—' }}
                    @if($license->days_remaining !== null)
                        ({{ $license->days_remaining }}j)
                    @endif
                </td>
                <td style="text-align:right">
                    {{ $license->cost
                       ? number_format($license->cost, 2) : '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        ITAM TechCorp — Système de Gestion de Parc Informatique —
        Confidentiel
    </div>
</body>
</html>