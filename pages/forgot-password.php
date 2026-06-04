<?php include_once "../include/functions.php"; ?><!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#7C3AED",
                        "secondary": "#4F46E5",
                        "accent": "#EC4899",
                        "background-light": "#F3F4F6",
                        "background-dark": "#111827",
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <title>Forgot Password - Blog Fusion</title>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen">
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            <main class="flex-1 flex items-center justify-center p-4 md:p-8">

                <?php inject_project_toast(); ?>

                <!-- Toast Component -->
                <div id="toast" class="hidden fixed top-6 left-1/2 -translate-x-1/2 z-[9999] px-5 py-3 rounded-2xl shadow-xl text-sm font-bold flex items-center gap-2 bg-slate-900 text-white dark:bg-white dark:text-slate-900 transition-all duration-300"></div>

                <div class="layout-content-container flex flex-col w-full max-w-[480px] bg-white dark:bg-slate-900 rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800">
                    <!-- Hero Image Area -->
                    <div class="@container">
                        <div class="w-full bg-center bg-no-repeat bg-cover flex flex-col justify-end min-h-[160px] relative"
                            style="background-image: linear-gradient(135deg, #7C3AED 0%, #4F46E5 100%);">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="relative p-6">
                                <h1 class="text-white text-3xl font-black leading-tight tracking-[-0.033em]">Reset Password</h1>
                                <p class="text-slate-200 text-sm font-normal leading-normal">Recover access to your account</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Section -->
                    <div class="flex flex-col gap-y-4 p-6 md:p-8">
                        
                        <!-- STEP 1: Enter email -->
                        <div id="reset-step-1" class="space-y-4">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Enter your registered email address below. We'll send you a 6-digit verification code to reset your password.</p>
                            <div class="flex flex-col w-full">
                                <label class="text-slate-700 dark:text-slate-300 text-sm font-semibold leading-normal pb-2 px-1">Email Address</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors text-[20px]">mail</span>
                                    <input id="emailInput" class="form-input flex w-full rounded-xl text-slate-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:border-primary h-14 placeholder:text-slate-400 dark:placeholder:text-slate-500 pl-12 pr-4 text-base font-normal transition-all"
                                        placeholder="you@example.com" type="email" required />
                                </div>
                            </div>
                            <button id="send-code-btn" onclick="sendResetOtp()" class="mt-4 flex h-14 w-full items-center justify-center rounded-xl bg-primary text-white text-base font-bold tracking-tight hover:bg-secondary active:scale-[0.98] transition-all shadow-lg shadow-primary/25 gap-2">
                                <span class="material-symbols-outlined text-lg">send</span> Send Verification Code
                            </button>
                        </div>

                        <!-- STEP 2: Verify OTP and Reset -->
                        <div id="reset-step-2" class="space-y-4 hidden">
                            <p class="text-sm text-slate-500 dark:text-slate-400">We sent a verification code to <strong id="sent-email-label"></strong>. Enter the code and set your new password.</p>
                            
                            <!-- 6-digit OTP Inputs -->
                            <div class="flex flex-col w-full">
                                <label class="text-slate-700 dark:text-slate-300 text-sm font-semibold leading-normal pb-2 px-1 text-center">6-Digit Code</label>
                                <div class="flex gap-2 justify-between" id="otp-boxes">
                                    <input type="text" maxlength="1" class="otp-digit w-12 h-14 text-center text-2xl font-black bg-slate-50 dark:bg-slate-800 border border-slate-350 dark:border-slate-700 rounded-xl focus:border-primary focus:ring-0 outline-none transition-all text-slate-900 dark:text-white" />
                                    <input type="text" maxlength="1" class="otp-digit w-12 h-14 text-center text-2xl font-black bg-slate-50 dark:bg-slate-800 border border-slate-350 dark:border-slate-700 rounded-xl focus:border-primary focus:ring-0 outline-none transition-all text-slate-900 dark:text-white" />
                                    <input type="text" maxlength="1" class="otp-digit w-12 h-14 text-center text-2xl font-black bg-slate-50 dark:bg-slate-800 border border-slate-350 dark:border-slate-700 rounded-xl focus:border-primary focus:ring-0 outline-none transition-all text-slate-900 dark:text-white" />
                                    <input type="text" maxlength="1" class="otp-digit w-12 h-14 text-center text-2xl font-black bg-slate-50 dark:bg-slate-800 border border-slate-350 dark:border-slate-700 rounded-xl focus:border-primary focus:ring-0 outline-none transition-all text-slate-900 dark:text-white" />
                                    <input type="text" maxlength="1" class="otp-digit w-12 h-14 text-center text-2xl font-black bg-slate-50 dark:bg-slate-800 border border-slate-350 dark:border-slate-700 rounded-xl focus:border-primary focus:ring-0 outline-none transition-all text-slate-900 dark:text-white" />
                                    <input type="text" maxlength="1" class="otp-digit w-12 h-14 text-center text-2xl font-black bg-slate-50 dark:bg-slate-800 border border-slate-350 dark:border-slate-700 rounded-xl focus:border-primary focus:ring-0 outline-none transition-all text-slate-900 dark:text-white" />
                                </div>
                            </div>

                            <!-- New Password -->
                            <div class="flex flex-col w-full">
                                <label class="text-slate-700 dark:text-slate-300 text-sm font-semibold leading-normal pb-2 px-1">New Password</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors text-[20px]">lock</span>
                                    <input id="newPwd" oninput="checkStrength(this.value)" class="form-input flex w-full rounded-xl text-slate-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:border-primary h-14 placeholder:text-slate-400 dark:placeholder:text-slate-500 pl-12 pr-4 text-base font-normal transition-all"
                                        placeholder="••••••••" type="password" required />
                                </div>
                                <div class="h-1.5 rounded-full bg-slate-100 dark:bg-slate-800 mt-2 overflow-hidden">
                                    <div id="sfill" class="h-full rounded-full transition-all duration-300" style="width:0%"></div>
                                </div>
                                <p id="stext" class="text-xs text-slate-500 dark:text-slate-400 mt-1"></p>
                            </div>

                            <!-- Confirm Password -->
                            <div class="flex flex-col w-full">
                                <label class="text-slate-700 dark:text-slate-300 text-sm font-semibold leading-normal pb-2 px-1">Confirm Password</label>
                                <div class="relative group">
                                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors text-[20px]">lock</span>
                                    <input id="confirmPwd" class="form-input flex w-full rounded-xl text-slate-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:border-primary h-14 placeholder:text-slate-400 dark:placeholder:text-slate-500 pl-12 pr-4 text-base font-normal transition-all"
                                        placeholder="••••••••" type="password" required />
                                </div>
                            </div>

                            <button id="reset-btn" onclick="verifyAndResetPassword()" class="mt-4 flex h-14 w-full items-center justify-center rounded-xl bg-primary text-white text-base font-bold tracking-tight hover:bg-secondary active:scale-[0.98] transition-all shadow-lg shadow-primary/25 gap-2">
                                <span class="material-symbols-outlined text-lg">verified</span> Verify & Reset Password
                            </button>
                            
                            <button onclick="switchToStep1()" class="w-full text-center text-xs text-primary font-bold hover:underline py-1">Request new code</button>
                        </div>

                    </div>
                    <!-- Footer Link -->
                    <div class="p-6 text-center border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                        <p class="text-slate-600 dark:text-slate-400 text-sm">
                            Remember your password?
                            <a class="font-bold text-primary hover:text-secondary hover:underline transition-all"
                                href="login.php">Log In</a>
                        </p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // OTP Boxes auto-advance & backspace
        document.addEventListener('DOMContentLoaded', () => {
            const boxes = document.querySelectorAll('.otp-digit');
            boxes.forEach((box, idx, all) => {
                box.addEventListener('input', () => {
                    box.value = box.value.replace(/\D/g, '').slice(-1);
                    if (box.value && idx < all.length - 1) all[idx + 1].focus();
                });
                box.addEventListener('keydown', e => {
                    if (e.key === 'Backspace' && !box.value && idx > 0) all[idx - 1].focus();
                });
                box.addEventListener('paste', e => {
                    const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                    all.forEach((b, i) => b.value = text[i] || '');
                    e.preventDefault();
                });
            });
        });

        function getOtpValue() {
            return Array.from(document.querySelectorAll('.otp-digit')).map(b => b.value).join('');
        }

        function setBtnLoading(id, loading, label) {
            const btn = document.getElementById(id);
            if (!btn) return;
            btn.disabled = loading;
            btn.innerHTML = loading
                ? '<span class="animate-spin material-symbols-outlined text-lg">progress_activity</span> Processing...'
                : label;
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            if (!toast) return;
            toast.textContent = msg;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3500);
        }

        function checkStrength(pwd) {
            const fill = document.getElementById('sfill');
            const txt  = document.getElementById('stext');
            if (!pwd) { fill.style.width = '0%'; txt.textContent = ''; return; }
            let score = 0;
            if (pwd.length >= 8) score++;
            if (/[A-Z]/.test(pwd)) score++;
            if (/[0-9]/.test(pwd)) score++;
            if (/[^A-Za-z0-9]/.test(pwd)) score++;
            const pct = (score / 4) * 100;
            fill.style.width = pct + '%';
            if (score <= 1) {
                fill.className = 'h-full rounded-full transition-all duration-300 bg-red-500';
                txt.textContent = 'Weak';
                txt.className = 'text-xs text-red-500 mt-1 font-semibold';
            } else if (score <= 3) {
                fill.className = 'h-full rounded-full transition-all duration-300 bg-amber-500';
                txt.textContent = 'Medium';
                txt.className = 'text-xs text-amber-500 mt-1 font-semibold';
            } else {
                fill.className = 'h-full rounded-full transition-all duration-300 bg-green-500';
                txt.textContent = 'Strong';
                txt.className = 'text-xs text-green-500 mt-1 font-semibold';
            }
        }

        async function sendResetOtp() {
            const email = document.getElementById('emailInput').value.trim();
            if (!email) { showToast('Please enter your email address.'); return; }
            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) { showToast('Please enter a valid email address.'); return; }
            
            setBtnLoading('send-code-btn', true);
            const fd = new FormData();
            fd.append('action', 'send_reset_otp');
            fd.append('email', email);
            
            try {
                const res = await fetch('../actions/author_forgot_password.php', { method: 'POST', body: fd });
                const json = await res.json();
                if (json.success) {
                    document.getElementById('sent-email-label').textContent = json.email;
                    document.getElementById('reset-step-1').classList.add('hidden');
                    document.getElementById('reset-step-2').classList.remove('hidden');
                    
                    // Clear OTP fields
                    document.querySelectorAll('.otp-digit').forEach(b => b.value = '');
                    document.getElementById('newPwd').value = '';
                    document.getElementById('confirmPwd').value = '';
                    document.getElementById('sfill').style.width = '0%';
                    document.getElementById('stext').textContent = '';
                    setTimeout(() => document.querySelector('.otp-digit').focus(), 100);
                } else {
                    showToast(json.error || 'Failed to send OTP.');
                }
            } catch (e) {
                showToast('Network error occurred.');
            } finally {
                setBtnLoading('send-code-btn', false, '<span class="material-symbols-outlined text-lg">send</span> Send Verification Code');
            }
        }

        async function verifyAndResetPassword() {
            const otp = getOtpValue();
            const newPwd = document.getElementById('newPwd').value;
            const confirmPwd = document.getElementById('confirmPwd').value;
            
            if (otp.length < 6) { showToast('Please enter the 6-digit verification code.'); return; }
            if (!newPwd || !confirmPwd) { showToast('Please fill in all password fields.'); return; }
            if (newPwd.length < 8) { showToast('Password must be at least 8 characters.'); return; }
            if (newPwd !== confirmPwd) { showToast('Passwords do not match.'); return; }
            
            setBtnLoading('reset-btn', true);
            
            const fd1 = new FormData();
            fd1.append('action', 'verify_reset_otp');
            fd1.append('otp', otp);
            
            try {
                const res1 = await fetch('../actions/author_forgot_password.php', { method: 'POST', body: fd1 });
                const json1 = await res1.json();
                if (!json1.success) {
                    document.querySelectorAll('.otp-digit').forEach(b => b.classList.add('border-red-400'));
                    setTimeout(() => document.querySelectorAll('.otp-digit').forEach(b => b.classList.remove('border-red-400')), 1200);
                    showToast(json1.error || 'Incorrect OTP code.');
                    setBtnLoading('reset-btn', false, '<span class="material-symbols-outlined text-lg">verified</span> Verify & Reset Password');
                    return;
                }
                
                const fd2 = new FormData();
                fd2.append('action', 'reset_password');
                fd2.append('new_password', newPwd);
                fd2.append('confirm_password', confirmPwd);
                
                const res2 = await fetch('../actions/author_forgot_password.php', { method: 'POST', body: fd2 });
                const json2 = await res2.json();
                if (json2.success) {
                    showToast('✅ Password reset successfully! Redirecting to login...');
                    setTimeout(() => {
                        window.location.href = 'login.php?msg=reset_success';
                    }, 2000);
                } else {
                    showToast(json2.error || 'Password reset failed.');
                }
            } catch (e) {
                showToast('Network error occurred.');
            } finally {
                setBtnLoading('reset-btn', false, '<span class="material-symbols-outlined text-lg">verified</span> Verify & Reset Password');
            }
        }

        function switchToStep1() {
            document.getElementById('reset-step-2').classList.add('hidden');
            document.getElementById('reset-step-1').classList.remove('hidden');
        }
    </script>
</body>
</html>
