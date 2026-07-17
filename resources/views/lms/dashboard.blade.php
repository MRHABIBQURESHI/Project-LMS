<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global alert override with SweetAlert2
        window.alert = function(message) {
            var isSuccess = /success|complete|confirmed|verified|approved/i.test(message);
            Swal.fire({
                icon: isSuccess ? 'success' : 'warning',
                title: isSuccess ? 'Confirmation' : 'Registry Notice',
                text: message,
                confirmButtonColor: '#002F6C'
            });
        };
        // Apply saved theme immediately to avoid FOUC
        (function(){var t=localStorage.getItem('lms_theme');if(t)document.documentElement.setAttribute('data-theme',t);})();
    </script>
    
    <!-- ANTI-CHEAT ENGINE (FOR STUDENT TIMED EXAMS) -->
    @if ($currentUser['role'] === 'student' && $currentUser['account_status'] === 'active' && !empty($active_exam))
        @php
            $seconds_left = ($active_exam['duration_minutes'] ?: 120) * 60;
            // Check if there is an in_progress attempt
            $check_term = DB::selectOne("SELECT id, start_time FROM exam_attempts WHERE user_id = ? AND exam_id = ? AND status = 'in_progress'", [$currentUser['id'], $active_exam['id']]);
            if ($check_term) {
                $elapsed = time() - strtotime($check_term->start_time);
                $seconds_left = (($active_exam['duration_minutes'] ?: 120) * 60) - $elapsed;
                if ($seconds_left <= 0) {
                    $seconds_left = 0;
                }
            }
        @endphp
        <style>
            .exam-terminal-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: #ffffff;
                z-index: 9999;
                padding: 40px;
                overflow-y: auto;
                user-select: none;
                -webkit-user-select: none;
            }
            
            .timer-badge {
                position: fixed;
                top: 20px;
                right: 40px;
                background-color: #002F6C;
                color: #ffffff;
                padding: 10px 20px;
                font-size: 20px;
                font-weight: bold;
            }
        </style>
        <script>
            var examTimer;
            var secondsLeft = {{ $seconds_left }};
            var violationsCount = 0;
            var examActive = false;

            window.addEventListener('DOMContentLoaded', (event) => {
                @if ($check_term)
                    if (secondsLeft <= 0) {
                        forceSubmitExam('timeout');
                    } else {
                        startExamEngine();
                    }
                @endif
            });

            function startExamEngine() {
                examActive = true;
                document.getElementById('examTerminal').style.display = 'block';
                document.body.style.overflow = 'hidden';

                examTimer = setInterval(function() {
                    secondsLeft--;
                    var mins = Math.floor(secondsLeft / 60);
                    var secs = secondsLeft % 60;
                    document.getElementById('countdownText').innerText = mins + ":" + (secs < 10 ? "0" : "") + secs;

                    if (secondsLeft <= 0) {
                        clearInterval(examTimer);
                        forceSubmitExam('timeout');
                    }
                }, 1000);

                document.addEventListener('visibilitychange', handleCheatViolation);
                window.addEventListener('blur', handleCheatViolation);
                document.addEventListener('contextmenu', preventDefaultAction);
                document.addEventListener('keydown', handleKeyBlock);
            }

            function preventDefaultAction(e) {
                e.preventDefault();
            }

            function handleKeyBlock(e) {
                if (e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 'x' || e.key === 'u')) {
                    e.preventDefault();
                    alert('Action locked: Copy/Paste shortcut disabled.');
                }
                if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
                    e.preventDefault();
                }
            }

            function handleCheatViolation() {
                if (!examActive) return;
                violationsCount++;
                
                if (violationsCount === 1) {
                    alert('WARNING (Violation 1/2): You switched tabs or exited the exam terminal. The next violation will submit your exam with 0% score and LOCK your account.');
                } else if (violationsCount >= 2) {
                    forceSubmitExam('cheat');
                }
            }

            function forceSubmitExam(reason) {
                examActive = false;
                clearInterval(examTimer);
                
                document.removeEventListener('visibilitychange', handleCheatViolation);
                window.removeEventListener('blur', handleCheatViolation);
                document.removeEventListener('contextmenu', preventDefaultAction);
                document.removeEventListener('keydown', handleKeyBlock);

                if (reason === 'cheat') {
                    document.getElementById('force_submit_flag').value = '1';
                    document.getElementById('violations_field').value = violationsCount;
                    document.getElementById('exam_score_field').value = '0.00';
                    document.getElementById('examForm').submit();
                } else if (reason === 'timeout') {
                    alert('Time expired. Submitting assessment.');
                    document.getElementById('violations_field').value = violationsCount;
                    document.getElementById('exam_score_field').value = '0.00';
                    document.getElementById('examForm').submit();
                }
            }

            function finishExamNormal() {
                var correctAnswers = {
                    'business': ['B', 'B', 'B'],
                    'health': ['B', 'B', 'B'],
                    'nutrition': ['A', 'B', 'B']
                };
                var faculty = '{{ strtolower($enrollment ? ($enrollment["name"] ?? "") : "") }}';
                var correctCount = 0;
                var total = 3;
                
                for (var i = 1; i <= total; i++) {
                    var radios = document.getElementsByName('q' + i);
                    var answered = false;
                    for (var r = 0; r < radios.length; r++) {
                        if (radios[r].checked) {
                            answered = true;
                            if (radios[r].value === correctAnswers[faculty][i-1]) {
                                correctCount++;
                            }
                            break;
                        }
                    }
                    if (!answered) {
                        alert('Please answer Question ' + i + ' before submitting your paper.');
                        return;
                    }
                }
                
                var calculatedScore = (correctCount / total) * 100;
                
                examActive = false;
                clearInterval(examTimer);
                
                document.removeEventListener('visibilitychange', handleCheatViolation);
                window.removeEventListener('blur', handleCheatViolation);
                document.removeEventListener('contextmenu', preventDefaultAction);
                document.removeEventListener('keydown', handleKeyBlock);
                
                document.getElementById('exam_score_field').value = calculatedScore.toFixed(2);
                document.getElementById('violations_field').value = violationsCount;
                document.getElementById('examForm').submit();
            }
        </script>
    @endif
