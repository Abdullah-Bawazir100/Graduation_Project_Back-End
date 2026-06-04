<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير حركة الملفات</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* ألوان مستوحاة من الشعار (أحمر داكن) */
            --primary-color: #ae0014;
            --secondary-color: #cc0018;
            --bg-color: #f1f5f9;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
        }

        @page {
            size: A4 landscape; /* عرضي ليتسع للجدول */
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Almarai', sans-serif;
            background-color: var(--card-bg);
            color: var(--text-main);
            margin: 0;
            padding: 30px;
            font-size: 13px;
            line-height: 1.5;
        }

        .container {
            width: 100%;
            max-width: 100%;
            height: 100%;
            border: 1px solid var(--border-color);
            border-top: 8px solid var(--primary-color);
            border-radius: 12px;
            padding: 30px;
            background: url('data:image/svg+xml;utf8,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" fill="none" stroke="%23ae0014" stroke-width="2" stroke-opacity="0.03"/></svg>') repeat;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header-content {
            flex: 1;
        }

        .logo {
            max-width: 120px;
            max-height: 80px;
            object-fit: contain;
        }

        .title {
            color: var(--primary-color);
            font-size: 24px;
            margin: 0 0 4px 0;
            font-weight: 800;
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 700;
            margin: 0;
        }

        .table-container {
            width: 100%;
            background-color: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }

        th {
            background-color: var(--primary-color);
            color: #ffffff;
            font-weight: 700;
            padding: 12px 10px;
            font-size: 13px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid var(--border-color);
            font-size: 13px;
            font-weight: 400;
            vertical-align: middle;
        }

        tr:nth-child(even) td {
            background-color: var(--bg-color);
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .badge-warning {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .badge-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .badge-primary {
            background-color: #eff6ff;
            color: #1e3a8a;
            border: 1px solid #bfdbfe;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
            border-top: 1px solid var(--border-color);
            padding-top: 15px;
            font-weight: 400;
        }

        .summary {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            border-right: 4px solid var(--secondary-color);
        }

        .summary-item {
            display: flex;
            flex-direction: column;
        }

        .summary-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 700;
        }

        .summary-value {
            font-size: 16px;
            color: var(--primary-color);
            font-weight: 800;
        }

    </style>
</head>
<body>
    @php
        $logoPath = storage_path('app/public/app-logo/TaxLogo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        function getStatusDetails($status) {
            if ($status->value === 'InsideArchive') return ['label' => 'داخل الأرشيف', 'class' => 'badge-success'];
            if ($status->value === 'OutsideArchive') return ['label' => 'خارج الأرشيف', 'class' => 'badge-warning'];
            if ($status->value === 'Missing') return ['label' => 'مفقود', 'class' => 'badge-danger'];
            return ['label' => 'غير معروف', 'class' => 'badge-primary'];
        }
    @endphp

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1 class="title">نظام خدمات المكلفين</h1>
                <p class="subtitle">تقرير شامل بحركة الملفات الضريبية</p>
            </div>
            @if($logoBase64)
                <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo" alt="شعار النظام" />
            @endif
        </div>

        <div class="summary">
            <div class="summary-item">
                <span class="summary-label">إجمالي الحركات</span>
                <span class="summary-value">{{ count($filesMovements) }} حركة</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">تاريخ استخراج التقرير</span>
                <span class="summary-value" style="direction: ltr;">{{ date('Y-m-d') }}</span>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الرقم الحصري</th>
                        <th>المكلف (الاسم التجاري)</th>
                        <th>المأمور</th>
                        <th>القسم</th>
                        <th>حالة الحركة</th>
                        <th>تاريخ الحركة</th>
                        <th>منشئ الحركة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filesMovements as $index => $movement)
                        @php
                            $statusInfo = getStatusDetails($movement->status);
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="direction: ltr; text-align: right; font-weight: 700;">{{ $movement->file->inventoryNumber }}</td>
                            <td>{{ $movement->file->taxPayer->tradeName ?? 'غير محدد' }}</td>
                            <td>{{ $movement->taxCollector->fullName ?? 'غير محدد' }}</td>
                            <td>{{ $movement->department->name ?? 'غير محدد' }}</td>
                            <td><span class="badge {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span></td>
                            <td style="direction: ltr; text-align: right;">{{ $movement->date ? $movement->date->format('Y-m-d') : 'غير محدد' }}</td>
                            <td>{{ $movement->creator ? $movement->creator->firstName . ' ' . $movement->creator->lastName : 'نظام' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0;">تم استخراج هذا التقرير آلياً من نظام خدمات المكلفين حسب صلاحيات المستخدم - جميع الحقوق محفوظة &copy; {{ date('Y') }}</p>
            <p style="margin: 5px 0 0 0; color: #94a3b8;">الوقت: {{ date('H:i') }}</p>
        </div>
    </div>
</body>
</html>
