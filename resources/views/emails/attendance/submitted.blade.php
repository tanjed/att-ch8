<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <style>
        /* Reset */
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            height: 100% !important;
            background-color: #f0f4f8;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f0f4f8;
            padding: 40px 0;
        }

        .email-container {
            max-width: 560px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        /* Header */
        .email-header {
            padding: 36px 40px 28px;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .email-header .icon-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .email-header .icon-circle.success {
            background: rgba(72, 199, 142, 0.3);
        }

        .email-header .icon-circle.failed {
            background: rgba(255, 107, 107, 0.3);
        }

        .email-header .icon-circle.skipped {
            background: rgba(255, 193, 7, 0.3);
        }

        .email-header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .email-header .subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            margin-top: 6px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 16px;
        }

        .status-badge.success {
            background: rgba(72, 199, 142, 0.2);
            color: #48c78e;
        }

        .status-badge.failed {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
        }

        .status-badge.skipped {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        /* Body */
        .email-body {
            padding: 32px 40px;
        }

        .greeting {
            font-size: 16px;
            color: #374151;
            margin: 0 0 24px;
            line-height: 1.5;
        }

        .greeting strong {
            color: #1f2937;
        }

        /* Info Cards */
        .info-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .info-card {
            background: #f8fafc;
            border-radius: 10px;
            padding: 14px 18px;
        }

        .info-card .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #9ca3af;
            font-weight: 600;
            margin: 0 0 4px;
        }

        .info-card .value {
            font-size: 15px;
            color: #1f2937;
            font-weight: 600;
            margin: 0;
        }

        /* Response Panel */
        .response-panel {
            margin-top: 24px;
            border-radius: 10px;
            overflow: hidden;
        }

        .response-panel.success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .response-panel.failed {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .response-panel.skipped {
            background: #fffbeb;
            border: 1px solid #fde68a;
        }

        .response-panel-header {
            padding: 14px 18px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .response-panel-header .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .response-panel-header .dot.success {
            background: #22c55e;
        }

        .response-panel-header .dot.failed {
            background: #ef4444;
        }

        .response-panel-header .dot.skipped {
            background: #f59e0b;
        }

        .response-panel-header span {
            font-size: 13px;
            font-weight: 700;
        }

        .response-panel-header span.success {
            color: #16a34a;
        }

        .response-panel-header span.failed {
            color: #dc2626;
        }

        .response-panel-header span.skipped {
            color: #d97706;
        }

        .response-panel-body {
            padding: 10px 18px 14px;
            font-size: 13px;
            line-height: 1.6;
            color: #4b5563;
        }

        .response-code {
            background: rgba(0, 0, 0, 0.04);
            border-radius: 6px;
            padding: 10px 14px;
            font-family: 'SF Mono', 'Fira Code', monospace;
            font-size: 12px;
            color: #6b7280;
            word-break: break-all;
            margin-top: 6px;
        }

        /* Footer */
        .email-footer {
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
        }

        .email-footer .app-name {
            font-size: 14px;
            font-weight: 700;
            color: #667eea;
            margin: 0 0 4px;
        }

        .email-footer .tagline {
            font-size: 12px;
            color: #9ca3af;
            margin: 0;
        }

        .divider {
            height: 1px;
            background: #f1f5f9;
            margin: 16px 0;
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .email-wrapper {
                background-color: #1a1a2e !important;
            }

            .email-container {
                background-color: #16213e !important;
            }

            .greeting,
            .info-card .value {
                color: #e5e7eb !important;
            }

            .info-card {
                background: #1a1a2e !important;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr>
                <td align="center" style="padding: 40px 16px;">
                    <table class="email-container" role="presentation" cellspacing="0" cellpadding="0" border="0"
                        width="560"
                        style="max-width:560px; background:#fff; border-radius:16px; overflow:hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">

                        {{-- ===== HEADER ===== --}}
                        <tr>
                            <td
                                style="padding: 36px 40px 28px; text-align:center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                {{-- Status Icon --}}
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
                                    <tr>
                                        <td style="width:56px; height:56px; border-radius:50%; text-align:center; vertical-align:middle;
                                            @if($status === 'success') background: rgba(72,199,142,0.3);
                                            @elseif($status === 'skipped') background: rgba(255,193,7,0.3);
                                            @else background: rgba(255,107,107,0.3);
                                            @endif
                                        ">
                                            <span style="font-size:26px;">
                                                @if($status === 'success') ✓ @elseif($status === 'skipped') ⏭ @else ✕
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                </table>

                                <h1
                                    style="margin:16px 0 0; color:#fff; font-size:22px; font-weight:700; letter-spacing:-0.3px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                    Attendance Report
                                </h1>
                                <p
                                    style="color:rgba(255,255,255,0.8); font-size:14px; margin:6px 0 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                    {{ now()->format('l, F j, Y') }}
                                </p>

                                {{-- Status Badge --}}
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center"
                                    style="margin-top:16px;">
                                    <tr>
                                        <td style="padding:5px 14px; border-radius:20px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                                            @if($status === 'success') background:rgba(72,199,142,0.2); color:#48c78e;
                                            @elseif($status === 'skipped') background:rgba(255,193,7,0.2); color:#ffc107;
                                            @else background:rgba(255,107,107,0.2); color:#ff6b6b;
                                            @endif
                                        ">
                                            {{ ucfirst($status) }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        {{-- ===== BODY ===== --}}
                        <tr>
                            <td style="padding: 32px 40px;">

                                {{-- Greeting --}}
                                <p
                                    style="font-size:16px; color:#374151; margin:0 0 24px; line-height:1.5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                    Hello <strong style="color:#1f2937;">{{ $setting->user->name }}</strong>, here's
                                    your attendance summary.
                                </p>

                                {{-- Info Cards --}}
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    {{-- Platform --}}
                                    <tr>
                                        <td style="padding-bottom:10px;">
                                            <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                                width="100%">
                                                <tr>
                                                    <td
                                                        style="background:#f8fafc; border-radius:10px; padding:14px 18px;">
                                                        <p
                                                            style="font-size:11px; text-transform:uppercase; letter-spacing:0.6px; color:#9ca3af; font-weight:600; margin:0 0 4px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                                            Platform</p>
                                                        <p
                                                            style="font-size:15px; color:#1f2937; font-weight:600; margin:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                                            {{ $setting->platformAction->platform->name ?? 'N/A' }}</p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    {{-- Action --}}
                                    <tr>
                                        <td style="padding-bottom:10px;">
                                            <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                                width="100%">
                                                <tr>
                                                    <td
                                                        style="background:#f8fafc; border-radius:10px; padding:14px 18px;">
                                                        <p
                                                            style="font-size:11px; text-transform:uppercase; letter-spacing:0.6px; color:#9ca3af; font-weight:600; margin:0 0 4px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                                            Action</p>
                                                        <p
                                                            style="font-size:15px; color:#1f2937; font-weight:600; margin:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                                            {{ $setting->platformAction->name ?? 'N/A' }}</p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    {{-- Scheduled Time --}}
                                    <tr>
                                        <td style="padding-bottom:10px;">
                                            <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                                width="100%">
                                                <tr>
                                                    <td
                                                        style="background:#f8fafc; border-radius:10px; padding:14px 18px;">
                                                        <p
                                                            style="font-size:11px; text-transform:uppercase; letter-spacing:0.6px; color:#9ca3af; font-weight:600; margin:0 0 4px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                                            Scheduled Time</p>
                                                        <p
                                                            style="font-size:15px; color:#1f2937; font-weight:600; margin:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                                            {{ \Carbon\Carbon::parse($setting->target_time)->format('h:i A') }}
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                {{-- ===== RESPONSE PANEL ===== --}}
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                    style="margin-top:24px;">
                                    <tr>
                                        <td style="border-radius:10px; overflow:hidden;
                                            @if($status === 'success') background:#f0fdf4; border:1px solid #bbf7d0;
                                            @elseif($status === 'skipped') background:#fffbeb; border:1px solid #fde68a;
                                            @else background:#fef2f2; border:1px solid #fecaca;
                                            @endif
                                        ">
                                            {{-- Panel Header --}}
                                            <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                                width="100%">
                                                <tr>
                                                    <td style="padding:14px 18px 0;">
                                                        <table role="presentation" cellspacing="0" cellpadding="0"
                                                            border="0">
                                                            <tr>
                                                                <td style="width:8px; height:8px; border-radius:50%;
                                                                    @if($status === 'success') background:#22c55e;
                                                                    @elseif($status === 'skipped') background:#f59e0b;
                                                                    @else background:#ef4444;
                                                                    @endif
                                                                ">&nbsp;</td>
                                                                <td style="padding-left:8px; font-size:13px; font-weight:700; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                                                                    @if($status === 'success') color:#16a34a;
                                                                    @elseif($status === 'skipped') color:#d97706;
                                                                    @else color:#dc2626;
                                                                    @endif
                                                                ">
                                                                    @if($status === 'success')
                                                                        Completed Successfully
                                                                    @elseif($status === 'skipped')
                                                                        Action Skipped
                                                                    @else
                                                                        Execution Failed
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>

                                            {{-- Panel Body --}}
                                            <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                                width="100%">
                                                <tr>
                                                    <td
                                                        style="padding:10px 18px 14px; font-size:13px; line-height:1.6; color:#4b5563; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                                        @if($status === 'success')
                                                            Your attendance action was submitted without any errors. No
                                                            further action is needed.
                                                        @elseif($status === 'skipped')
                                                            {{ substr(is_string($response) ? $response : json_encode($response), 0, 500) }}
                                                        @else
                                                            <p style="margin:0 0 8px;">The server responded with the
                                                                following output:</p>
                                                            <div
                                                                style="background:rgba(0,0,0,0.04); border-radius:6px; padding:10px 14px; font-family:'SF Mono','Fira Code',monospace; font-size:12px; color:#6b7280; word-break:break-all;">
                                                                {{ substr(is_string($response) ? $response : json_encode($response), 0, 500) }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>

                        {{-- ===== FOOTER ===== --}}
                        <tr>
                            <td style="padding:24px 40px; text-align:center; border-top:1px solid #f1f5f9;">
                                <p
                                    style="font-size:14px; font-weight:700; color:#667eea; margin:0 0 4px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                    {{ config('app.name') }}
                                </p>
                                <p
                                    style="font-size:12px; color:#9ca3af; margin:0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                                    Automated Attendance System
                                </p>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>