</head>
<body class="db-body">
    <div class="db-layout-container {{ (request()->cookie('db_sidebar_collapsed', '0') === '1') ? 'collapsed' : '' }}">

        <!-- ====================================================================== -->
        <!-- LEFT SIDEBAR PANEL -->
        <!-- ====================================================================== -->
        <aside class="db-sidebar" id="dbSidebar">
            <div class="db-brand" style="display:flex; align-items:center; gap:8px; padding: 15px 15px;">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-height: 42px; object-fit: contain; display: block;">
                <span style="font-weight:700; font-size:12px; color: var(--text-sidebar); line-height: 1.2;">
                    CPD UK LONDON ACADEMIC INSTITUTE
                    <span style="display:block; font-size:8px; color: var(--text-hint); font-weight:normal; margin-top:2px; text-transform:uppercase; letter-spacing:0.3px;">CPD UK LONDON | INTERNATIONAL CERTIFICATION AWARD BOARD</span>
                </span>
            </div>
            
            <ul class="db-nav-menu">
                <li class="db-nav-section-title">Academic Portal</li>
                <li class="db-nav-item {{ ($page === 'dashboard' || empty($page)) ? 'active' : '' }}">
                    <a href="{{ route('lms.dashboard', ['page' => 'dashboard']) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                        <span class="nav-text">Overview</span>
                    </a>
                </li>
                
                @if ($currentUser['role'] === 'student' && $currentUser['account_status'] === 'active')
                    <li class="db-nav-item {{ $page === 'coursework' ? 'active' : '' }}">
                        <a href="{{ route('lms.dashboard', ['page' => 'coursework']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            <span class="nav-text">My Coursework</span>
                        </a>
                    </li>
                    <li class="db-nav-item {{ $page === 'exams' ? 'active' : '' }}">
                        <a href="{{ route('lms.dashboard', ['page' => 'exams']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span class="nav-text">Timed Exams</span>
                        </a>
                    </li>
                    <li class="db-nav-item {{ $page === 'certificates' ? 'active' : '' }}">
                        <a href="{{ route('lms.dashboard', ['page' => 'certificates']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                            <span class="nav-text">Certificates</span>
                        </a>
                    </li>
                    <li class="db-nav-item {{ $page === 'payments' ? 'active' : '' }}">
                        <a href="{{ route('lms.dashboard', ['page' => 'payments']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                            <span class="nav-text">Tuition Payments</span>
                        </a>
                    </li>
                @elseif ($currentUser['role'] === 'admin')
                    <li class="db-nav-item {{ $page === 'students' ? 'active' : '' }}">
                        <a href="{{ route('lms.dashboard', ['page' => 'students']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span class="nav-text">Students Registry</span>
                        </a>
                    </li>
                    <li class="db-nav-item {{ $page === 'courses' ? 'active' : '' }}">
                        <a href="{{ route('lms.dashboard', ['page' => 'courses']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                            <span class="nav-text">Manage Courses</span>
                        </a>
                    </li>
                    <li class="db-nav-item {{ $page === 'exams_report' ? 'active' : '' }}">
                        <a href="{{ route('lms.dashboard', ['page' => 'exams_report']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            <span class="nav-text">Exam Reports</span>
                        </a>
                    </li>
                    <li class="db-nav-item {{ $page === 'certificates_registry' ? 'active' : '' }}">
                        <a href="{{ route('lms.dashboard', ['page' => 'certificates_registry']) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            <span class="nav-text">Certificates Ledger</span>
                        </a>
                    </li>
                @endif
                
                <li class="db-nav-section-title">Regulations</li>
                <li class="db-nav-item {{ $page === 'dispute' ? 'active' : '' }}">
                    <a href="{{ route('lms.dashboard', ['page' => 'dispute']) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        <span class="nav-text">Complaints & Dispute</span>
                    </a>
                </li>
                
                <li class="db-nav-section-title">Account</li>
                <li class="db-nav-item">
                    <a href="{{ route('lms.logout') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:block; flex-shrink:0;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        <span class="nav-text">Sign Out</span>
                    </a>
                </li>
            </ul>

            <div class="db-sidebar-footer">
                <a href="{{ route('lms.dashboard', ['page' => 'profile']) }}" style="text-decoration:none; display:block;">
                    <div class="db-user-profile" style="cursor:pointer;">
                        <div class="db-user-avatar">
                            {{ strtoupper(substr($currentUser['full_name'], 0, 2)) }}
                        </div>
                        <div class="db-user-info">
                            <div class="db-user-name">{{ $currentUser['full_name'] }}</div>
                            <div class="db-user-role">{{ $currentUser['role'] }}</div>
                        </div>
                    </div>
                </a>
            </div>
        </aside>

        <!-- ====================================================================== -->
        <!-- MAIN PANEL CONTENT -->
        <!-- ====================================================================== -->
        <div class="db-main">
            
            <!-- Topbar header -->
            <header class="db-topbar">
                <div style="display: flex; align-items: center;">
                    <button class="db-mobile-toggle" onclick="toggleSidebar()">☰</button>
                </div>
                <div class="db-topbar-actions">
                    <div class="theme-toggle-wrap">
                        <button class="theme-toggle-btn" onclick="toggleThemeDropdown()" title="Switch Theme">
                            🎨 <span style="font-size:12px; font-weight:500;">Theme</span>
                        </button>
                        <div class="theme-dropdown" id="themeDropdown">
                            <button class="theme-option" onclick="setTheme('light')">
                                <span class="theme-option-icon">☀️</span> Light
                            </button>
                            <button class="theme-option" onclick="setTheme('dark')">
                                <span class="theme-option-icon">🌙</span> Dark
                            </button>
                            <button class="theme-option" onclick="setTheme('classic')">
                                <span class="theme-option-icon">📜</span> Classic
                            </button>
                        </div>
                    </div>
                    <div class="db-topbar-icon">
                        🔔<span class="db-topbar-badge"></span>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content wrapper -->
            <div class="db-content">

                @if (!empty($success_msg))
                    <div class="gov-success-banner">
                        <div class="gov-success-title">Success</div>
                        <p>{!! $success_msg !!}</p>
                    </div>
                @endif

                @if (!empty($error_msg))
                    <div class="gov-error-banner">
                        <div class="gov-error-title">Alert</div>
                        <p>{{ $error_msg }}</p>
                    </div>
                @endif

                <!-- ====================================================================== -->
                <!-- ACCOUNT LOCKED GATE -->
                <!-- ====================================================================== -->
                @if ($currentUser['account_status'] === 'locked')
                    <div class="db-card">
                        <div class="gov-error-banner" style="margin-bottom: 0;">
                            <div class="gov-error-title">Account Locked</div>
                            <p>Your student profile has been locked due to an anti-cheat exam violation or administrative hold.</p>
                            <p style="margin-top: 10px; font-weight: 600;">Please contact the registry at registry@cpduk.london to appeal.</p>
                        </div>
                    </div>

                <!-- ====================================================================== -->
                <!-- TUITION PENDING PAYWALL GATE -->
                <!-- ====================================================================== -->
                @elseif ($currentUser['account_status'] === 'pending_manual_unlock')
                    <div class="db-card">
                        <div class="gov-error-banner" style="border-color: #f47738; margin-bottom: 25px;">
                            <div class="gov-error-title" style="color: #f47738;">Account Pending Tuition Payment Verification</div>
                            <p>To access your coursework catalog, modules, and exam terminal, you must complete your registration tuition fee of <strong>$450.00</strong>.</p>
                        </div>

                        <div class="gov-grid-row">
                            <div class="gov-grid-column-two-thirds">
                                <h2>Manual Remittance Verification Gate</h2>
                                <p>To complete your transaction, please contact your authorized regional representative or email accounts@cpduk.london to request a secure, single-use active recipient allocation token. Once sent, submit the reference details below for confirmation.</p>

                                <form action="{{ route('lms.remittance') }}" method="POST" style="background-color:#fafcff; padding: 25px; border: 2px solid #002F6C; border-radius:4px; margin-bottom: 20px;">
                                    @csrf
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="sender_name">Sender name</label>
                                        <input class="gov-input" id="sender_name" name="sender_name" type="text" style="max-width:100%;" required>
                                    </div>

                                    <div class="gov-form-group">
                                        <label class="gov-label" for="method">Money Order Provider</label>
                                        <select class="gov-select" id="method" name="method" style="max-width:100%;" required>
                                            <option value="">-- Choose Provider --</option>
                                            <option value="western_union">Western Union</option>
                                            <option value="ria">Ria Money Transfer</option>
                                            <option value="worldremit">WorldRemit</option>
                                        </select>
                                    </div>

                                    <div class="gov-form-group">
                                        <label class="gov-label" for="transaction_ref">Reference MTCN / Code</label>
                                        <input class="gov-input" id="transaction_ref" name="transaction_ref" type="text" style="max-width:100%;" required>
                                    </div>

                                    <div class="gov-form-group">
                                        <label class="gov-label" for="amount">Amount Remitted ($)</label>
                                        <input class="gov-input" id="amount" name="amount" type="number" step="0.01" value="450.00" style="max-width:100%;" required>
                                    </div>

                                    <button type="submit" class="gov-button" style="width: 100%;">Submit payment reference</button>
                                </form>
                            </div>

                            <div class="gov-grid-column-one-third" style="border-left: 1px solid #EBF3FC; padding-left: 20px;">
                                <h2>Online Card Checkout</h2>
                                <p style="font-size:15px; color:#555; line-height: 1.5;">Alternatively, pay securely with your debit or credit card for instant catalog access.</p>
                                <a href="{{ route('lms.checkout') }}" class="gov-button" style="width:100%; margin-top: 15px; text-decoration:none; text-align:center; display:block;">Unlock instantly with Card</a>
                            </div>
                        </div>
                    </div>

                <!-- ====================================================================== -->
                <!-- STUDENT PORTAL CORE VIEW -->
                <!-- ====================================================================== -->
                @elseif ($currentUser['role'] === 'student')
                    
                    @if ($page === 'dashboard' || empty($page))
                        <!-- Metrics grid -->
                        <div class="db-stat-grid">
                            <div class="db-stat-card">
                                <div class="db-stat-icon">📚</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value">{{ count($modules) }}</div>
                                    <div class="db-stat-label">Total Modules</div>
                                </div>
                            </div>
                            <div class="db-stat-card">
                                <div class="db-stat-icon">📝</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value">{{ count($assignments_uploaded) }}</div>
                                    <div class="db-stat-label">Coursework Uploads</div>
                                </div>
                            </div>
                            <div class="db-stat-card">
                                <div class="db-stat-icon">⏱️</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value">{{ count($exam_results) > 0 ? end($exam_results)['score'] . '%' : 'No Attempt' }}</div>
                                    <div class="db-stat-label">Exam Score</div>
                                </div>
                            </div>
                            <div class="db-stat-card">
                                <div class="db-stat-icon">🏆</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value">{{ count($certificates) }}</div>
                                    <div class="db-stat-label">Certificates Issued</div>
                                </div>
                            </div>
                        </div>

                        <div class="gov-grid-row">
                            <div class="gov-grid-column-two-thirds">
                                <!-- Enrollment card -->
                                <div class="db-card">
                                    <div class="db-card-title">Enrolled Academic Program</div>
                                    <p style="font-size:16px; font-weight:600; color:#002F6C; margin-bottom: 5px;">Faculty of {{ $enrollment ? $enrollment['name'] : "Not Assigned" }}</p>
                                    <p class="gov-hint" style="margin-bottom:15px;">Registered Student ID: LIAB-ST-{{ $currentUser['id'] }}</p>
                                    <p style="font-size:14px; line-height:1.5; margin-bottom: 10px;">Welcome to your academic terminal! Please use the left-hand navigation links to access your coursework modules, launch the timed exams terminal, download your certificates, or check your billing transaction logs.</p>
                                </div>

                                <div class="db-card">
                                    <div class="db-card-title">Portal Quick Start Guide</div>
                                    <ul style="font-size:14px; line-height:1.8; color:var(--text-primary); margin-left:20px; list-style-type: disc;">
                                        <li><strong>Coursework:</strong> Review the universal and faculty coursework modules, and submit your homework assignments for evaluation.</li>
                                        <li><strong>Timed Exam:</strong> Once you are ready, start your comprehensive timed assessment exam (2-hour limit). Ensure you maintain window focus, as switching tabs will trigger security lockouts.</li>
                                        <li><strong>Certificate:</strong> A Gold Crest verifiable diploma certificate is generated automatically upon passing the final assessment with a grade of 50% or higher.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="gov-grid-column-one-third" style="border-left: 1px solid #EBF3FC; padding-left: 20px;">
                                <div class="db-card">
                                    <div class="db-card-title" style="font-size: 16px;">Representative Rep Code</div>
                                    <p style="font-size:13px; color:#555;">Linked Affiliate Consultant:</p>
                                    <div style="background-color:#f6f8fa; padding:10px; border-radius:4px; font-size:13px; font-weight:600; margin-top:8px; display:inline-block;">
                                        {{ $currentUser['rep_code'] ?: 'Independent Direct Signup' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                    @elseif ($page === 'coursework')
                        <!-- Course Modules card -->
                        <div class="db-card" id="modulesSection">
                            <div class="db-card-title">Coursework Modules & Submissions</div>
                            <p style="font-size:14px; margin-bottom: 20px;">Complete modules 1 & 2 (universal) and modules 3 & 4 (faculty-specific). Upload coursework files here (Max 25MB, PDF/DOCX/JPG/PNG).</p>

                            <div class="gov-list-group" style="margin-top: 10px;">
                                @foreach ($modules as $mod)
                                    <div class="gov-list-row" style="flex-direction: column; align-items: flex-start; gap: 12px; padding: 20px 0;">
                                        <div style="display:flex; justify-content:space-between; width:100%;">
                                            <span class="gov-list-key">Module {{ $mod['module_number'] }}: {{ $mod['title'] }}</span>
                                            <div>
                                                @if ($mod['faculty_id'] === NULL)
                                                    <span class="gov-tag gov-tag-grey" style="font-size:10px;">Universal</span>
                                                @else
                                                    <span class="gov-tag" style="font-size:10px;">Faculty Focus</span>
                                                @endif
                                            </div>
                                        </div>

                                        <p style="font-size: 14px; margin-bottom:5px; color:#555;">{{ $mod['content_path'] }}</p>

                                        @if (isset($assignments_uploaded[$mod['id']]))
                                            @php $sub = $assignments_uploaded[$mod['id']]; @endphp
                                            <div style="font-size: 13px; background-color:#fafbfe; padding:10px; width:100%; border-left: 3px solid #002F6C; border-radius: 4px;">
                                                Uploaded Document: <a href="{{ asset($sub['file_path']) }}" target="_blank">{{ basename($sub['file_path']) }}</a> ({{ $sub['file_size'] }})<br>
                                                Status: <strong>{{ strtoupper($sub['status']) }}</strong> 
                                                @if ($sub['status'] === 'reviewed')
                                                    | Grade: <strong style="color:#00703c;">{{ $sub['grade'] }}</strong>
                                                @endif
                                                @if (!empty($sub['feedback']))
                                                    <br><strong>Feedback:</strong> <em>{{ $sub['feedback'] }}</em>
                                                @endif
                                            </div>
                                        @else
                                            <span class="gov-tag gov-tag-grey" style="font-size: 10px;">Awaiting Submission</span>
                                        @endif

                                        @if (!empty($phase2_locked) && $mod['phase'] === 'II')
                                            <div style="background-color: #fff9e6; color: #663c00; font-size:12px; padding: 10px; border-radius: 4px; width: 100%; margin-top: 8px;">
                                                ⚠️ This specialty module is locked under the 14-day speed trap protocol. Access opens on Day 15 (or request a manual override from the academic committee).
                                            </div>
                                        @else
                                            <!-- Upload form -->
                                            <form action="{{ route('lms.dashboard') }}?page=coursework" method="POST" enctype="multipart/form-data" style="display:flex; align-items:center; gap: 15px; width:100%; margin-top: 8px;">
                                                @csrf
                                                <input type="hidden" name="module_id" value="{{ $mod['id'] }}">
                                                <input type="file" name="assignment_file" required style="font-size:13px;">
                                                <button type="submit" name="upload_assignment" class="gov-button" style="font-size:12px; padding: 6px 12px; border-radius: 4px;">Upload Assignment</button>
                                            </form>
                                        @endif

                                    </div>
                                @endforeach
                            </div>
                        </div>

                    @elseif ($page === 'exams')
                        <!-- Exams card -->
                        <div class="db-card" id="examsSection">
                            <div class="db-card-title">Faculty Timed Assessment</div>
                            <p style="font-size: 14px; margin-bottom: 20px;">Complete your timed examination. Anti-cheat visibility tracking metrics are active. Minimum passing grade is 50%.</p>

                            <div class="gov-list-group" style="margin-top: 10px; margin-bottom: 0;">
                                @if (empty($active_exam))
                                    <p class="gov-hint">No examinations configured for this faculty.</p>
                                @else
                                    <div class="gov-list-row" style="padding: 15px 0; border-bottom: none; flex-direction:column; align-items:flex-start; gap:12px;">
                                        <div style="display:flex; justify-content:space-between; width:100%; align-items:center;">
                                            <div>
                                                <span class="gov-list-key">Timed Comprehensive Assessment</span>
                                                <span class="gov-hint" style="margin-top: 5px;">Pass Threshold: 50% | Duration: {{ $active_exam['duration_minutes'] }} mins</span>
                                            </div>
                                            <div>
                                                @if (!empty($exam_results))
                                                    @php $latest_attempt = end($exam_results); @endphp
                                                    <span class="gov-tag {{ $latest_attempt['score'] >= 50 ? 'gov-tag-green' : 'gov-tag-yellow' }}">
                                                        Score: {{ $latest_attempt['score'] }}% ({{ strtoupper($latest_attempt['status']) }})
                                                    </span>
                                                @else
                                                    <span class="gov-tag gov-tag-grey" style="text-transform:none;">No Attempts Completed</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div style="width:100%; margin-top: 10px;">
                                            @if ($exam_passed)
                                                <div style="background-color: #fafcff; padding: 20px; border-left: 5px solid #00703c; border-radius: 4px; width:100%;">
                                                    <h3 style="color:#00703c; margin-bottom:8px; font-size:14px; font-weight:bold;">✓ Assessment Passed & Locked</h3>
                                                    <p style="font-size:12px; color:#555; margin-bottom:0; line-height:1.45;">You have successfully passed the final assessment with a score of 50% or higher. Your result is locked and your certificate has been awarded. You can view or download it from the Certificates tab.</p>
                                                </div>
                                            @elseif ($exam_failed)
                                                @if ($resit_unlocked)
                                                    <div style="margin-bottom:12px; font-size:13px; color:#00703c; font-weight:600;">✓ Exam Resit eligibility unlocked. Ready to start attempt.</div>
                                                    <form action="{{ route('lms.dashboard') }}?page=exams" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="start_exam_attempt" value="1">
                                                        <input type="hidden" name="exam_id" value="{{ $active_exam['id'] }}">
                                                        <button type="submit" class="gov-button" style="font-size:12px; padding: 8px 16px; border-radius: 4px;">Retake Assessment Now</button>
                                                    </form>
                                                @else
                                                    <!-- Render Resit Paywall Form -->
                                                    <div style="background-color: #fafbfe; padding: 20px; border-left: 5px solid #d4351c; border-radius: 4px; width:100%;">
                                                        <h3 style="color:#d4351c; margin-bottom:8px; font-size:14px;">Assessment Resit Paywall</h3>
                                                        <p style="font-size:12px; color:#555; margin-bottom:15px; line-height:1.45;">You did not achieve the required passing threshold of 50% on your exam attempt. To reactivate the assessment terminal and try again, you must process the Board Resit Fee of <strong>£229.00</strong>.</p>
                                                        
                                                        <form action="{{ route('lms.dashboard') }}?page=exams" method="POST" style="max-width:360px;">
                                                            @csrf
                                                            <input type="hidden" name="pay_resit_fee" value="1">
                                                            <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:12px;">
                                                                <input class="gov-input" type="text" name="card_holder" placeholder="Cardholder Name" required style="font-size:11px; padding:6px; border:1px solid #ccc; max-width:100%;">
                                                                <input class="gov-input" type="text" name="card_number" placeholder="Card Number" required style="font-size:11px; padding:6px; border:1px solid #ccc; max-width:100%;">
                                                                <div style="display:flex; gap:6px;">
                                                                    <input class="gov-input" type="text" name="card_exp" placeholder="MM/YY" required style="font-size:11px; padding:6px; border:1px solid #ccc; width:60%; max-width:100%;">
                                                                    <input class="gov-input" type="text" name="card_cvc" placeholder="CVC" required style="font-size:11px; padding:6px; border:1px solid #ccc; width:40%; max-width:100%;">
                                                                </div>
                                                            </div>
                                                            <button type="submit" class="gov-button" style="font-size:11px; padding: 8px 16px; border-radius: 4px; background-color:#00703c; border-bottom:none;">Pay £229 Resit Fee</button>
                                                        </form>
                                                    </div>
                                                @endif
                                            @else
                                                <!-- First attempt button -->
                                                @if (!empty($phase2_locked))
                                                    <div style="background-color: #fff9e6; color: #663c00; font-size:12px; padding: 15px; border-radius: 4px; width: 100%; border-left: 5px solid #f47738; margin-top: 10px;">
                                                        ⚠️ <strong>Examination Terminal Locked:</strong> The assessment terminal is restricted under the 14-day speed-protection control. Access is allowed from Day 15 of enrollment (or upon manual expedition by the Academic Committee).
                                                    </div>
                                                @else
                                                    <form action="{{ route('lms.dashboard') }}?page=exams" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="start_exam_attempt" value="1">
                                                        <input type="hidden" name="exam_id" value="{{ $active_exam['id'] }}">
                                                        <button type="submit" class="gov-button" style="font-size:12px; padding: 8px 16px; border-radius: 4px;">Start Assessment Now</button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @elseif ($page === 'certificates')
                        <!-- Certificate card -->
                        <div class="db-card" id="certsSection">
                            <div class="db-card-title">Verifiable Issued Certificate Credentials</div>
                            <div class="gov-list-group" style="margin-top: 10px; margin-bottom: 0;">
                                @if (empty($certificates))
                                    <p class="gov-hint" style="padding: 10px 0;">No certificates issued yet. Pass your exam with 50% or more to unlock.</p>
                                @else
                                    @foreach ($certificates as $c)
                                        <div class="gov-list-row" style="padding: 15px 0;">
                                            <div>
                                                <span class="gov-list-key">Faculty Diploma Certificate</span>
                                                <span class="gov-hint" style="margin-top: 5px;">UID Reference: <code>{{ $c['certificate_uid'] }}</code> | Issued: {{ $c['issue_date'] }}</span>
                                            </div>
                                            <div style="display:flex; gap:10px;">
                                                <a href="{{ route('lms.certificate', ['uid' => $c['certificate_uid']]) }}" target="_blank" class="gov-button" style="font-size:12px; padding: 6px 12px; border-radius: 4px; text-decoration:none; text-align:center;">View & Print</a>
                                                <a href="{{ asset($c['pdf_path']) }}" download class="gov-button gov-button-secondary" style="font-size:12px; padding: 6px 12px; border-radius: 4px; text-decoration:none; text-align:center;">Download PDF</a>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                    @elseif ($page === 'payments')
                        <!-- Payment history -->
                        <div class="db-card">
                            <div class="db-card-title" style="font-size: 18px; margin-bottom: 15px;">Tuition Payment History & Status</div>
                            
                            <div class="gov-grid-row">
                                <div class="gov-grid-column-two-thirds">
                                    <div style="background-color: #fafbfe; padding: 20px; border: 1.5px solid #EBF3FC; border-radius: 8px; margin-bottom:15px;">
                                        @if (empty($payments_history))
                                            <span class="gov-hint">No transactions registered.</span>
                                        @else
                                            <table class="gov-table" style="margin:0;">
                                                <thead>
                                                    <tr>
                                                        <th>Billing Ref</th>
                                                        <th>Amount</th>
                                                        <th>Service type</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($payments_history as $p)
                                                        <tr>
                                                            <td><code>{{ $p['transaction_ref'] ?: 'N/A' }}</code></td>
                                                            <td><strong>${{ number_format($p['amount'], 2) }}</strong></td>
                                                            <td>{{ strtoupper($p['type']) }}</td>
                                                            <td>
                                                                <span class="gov-tag {{ $p['status'] === 'paid' ? 'gov-tag-green' : 'gov-tag-yellow' }}" style="font-size:10px; padding:2px 6px;">
                                                                    {{ $p['status'] }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>

                                <div class="gov-grid-column-one-third">
                                    @if ($is_installment_plan && $installments_paid < 3)
                                        <div style="background-color: #fafcff; padding: 20px; border: 1.5px solid #002F6C; border-radius: 8px;">
                                            <div style="font-size:14px; font-weight:600; color:#002F6C; margin-bottom:8px;">Installment Tuition Status</div>
                                            <p style="font-size:13px; margin-bottom:15px; color:#555; line-height:1.45;">Paid: {{ $installments_paid }} of 3 installments.<br>Remaining balance: <strong>£{{ (3 - $installments_paid) * 749 }}.00</strong></p>
                                            
                                            <form action="{{ route('lms.dashboard') }}?page=payments" method="POST">
                                                @csrf
                                                <input type="hidden" name="pay_installment" value="1">
                                                <input type="hidden" name="installment_number" value="{{ $installments_paid + 1 }}">
                                                
                                                <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:12px;">
                                                    <input type="text" name="card_holder" placeholder="Cardholder Name" required style="width:100%; font-size:12px; padding:8px; border:1px solid #ccc; border-radius:4px;">
                                                    <input type="text" name="card_number" placeholder="Card Number" required style="width:100%; font-size:12px; padding:8px; border:1px solid #ccc; border-radius:4px;">
                                                    <div style="display:flex; gap:6px;">
                                                        <input type="text" name="card_exp" placeholder="MM/YY" required style="width:60%; font-size:12px; padding:8px; border:1px solid #ccc; border-radius:4px;">
                                                        <input type="text" name="card_cvc" placeholder="CVC" required style="width:40%; font-size:12px; padding:8px; border:1px solid #ccc; border-radius:4px;">
                                                    </div>
                                                </div>
                                                <button type="submit" class="gov-button" style="width:100%; font-size:12px; padding:10px; border-radius:4px;">Pay Installment {{ $installments_paid + 1 }} (£749.00)</button>
                                            </form>
                                        </div>
                                    @else
                                        <div style="background-color: #fafcff; padding: 20px; border: 1.5px solid #00703c; border-radius: 8px; font-size:13px; color:#00703c; font-weight:600;">
                                            ✓ All program tuition fee parameters are paid in full. No outstanding balance.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    @endif

                    <!-- TIMED EXAM OVERLAY TERMINAL -->
                    @if (!empty($active_exam))
                    <div id="examTerminal" class="exam-terminal-overlay">
                        <div class="timer-badge">Remaining: <span id="countdownText">120:00</span></div>
                        <h1 style="border-bottom: 2px solid #002F6C; padding-bottom: 10px; margin-bottom: 30px;">Timed Faculty Evaluation</h1>
                        
                        <div class="gov-error-banner" style="border-color: #f47738; margin-bottom: 30px;">
                            <div class="gov-error-title" style="color: #f47738;">Anti-Cheat Warning</div>
                            <p style="font-size:16px;">This assessment window is monitored. Exiting fullscreen, minimizing, switching tabs, or copying text will cancel the session, lock your profile, and record a 0% failure. contextmenu controls have been locked.</p>
                        </div>

                        <form id="examForm" action="{{ route('lms.dashboard') }}" method="POST">
                            @csrf
                            <input type="hidden" name="submit_exam_score" value="1">
                            <input type="hidden" name="exam_id" value="{{ $active_exam['id'] }}">
                            <input type="hidden" id="exam_score_field" name="exam_score" value="0">
                            <input type="hidden" id="violations_field" name="violations" value="0">
                            <input type="hidden" id="force_submit_flag" name="force_submit_violation" value="0">

                            <!-- Dynamic exam questions based on Faculty -->
                            @php $fac_name_lower = strtolower($enrollment ? ($enrollment['name'] ?? "") : ""); @endphp
                            @if ($fac_name_lower === 'business')
                                <div class="gov-form-group">
                                    <label class="gov-label" style="font-size:16px;">Question 1: Which core document outlines business regulations and research ethics?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="A" required> A. Standard Ledger Guide</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="B"> B. Orientation Ethics Guide</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="C"> C. Financial Audit Manual</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="D"> D. Code of Business Conduct</label>
                                    </div>
                                </div>
                                <div class="gov-form-group" style="margin-top:25px;">
                                    <label class="gov-label" style="font-size:16px;">Question 2: What defines strategic human resource compliance in human capital?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="A" required> A. Setting standardized payroll metrics</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="B"> B. Aligning workforce protocols with organizational ethics and goals</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="C"> C. Implementing automated contractor shifts</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="D"> D. Daily employee time logging audits</label>
                                    </div>
                                </div>
                                <div class="gov-form-group" style="margin-top:25px;">
                                    <label class="gov-label" style="font-size:16px;">Question 3: Which protocol is used to evaluate startup financial viability?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="A" required> A. Ledger double-entry checking</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="B"> B. Net Present Value (NPV) and operational break-even analysis</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="C"> C. Cash count index checking</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="D"> D. Rep code referral tracking</label>
                                    </div>
                                </div>
                            @elseif ($fac_name_lower === 'health')
                                <div class="gov-form-group">
                                    <label class="gov-label" style="font-size:16px;">Question 1: What is the primary procedure for clinical contamination safety?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="A" required> A. Wiping surfaces once daily</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="B"> B. Multi-barrier isolation and strict sterile field protocols</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="C"> C. Maintaining open ventilation parameters</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="D"> D. Standard medical gloves audits</label>
                                    </div>
                                </div>
                                <div class="gov-form-group" style="margin-top:25px;">
                                    <label class="gov-label" style="font-size:16px;">Question 2: What does HIPAA require for digital patient record tracking?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="A" required> A. Maintaining printed files in registry folders</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="B"> B. End-to-end audit logs, access tracking, and storage encryption</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="C"> C. Sharing registry files with authorized rep consultants</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="D"> D. Storing clinical dossiers in local PC directories</label>
                                    </div>
                                </div>
                                <div class="gov-form-group" style="margin-top:25px;">
                                    <label class="gov-label" style="font-size:16px;">Question 3: What defines an epidemiological outbreak audit workflow?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="A" required> A. Reviewing daily pharmacy medicine logs</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="B"> B. Tracing index cases, auditing compliance, and setting quarantine guidelines</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="C"> C. Dispatching public health warning leaflets</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="D"> D. Surveying community hospital numbers</label>
                                    </div>
                                </div>
                            @else <!-- Nutrition -->
                                <div class="gov-form-group">
                                    <label class="gov-label" style="font-size:16px;">Question 1: What cellular process is directly regulated by micronutrient profiles?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="A" required> A. Digestion enzyme activation and mitochondrial respiration cofactors</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="B"> B. Standard muscular tissue ATP contractions</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="C"> C. Pancreas insulin synthesis pathways</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q1" value="D"> D. Cell membrane fatty acid balance</label>
                                    </div>
                                </div>
                                <div class="gov-form-group" style="margin-top:25px;">
                                    <label class="gov-label" style="font-size:16px;">Question 2: Which profile is recommended for a clinical cardiovascular management audit?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="A" required> A. High sucrose carbohydrate loading</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="B"> B. Low sodium DASH diet rich in magnesium and omega-3 fatty acids</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="C"> C. Pure plant proteins loading profile</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q2" value="D"> D. Intermittent liquid fasting protocols</label>
                                    </div>
                                </div>
                                <div class="gov-form-group" style="margin-top:25px;">
                                    <label class="gov-label" style="font-size:16px;">Question 3: What represents the highest level of nutritional research verification?</label>
                                    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px; font-size:14px; color:var(--text-primary);">
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="A" required> A. Individual patient case diaries</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="B"> B. Randomized Double-Blind Controlled Trials and Systematic Meta-Analyses</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="C"> C. Peer review nutrition guides</label>
                                        <label style="cursor:pointer;"><input type="radio" name="q3" value="D"> D. University focus research panels</label>
                                    </div>
                                </div>
                            @endif

                            <div style="margin-top: 40px; border-top: 1.5px solid var(--border-main); padding-top: 20px;">
                                <button type="button" onclick="finishExamNormal()" class="gov-button" style="border-radius:6px; padding:12px 30px;">Submit Exam Paper</button>
                            </div>
                        </form>
                    </div>
                    @endif

                <!-- ====================================================================== -->
                <!-- ASSESSOR/ADMIN PORTAL CORE VIEW -->
                <!-- ====================================================================== -->
                @else

                    <!-- ====================================================================== -->
                    <!-- TAB PAGE A: OVERVIEW / DASHBOARD -->
                    <!-- ====================================================================== -->
                    @if ($page === 'dashboard')
                        <!-- Metrics grid -->
                        <div class="db-stat-grid">
                            <div class="db-stat-card">
                                <div class="db-stat-icon">✏️</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value">{{ count($pending_grading) }}</div>
                                    <div class="db-stat-label">Homeworks to Grade</div>
                                </div>
                            </div>
                            <div class="db-stat-card">
                                <div class="db-stat-icon">💵</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value">{{ count($pending_remittance) }}</div>
                                    <div class="db-stat-label">Pending Remittances</div>
                                </div>
                            </div>
                            <div class="db-stat-card">
                                <div class="db-stat-icon">🤝</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value">{{ count($affiliate_applications) }}</div>
                                    <div class="db-stat-label">Partner Applications</div>
                                </div>
                            </div>
                            <div class="db-stat-card">
                                <div class="db-stat-icon">🎓</div>
                                <div class="db-stat-info">
                                    <div class="db-stat-value">{{ count($certificate_registry) }}</div>
                                    <div class="db-stat-label">Issued Credentials</div>
                                </div>
                            </div>
                        </div>

                        <div class="gov-grid-row">
                            <!-- Main grading column -->
                            <div class="gov-grid-column-two-thirds">
                                
                                <!-- Homework Evaluation -->
                                <div class="db-card" id="gradingSection">
                                    <div class="db-card-title">Evaluate Student Coursework</div>
                                    @if (empty($pending_grading))
                                        <p class="gov-hint">No submissions currently pending review.</p>
                                    @else
                                        <table class="gov-table">
                                            <thead>
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Module</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pending_grading as $g)
                                                    <tr>
                                                        <td><strong>{{ $g['student_name'] }}</strong></td>
                                                        <td>Mod {{ $g['module_number'] }}: {{ $g['module_title'] }}<br><span class="gov-hint"><a href="{{ asset($g['file_path']) }}" target="_blank">Download file ({{ $g['file_size'] }})</a></span></td>
                                                        <td>
                                                            <form action="{{ route('lms.dashboard') }}?page=dashboard" method="POST" style="display:flex; flex-direction:column; gap:6px;">
                                                                @csrf
                                                                <input type="hidden" name="assignment_id" value="{{ $g['id'] }}">
                                                                <select class="gov-select" name="grade" style="font-size:13px; padding:6px; max-width:140px;" required>
                                                                    <option value="">-- Grade --</option>
                                                                    <option value="Pass">Pass</option>
                                                                    <option value="Merit">Merit</option>
                                                                    <option value="Distinction">Distinction</option>
                                                                    <option value="Refer">Refer (Fail)</option>
                                                                </select>
                                                                <input class="gov-input" name="feedback" type="text" placeholder="Remarks" style="font-size:13px; padding:6px; max-width:140px;">
                                                                <button type="submit" name="grade_assignment" class="gov-button" style="font-size:11px; padding: 4px 8px; max-width:100px; border-radius: 4px;">Submit</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                                <!-- Remittance Approvals -->
                                <div class="db-card" id="remittanceSection">
                                    <div class="db-card-title">Pending Payments Queue</div>
                                    @if (empty($pending_remittance))
                                        <p class="gov-hint">No payment remittances pending confirmation.</p>
                                    @else
                                        <table class="gov-table">
                                            <thead>
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Reference MTCN</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pending_remittance as $p)
                                                    <tr>
                                                        <td><strong>{{ $p['student_name'] }}</strong><br><span class="gov-hint">{{ strtoupper($p['method']) }} (${{ $p['amount'] }})</span></td>
                                                        <td><code>{{ $p['transaction_ref'] }}</code></td>
                                                        <td>
                                                            <form action="{{ route('lms.dashboard') }}?page=dashboard" method="POST" style="display:inline;">
                                                                @csrf
                                                                <input type="hidden" name="payment_id" value="{{ $p['id'] }}">
                                                                <input type="hidden" name="payment_action" value="approve">
                                                                <button type="submit" name="review_remittance" class="gov-button" style="font-size:11px; padding: 4px 8px; border-radius:4px; background-color:#00703c;">Approve</button>
                                                            </form>
                                                            <form action="{{ route('lms.dashboard') }}?page=dashboard" method="POST" style="display:inline; margin-left: 4px;">
                                                                @csrf
                                                                <input type="hidden" name="payment_id" value="{{ $p['id'] }}">
                                                                <input type="hidden" name="payment_action" value="reject">
                                                                <button type="submit" name="review_remittance" class="gov-button gov-button-secondary" style="font-size:11px; padding: 4px 8px; border-radius:4px; background-color:#d4351c; color:#fff; border-bottom-color:#80180a;">Reject</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                                <!-- Certificate registry -->
                                <div class="db-card" id="certRegistrySection">
                                    <div class="db-card-title">Verifiable Certificate Registry</div>
                                    <table class="gov-table">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Course Program</th>
                                                <th>Verifiable UID</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (empty($certificate_registry))
                                                <tr>
                                                    <td colspan="3" class="gov-hint" style="text-align:center;">No certificate credentials issued.</td>
                                                </tr>
                                            @else
                                                @foreach ($certificate_registry as $c)
                                                    <tr>
                                                        <td><strong>{{ $c['student_name'] }}</strong></td>
                                                        <td>Faculty of {{ $c['faculty_name'] }}</td>
                                                        <td><code>{{ $c['certificate_uid'] }}</code></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                             </div>

                             <!-- Sidebar partner Column -->
                             <div class="gov-grid-column-one-third" style="border-left: 1px solid #EBF3FC; padding-left: 20px;">
                                <div class="db-card" id="affiliateSection">
                                    <div class="db-card-title" style="font-size: 16px;">Affiliate Partners</div>
                                    <p style="font-size:13px; color:#777; margin-bottom:15px;">Review consultant onboarding applications.</p>
                                    
                                    <!-- Pending Applications -->
                                    @if (empty($affiliate_applications))
                                        <span class="gov-hint">No applications pending.</span>
                                    @else
                                        @foreach ($affiliate_applications as $app)
                                            <div style="font-size:13px; padding: 12px; background-color:#fafbfe; border: 1px solid #EBF3FC; border-radius:6px; margin-bottom:12px; line-height: 1.45;">
                                                <strong>{{ $app['name'] }}</strong><br>
                                                Code: <code>{{ $app['rep_code'] }}</code><br>
                                                <span style="font-size: 11px; color:#555;">{{ $app['contact_info'] }}</span>
                                                
                                                <form action="{{ route('lms.dashboard') }}?page=dashboard" method="POST" style="display:inline; margin-top:8px;">
                                                    @csrf
                                                    <input type="hidden" name="affiliate_id" value="{{ $app['id'] }}">
                                                    <input type="hidden" name="aff_action" value="approve">
                                                    <button type="submit" name="review_affiliate" class="gov-button" style="font-size:10px; padding: 4px 8px; border-radius:3px; margin-top: 5px;">Approve</button>
                                                </form>
                                                <form action="{{ route('lms.dashboard') }}?page=dashboard" method="POST" style="display:inline; margin-left:4px;">
                                                    @csrf
                                                    <input type="hidden" name="affiliate_id" value="{{ $app['id'] }}">
                                                    <input type="hidden" name="aff_action" value="reject">
                                                    <button type="submit" name="review_affiliate" class="gov-button gov-button-secondary" style="font-size:10px; padding: 4px 8px; border-radius:3px; background-color:#d4351c; color:#fff; border-bottom-color:#80180a;">Reject</button>
                                                </form>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                    <!-- Active Agents List -->
                                    <div style="margin-top:20px; border-top:1px solid #EBF3FC; padding-top:15px;">
                                        <strong style="font-size:12px; color:#002F6C; text-transform:uppercase;">Active Agents</strong>
                                        @if (empty($approved_affiliates))
                                            <p style="font-size:12px; color:#777; margin-top:5px;">No active agents found.</p>
                                        @else
                                            <div style="max-height: 250px; overflow-y: auto; margin-top:10px;">
                                                @foreach ($approved_affiliates as $agent)
                                                    <div style="font-size:12px; padding:8px; border-bottom:1px solid #f0f0f0; line-height:1.4;">
                                                        <strong>{{ $agent['name'] }}</strong><br>
                                                        Code: <code>{{ $agent['rep_code'] }}</code><br>
                                                        Linked Students: <span style="font-weight:bold; color:#002F6C;">{{ intval($agent['linked_students_count'] ?? 0) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- ====================================================================== -->
                    <!-- TAB PAGE B: STUDENTS DIRECTORY -->
                    <!-- ====================================================================== -->
                    @elseif ($page === 'students')
                        
                        @if ($view_student)
                            <!-- Student Profile Details View Card -->
                            <div class="db-card">
                                <div class="db-card-header">
                                    <h2>Student Profile Review</h2>
                                    <a href="{{ route('lms.dashboard', ['page' => 'students']) }}" class="gov-button gov-button-secondary" style="padding: 8px 16px; font-size:14px; border-radius:4px; text-decoration:none; text-align:center; display:block;">&larr; Back to Students List</a>
                                </div>
                                
                                <div style="background-color: var(--bg-secondary); padding: 25px; border: 1.5px solid var(--border-main); border-radius: 8px; margin-bottom: 30px;">
                                    <h3 style="color: var(--text-heading); margin-bottom:15px; font-size:16px; border-bottom:1.5px solid var(--border-main); padding-bottom:5px;">Personal & Account Info</h3>
                                    <div class="gov-grid-row">
                                        <div class="gov-grid-column-one-third">
                                            <p style="margin-bottom:8px; font-size:14px;"><strong>Student Name:</strong> {{ $view_student['full_name'] }}</p>
                                            <p style="margin-bottom:8px; font-size:14px;"><strong>Student ID:</strong> LIAB-ST-{{ $view_student['id'] }}</p>
                                            <p style="margin-bottom:0; font-size:14px;"><strong>Account Status:</strong> 
                                                <span class="gov-tag {{ $view_student['account_status'] === 'active' ? 'gov-tag-green' : ($view_student['account_status'] === 'locked' ? 'gov-tag-grey' : 'gov-tag-yellow') }}" style="font-size:10px; padding: 2px 6px; text-transform:none;">
                                                    {{ $view_student['account_status'] }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="gov-grid-column-one-third">
                                            <p style="margin-bottom:8px; font-size:14px;"><strong>Email:</strong> {{ $view_student['email'] }}</p>
                                            <p style="margin-bottom:8px; font-size:14px;"><strong>WhatsApp:</strong> {{ $view_student['whatsapp_number'] }}</p>
                                            <p style="margin-bottom:0; font-size:14px;"><strong>Date of Birth:</strong> {{ $view_student['dob'] }}</p>
                                        </div>
                                        <div class="gov-grid-column-one-third">
                                            <p style="margin-bottom:8px; font-size:14px;"><strong>Faculty Program:</strong> Faculty of {{ $view_student['faculty_name'] ?: 'Not Enrolled' }}</p>
                                            <p style="margin-bottom:8px; font-size:14px;"><strong>Representative Code:</strong> <code>{{ $view_student['rep_code'] ?: 'None' }}</code></p>
                                            <p style="margin-bottom:0; font-size:14px;"><strong>Created On:</strong> {{ $view_student['created_at'] }}</p>
                                        </div>
                                    </div>
                                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1.5px solid var(--border-main); display:grid; grid-template-columns: 1fr 1fr 1fr; gap:15px;">
                                        <p style="margin-bottom:0; font-size:14px; grid-column: span 3; color: var(--text-primary);"><strong>Street Address:</strong> {{ $view_student['street_address'] }}</p>
                                        <p style="margin-bottom:0; font-size:14px; color: var(--text-primary);"><strong>City / Town:</strong> {{ $view_student['city'] }}</p>
                                        <p style="margin-bottom:0; font-size:14px; color: var(--text-primary);"><strong>Country:</strong> {{ $view_student['country'] }}</p>
                                        <p style="margin-bottom:0; font-size:14px; color: var(--text-primary);"><strong>Zip / Postcode:</strong> {{ $view_student['zip_code'] ?: 'None' }}</p>
                                    </div>
                                </div>

                                <div class="gov-grid-row">
                                    <!-- Left Column: Assignments & Exams -->
                                    <div class="gov-grid-column-two-thirds">
                                        <h3 style="color:#002F6C; margin-bottom:15px; font-size:16px;">Coursework Uploads & Submissions</h3>
                                        <table class="gov-table" style="margin-bottom: 30px;">
                                            <thead>
                                                <tr>
                                                    <th>Module</th>
                                                    <th>Document File</th>
                                                    <th>Uploaded At</th>
                                                    <th>Grade</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (empty($view_assignments))
                                                    <tr>
                                                        <td colspan="4" class="gov-hint" style="text-align:center;">No coursework assignments submitted.</td>
                                                    </tr>
                                                @else
                                                    @foreach ($view_assignments as $va)
                                                        <tr>
                                                            <td>Mod {{ $va['module_number'] }}: {{ $va['module_title'] }}</td>
                                                            <td><a href="{{ asset($va['file_path']) }}" target="_blank">{{ basename($va['file_path']) }}</a> ({{ $va['file_size'] }})</td>
                                                            <td>{{ $va['uploaded_at'] }}</td>
                                                            <td>
                                                                <span class="gov-tag" style="font-size:10px;">{{ $va['status'] }}</span>
                                                                @if ($va['grade'])
                                                                    | <strong>{{ $va['grade'] }}</strong>
                                                                @endif
                                                                <form action="{{ route('lms.dashboard') }}?page=students&view_id={{ $viewId }}" method="POST" style="margin-top:8px; display:block;">
                                                                    @csrf
                                                                    <input type="hidden" name="edit_assignment_grade" value="1">
                                                                    <input type="hidden" name="assignment_id" value="{{ $va['id'] }}">
                                                                    <div style="display:flex; gap:4px; align-items:center;">
                                                                        <select class="gov-select" name="grade" style="font-size:10px; padding:2px; height:24px; width:90px;" required>
                                                                            <option value="Pass" {{ $va['grade'] === 'Pass' ? 'selected' : '' }}>Pass</option>
                                                                            <option value="Merit" {{ $va['grade'] === 'Merit' ? 'selected' : '' }}>Merit</option>
                                                                            <option value="Distinction" {{ $va['grade'] === 'Distinction' ? 'selected' : '' }}>Distinction</option>
                                                                            <option value="Refer" {{ $va['grade'] === 'Refer' ? 'selected' : '' }}>Refer (Fail)</option>
                                                                        </select>
                                                                        <input class="gov-input" name="feedback" type="text" placeholder="Remarks" value="{{ $va['feedback'] ?? '' }}" style="font-size:10px; padding:2px 4px; height:24px; width:100px;">
                                                                        <button type="submit" class="gov-button" style="font-size:9px; padding:2px 6px; border-radius:3px; background-color:#002F6C; height:24px;">Save</button>
                                                                    </div>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>

                                        <h3 style="color:#002F6C; margin-bottom:15px; font-size:16px;">Timed Examination Attempts</h3>
                                        <table class="gov-table">
                                            <thead>
                                                <tr>
                                                    <th>Date Completed</th>
                                                    <th>Score</th>
                                                    <th>Violations</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (empty($view_exams))
                                                    <tr>
                                                        <td colspan="4" class="gov-hint" style="text-align:center;">No exam attempts logged.</td>
                                                    </tr>
                                                @else
                                                    @foreach ($view_exams as $ve)
                                                        <tr>
                                                            <td>{{ $ve['end_time'] }}</td>
                                                            <td>
                                                                <strong>{{ $ve['score'] }}%</strong> (Threshold: 50%)
                                                                <form action="{{ route('lms.dashboard') }}?page=students&view_id={{ $viewId }}" method="POST" style="margin-top:6px; display:block;">
                                                                    @csrf
                                                                    <input type="hidden" name="edit_exam_score" value="1">
                                                                    <input type="hidden" name="attempt_id" value="{{ $ve['id'] }}">
                                                                    <div style="display:flex; gap:4px; align-items:center;">
                                                                        <input class="gov-input" name="score" type="number" step="0.01" min="0" max="100" value="{{ $ve['score'] }}" required style="font-size:10px; padding:2px 4px; height:24px; width:55px;">
                                                                        <select class="gov-select" name="status" style="font-size:10px; padding:2px; height:24px; width:90px;" required>
                                                                            <option value="completed" {{ $ve['status'] === 'completed' ? 'selected' : '' }}>Completed</option>
                                                                            <option value="force_submitted_violation" {{ $ve['status'] === 'force_submitted_violation' ? 'selected' : '' }}>Violation</option>
                                                                        </select>
                                                                        <button type="submit" class="gov-button" style="font-size:9px; padding:2px 6px; border-radius:3px; background-color:#002F6C; height:24px;">Save</button>
                                                                    </div>
                                                                </form>
                                                            </td>
                                                            <td>{{ $ve['violation_count'] }} Violations</td>
                                                            <td>
                                                                <span class="gov-tag {{ $ve['score'] >= 50 ? 'gov-tag-green' : 'gov-tag-yellow' }}" style="font-size:10px; text-transform:none;">
                                                                    {{ $ve['status'] }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Right Column: Payments & Certificates -->
                                    <div class="gov-grid-column-one-third" style="border-left: 1px solid #EBF3FC; padding-left: 20px;">
                                        <h3 style="color:#002F6C; margin-bottom:15px; font-size:16px;">Verifiable Credentials</h3>
                                        <div style="background-color:#fafbfe; padding: 15px; border:1px solid #EBF3FC; border-radius:6px; margin-bottom: 25px;">
                                            @if (empty($view_certificates))
                                                <span class="gov-hint">No certificates issued.</span>
                                            @else
                                                @foreach ($view_certificates as $vc)
                                                    <div style="font-size:13px; margin-bottom: 10px; border-bottom: 1px solid #EBF3FC; padding-bottom:8px;">
                                                        ID: <code>{{ $vc['certificate_uid'] }}</code><br>
                                                        Date: {{ $vc['issue_date'] }}<br>
                                                        Status: <span class="gov-tag {{ $vc['verification_status'] === 'approved' ? 'gov-tag-green' : 'gov-tag-red' }}" style="font-size:9px; padding:1px 4px; text-transform:none;">{{ $vc['verification_status'] }}</span><br>
                                                        <a href="{{ asset($vc['pdf_path']) }}" download style="font-size:12px; font-weight:600; display:inline-block; margin-top:5px;">Download PDF &rarr;</a>
                                                    </div>
                                                @endforeach
                                            @endif
                                            
                                            <!-- Manual Award Form -->
                                            <div style="border-top:1px solid #EBF3FC; margin-top:15px; padding-top:15px;">
                                                <h4 style="font-size:12px; color:#002F6C; margin-bottom:8px; font-weight:600;">Manual Award Certificate</h4>
                                                <form action="{{ route('lms.dashboard') }}?page=students&view_id={{ $viewId }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="manual_award_certificate" value="1">
                                                    <input type="hidden" name="user_id" value="{{ $viewId }}">
                                                    <select class="gov-select" name="course_id" style="font-size:11px; padding:3px; height:28px; width:100%; margin-bottom:8px;" required>
                                                        <option value="">-- Choose Course --</option>
                                                        @php
                                                            $facs = DB::select("SELECT * FROM faculties");
                                                        @endphp
                                                        @foreach ($facs as $f)
                                                            <option value="{{ $f->id }}" {{ $f->id == $view_student['faculty_id'] ? 'selected' : '' }}>Faculty of {{ $f->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="gov-button" style="font-size:10px; padding:6px 10px; border-radius:3px; width:100%; background-color:#00703c;">Award Now</button>
                                                </form>
                                            </div>
                                            
                                            <!-- Exam Retake Unlock Form -->
                                            <div style="border-top:1px solid #EBF3FC; margin-top:15px; padding-top:15px;">
                                                <h4 style="font-size:12px; color:#002F6C; margin-bottom:8px; font-weight:600;">Exam Terminal Control</h4>
                                                @if (intval($view_student['exam_retake_unlocked'] ?? 0) === 1)
                                                    <span style="font-size:11px; color:#00703c; font-weight:bold; display:block; margin-bottom:8px;">✓ Retake Unlocked (Ready for Candidate)</span>
                                                @else
                                                    <span style="font-size:11px; color:#d4351c; font-weight:bold; display:block; margin-bottom:8px;">Locked (No Active Retake Permission)</span>
                                                @endif
                                                <form action="{{ route('lms.dashboard') }}?page=students&view_id={{ $viewId }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="toggle_exam_retake" value="1">
                                                    <input type="hidden" name="user_id" value="{{ $viewId }}">
                                                    <input type="hidden" name="new_state" value="{{ (intval($view_student['exam_retake_unlocked'] ?? 0) === 1) ? '0' : '1' }}">
                                                    <button type="submit" class="gov-button" style="font-size:10px; padding:6px 10px; border-radius:3px; width:100%; background-color: {{ (intval($view_student['exam_retake_unlocked'] ?? 0) === 1) ? '#d4351c' : '#002F6C' }}; border-bottom: none;">
                                                        {{ (intval($view_student['exam_retake_unlocked'] ?? 0) === 1) ? 'Lock Retake Terminal' : 'Unlock Retake Terminal' }}
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Phase II Lock Bypass control -->
                                            <div style="border-top:1px solid #EBF3FC; margin-top:15px; padding-top:15px;">
                                                <h4 style="font-size:12px; color:#002F6C; margin-bottom:8px; font-weight:600;">Phase II Coursework Lock Control</h4>
                                                @if (intval($view_student['phase2_expedited'] ?? 0) === 1)
                                                    <span style="font-size:11px; color:#00703c; font-weight:bold; display:block; margin-bottom:8px;">✓ Phase II Expedited (14-Day Lock Bypassed)</span>
                                                @else
                                                    <span style="font-size:11px; color:#f47738; font-weight:bold; display:block; margin-bottom:8px;">Standard 14-Day Speed Lock Active</span>
                                                @endif
                                                <form action="{{ route('lms.dashboard') }}?page=students&view_id={{ $viewId }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="toggle_phase2_expedite" value="1">
                                                    <input type="hidden" name="user_id" value="{{ $viewId }}">
                                                    <input type="hidden" name="new_state" value="{{ (intval($view_student['phase2_expedited'] ?? 0) === 1) ? '0' : '1' }}">
                                                    <button type="submit" class="gov-button" style="font-size:10px; padding:6px 10px; border-radius:3px; width:100%; background-color: {{ (intval($view_student['phase2_expedited'] ?? 0) === 1) ? '#d4351c' : '#002F6C' }}; border-bottom: none;">
                                                        {{ (intval($view_student['phase2_expedited'] ?? 0) === 1) ? 'Re-Apply 14-Day Lock' : 'Manually Expedite Phase II' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <h3 style="color:#002F6C; margin-bottom:15px; font-size:16px;">Remittance Transactions</h3>
                                        <div style="background-color:#fafbfe; padding: 15px; border:1px solid #EBF3FC; border-radius:6px;">
                                            @if (empty($view_payments))
                                                <span class="gov-hint">No transactions logged.</span>
                                            @else
                                                @foreach ($view_payments as $vp)
                                                    <div style="font-size:13px; margin-bottom: 10px; border-bottom: 1px solid #EBF3FC; padding-bottom:8px; line-height: 1.4;">
                                                        Amount: <strong>${{ number_format($vp['amount'], 2) }}</strong> ({{ strtoupper($vp['type']) }})<br>
                                                        Ref: <code>{{ $vp['transaction_ref'] }}</code><br>
                                                        Status: <span class="gov-tag {{ $vp['status'] === 'paid' ? 'gov-tag-green' : 'gov-tag-yellow' }}" style="font-size:9px; padding:1px 4px; text-transform:none;">{{ $vp['status'] }}</span>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Students Directory Table Card -->
                            <div class="db-card">
                                <div class="db-card-header">
                                    <h2>Students Management Registry</h2>
                                    <button onclick="showCreateModal()" class="gov-button" style="padding: 8px 16px; font-size:14px; border-radius:4px;">+ Register Student</button>
                                </div>

                                <!-- SEARCH & FILTER FORM -->
                                <form action="{{ route('lms.dashboard') }}" method="GET" style="display:flex; flex-wrap:wrap; gap:15px; background-color: var(--bg-secondary); padding:20px; border:1.5px solid var(--border-main); border-radius:8px; margin-bottom:25px; align-items:flex-end;">
                                    <input type="hidden" name="page" value="students">
                                    
                                    <div style="flex: 1; min-width: 200px;">
                                        <label class="gov-label" for="search_input" style="font-size:13px; margin-bottom:4px;">Search by Name</label>
                                        <input class="gov-input" id="search_input" name="search" type="text" value="{{ $search }}" placeholder="Type student name..." style="max-width:100%; height:40px; font-size:13px;">
                                    </div>

                                    <div style="flex: 1; min-width: 180px;">
                                        <label class="gov-label" for="status_select" style="font-size:13px; margin-bottom:4px;">Filter by Status</label>
                                        <select class="gov-select" id="status_select" name="status_filter" style="max-width:100%; height:40px; font-size:13px; padding-top: 8px; padding-bottom: 8px;">
                                            <option value="">-- All Statuses --</option>
                                            <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active (Tuition Paid)</option>
                                            <option value="pending_manual_unlock" {{ $statusFilter === 'pending_manual_unlock' ? 'selected' : '' }}>Pending Tuition Payment</option>
                                            <option value="locked" {{ $statusFilter === 'locked' ? 'selected' : '' }}>Locked</option>
                                        </select>
                                    </div>

                                    <div style="display:flex; gap:10px;">
                                        <button type="submit" class="gov-button" style="padding: 10px 20px; font-size:13px; height:40px; border-radius:4px;">Filter</button>
                                        @if (!empty($search) || !empty($statusFilter))
                                            <a href="{{ route('lms.dashboard', ['page' => 'students']) }}" class="gov-button gov-button-secondary" style="padding: 10px 20px; font-size:13px; height:40px; border-radius:4px; text-decoration:none; display:flex; align-items:center; justify-content:center;">Clear</a>
                                        @endif
                                    </div>
                                </form>

                                <div class="premium-table-wrapper">
                                    <table class="gov-table" style="margin-bottom: 0;">
                                        <thead>
                                            <tr>
                                                <th>Student ID</th>
                                                <th>Full Name</th>
                                                <th>Registered Email</th>
                                                <th>WhatsApp</th>
                                                <th>Faculty Program</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (empty($students_list))
                                                <tr>
                                                    <td colspan="7" class="gov-hint" style="text-align:center;">No students registered in the database.</td>
                                                </tr>
                                            @else
                                                @foreach ($students_list as $st)
                                                    <tr>
                                                        <td><strong>LIAB-ST-{{ $st['id'] }}</strong></td>
                                                        <td>{{ $st['full_name'] }}</td>
                                                        <td>{{ $st['email'] }}</td>
                                                        <td>{{ $st['whatsapp_number'] }}</td>
                                                        <td>{{ $st['faculty_name'] ?: 'Not Enrolled' }}</td>
                                                        <td>
                                                            <span class="gov-tag {{ $st['account_status'] === 'active' ? 'gov-tag-green' : ($st['account_status'] === 'locked' ? 'gov-tag-grey' : 'gov-tag-yellow') }}" style="font-size:11px; padding:3px 8px; text-transform:none;">
                                                                {{ $st['account_status'] }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('lms.dashboard', ['page' => 'students', 'view_id' => $st['id']]) }}" class="btn-action btn-view">View</a>
                                                            <button onclick="showEditModal({{ json_encode($st) }})" class="btn-action btn-edit" style="cursor:pointer; border:none; outline:none; background:none;">Edit</button>
                                                            <form action="{{ route('lms.dashboard') }}?page=students" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this student profile? All associated coursework, payments, and certificates will be deleted.')">
                                                                @csrf
                                                                <input type="hidden" name="delete_student" value="1">
                                                                <input type="hidden" name="delete_id" value="{{ $st['id'] }}">
                                                                <button type="submit" class="btn-action btn-delete" style="cursor:pointer; border:none; outline:none; background:none;">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                    <!-- ====================================================================== -->
                    <!-- TAB PAGE C: EXAM REPORTS -->
                    <!-- ====================================================================== -->
                    @elseif ($page === 'exams_report')
                        <div class="db-card">
                            <div class="db-card-header">
                                <h2>Timed Assessment Attempts Ledger</h2>
                            </div>
                            
                            <table class="gov-table">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Academic Program Focus</th>
                                        <th>Evaluation Score</th>
                                        <th>Anti-Cheat Violations</th>
                                        <th>End Time Log</th>
                                        <th>Status Flag</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (empty($all_exam_attempts))
                                        <tr>
                                            <td colspan="7" class="gov-hint" style="text-align:center;">No examination attempts recorded under the ledger.</td>
                                        </tr>
                                    @else
                                        @foreach ($all_exam_attempts as $ea)
                                            <tr>
                                                <td><strong>LIAB-ST-{{ $ea['user_id'] }}</strong></td>
                                                <td>{{ $ea['student_name'] }}</td>
                                                <td>Faculty of {{ $ea['faculty_name'] }}</td>
                                                <td><strong>{{ $ea['score'] }}%</strong></td>
                                                <td>{{ $ea['violation_count'] }} violations</td>
                                                <td>{{ $ea['end_time'] }}</td>
                                                <td>
                                                    <span class="gov-tag {{ $ea['score'] >= 70 ? 'gov-tag-green' : 'gov-tag-yellow' }}" style="font-size:11px; padding:3px 8px; text-transform:none;">
                                                        {{ $ea['status'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                    <!-- ====================================================================== -->
                    <!-- TAB PAGE: MANAGE COURSES -->
                    <!-- ====================================================================== -->
                    @elseif ($page === 'courses')
                        @if ($view_course)
                            <!-- Course Profile Details View Card -->
                            <div class="db-card">
                                <div class="db-card-header">
                                    <h2>Course Program Review</h2>
                                    <a href="{{ route('lms.dashboard', ['page' => 'courses']) }}" class="gov-button gov-button-secondary" style="padding: 8px 16px; font-size:14px; border-radius:4px; text-decoration:none; text-align:center; display:block;">&larr; Back to Courses List</a>
                                </div>
                                
                                <div style="background-color: var(--bg-secondary); padding: 25px; border: 1.5px solid var(--border-main); border-radius: 8px; margin-bottom: 30px;">
                                    <h3 style="color: var(--text-heading); margin-bottom:15px; font-size:16px; border-bottom:1.5px solid var(--border-main); padding-bottom:5px;">Course Track Overview</h3>
                                    <div class="gov-grid-row">
                                        <div class="gov-grid-column-one-half">
                                            <p style="margin-bottom:8px; font-size:14px; color: var(--text-primary);"><strong>Course Name:</strong> Faculty of {{ $view_course['name'] }}</p>
                                            <p style="margin-bottom:8px; font-size:14px; color: var(--text-primary);"><strong>Course Code:</strong> <code>{{ $view_course['code'] ?: 'N/A' }}</code></p>
                                            <p style="margin-bottom:8px; font-size:14px; color: var(--text-primary);"><strong>Course ID Reference:</strong> LIAB-CR-{{ $view_course['id'] }}</p>
                                            <p style="margin-bottom:0; font-size:14px; color: var(--text-primary);"><strong>Course Description:</strong> {{ $view_course['description'] ?: 'No description provided.' }}</p>
                                        </div>
                                        <div class="gov-grid-column-one-half" style="text-align: right;">
                                            <p style="margin-bottom:8px; font-size:14px; color: var(--text-primary);"><strong>Track Duration:</strong> {{ $view_course['duration'] ?: 'N/A' }}</p>
                                            <p style="margin-bottom:8px; font-size:14px; color: var(--text-primary);"><strong>Tuition Fee:</strong> £{{ number_format($view_course['fee'] ?? 0.00, 2) }}</p>
                                            <p style="margin-bottom:8px; font-size:14px; color: var(--text-primary);"><strong>Total Associated Modules:</strong> {{ count($view_course_modules) }} Modules</p>
                                            <p style="margin-bottom:0; font-size:14px; color: var(--text-primary);"><strong>Enrolled Active Students:</strong> {{ count($view_course_students) }} Students</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="gov-grid-row">
                                    <!-- Left Column: Academic Modules -->
                                    <div class="gov-grid-column-two-thirds">
                                        <h3 style="color: var(--text-heading); margin-bottom:15px; font-size:16px;">Curriculum & Academic Modules</h3>
                                        <table class="gov-table" style="margin-bottom: 30px;">
                                            <thead>
                                                <tr>
                                                    <th>Module No.</th>
                                                    <th>Phase</th>
                                                    <th>Title</th>
                                                    <th>Content Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (empty($view_course_modules))
                                                    <tr>
                                                        <td colspan="4" class="gov-hint" style="text-align:center;">No modules assigned to this course track.</td>
                                                    </tr>
                                                @else
                                                    @foreach ($view_course_modules as $mod)
                                                        <tr>
                                                            <td><strong>Module {{ $mod['module_number'] }}</strong></td>
                                                            <td><span class="gov-tag gov-tag-grey" style="font-size:10px; padding:2px 6px;">Phase {{ $mod['phase'] }}</span></td>
                                                            <td>
                                                                {{ $mod['title'] }}
                                                                @if ($mod['faculty_id'] === null)
                                                                    <span style="font-size: 10px; color: var(--text-hint); font-style: italic; display:block;">(Core Universal Module)</span>
                                                                @endif
                                                            </td>
                                                            <td><span style="text-transform: capitalize;">{{ $mod['content_type'] }}</span></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>

                                        <h3 style="color: var(--text-heading); margin-bottom:15px; font-size:16px;">Course Timed Exams Configuration</h3>
                                        <table class="gov-table">
                                            <thead>
                                                <tr>
                                                    <th>Exam ID</th>
                                                    <th>Duration</th>
                                                    <th>Pass Threshold</th>
                                                    <th>Total Questions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (empty($view_course_exams))
                                                    <tr>
                                                        <td colspan="4" class="gov-hint" style="text-align:center;">No timed exam configured for this course.</td>
                                                    </tr>
                                                @else
                                                    @foreach ($view_course_exams as $ex)
                                                        <tr>
                                                            <td><strong>#{{ $ex['id'] }}</strong></td>
                                                            <td>{{ $ex['duration_minutes'] }} Minutes</td>
                                                            <td>{{ $ex['pass_threshold'] }}% Correct Answer Grade</td>
                                                            <td>{{ $ex['total_questions'] }} Questions</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Right Column: Enrolled Students -->
                                    <div class="gov-grid-column-one-third">
                                        <h3 style="color: var(--text-heading); margin-bottom:15px; font-size:16px;">Enrolled Student Profiles</h3>
                                        <div style="background-color: var(--bg-secondary); padding: 15px; border: 1.5px solid var(--border-main); border-radius: 8px;">
                                            @if (empty($view_course_students))
                                                <span class="gov-hint">No students currently enrolled in this track.</span>
                                            @else
                                                @foreach ($view_course_students as $st)
                                                    <div style="font-size:13px; margin-bottom: 12px; border-bottom: 1px solid var(--border-main); padding-bottom:10px; line-height: 1.45;">
                                                        <strong>{{ $st['full_name'] }}</strong><br>
                                                        Email: <span style="color: var(--text-secondary);">{{ $st['email'] }}</span><br>
                                                        WhatsApp: <span style="color: var(--text-secondary);">{{ $st['whatsapp_number'] }}</span><br>
                                                        Status: <span class="gov-tag {{ $st['account_status'] === 'active' ? 'gov-tag-green' : ($st['account_status'] === 'locked' ? 'gov-tag-grey' : 'gov-tag-yellow') }}" style="font-size:9px; padding:1px 4px; text-transform:none;">{{ $st['account_status'] }}</span>
                                                        <a href="{{ route('lms.dashboard', ['page' => 'students', 'view_id' => $st['id']]) }}" class="gov-button" style="display:block; width:100%; text-align:center; padding: 6px; font-size:10px; margin-top:8px; border-radius:4px; text-decoration:none; text-align:center;">View Student Profile</a>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Courses Directory Table Card -->
                            <div class="db-card">
                                <div class="db-card-header">
                                    <h2>Manage Program Courses</h2>
                                    <button class="gov-button" onclick="showAddCourseModal()" style="border-radius:6px; padding: 8px 16px; font-size:14px;">+ Add New Course</button>
                                </div>

                                <!-- COURSE SEARCH FILTERS -->
                                <form action="{{ route('lms.dashboard') }}" method="GET" style="display:flex; flex-wrap:wrap; gap:15px; background-color: var(--bg-secondary); padding:20px; border:1.5px solid var(--border-main); border-radius:8px; margin-bottom:25px; align-items:flex-end;">
                                    <input type="hidden" name="page" value="courses">
                                    
                                    <div style="flex: 1; min-width: 200px;">
                                        <label class="gov-label" for="course_search" style="font-size:13px; margin-bottom:4px;">Search Course by Name</label>
                                        <input class="gov-input" id="course_search" name="search" type="text" value="{{ $search }}" placeholder="Type course name..." style="max-width:100%; height:40px; font-size:13px;">
                                    </div>

                                    <div style="display:flex; gap:10px;">
                                        <button type="submit" class="gov-button" style="padding: 10px 20px; font-size:13px; height:40px; border-radius:4px;">Filter</button>
                                        @if (!empty($search))
                                            <a href="{{ route('lms.dashboard', ['page' => 'courses']) }}" class="gov-button gov-button-secondary" style="padding: 10px 20px; font-size:13px; height:40px; border-radius:4px; text-decoration:none; display:flex; align-items:center; justify-content:center;">Clear</a>
                                        @endif
                                    </div>
                                </form>
                                
                                <div class="premium-table-wrapper">
                                    <table class="gov-table" style="margin-bottom: 0;">
                                        <thead>
                                            <tr>
                                                <th>Course ID</th>
                                                <th>Academic Program Focus</th>
                                                <th>Code</th>
                                                <th>Duration</th>
                                                <th>Tuition Fee</th>
                                                <th>Modules</th>
                                                <th>Students</th>
                                                <th style="text-align:right;">Registry Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (empty($courses_list))
                                                <tr>
                                                    <td colspan="8" class="gov-hint" style="text-align:center;">No courses matched your query.</td>
                                                </tr>
                                            @else
                                                @foreach ($courses_list as $c)
                                                    <tr>
                                                        <td><strong>#{{ $c['id'] }}</strong></td>
                                                        <td>Faculty of {{ $c['name'] }}</td>
                                                        <td><code>{{ $c['code'] ?: 'N/A' }}</code></td>
                                                        <td>{{ $c['duration'] ?: 'N/A' }}</td>
                                                        <td>£{{ number_format($c['fee'] ?? 0.00, 2) }}</td>
                                                        <td><span class="gov-tag gov-tag-grey" style="font-size:11px; padding:2px 6px;">{{ $c['modules_count'] ?? 0 }} Modules</span></td>
                                                        <td><span class="gov-tag gov-tag-blue" style="font-size:11px; padding:2px 6px;">{{ $c['students_count'] ?? 0 }} Students</span></td>
                                                        <td style="text-align:right; white-space: nowrap;">
                                                            <a href="{{ route('lms.dashboard', ['page' => 'courses', 'view_id' => $c['id']]) }}" class="btn-action btn-view" style="cursor:pointer; border:none; outline:none; text-decoration:none; font-weight:600; font-size:12px; margin-right: 6px; display:inline-block;">View</a>
                                                            <button onclick='showEditCourseModal(@json($c))' class="btn-action btn-edit" style="cursor:pointer; border:none; outline:none; background:none; margin-right: 6px;">Edit</button>
                                                            
                                                            <form action="{{ route('lms.dashboard') }}?page=courses" method="POST" style="display:inline;" onsubmit="return confirm('Warning: Deleting this course will also delete all modules, exams, and student assignments associated with it. Are you sure you want to proceed?');">
                                                                @csrf
                                                                <input type="hidden" name="delete_course" value="1">
                                                                <input type="hidden" name="delete_id" value="{{ $c['id'] }}">
                                                                <button type="submit" class="btn-action btn-delete" style="cursor:pointer; border:none; outline:none; background:none; padding:0;">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                    <!-- ====================================================================== -->
                    <!-- TAB PAGE D: CERTIFICATE LEDGER REGISTRY -->
                    <!-- ====================================================================== -->
                    @elseif ($page === 'certificates_registry')
                        <div class="db-card">
                            <div class="db-card-header">
                                <h2>Issued Credentials & Verifiable Ledger Logs</h2>
                            </div>
                            
                            <table class="gov-table">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Academic Program Focus</th>
                                        <th>Registry UID</th>
                                        <th>Registry Issue Date</th>
                                        <th>Verification Status</th>
                                        <th>Ledger Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (empty($all_certificates))
                                        <tr>
                                            <td colspan="6" class="gov-hint" style="text-align:center;">No certificate credentials registered.</td>
                                        </tr>
                                    @else
                                        @foreach ($all_certificates as $c)
                                            <tr>
                                                <td><strong>{{ $c['student_name'] }}</strong></td>
                                                <td>Faculty of {{ $c['faculty_name'] }}</td>
                                                <td><code style="font-weight:600; color:#002F6C;">{{ $c['certificate_uid'] }}</code></td>
                                                <td>{{ $c['issue_date'] }}</td>
                                                <td>
                                                    <span class="gov-tag {{ $c['verification_status'] === 'approved' ? 'gov-tag-green' : 'gov-tag-red' }}" style="font-size:11px; padding:3px 8px; text-transform:none;">
                                                        {{ $c['verification_status'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('lms.certificate', ['uid' => $c['certificate_uid']]) }}" target="_blank" class="btn-action btn-view" style="display:inline-block; line-height:1.5;">View</a>
                                                    @if ($c['verification_status'] === 'approved')
                                                        <form action="{{ route('lms.dashboard') }}?page=certificates_registry" method="POST" style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="revoke_certificate" value="1">
                                                            <input type="hidden" name="cert_id" value="{{ $c['id'] }}">
                                                            <button type="submit" class="btn-action btn-delete" style="cursor:pointer; border:none; outline:none; background:none;">Revoke</button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('lms.dashboard') }}?page=certificates_registry" method="POST" style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="approve_certificate" value="1">
                                                            <input type="hidden" name="cert_id" value="{{ $c['id'] }}">
                                                            <button type="submit" class="btn-action btn-edit" style="cursor:pointer; border:none; outline:none; background:none; background-color:#00703c; color:#fff;">Re-Approve</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                    @endif

                @endif

                <!-- ====================================================================== -->
                <!-- UNIVERSAL TAB PAGE: PROFILE & SECURITY SETTINGS -->
                <!-- ====================================================================== -->
                @if ($page === 'profile')
                    <div class="db-card">
                        <div class="db-card-header">
                            <h2>Profile & Security Settings</h2>
                        </div>

                        <div class="gov-grid-row">
                            <!-- Update Profile Details -->
                            <div class="gov-grid-column-one-half">
                                <h3>Personal Profile Dossier</h3>
                                <p class="gov-hint" style="margin-bottom:20px;">Review and manage your institutional name and contact email parameters.</p>
                                
                                <form action="{{ route('lms.dashboard') }}?page=profile" method="POST" onsubmit="return validateProfileInfo()">
                                    @csrf
                                    <input type="hidden" name="update_profile" value="1">
                                    
                                    <div class="gov-form-group">
                                        <label class="gov-label" for="p_name">Candidate Legal Name</label>
                                        <span class="gov-hint" style="font-size:11px;">Must match passports or legal identities exactly.</span>
                                        <input class="gov-input" id="p_name" name="profile_name" type="text" style="max-width:100%;" required value="{{ $currentUser['full_name'] }}">
                                    </div>

                                    <div class="gov-form-group">
                                        <label class="gov-label" for="p_email">Contact Email Address</label>
                                        <input class="gov-input" id="p_email" name="profile_email" type="email" style="max-width:100%;" required value="{{ $currentUser['email'] }}">
                                    </div>

                                    <button type="submit" class="gov-button" style="border-radius:4px; padding: 10px 20px;">Save Profile Changes</button>
                                </form>
                            </div>

                            <!-- Change Password -->
                            <div class="gov-grid-column-one-half" style="border-left: 1px solid #EBF3FC; padding-left: 20px;">
                                <h3>Account Security Passkey</h3>
                                <p class="gov-hint" style="margin-bottom:20px;">Change your current password parameters to maintain credentials lock security.</p>
                                
                                <form action="{{ route('lms.dashboard') }}?page=profile" method="POST">
                                    @csrf
                                    <input type="hidden" name="change_password" value="1">

                                    <div class="gov-form-group">
                                        <label class="gov-label" for="c_password">Current Password</label>
                                        <div class="pw-wrapper">
                                            <input class="gov-input" id="c_password" name="current_password" type="password" required style="max-width:100%;">
                                            <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('c_password', this)" aria-label="Show password">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="gov-form-group">
                                        <label class="gov-label" for="n_password">New Secure Password</label>
                                        <div class="pw-wrapper">
                                            <input class="gov-input" id="n_password" name="new_password" type="password" required style="max-width:100%;" placeholder="Min 6 characters">
                                            <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('n_password', this)" aria-label="Show password">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="gov-form-group">
                                        <label class="gov-label" for="cf_password">Confirm New Password</label>
                                        <div class="pw-wrapper">
                                            <input class="gov-input" id="cf_password" name="confirm_password" type="password" required style="max-width:100%;">
                                            <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('cf_password', this)" aria-label="Show password">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <button type="submit" class="gov-button" style="border-radius:4px; padding: 10px 20px;">Change Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ====================================================================== -->
                <!-- UNIVERSAL TAB PAGE: COMPLAINTS & DISPUTE RESOLUTION -->
                <!-- ====================================================================== -->
                @if ($page === 'dispute')
                    <div class="db-card" style="max-width: 900px; margin: 0 auto 30px auto; padding: 30px;">
                        <div class="db-card-header" style="border-bottom: 2px solid var(--border-main); padding-bottom: 15px; margin-bottom: 25px;">
                            <h2 style="color: var(--text-heading); margin: 0; font-size: 22px; font-weight: 700;">Complaints & Dispute Resolution</h2>
                            <p class="gov-hint" style="margin: 5px 0 0 0;">Official Grievances & Arbitration Regulations</p>
                        </div>

                        <div style="line-height: 1.7; color: var(--text-primary); font-size: 14.5px;">
                            <h3 style="color: var(--text-heading); font-size: 16px; margin-top: 25px; margin-bottom: 10px; font-weight: 600;">1. Lodging an Official Institutional Grievance</h3>
                            <p style="margin-bottom: 15px;">Candidates wishing to lodge a formal complaint regarding evaluation metrics, syllabus access tracking logs, or administrative processing paths must submit a signed case portfolio file directly to the compliance panel via email at <a href="mailto:compliance@cpduk.london" style="color: #002F6C; text-decoration: underline;">compliance@cpduk.london</a>.</p>
                            <p style="margin-bottom: 25px; background-color: var(--bg-secondary); padding: 12px 16px; border-left: 4px solid var(--border-main); border-radius: 4px;">Anonymous reports, informal live-chat complaints, or messages sent via external channels will not enter the registry tracking archives.</p>

                            <h3 style="color: var(--text-heading); font-size: 16px; margin-top: 25px; margin-bottom: 10px; font-weight: 600;">2. Evaluation Appeals & Verification Timelines</h3>
                            <p style="margin-bottom: 15px;">If a student disputes a final transcript mark landing below our strict 50% database-wide passing threshold, they have exactly 7 calendar days from the conclusion of the 14-day hold review state to request an independent script-verification audit.</p>
                            <p style="margin-bottom: 25px;">Grievances regarding portfolio markings require a flat administrative reinvestigation processing fee of £99.00 GBP, securely cleared via our card gateway before the Academic Assessment Committee re-evaluates the script logs.</p>

                            <h3 style="color: var(--text-heading); font-size: 16px; margin-top: 25px; margin-bottom: 10px; font-weight: 600;">3. Finality of Financial Covenants & Refund Terms</h3>
                            <p style="margin-bottom: 15px;">The Complaints Department strictly enforces Section 4.3 of our institutional student handbook rules. All onboarding fees, course installment tokens, and exam resit entry costs remain 100% non-refundable and non-transferable under all operational circumstances, without exception.</p>
                            <p style="margin-bottom: 25px; background-color: var(--bg-secondary); padding: 12px 16px; border-left: 4px solid var(--border-main); border-radius: 4px;">Filing a grievance or open complaint log does not pause, suspend, or change ongoing monthly installment liabilities or system accounting collection cycles.</p>

                            <h3 style="color: var(--text-heading); font-size: 16px; margin-top: 25px; margin-bottom: 10px; font-weight: 600;">4. Arbitration Boundaries & Jurisdiction Laws</h3>
                            <p style="margin-bottom: 15px;">The International Certification Award Board operates entirely as an independent international registry separate from standard state regulatory bodies.</p>
                            <p style="margin-bottom: 0;">All formal dispute resolution, contract execution rules, and legal liabilities are subject to the exclusive jurisdiction of the courts of London, United Kingdom.</p>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- ====================================================================== -->
    <!-- CREATE STUDENT MODAL POPUP -->
    <!-- ====================================================================== -->
    <div id="createStudentModal" class="db-modal-overlay" style="display: none;">
        <div class="db-modal">
            <span class="db-modal-close" onclick="hideCreateModal()">&times;</span>
            <h3 style="margin-bottom: 25px; font-size:18px; color: #002F6C;">Create Student Profile</h3>
            
            <form action="{{ route('lms.dashboard') }}?page=students" method="POST" novalidate onsubmit="return validateCreateStudent()">
                @csrf
                <input type="hidden" name="create_student" value="1">
                
                <div class="modal-form-grid">
                    <div class="gov-form-group">
                        <label class="gov-label" for="c_name">Full Name</label>
                        <input class="gov-input" id="c_name" name="student_name" type="text" required placeholder="e.g. John Doe">
                        <span class="validation-error-msg" id="error_c_name"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_dob">Date of Birth</label>
                        <input class="gov-input" id="c_dob" name="student_dob" type="date" required>
                        <span class="validation-error-msg" id="error_c_dob"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_email">Email Address</label>
                        <input class="gov-input" id="c_email" name="student_email" type="email" required placeholder="e.g. name@example.com">
                        <span class="validation-error-msg" id="error_c_email"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_whatsapp">WhatsApp Number</label>
                        <input class="gov-input" id="c_whatsapp" name="student_whatsapp" type="tel" required placeholder="e.g. +44700000000">
                        <span class="validation-error-msg" id="error_c_whatsapp"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_faculty">Faculty Program</label>
                        <select class="gov-select" id="c_faculty" name="student_faculty" required>
                            <option value="">-- Choose Faculty --</option>
                            @php
                                $facs = DB::select("SELECT * FROM faculties");
                            @endphp
                            @foreach ($facs as $f)
                                <option value="{{ $f->id }}">Faculty of {{ $f->name }}</option>
                            @endforeach
                        </select>
                        <span class="validation-error-msg" id="error_c_faculty"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_rep">Representative Code</label>
                        <input class="gov-input" id="c_rep" name="student_rep" type="text" placeholder="e.g. REP-CODE">
                    </div>

                    <div class="gov-form-group" style="grid-column: span 2;">
                        <h4 style="margin: 10px 0 5px 0; color: var(--text-heading); font-size:14px; border-bottom:1.5px solid var(--border-main); padding-bottom:3px;">Permanent Residential Address</h4>
                    </div>

                    <div class="gov-form-group" style="grid-column: span 2;">
                        <label class="gov-label" for="c_street_address">Street Address <span style="color:#d4351c;">*</span></label>
                        <input class="gov-input" id="c_street_address" name="student_street_address" type="text" required placeholder="Street name and house/apartment number">
                        <span class="validation-error-msg" id="error_c_street_address"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_city">City / Town <span style="color:#d4351c;">*</span></label>
                        <input class="gov-input" id="c_city" name="student_city" type="text" required placeholder="e.g. London">
                        <span class="validation-error-msg" id="error_c_city"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_country">Country <span style="color:#d4351c;">*</span></label>
                        <select class="gov-select" id="c_country" name="student_country" required style="padding-top:8px; padding-bottom:8px;">
                            <option value="">-- Choose Country --</option>
                            <option value="United Kingdom">🇬🇧 United Kingdom</option>
                            <option value="United States">🇺🇸 United States</option>
                            <option value="Canada">🇨🇦 Canada</option>
                            <option value="Australia">🇦🇺 Australia</option>
                            <option value="Pakistan">🇵🇰 Pakistan</option>
                            <option value="India">🇮🇳 India</option>
                            <option value="United Arab Emirates">🇦🇪 United Arab Emirates</option>
                            <option value="Saudi Arabia">🇸🇦 Saudi Arabia</option>
                            <option value="Germany">🇩🇪 Germany</option>
                            <option value="France">🇫🇷 France</option>
                            <option value="South Africa">🇿🇦 South Africa</option>
                            <option value="Nigeria">🇳🇬 Nigeria</option>
                            <option value="Ireland">🇮🇪 Ireland</option>
                            <option value="New Zealand">🇳🇿 New Zealand</option>
                            <option value="Singapore">🇸🇬 Singapore</option>
                            <option value="Malaysia">🇲🇾 Malaysia</option>
                        </select>
                        <span class="validation-error-msg" id="error_c_country"></span>
                    </div>

                    <div class="gov-form-group" style="grid-column: span 2;">
                        <label class="gov-label" for="c_zip_code">Zip Code / Postcode <span style="color:#999;font-weight:400;">(optional)</span></label>
                        <input class="gov-input" id="c_zip_code" name="student_zip_code" type="text" placeholder="e.g. SW1A 1AA">
                        <span class="validation-error-msg" id="error_c_zip_code"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="c_status">Account Status</label>
                        <select class="gov-select" id="c_status" name="student_status" required>
                            <option value="active">Active (Tuition Paid)</option>
                            <option value="pending_manual_unlock">Pending Tuition Payment</option>
                        </select>
                    </div>
                </div>

                <div class="modal-form-actions">
                    <button type="button" class="gov-button gov-button-secondary" onclick="hideCreateModal()" style="border-radius:6px; padding: 10px 20px;">Cancel</button>
                    <button type="submit" class="gov-button" style="border-radius:6px; padding: 10px 24px;">Create Student Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ====================================================================== -->
    <!-- EDIT STUDENT MODAL POPUP -->
    <!-- ====================================================================== -->
    <div id="editStudentModal" class="db-modal-overlay" style="display: none;">
        <div class="db-modal">
            <span class="db-modal-close" onclick="hideEditModal()">&times;</span>
            <h3 style="margin-bottom: 25px; font-size:18px; color: #002F6C;">Edit Student Record</h3>
            
            <form action="{{ route('lms.dashboard') }}?page=students" method="POST" novalidate onsubmit="return validateEditStudent()">
                @csrf
                <input type="hidden" name="edit_student" value="1">
                <input type="hidden" id="e_id" name="student_id">
                
                <div class="modal-form-grid">
                    <div class="gov-form-group">
                        <label class="gov-label" for="e_name">Full Name</label>
                        <input class="gov-input" id="e_name" name="student_name" type="text" required>
                        <span class="validation-error-msg" id="error_e_name"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_dob">Date of Birth</label>
                        <input class="gov-input" id="e_dob" name="student_dob" type="date" required>
                        <span class="validation-error-msg" id="error_e_dob"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_email">Email Address</label>
                        <input class="gov-input" id="e_email" name="student_email" type="email" required>
                        <span class="validation-error-msg" id="error_e_email"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_whatsapp">WhatsApp Number</label>
                        <input class="gov-input" id="e_whatsapp" name="student_whatsapp" type="tel" required>
                        <span class="validation-error-msg" id="error_e_whatsapp"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_faculty">Faculty Program</label>
                        <select class="gov-select" id="e_faculty" name="student_faculty" required>
                            <option value="">-- Choose Faculty --</option>
                            @foreach ($facs as $f)
                                <option value="{{ $f->id }}">Faculty of {{ $f->name }}</option>
                            @endforeach
                        </select>
                        <span class="validation-error-msg" id="error_e_faculty"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_rep">Representative Code</label>
                        <input class="gov-input" id="e_rep" name="student_rep" type="text">
                    </div>

                    <div class="gov-form-group" style="grid-column: span 2;">
                        <h4 style="margin: 10px 0 5px 0; color: var(--text-heading); font-size:14px; border-bottom:1.5px solid var(--border-main); padding-bottom:3px;">Permanent Residential Address</h4>
                    </div>

                    <div class="gov-form-group" style="grid-column: span 2;">
                        <label class="gov-label" for="e_street_address">Street Address <span style="color:#d4351c;">*</span></label>
                        <input class="gov-input" id="e_street_address" name="student_street_address" type="text" required>
                        <span class="validation-error-msg" id="error_e_street_address"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_city">City / Town <span style="color:#d4351c;">*</span></label>
                        <input class="gov-input" id="e_city" name="student_city" type="text" required>
                        <span class="validation-error-msg" id="error_e_city"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_country">Country <span style="color:#d4351c;">*</span></label>
                        <select class="gov-select" id="e_country" name="student_country" required style="padding-top:8px; padding-bottom:8px;">
                            <option value="">-- Choose Country --</option>
                            <option value="United Kingdom">🇬🇧 United Kingdom</option>
                            <option value="United States">🇺🇸 United States</option>
                            <option value="Canada">🇨🇦 Canada</option>
                            <option value="Australia">🇦🇺 Australia</option>
                            <option value="Pakistan">🇵🇰 Pakistan</option>
                            <option value="India">🇮🇳 India</option>
                            <option value="United Arab Emirates">🇦🇪 United Arab Emirates</option>
                            <option value="Saudi Arabia">🇸🇦 Saudi Arabia</option>
                            <option value="Germany">🇩🇪 Germany</option>
                            <option value="France">🇫🇷 France</option>
                            <option value="South Africa">🇿🇦 South Africa</option>
                            <option value="Nigeria">🇳🇬 Nigeria</option>
                            <option value="Ireland">🇮🇪 Ireland</option>
                            <option value="New Zealand">🇳🇿 New Zealand</option>
                            <option value="Singapore">🇸🇬 Singapore</option>
                            <option value="Malaysia">🇲🇾 Malaysia</option>
                        </select>
                        <span class="validation-error-msg" id="error_e_country"></span>
                    </div>

                    <div class="gov-form-group" style="grid-column: span 2;">
                        <label class="gov-label" for="e_zip_code">Zip Code / Postcode <span style="color:#999;font-weight:400;">(optional)</span></label>
                        <input class="gov-input" id="e_zip_code" name="student_zip_code" type="text">
                        <span class="validation-error-msg" id="error_e_zip_code"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="e_status">Account Status</label>
                        <select class="gov-select" id="e_status" name="student_status" required>
                            <option value="active">Active (Tuition Paid)</option>
                            <option value="pending_manual_unlock">Pending Tuition Payment</option>
                            <option value="locked">Locked</option>
                        </select>
                    </div>
                </div>

                <div class="modal-form-actions">
                    <button type="button" class="gov-button gov-button-secondary" onclick="hideEditModal()" style="border-radius:6px; padding: 10px 20px;">Cancel</button>
                    <button type="submit" class="gov-button" style="border-radius:6px; padding: 10px 24px;">Save Updates</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ====================================================================== -->
    <!-- ADD COURSE MODAL POPUP -->
    <!-- ====================================================================== -->
    <div id="addCourseModal" class="db-modal-overlay" style="display: none;">
        <div class="db-modal" style="max-width: 600px;">
            <span class="db-modal-close" onclick="hideAddCourseModal()">&times;</span>
            <h3 style="margin-bottom: 25px; font-size:18px; color: var(--text-heading);">Add Academic Course</h3>
            
            <form action="{{ route('lms.dashboard') }}?page=courses" method="POST" novalidate onsubmit="return validateAddCourse()">
                @csrf
                <input type="hidden" name="add_course" value="1">
                
                <div class="modal-form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="gov-form-group" style="grid-column: span 2;">
                        <label class="gov-label" for="add_course_name">Course / Faculty Name</label>
                        <input class="gov-input" id="add_course_name" name="course_name" type="text" required placeholder="e.g. Nursing, Midwifery & Health Sciences" style="width:100%;">
                        <span class="validation-error-msg" id="error_add_course_name"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="add_course_code">Course Code</label>
                        <input class="gov-input" id="add_course_code" name="course_code" type="text" placeholder="e.g. NUR-101" style="width:100%;">
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="add_course_duration">Duration</label>
                        <input class="gov-input" id="add_course_duration" name="course_duration" type="text" placeholder="e.g. 1 Year / 6 Months" style="width:100%;">
                    </div>

                    <div class="gov-form-group" style="grid-column: span 2;">
                        <label class="gov-label" for="add_course_fee">Tuition Fee (£)</label>
                        <input class="gov-input" id="add_course_fee" name="course_fee" type="number" step="0.01" placeholder="e.g. 2249.00" style="width:100%;">
                        <span class="validation-error-msg" id="error_add_course_fee"></span>
                    </div>

                    <div class="gov-form-group" style="grid-column: span 2;">
                        <label class="gov-label" for="add_course_description">Course Description</label>
                        <textarea class="gov-input" id="add_course_description" name="course_description" rows="3" placeholder="Provide course summary..." style="width:100%; height: auto; min-height: 80px; resize: vertical; padding: 10px; font-family: inherit;"></textarea>
                    </div>
                </div>

                <div class="modal-form-actions" style="margin-top: 25px;">
                    <button type="button" class="gov-button gov-button-secondary" onclick="hideAddCourseModal()" style="border-radius:6px; padding: 10px 20px;">Cancel</button>
                    <button type="submit" class="gov-button" style="border-radius:6px; padding: 10px 24px;">Add Course</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ====================================================================== -->
    <!-- EDIT COURSE MODAL POPUP -->
    <!-- ====================================================================== -->
    <div id="editCourseModal" class="db-modal-overlay" style="display: none;">
        <div class="db-modal" style="max-width: 600px;">
            <span class="db-modal-close" onclick="hideEditCourseModal()">&times;</span>
            <h3 style="margin-bottom: 25px; font-size:18px; color: var(--text-heading);">Edit Academic Course</h3>
            
            <form action="{{ route('lms.dashboard') }}?page=courses" method="POST" novalidate onsubmit="return validateEditCourse()">
                @csrf
                <input type="hidden" name="edit_course" value="1">
                <input type="hidden" id="edit_course_id" name="course_id">
                
                <div class="modal-form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="gov-form-group" style="grid-column: span 2;">
                        <label class="gov-label" for="edit_course_name">Course / Faculty Name</label>
                        <input class="gov-input" id="edit_course_name" name="course_name" type="text" required style="width:100%;">
                        <span class="validation-error-msg" id="error_edit_course_name"></span>
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="edit_course_code">Course Code</label>
                        <input class="gov-input" id="edit_course_code" name="course_code" type="text" style="width:100%;">
                    </div>

                    <div class="gov-form-group">
                        <label class="gov-label" for="edit_course_duration">Duration</label>
                        <input class="gov-input" id="edit_course_duration" name="course_duration" type="text" style="width:100%;">
                    </div>

                    <div class="gov-form-group" style="grid-column: span 2;">
                        <label class="gov-label" for="edit_course_fee">Tuition Fee (£)</label>
                        <input class="gov-input" id="edit_course_fee" name="course_fee" type="number" step="0.01" style="width:100%;">
                        <span class="validation-error-msg" id="error_edit_course_fee"></span>
                    </div>

                    <div class="gov-form-group" style="grid-column: span 2;">
                        <label class="gov-label" for="edit_course_description">Course Description</label>
                        <textarea class="gov-input" id="edit_course_description" name="course_description" rows="3" style="width:100%; height: auto; min-height: 80px; resize: vertical; padding: 10px; font-family: inherit;"></textarea>
                    </div>
                </div>

                <div class="modal-form-actions" style="margin-top: 25px;">
                    <button type="button" class="gov-button gov-button-secondary" onclick="hideEditCourseModal()" style="border-radius:6px; padding: 10px 20px;">Cancel</button>
                    <button type="submit" class="gov-button" style="border-radius:6px; padding: 10px 24px;">Save Updates</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toggle Sidebar JavaScript for Mobile -->
    <script>
        function toggleSidebar() {
            if (window.innerWidth <= 992) {
                var sidebar = document.getElementById('dbSidebar');
                sidebar.classList.toggle('open');
            } else {
                var container = document.querySelector('.db-layout-container');
                container.classList.toggle('collapsed');
                var isCollapsed = container.classList.contains('collapsed');
                document.cookie = "db_sidebar_collapsed=" + (isCollapsed ? "1" : "0") + "; path=/; max-age=31536000";
            }
        }

        // Close sidebar when clicking main content area on mobile viewports
        document.addEventListener('DOMContentLoaded', function() {
            var mainContent = document.querySelector('.db-main');
            if (mainContent) {
                mainContent.addEventListener('click', function(e) {
                    var sidebar = document.getElementById('dbSidebar');
                    var toggleBtn = document.querySelector('.db-mobile-toggle');
                    if (sidebar && sidebar.classList.contains('open') && e.target !== toggleBtn && !toggleBtn.contains(e.target) && !sidebar.contains(e.target)) {
                        sidebar.classList.remove('open');
                    }
                });
            }
        });

        // Modal Helpers
        function clearModalErrors() {
            document.querySelectorAll('.validation-error-msg').forEach(function(span) {
                span.style.display = 'none';
                span.innerText = '';
            });
        }

        function showModalError(fieldId, msg) {
            var span = document.getElementById('error_' + fieldId);
            if (span) {
                span.innerText = msg;
                span.style.display = 'block';
            }
        }

        function showCreateModal() {
            clearModalErrors();
            document.getElementById('c_street_address').value = '';
            document.getElementById('c_city').value = '';
            document.getElementById('c_country').value = '';
            document.getElementById('c_zip_code').value = '';
            document.getElementById('createStudentModal').style.display = 'flex';
        }
        function hideCreateModal() {
            clearModalErrors();
            document.getElementById('createStudentModal').style.display = 'none';
        }

        function showEditModal(studentData) {
            clearModalErrors();
            document.getElementById('e_id').value = studentData.id;
            document.getElementById('e_name').value = studentData.full_name;
            document.getElementById('e_dob').value = studentData.dob;
            document.getElementById('e_email').value = studentData.email;
            document.getElementById('e_whatsapp').value = studentData.whatsapp_number;
            document.getElementById('e_faculty').value = studentData.faculty_id || '';
            document.getElementById('e_rep').value = studentData.rep_code || '';
            document.getElementById('e_street_address').value = studentData.street_address || '';
            document.getElementById('e_city').value = studentData.city || '';
            document.getElementById('e_country').value = studentData.country || '';
            document.getElementById('e_zip_code').value = studentData.zip_code || '';
            document.getElementById('e_status').value = studentData.account_status;

            document.getElementById('editStudentModal').style.display = 'flex';
        }
        function hideEditModal() {
            clearModalErrors();
            document.getElementById('editStudentModal').style.display = 'none';
        }

        // ============================================================
        // THEME SWITCHING ENGINE (localStorage Persistence)
        // ============================================================
        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('lms_theme', theme);
            updateThemeOptions(theme);
            document.getElementById('themeDropdown').classList.remove('open');
        }

        function toggleThemeDropdown() {
            var dd = document.getElementById('themeDropdown');
            dd.classList.toggle('open');
        }

        function updateThemeOptions(activeTheme) {
            var options = document.querySelectorAll('.theme-option');
            options.forEach(function(opt) {
                opt.classList.remove('active');
                if (opt.textContent.trim().toLowerCase() === activeTheme) {
                    opt.classList.add('active');
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            var wrap = document.querySelector('.theme-toggle-wrap');
            if (wrap && !wrap.contains(e.target)) {
                document.getElementById('themeDropdown').classList.remove('open');
            }
        });

        function togglePasswordVisibility(inputId, buttonEl) {
            var input = document.getElementById(inputId);
            if (!input) return;
            var type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            if (type === 'password') {
                buttonEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                buttonEl.setAttribute('aria-label', 'Show password');
            } else {
                buttonEl.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
                buttonEl.setAttribute('aria-label', 'Hide password');
            }
        }

        // Apply saved theme on page load
        (function() {
            var saved = localStorage.getItem('lms_theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
            updateThemeOptions(saved);
        })();

        // Input sanitization & Validation for Dashboard
        document.addEventListener('DOMContentLoaded', function() {
            var c_whatsapp = document.getElementById('c_whatsapp');
            if (c_whatsapp) {
                c_whatsapp.addEventListener('input', function(e) {
                    e.target.value = e.target.value.replace(/[^0-9+\s-]/g, '');
                });
            }

            var e_whatsapp = document.getElementById('e_whatsapp');
            if (e_whatsapp) {
                e_whatsapp.addEventListener('input', function(e) {
                    e.target.value = e.target.value.replace(/[^0-9+\s-]/g, '');
                });
            }
        });

        function validateProfileInfo() {
            var email = document.getElementById('p_email').value.trim();
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('Please enter a valid email address.');
                return false;
            }
            return true;
        }

        function validateCreateStudent() {
            clearModalErrors();
            var name = document.getElementById('c_name').value.trim();
            var dob = document.getElementById('c_dob').value;
            var email = document.getElementById('c_email').value.trim();
            var whatsapp = document.getElementById('c_whatsapp').value.trim();
            var faculty = document.getElementById('c_faculty').value;
            var street = document.getElementById('c_street_address').value.trim();
            var city = document.getElementById('c_city').value.trim();
            var country = document.getElementById('c_country').value;
            var hasError = false;

            if (!street) {
                showModalError('c_street_address', 'Street Address is required.');
                hasError = true;
            }
            if (!city) {
                showModalError('c_city', 'City is required.');
                hasError = true;
            }
            if (!country) {
                showModalError('c_country', 'Country selection is required.');
                hasError = true;
            }
            if (!name) {
                showModalError('c_name', 'Full Name is required.');
                hasError = true;
            }
            if (!dob) {
                showModalError('c_dob', 'Date of Birth is required.');
                hasError = true;
            }
            if (!email) {
                showModalError('c_email', 'Email Address is required.');
                hasError = true;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showModalError('c_email', 'Please enter a valid email address.');
                hasError = true;
            }
            if (!whatsapp) {
                showModalError('c_whatsapp', 'WhatsApp number is required.');
                hasError = true;
            }
            if (!faculty) {
                showModalError('c_faculty', 'Faculty Program selection is required.');
                hasError = true;
            }

            return !hasError;
        }

        function validateEditStudent() {
            clearModalErrors();
            var name = document.getElementById('e_name').value.trim();
            var dob = document.getElementById('e_dob').value;
            var email = document.getElementById('e_email').value.trim();
            var whatsapp = document.getElementById('e_whatsapp').value.trim();
            var faculty = document.getElementById('e_faculty').value;
            var street = document.getElementById('e_street_address').value.trim();
            var city = document.getElementById('e_city').value.trim();
            var country = document.getElementById('e_country').value;
            var hasError = false;

            if (!street) {
                showModalError('e_street_address', 'Street Address is required.');
                hasError = true;
            }
            if (!city) {
                showModalError('e_city', 'City is required.');
                hasError = true;
            }
            if (!country) {
                showModalError('e_country', 'Country selection is required.');
                hasError = true;
            }
            if (!name) {
                showModalError('e_name', 'Full Name is required.');
                hasError = true;
            }
            if (!dob) {
                showModalError('e_dob', 'Date of Birth is required.');
                hasError = true;
            }
            if (!email) {
                showModalError('e_email', 'Email Address is required.');
                hasError = true;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showModalError('e_email', 'Please enter a valid email address.');
                hasError = true;
            }
            if (!whatsapp) {
                showModalError('e_whatsapp', 'WhatsApp number is required.');
                hasError = true;
            }
            if (!faculty) {
                showModalError('e_faculty', 'Faculty Program selection is required.');
                hasError = true;
            }

            return !hasError;
        }

        // Course Modal Helpers
        function showAddCourseModal() {
            clearModalErrors();
            document.getElementById('add_course_name').value = '';
            document.getElementById('add_course_code').value = '';
            document.getElementById('add_course_duration').value = '';
            document.getElementById('add_course_fee').value = '';
            document.getElementById('add_course_description').value = '';
            document.getElementById('addCourseModal').style.display = 'flex';
        }
        function hideAddCourseModal() {
            clearModalErrors();
            document.getElementById('addCourseModal').style.display = 'none';
        }

        function showEditCourseModal(courseData) {
            clearModalErrors();
            document.getElementById('edit_course_id').value = courseData.id;
            document.getElementById('edit_course_name').value = courseData.name;
            document.getElementById('edit_course_code').value = courseData.code || '';
            document.getElementById('edit_course_duration').value = courseData.duration || '';
            document.getElementById('edit_course_fee').value = courseData.fee || '0.00';
            document.getElementById('edit_course_description').value = courseData.description || '';
            document.getElementById('editCourseModal').style.display = 'flex';
        }
        function hideEditCourseModal() {
            clearModalErrors();
            document.getElementById('editCourseModal').style.display = 'none';
        }

        function validateAddCourse() {
            clearModalErrors();
            var name = document.getElementById('add_course_name').value.trim();
            var fee = document.getElementById('add_course_fee').value.trim();
            var hasError = false;
            if (!name) {
                showModalError('add_course_name', 'Course Name is required.');
                hasError = true;
            }
            if (fee && isNaN(fee)) {
                showModalError('add_course_fee', 'Please enter a valid numeric fee.');
                hasError = true;
            }
            return !hasError;
        }

        function validateEditCourse() {
            clearModalErrors();
            var name = document.getElementById('edit_course_name').value.trim();
            var fee = document.getElementById('edit_course_fee').value.trim();
            var hasError = false;
            if (!name) {
                showModalError('edit_course_name', 'Course Name is required.');
                hasError = true;
            }
            if (fee && isNaN(fee)) {
                showModalError('edit_course_fee', 'Please enter a valid numeric fee.');
                hasError = true;
            }
            return !hasError;
        }
    </script>

    <!-- Floating WhatsApp Support Button -->
    <a href="https://wa.me/447000000000" target="_blank" class="whatsapp-float-btn" aria-label="Chat with Support on WhatsApp">
        <svg viewBox="0 0 24 24" class="whatsapp-icon" xmlns="http://www.w3.org/2000/svg">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.717-1.456L0 24zm6.59-4.846c1.6.95 3.498 1.45 5.424 1.451 5.513 0 10.002-4.489 10.005-10.003.002-2.67-1.036-5.182-2.924-7.072-1.888-1.89-4.403-2.93-7.079-2.931-5.519 0-10.01 4.49-10.014 10.004-.002 1.933.504 3.82 1.465 5.433l-.963 3.52 3.606-.946zm11.517-7.234c-.303-.152-1.793-.884-2.071-.985-.278-.101-.48-.152-.682.152-.202.304-.783.985-.96 1.187-.178.203-.355.228-.658.076-1.218-.61-2.185-1.066-3.045-2.545-.228-.393.228-.364.65-.183.125.038.25.076.353.127.303.152.329.253.481.557.152.304.076.582-.038.81-.114.228-.682 1.088-.86 1.392-.177.304-.367.33-.67.177-1.23-.615-2.193-1.077-3.056-2.57-.23-.4-.015-.62.196-.827.19-.187.354-.354.48-.53.127-.178.189-.253.253-.405.064-.152.03-.304-.015-.405-.045-.101-.405-1.088-.557-1.443-.152-.354-.304-.304-.43-.304l-.38-.013c-.152-.002-.38.053-.582.253-.202.203-.783.76-1.063 1.342-.28.582-.81 1.747-.81 3.544 0 1.797 1.316 3.544 1.493 3.797.177.253 2.592 3.96 6.28 5.556.88.38 1.56.607 2.09.775.88.28 1.68.24 2.3.15.7-.1 2.07-.84 2.37-1.67.3-.83.3-1.545.21-1.696-.09-.15-.303-.228-.606-.38z"/>
        </svg>
        <span class="whatsapp-tooltip">Chat with Support</span>
    </a>

</body>
</html>
