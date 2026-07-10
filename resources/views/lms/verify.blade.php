<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Certificate Registry Verification - CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD</title>
    <!-- Modern Premium Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090e1a;
            --card-bg: rgba(17, 25, 40, 0.75);
            --border-color: rgba(255, 255, 255, 0.08);
            --primary: #cba135;
            --primary-hover: #b08a28;
            --text-main: #f8fafc;
            --text-secondary: #94a3b8;
            --success: #10b981;
            --danger: #ef4444;
            --glow-color: rgba(203, 161, 53, 0.15);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at 50% 0%, #152035 0%, var(--bg-dark) 70%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            overflow-x: hidden;
        }

        .header {
            width: 100%;
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            border-bottom: 1px solid var(--border-color);
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: var(--text-main);
        }

        .header-logo img {
            max-height: 48px;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
        }

        .header-title {
            font-weight: 700;
            font-size: 18px;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #fff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.2s ease;
            background: rgba(255, 255, 255, 0.03);
            padding: 8px 18px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
        }

        .nav-link:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.08);
        }

        .main-container {
            width: 100%;
            max-width: 650px;
            padding: 40px 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 
                        inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, var(--glow-color) 0%, transparent 50%);
            pointer-events: none;
        }

        .card-header-accent {
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, #f3e5ab 100%);
            border-radius: 2px;
            margin-bottom: 25px;
        }

        h1 {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .description {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-field {
            width: 100%;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 16px 20px;
            font-size: 16px;
            font-weight: 500;
            color: var(--text-main);
            outline: none;
            transition: all 0.3s ease;
            font-family: monospace;
            letter-spacing: 1px;
        }

        .input-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(203, 161, 53, 0.2);
            background: rgba(0, 0, 0, 0.4);
        }

        .hint {
            display: block;
            margin-top: 8px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
            border: none;
            border-radius: 12px;
            color: #000;
            font-size: 15px;
            font-weight: 700;
            padding: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(203, 161, 53, 0.25);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(203, 161, 53, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Result Section Styling */
        .result-container {
            margin-top: 30px;
            border-top: 1px solid var(--border-color);
            padding-top: 30px;
            animation: fadeIn 0.5s ease both;
        }

        .result-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-badge {
            font-size: 13px;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        }

        .status-badge.valid {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-badge.invalid {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .result-list {
            list-style: none;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .result-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            padding-bottom: 12px;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.05);
        }

        .result-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .result-label {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .result-val {
            color: var(--text-main);
            font-weight: 600;
            text-align: right;
        }

        .result-val.highlight {
            color: var(--primary);
            font-family: monospace;
            font-size: 15px;
        }

        .result-val.status-text {
            font-weight: 800;
            text-transform: uppercase;
        }

        .result-val.status-text.valid {
            color: var(--success);
        }

        .result-val.status-text.invalid {
            color: var(--danger);
        }

        .banner-msg {
            margin-top: 20px;
            border-radius: 12px;
            padding: 16px;
            font-size: 13px;
            line-height: 1.5;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .banner-msg.error {
            background: rgba(239, 68, 68, 0.08);
            border-left: 4px solid var(--danger);
            color: #fca5a5;
        }

        .btn-reset {
            display: block;
            text-align: center;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13px;
            margin-top: 25px;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .btn-reset:hover {
            color: var(--text-main);
        }

        .footer {
            width: 100%;
            padding: 30px;
            text-align: center;
            border-top: 1px solid var(--border-color);
            margin-top: auto;
        }

        .footer p {
            font-size: 13px;
            color: var(--text-secondary);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <header class="header">
        <a href="{{ route('lms.home') }}" class="header-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Board Logo">
            <span class="header-title">CPD UK LONDON | CERTIFICATE REGISTRY</span>
        </a>
        <a href="{{ route('lms.home') }}" class="nav-link">Portal Home</a>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        
        <div class="card">
            <div class="card-header-accent"></div>
            
            @if (!$search_performed)
                <h1>Official Credential Registry</h1>
                <p class="description">Verify the matriculation, evaluation records, and authenticity of certificate awards issued by the CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD registry ledger.</p>
                
                <form action="{{ route('lms.verify') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="input-label" for="cert_uid">Certificate Serial ID</label>
                        <div class="input-wrapper">
                            <input class="input-field" type="text" name="cert_uid" id="cert_uid" placeholder="REG-LDN-2026-00001" required style="text-transform: uppercase;">
                        </div>
                        <small class="hint">Formatting example: REG-LDN-[YEAR]-[SERIAL]</small>
                    </div>
                    
                    <button type="submit" class="btn-submit">Query Ledger Registry</button>
                </form>
            @else
                @if ($success)
                    <h1>Award Verification Status</h1>
                    <p class="description">Registry database record verified. The following qualification award has been validated under the public board ledger.</p>
                    
                    <div class="result-container">
                        <div class="result-title">
                            <span>Ledger Status</span>
                            <span class="status-badge valid">Verified Active</span>
                        </div>
                        
                        <ul class="result-list">
                            <li class="result-item">
                                <span class="result-label">Candidate Name</span>
                                <span class="result-val">{{ $result['full_name'] }}</span>
                            </li>
                            <li class="result-item">
                                <span class="result-label">Award Qualification Focus</span>
                                <span class="result-val">{{ $result['course_title'] }}</span>
                            </li>
                            <li class="result-item">
                                <span class="result-label">Verifiable Reference ID</span>
                                <span class="result-val highlight">{{ $result['certificate_uid'] }}</span>
                            </li>
                            <li class="result-item">
                                <span class="result-label">Registry Ledger Status</span>
                                <span class="result-val status-text valid">APPROVED</span>
                            </li>
                            <li class="result-item">
                                <span class="result-label">Registry Issue Date</span>
                                <span class="result-val">{{ $result['issue_date'] }}</span>
                            </li>
                        </ul>
                    </div>
                    
                    <a href="{{ route('lms.verify') }}" class="btn-reset">Validate Another Credential</a>
                @else
                    <h1>Registry Lookup Failed</h1>
                    <p class="description">The query did not yield a valid matriculation award match in the public certificate registry ledger.</p>
                    
                    <div class="banner-msg error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <div>
                            <strong>Registry Error:</strong> {{ $error }}
                        </div>
                    </div>
                    
                    <a href="{{ route('lms.verify') }}" class="btn-reset">Return to Verification Portal</a>
                @endif
            @endif
            
        </div>
        
    </div>

    <!-- Footer Section -->
    <footer class="footer">
        <p>&copy; 2026 CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD. All rights reserved.</p>
    </footer>

</body>
</html>
