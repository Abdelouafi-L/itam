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
        tr:nth-child(even) { background-color: #f8f9fa; }
        tfoot tr {
            background-color: #e9ecef;
            font-weight: bold;
        }
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
        <h1>ITAM TechCorp — Rapport de Maintenance</h1>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }} — RF-27</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Équipement</th>
                <th>Type</th>
                <th>Technicien</th>
                <th>Date</th>
                <th>Statut</th>
                <th style="text-align:right">Coût (MAD)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($maintenances as $maintenance)
            <tr>
                <td>
                    {{ $maintenance->hardware->product->name ?? '—' }}
                </td>
                <td>{{ $maintenance->type }}</td>
                <td>{{ $maintenance->technician->full_name ?? '—' }}</td>
                <td>{{ $maintenance->date->format('d/m/Y') }}</td>
                <td>{{ $maintenance->status }}</td>
                <td style="text-align:right">
                    {{ $maintenance->cost
                       ? number_format($maintenance->cost, 2) : '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align:right">
                    Coût total:
                </td>
                <td style="text-align:right">
                    {{ number_format($summary['total_cost'], 2) }} MAD
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        ITAM TechCorp — Système de Gestion de Parc Informatique —
        Confidentiel
    </div>
</body>
</html>