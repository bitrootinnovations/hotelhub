<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account — HotelHub</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f6fa; color: #333; line-height: 1.6; }
        .wrapper { max-width: 620px; margin: 0 auto; padding: 40px 20px 80px; }

        .header { text-align: center; padding: 40px 0 28px; border-bottom: 1px solid #e0e0e0; margin-bottom: 32px; }
        .logo { font-size: 24px; font-weight: 700; color: #1a73e8; letter-spacing: -0.5px; }
        .logo span { color: #f59e0b; }
        h1 { font-size: 24px; font-weight: 700; color: #c0392b; margin-top: 12px; }
        .subtitle { font-size: 14px; color: #888; margin-top: 6px; }

        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.08); padding: 32px; }

        .info-box { background: #fff8e1; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 14px 18px; margin-bottom: 28px; font-size: 14px; color: #7a5c00; }
        .info-box strong { display: block; margin-bottom: 4px; }

        .success-box { background: #e8f5e9; border-left: 4px solid #27ae60; border-radius: 6px; padding: 24px 28px; text-align: center; }
        .success-box .icon { font-size: 48px; margin-bottom: 12px; }
        .success-box h2 { color: #1e7e34; font-size: 20px; margin-bottom: 8px; }
        .success-box p { color: #444; font-size: 14px; }
        .success-box .ref-email { font-weight: 600; color: #1a73e8; }

        label { display: block; font-size: 14px; font-weight: 600; color: #444; margin-bottom: 5px; }
        .required { color: #e74c3c; margin-left: 2px; }
        .form-group { margin-bottom: 20px; }
        input[type="text"], input[type="email"], input[type="tel"], select, textarea {
            width: 100%; padding: 10px 14px; border: 1.5px solid #ddd; border-radius: 8px;
            font-size: 14px; color: #333; background: #fafafa; transition: border .2s;
            font-family: inherit;
        }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #1a73e8; background: #fff; }
        textarea { resize: vertical; min-height: 90px; }
        .error { color: #e74c3c; font-size: 12px; margin-top: 4px; }

        .checkbox-group { display: flex; align-items: flex-start; gap: 10px; background: #fff3f3; border: 1.5px solid #f5c6c6; border-radius: 8px; padding: 14px 16px; }
        .checkbox-group input[type="checkbox"] { width: 18px; height: 18px; flex-shrink: 0; margin-top: 2px; accent-color: #e74c3c; cursor: pointer; }
        .checkbox-group label { font-weight: 500; font-size: 14px; color: #555; cursor: pointer; margin: 0; }

        .btn-delete { width: 100%; padding: 13px; background: #e74c3c; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; margin-top: 24px; transition: background .2s; letter-spacing: .3px; }
        .btn-delete:hover { background: #c0392b; }
        .btn-delete:disabled { background: #ccc; cursor: not-allowed; }

        .back-link { display: block; text-align: center; margin-top: 20px; font-size: 13px; color: #888; }
        .back-link a { color: #1a73e8; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }

        .footer { text-align: center; margin-top: 32px; font-size: 12px; color: #bbb; }

        .err-list { background: #fff3f3; border-left: 4px solid #e74c3c; border-radius: 6px; padding: 14px 18px; margin-bottom: 24px; }
        .err-list ul { margin: 6px 0 0 16px; }
        .err-list ul li { font-size: 13px; color: #c0392b; margin-bottom: 3px; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <div class="logo">Hotel<span>Hub</span></div>
        <h1>Delete My Account</h1>
        <p class="subtitle">Submit a request to permanently delete your HotelHub account and data</p>
    </div>

    <div class="card">

        @if(session('success'))
        {{-- ── Success state ──────────────────────────────────────────── --}}
        <div class="success-box">
            <div class="icon">✅</div>
            <h2>Request Submitted</h2>
            <p>We have received your account deletion request for<br>
               <span class="ref-email">{{ session('submitted_email') }}</span>
            </p>
            <p style="margin-top:12px;">Our team will process your request within <strong>7 business days</strong> and send a confirmation to your email.</p>
            <p style="margin-top:12px; color:#888; font-size:13px;">If you have any questions, contact us at <a href="mailto:nisarg@bitrootinnovations.com">nisarg@bitrootinnovations.com</a></p>
        </div>

        @else
        {{-- ── Form ───────────────────────────────────────────────────── --}}
        <div class="info-box">
            <strong>⚠ Warning: This action is permanent</strong>
            Deleting your account will permanently remove all your data including orders, menu items, employee records, and settings. This cannot be undone.
        </div>

        @if($errors->any())
        <div class="err-list">
            <strong style="font-size:14px;color:#c0392b;">Please fix the following:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('account-delete.submit') }}">
            @csrf

            <div class="form-group">
                <label for="full_name">Full Name <span class="required">*</span></label>
                <input type="text" id="full_name" name="full_name"
                    value="{{ old('full_name') }}" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">Registered Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email"
                    value="{{ old('email') }}" placeholder="Enter your registered email" required>
            </div>

            <div class="form-group">
                <label for="phone">Registered Phone Number <span style="font-weight:400;color:#aaa;">(optional)</span></label>
                <input type="tel" id="phone" name="phone"
                    value="{{ old('phone') }}" placeholder="e.g. +91 9876543210">
            </div>

            <div class="form-group">
                <label for="reason">Reason for Deletion <span class="required">*</span></label>
                <select id="reason" name="reason" required>
                    <option value="" disabled {{ old('reason') ? '' : 'selected' }}>-- Select a reason --</option>
                    <option value="no_longer_using"   {{ old('reason') == 'no_longer_using'   ? 'selected' : '' }}>No longer using the app</option>
                    <option value="privacy_concerns"  {{ old('reason') == 'privacy_concerns'  ? 'selected' : '' }}>Privacy concerns</option>
                    <option value="switching_service" {{ old('reason') == 'switching_service' ? 'selected' : '' }}>Switching to another service</option>
                    <option value="data_concerns"     {{ old('reason') == 'data_concerns'     ? 'selected' : '' }}>Concerns about data usage</option>
                    <option value="other"             {{ old('reason') == 'other'             ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="notes">Additional Notes <span style="font-weight:400;color:#aaa;">(optional)</span></label>
                <textarea id="notes" name="notes" placeholder="Any additional information you'd like to share...">{{ old('notes') }}</textarea>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="confirm" name="confirm" value="1" {{ old('confirm') ? 'checked' : '' }}>
                <label for="confirm">I understand that deleting my account is <strong>permanent and irreversible</strong>. All my data including orders, menus, employees, and settings will be permanently deleted.</label>
            </div>

            <button type="submit" class="btn-delete" id="submitBtn">
                🗑 Submit Deletion Request
            </button>
        </form>

        @endif

    </div>

    <p class="back-link">
        <a href="{{ route('privacy-policy') }}">Privacy Policy</a> &nbsp;·&nbsp;
        Contact: <a href="mailto:nisarg@bitrootinnovations.com">nisarg@bitrootinnovations.com</a>
    </p>

    <div class="footer">
        &copy; {{ date('Y') }} Bitroot Innovations. All rights reserved.
    </div>

</div>
</body>
</html>
