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
        .summary {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .summary-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px 12px;
            text-align: center;
        }
        .summary-box .number {
            font-size: 20px;
            font-weight: bold;
            color: #0d6efd;
        }
        .summary-box .label {
            font-size: 9px;
            color: #666;
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
        tfoot tr {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .badge-hardware {
            background: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .badge-software {
            background: #ffc107;
            color: #333;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
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
        <h1>ITAM TechCorp — Inventaire des Actifs</h1>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }} —
           RF-25</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Type</th>
                <th>Marque</th>
                <th>Modèle</th>
                <th style="text-align:center">Total</th>
                <th style="text-align:center">Disponible</th>
                <th style="text-align:center">Affecté</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? '—' }}</td>
                <td>
                    @if($product->hardware)
                        <span class="badge-hardware">Hardware</span>
                    @else
                        <span class="badge-software">Software</span>
                    @endif
                </td>
                <td>{{ $product->brand ?? '—' }}</td>
                <td>{{ $product->model ?? '—' }}</td>
                <td style="text-align:center">
                    {{ $product->stock?->quantity_total ?? 0 }}
                </td>
                <td style="text-align:center">
                    {{ $product->stock?->quantity_available ?? 0 }}
                </td>
                <td style="text-align:center">
                    {{ $product->stock?->quantity_assigned ?? 0 }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align:right">
                    Totaux:
                </td>
                <td style="text-align:center">
                    {{ $products->sum(fn($p) =>
                       $p->stock?->quantity_total ?? 0) }}
                </td>
                <td style="text-align:center">
                    {{ $summary['available'] }}
                </td>
                <td style="text-align:center">
                    {{ $summary['assigned'] }}
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