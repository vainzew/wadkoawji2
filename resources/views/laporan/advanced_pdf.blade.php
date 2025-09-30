<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Advanced Report - {{ ucfirst(str_replace('_', ' ', $reportType)) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .date-range {
            font-size: 12px;
            color: #666;
        }
        .metrics-section {
            margin: 20px 0;
        }
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .metric-row {
            display: table-row;
        }
        .metric-cell {
            display: table-cell;
            width: 25%;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }
        .metric-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        .metric-value {
            font-size: 14px;
            font-weight: bold;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding: 5px 0;
            border-bottom: 1px solid #333;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .payment-item {
            display: table;
            width: 100%;
            margin: 5px 0;
        }
        .payment-method {
            display: table-cell;
            width: 60%;
            padding: 5px;
        }
        .payment-amount {
            display: table-cell;
            width: 40%;
            text-align: right;
            padding: 5px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ config('app.name', 'POS System') }}</div>
        <div class="report-title">Advanced Report - {{ ucfirst(str_replace('_', ' ', $reportType)) }}</div>
        <div class="date-range">Period: {{ date('d M Y', strtotime($tanggalAwal)) }} - {{ date('d M Y', strtotime($tanggalAkhir)) }}</div>
    </div>

    @if($reportType == 'sales_summary')
        <div class="section-title">Sales Summary</div>
        <div class="metrics-grid">
            <div class="metric-row">
                <div class="metric-cell">
                    <div class="metric-label">Gross Sales</div>
                    <div class="metric-value">Rp {{ number_format($metrics['gross_sales'], 0, ',', '.') }}</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-label">Discounts</div>
                    <div class="metric-value">(Rp {{ number_format($metrics['discounts'], 0, ',', '.') }})</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-label">Net Sales</div>
                    <div class="metric-value">Rp {{ number_format($metrics['net_sales'], 0, ',', '.') }}</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-label">Total Collected</div>
                    <div class="metric-value">Rp {{ number_format($metrics['total_collected'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    @endif

    @if($reportType == 'gross_profit')
        <div class="section-title">Gross Profit Analysis</div>
        <div class="metrics-grid">
            <div class="metric-row">
                <div class="metric-cell">
                    <div class="metric-label">Net Sales</div>
                    <div class="metric-value">Rp {{ number_format($metrics['net_sales'], 0, ',', '.') }}</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-label">Cost of Goods</div>
                    <div class="metric-value">Rp {{ number_format($metrics['total_cost'], 0, ',', '.') }}</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-label">Gross Profit</div>
                    <div class="metric-value">Rp {{ number_format($metrics['gross_profit'], 0, ',', '.') }}</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-label">Gross Margin</div>
                    <div class="metric-value">{{ $metrics['gross_margin'] }}%</div>
                </div>
            </div>
        </div>
    @endif

    @if($reportType == 'payment_methods')
        <div class="section-title">Payment Methods Breakdown</div>
        @foreach($metrics['payment_methods'] as $method => $data)
            <div class="payment-item">
                <div class="payment-method">{{ $method }}</div>
                <div class="payment-amount">
                    <strong>Rp {{ number_format($data['total'], 0, ',', '.') }}</strong>
                    ({{ $data['percentage'] }}% - {{ $data['count'] }} transactions)
                </div>
            </div>
        @endforeach
    @endif

    @if($reportType == 'item_sales')
        <div class="section-title">Top 5 Products</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($metrics['top_products'] as $product)
                    <tr>
                        <td>{{ $product->nama_produk }}</td>
                        <td>{{ $product->total_qty }}</td>
                        <td>Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($reportType == 'category_sales')
        <div class="section-title">Top 5 Categories</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($metrics['top_categories'] as $category)
                    <tr>
                        <td>{{ $category->nama_kategori }}</td>
                        <td>{{ $category->total_qty }}</td>
                        <td>Rp {{ number_format($category->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($reportType == 'discounts')
        <div class="section-title">Discounts & Promotions</div>
        <div class="metrics-grid">
            <div class="metric-row">
                <div class="metric-cell">
                    <div class="metric-label">Total Discounts</div>
                    <div class="metric-value">Rp {{ number_format($metrics['discounts'], 0, ',', '.') }}</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-label">Transactions</div>
                    <div class="metric-value">{{ $metrics['total_transactions'] }}</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-label">Average Transaction</div>
                    <div class="metric-value">Rp {{ number_format($metrics['average_transaction'], 0, ',', '.') }}</div>
                </div>
                <div class="metric-cell">
                    <div class="metric-label">-</div>
                    <div class="metric-value">-</div>
                </div>
            </div>
        </div>
    @endif

    <div class="footer">
        Generated on {{ date('d M Y H:i:s') }} | {{ config('app.name', 'POS System') }}
    </div>
</body>
</html>