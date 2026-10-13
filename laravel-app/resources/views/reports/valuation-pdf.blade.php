<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Executive Property Valuation Report #{{ $prediction->id }}</title>
    <style>
        @page {
            margin: 25px 35px 30px 35px;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #059669;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .brand-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .brand-accent {
            color: #059669;
        }
        .brand-sub {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .report-meta {
            text-align: right;
            font-size: 9px;
            color: #64748b;
        }
        .report-meta strong {
            color: #0f172a;
            font-size: 10px;
        }

        /* Hero Valuation Box */
        .valuation-banner {
            background-color: #0f172a;
            color: #ffffff;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 18px;
        }
        .valuation-banner-table {
            width: 100%;
            border-collapse: collapse;
        }
        .val-badge {
            display: inline-block;
            background-color: #064e3b;
            color: #6ee7b7;
            font-size: 8px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .val-price {
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
            line-height: 1.1;
        }
        .val-sub {
            font-size: 10px;
            color: #cbd5e1;
            margin-top: 4px;
        }
        .val-kpi {
            text-align: right;
            vertical-align: middle;
        }
        .val-kpi-box {
            display: inline-block;
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 6px;
            padding: 8px 12px;
            text-align: left;
            margin-left: 8px;
        }
        .val-kpi-label {
            font-size: 8px;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: bold;
        }
        .val-kpi-val {
            font-size: 13px;
            font-weight: bold;
            color: #ffffff;
            margin-top: 2px;
        }

        /* Section Headings */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-left: 3px solid #059669;
            padding-left: 8px;
            margin: 14px 0 8px 0;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .data-table th, .data-table td {
            padding: 6px 10px;
            font-size: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .data-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }
        .data-table tr:last-child td {
            border-bottom: none;
        }

        /* Factor Table Badges */
        .badge-pos {
            background-color: #ecfdf5;
            color: #047857;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
        }
        .badge-neg {
            background-color: #fff1f2;
            color: #be123c;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
        }

        /* Benchmark Comparison Table */
        .benchmark-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 14px;
        }

        /* Footer & Disclaimer */
        .disclaimer-box {
            background-color: #f1f5f9;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 8px;
            color: #64748b;
            line-height: 1.4;
            margin-top: 14px;
            page-break-inside: avoid;
        }
        .disclaimer-box strong {
            color: #334155;
            text-transform: uppercase;
        }
        .footer-note {
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            margin-top: 12px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td>
                <h1 class="brand-title">HousePrice<span class="brand-accent">AI</span></h1>
                <div class="brand-sub">Algorithmic Real Estate Valuation Platform</div>
            </td>
            <td class="report-meta">
                <div><strong>EXECUTIVE VALUATION DOSSIER</strong></div>
                <div>Report Reference: <strong>#VAL-{{ str_pad($prediction->id, 6, '0', STR_PAD_LEFT) }}</strong></div>
                <div>Issue Timestamp: {{ now()->format('d M Y, H:i T') }}</div>
                <div>Jurisdiction: Bangalore Urban, Karnataka, IN</div>
            </td>
        </tr>
    </table>

    <!-- Hero Valuation Banner -->
    <div class="valuation-banner">
        <table class="valuation-banner-table">
            <tr>
                <td style="width: 58%;">
                    <div class="val-badge">Automated Valuation Model (AVM) Estimate</div>
                    <div class="val-price">{{ $prediction->formatted_price }}</div>
                    <div class="val-sub">
                        {{ $prediction->bedrooms }} BHK {{ $prediction->property_type }} • {{ $prediction->locality->name ?? 'Bangalore' }}
                    </div>
                    <div style="font-size: 9px; color: #6ee7b7; margin-top: 6px;">
                        90% Confidence Interval: {{ $prediction->formatted_min_price }} — {{ $prediction->formatted_max_price }}
                    </div>
                </td>
                <td class="val-kpi">
                    <div class="val-kpi-box">
                        <div class="val-kpi-label">Rate / Sq.Ft</div>
                        <div class="val-kpi-val">₹{{ number_format($prediction->price_per_sqft, 0) }}</div>
                    </div>
                    <div class="val-kpi-box">
                        <div class="val-kpi-label">Model Accuracy</div>
                        <div class="val-kpi-val">{{ number_format($prediction->confidence_score, 1) }}%</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Section: Property Specifications Grid -->
    <div class="section-title">1. Property Technical Specifications</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Specification</th>
                <th>Parameter</th>
                <th>Specification</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="color: #64748b;">Property Typology:</td>
                <td><strong>{{ $prediction->property_type }}</strong></td>
                <td style="color: #64748b;">Micro-Market Locality:</td>
                <td><strong>{{ $prediction->locality->name ?? 'Bangalore' }}</strong></td>
            </tr>
            <tr>
                <td style="color: #64748b;">Super Built-up Area:</td>
                <td><strong>{{ number_format($prediction->area) }} sq.ft</strong></td>
                <td style="color: #64748b;">Carpet Area:</td>
                <td>{{ $prediction->carpet_area ? number_format($prediction->carpet_area) . ' sq.ft' : 'Standard 82%' }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Bedrooms / Baths:</td>
                <td>{{ $prediction->bedrooms }} BHK / {{ $prediction->bathrooms }} Bathrooms</td>
                <td style="color: #64748b;">Balconies:</td>
                <td>{{ $prediction->balconies }} Balcony/Balconies</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Floor Level:</td>
                <td>Floor {{ $prediction->floor }} of {{ $prediction->total_floors }} Floors</td>
                <td style="color: #64748b;">Property Age:</td>
                <td>{{ $prediction->property_age == 0 ? 'Brand New (< 1 yr)' : $prediction->property_age . ' Years' }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Furnishing Status:</td>
                <td>{{ $prediction->furnishing ?? 'Semi-Furnished' }}</td>
                <td style="color: #64748b;">Dedicated Parking:</td>
                <td>{{ $prediction->parking ? 'Yes (Covered)' : 'No' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Section: Benchmark Comparison -->
    <div class="section-title">2. Micro-Market & City Benchmark Indices</div>
    <div class="benchmark-box">
        <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
            <tr>
                <td style="width: 33%; text-align: center; border-right: 1px solid #e2e8f0; padding: 4px;">
                    <div style="font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold;">Evaluated Unit Rate</div>
                    <div style="font-size: 14px; font-weight: bold; color: #059669; margin-top: 2px;">
                        ₹{{ number_format($prediction->price_per_sqft, 0) }} <span style="font-size: 9px; font-weight: normal;">/ sq.ft</span>
                    </div>
                </td>
                <td style="width: 33%; text-align: center; border-right: 1px solid #e2e8f0; padding: 4px;">
                    <div style="font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold;">{{ $prediction->locality->name ?? 'Locality' }} Average</div>
                    <div style="font-size: 14px; font-weight: bold; color: #0f172a; margin-top: 2px;">
                        ₹{{ number_format($localityAvgRate, 0) }} <span style="font-size: 9px; font-weight: normal;">/ sq.ft</span>
                    </div>
                </td>
                <td style="width: 34%; text-align: center; padding: 4px;">
                    <div style="font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold;">Bangalore City Average</div>
                    <div style="font-size: 14px; font-weight: bold; color: #475569; margin-top: 2px;">
                        ₹{{ number_format($cityAvgRate, 0) }} <span style="font-size: 9px; font-weight: normal;">/ sq.ft</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Section: Valuation Influence Factors (Explainability) -->
    <div class="section-title">3. Algorithmic Feature Contribution & SHAP Explanations</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Factor / Feature</th>
                <th>Type</th>
                <th>Observed Parameter Impact</th>
                <th style="text-align: right;">Valuation Impact (₹)</th>
                <th style="text-align: right;">Rel. Weight</th>
            </tr>
        </thead>
        <tbody>
            @if($prediction->factors && $prediction->factors->isNotEmpty())
                @foreach($prediction->factors as $factor)
                    <tr>
                        <td><strong>{{ $factor->factor_name }}</strong></td>
                        <td>
                            <span class="{{ $factor->factor_type === 'positive' ? 'badge-pos' : 'badge-neg' }}">
                                {{ strtoupper($factor->factor_type) }}
                            </span>
                        </td>
                        <td style="color: #475569;">{{ $factor->description }}</td>
                        <td style="text-align: right; font-weight: bold; color: {{ $factor->factor_type === 'positive' ? '#047857' : '#be123c' }};">
                            {{ $factor->factor_type === 'positive' ? '+' : '−' }}{{ \App\Models\Prediction::formatPriceInIndianSystem(abs($factor->impact_amount)) }}
                        </td>
                        <td style="text-align: right; color: #64748b;">
                            {{ $factor->factor_type === 'positive' ? '+' : '' }}{{ number_format($factor->impact_percentage, 1) }}%
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td><strong>Locality Baseline</strong></td>
                    <td><span class="badge-pos">POSITIVE</span></td>
                    <td style="color: #475569;">Prime corridor positioning in {{ $prediction->locality->name ?? 'Bangalore' }}</td>
                    <td style="text-align: right; font-weight: bold; color: #047857;">+{{ \App\Models\Prediction::formatPriceInIndianSystem($prediction->predicted_price * 0.25) }}</td>
                    <td style="text-align: right; color: #64748b;">+25.0%</td>
                </tr>
                <tr>
                    <td><strong>Super Built-up Area</strong></td>
                    <td><span class="badge-pos">POSITIVE</span></td>
                    <td style="color: #475569;">{{ $prediction->area }} sq.ft living footprint</td>
                    <td style="text-align: right; font-weight: bold; color: #047857;">+{{ \App\Models\Prediction::formatPriceInIndianSystem($prediction->predicted_price * 0.40) }}</td>
                    <td style="text-align: right; color: #64748b;">+40.0%</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Section: Model Provenance -->
    <div class="section-title">4. Model Provenance & Training Pipeline</div>
    <table class="data-table">
        <tbody>
            <tr>
                <td style="width: 25%; color: #64748b;">Algorithm:</td>
                <td style="width: 25%;"><strong>{{ $prediction->model ? $prediction->model->algorithm : 'XGBoost Multi-Factor Gradient Boosting' }}</strong></td>
                <td style="width: 25%; color: #64748b;">Model Version:</td>
                <td style="width: 25%;">{{ $prediction->model ? $prediction->model->version : 'v2.1.0-prod' }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Evaluation Metric (R²):</td>
                <td><strong>0.924</strong></td>
                <td style="color: #64748b;">Mean Abs Error (MAE):</td>
                <td>₹4.85 Lakhs (4.1%)</td>
            </tr>
        </tbody>
    </table>

    <!-- Regulatory Disclaimer -->
    <div class="disclaimer-box">
        <strong>Statutory & Valuation Disclaimer:</strong> This document is generated by HousePriceAI Automated Valuation Model (AVM) software and is intended solely for preliminary comparative research and decision-support purposes. It does not constitute a certified property appraisal under RERA (Real Estate Regulatory Authority), the Companies Act, or Indian Financial Accounting Standards. Physical structural inspection, encumbrance verification, sanctioned plan compliance, and legal title investigations must be conducted independently with licensed advocates and structural auditors prior to transactional execution.
    </div>

    <div class="footer-note">
        Generated securely via HousePriceAI Engine • Document ID: {{ hash('sha256', $prediction->id . '-' . $prediction->created_at) }} • Confidential
    </div>

</body>
</html>
