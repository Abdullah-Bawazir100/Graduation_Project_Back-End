<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير ملف المكلف</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ae0014;
            --secondary-color: #cc0018;
            --bg-color: #f1f5f9;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
        }

        @page {
            size: A4;
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
            font-weight: 500;
            margin: 0;
        }

        .section {
            background-color: var(--bg-color);
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-right: 4px solid var(--secondary-color);
        }

        .section-title {
            font-size: 16px;
            color: var(--primary-color);
            font-weight: 800;
            margin-bottom: 12px;
            margin-top: 0;
        }

        .grid-2-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 20px;
        }

        .grid-3-cols {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px 15px;
        }

        .detail-item {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            padding: 8px 12px;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .detail-item.full-width {
            grid-column: 1 / -1;
        }

        .detail-label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 700;
            margin-bottom: 3px;
        }

        .detail-value {
            font-size: 14px;
            color: var(--text-main);
            font-weight: 700;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            background-color: #fee2e2;
            color: var(--primary-color);
            font-size: 12px;
            font-weight: 800;
            border: 1px solid #fecaca;
        }

        .badge-success {
            background-color: #f0fdf4;
            color: #166534;
            border-color: #bbf7d0;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: var(--text-muted);
            border-top: 1px solid var(--border-color);
            padding-top: 15px;
        }

        .qr-placeholder {
            width: 60px;
            height: 60px;
            background-color: #e2e8f0;
            border-radius: 4px;
            margin-right: 15px;
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
    @endphp

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1 class="title">نظام خدمات المكلفين</h1>
                <p class="subtitle">تقرير شامل لبيانات ملف المكلف</p>
            </div>
            @if($logoBase64)
                <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo" alt="شعار النظام" />
            @endif
        </div>

        <!-- TaxPayer Info Section -->
        <div class="section">
            <h2 class="section-title">البيانات الأساسية للمكلف</h2>
            <div class="grid-3-cols">
                <div class="detail-item">
                    <span class="detail-label">اسم المكلف الكامل</span>
                    <span class="detail-value">{{ $taxPayerUser->firstName }} {{ $taxPayerUser->lastName }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">الاسم التجاري</span>
                    <span class="detail-value">{{ $taxPayer->tradeName ?? 'غير محدد' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">نوع الملف</span>
                    <span class="detail-value"><span class="badge">{{ $taxPayer->fileType->label() }}</span></span>
                </div>
            </div>
        </div>

        <!-- File Details Section -->
        <div class="section">
            <h2 class="section-title">تفاصيل الملف الضريبي</h2>
            <div class="grid-2-cols">
                <div class="detail-item">
                    <span class="detail-label">الرقم الضريبي</span>
                    <span class="detail-value" style="direction: ltr; color: var(--primary-color);">{{ $file->taxNumber ?? 'غير محدد' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">الرقم الحصري</span>
                    <span class="detail-value" style="direction: ltr;">{{ $file->inventoryNumber }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">حالة الملف</span>
                    <span class="detail-value">
                        <span class="badge badge-success">{{ $file->fileStatus->statusName ?? 'غير محدد' }}</span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">تاريخ بدء النشاط</span>
                    <span class="detail-value" style="direction: ltr;">{{ $file->activityStartDate ?? 'غير محدد' }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">نوع النشاط</span>
                    <span class="detail-value">{{ $file->activityType->name ?? 'غير محدد' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">نوع الدفع</span>
                    <span class="detail-value">{{ $file->paymentType->name ?? 'غير محدد' }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">القسم التابع له</span>
                    <span class="detail-value">{{ $file->department->name ?? 'غير محدد' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">عدد الوثائق</span>
                    <span class="detail-value">{{ $file->docsCount }} وثيقة</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">المنطقة</span>
                    <span class="detail-value">{{ $file->region->name ?? 'غير محدد' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">الحي</span>
                    <span class="detail-value">{{ $file->district->name ?? 'غير محدد' }}</span>
                </div>

                <div class="detail-item full-width">
                    <span class="detail-label">ملاحظات</span>
                    <span class="detail-value" style="font-weight: 500;">{{ $file->note ?? 'لا توجد ملاحظات مسجلة على هذا الملف.' }}</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0;">تم استخراج هذا التقرير آلياً من نظام خدمات المكلفين - جميع الحقوق محفوظة &copy; {{ date('Y') }}</p>
            <p style="margin: 5px 0 0 0; color: #94a3b8;">تاريخ الطباعة: {{ date('Y-m-d') }} | الوقت: {{ date('H:i') }}</p>
        </div>
    </div>
</body>
</html>
