<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
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
    <title>Login - Blog Fusion</title>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen">
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            <main class="flex-1 flex items-center justify-center p-4 md:p-8">

                <?php if (isset($_GET['msg']) ) { ?>

                    <div id="alertBox" class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] w-full max-w-sm px-4">
                        <div
                            class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-lg">
                            <span class="material-symbols-outlined text-green-500">check_circle</span>
                            <p class="text-sm font-semibold">
                                <?php
                                    switch($_GET['msg']){
                                         case "registered":
                                            echo'Registration Successful';
                                            break;
                                        case "p_not_match":
                                            echo'Password not match. Please enter again';
                                            break;
                                        case "u_not_find":
                                            echo'Email not match. Please enter again';
                                            break;
                                    }
                                ?>
                            </p>
                        </div>
                    </div>

                    <script>
                        setTimeout(() => {
                            document.getElementById("alertBox")?.remove();
                        }, 2000);
                        if (window.history.replaceState) {
                            const url = new URL(window.location);
                            url.searchParams.delete("msg"); // remove msg parameter
                            window.history.replaceState({}, document.title, url.pathname);
                        }
                    </script>
                    

                <?php } ?>

                <div
                    class="layout-content-container flex flex-col w-full max-w-[480px] bg-white dark:bg-slate-900 rounded-xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800">
                    <!-- Hero Image Area -->
                    <div class="@container">
                        <div class="w-full bg-center bg-no-repeat bg-cover flex flex-col justify-end min-h-[160px] relative"
                            data-alt="Modern workspace with laptop and coffee"
                            style="background-image: linear-gradient(135deg, #7C3AED 0%, #4F46E5 100%);">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="relative p-6">
                                <h1 class="text-white text-3xl font-black leading-tight tracking-[-0.033em]">Welcome
                                    Back</h1>
                                <p class="text-slate-200 text-sm font-normal leading-normal">Login to your Blog Fusion
                                    account</p>
                            </div>
                        </div>
                    </div>
                    <!-- Form Section -->

                    <form class="flex flex-col gap-y-4 p-6 md:p-8" action="../actions/login_signup.php" method="POST">
                        <!-- Email Field -->
                        <div class="flex flex-col w-full">
                            <label
                                class="text-slate-700 dark:text-slate-300 text-sm font-semibold leading-normal pb-2 px-1">Email
                                Address</label>
                            <div class="relative group">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors text-[20px]">mail</span>
                                <input
                                    class="form-input flex w-full rounded-xl text-slate-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:border-primary h-14 placeholder:text-slate-400 dark:placeholder:text-slate-500 pl-12 pr-4 text-base font-normal transition-all"
                                    placeholder="you@example.com" type="email" name="email" />
                            </div>
                        </div>
                        <!-- Password Field -->
                        <div class="flex flex-col w-full">
                            <div class="flex justify-between items-center pb-2 px-1">
                                <label
                                    class="text-slate-700 dark:text-slate-300 text-sm font-semibold leading-normal">Password</label>
                                <a class="text-xs font-medium text-primary hover:text-secondary transition-colors"
                                    href="#">Forgot password?</a>
                            </div>
                            <div class="relative group" id="password-container">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors text-[20px]">lock</span>
                                <input
                                    class="form-input flex w-full rounded-xl text-slate-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/20 border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:border-primary h-14 placeholder:text-slate-400 dark:placeholder:text-slate-500 pl-12 pr-4 text-base font-normal transition-all"
                                    id="password-input" placeholder="••••••••" type="password" name="password" />
                                <button
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors focus:outline-none"
                                    id="toggle-password" type="button">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                        </div>
                        <!-- Login Button -->
                        <button
                            class="mt-4 flex h-14 w-full items-center justify-center rounded-xl bg-primary text-white text-base font-bold tracking-tight hover:bg-secondary active:scale-[0.98] transition-all shadow-lg shadow-primary/25"
                            type="submit" name="login">
                            Log In
                        </button>
                        <!-- Social Login Divider -->
                        <!-- Social Buttons -->
                    </form>
                    <!-- Footer Link -->
                    <div
                        class="p-6 text-center border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                        <p class="text-slate-600 dark:text-slate-400 text-sm">
                            Don't have an account?
                            <a class="font-bold text-primary hover:text-secondary hover:underline transition-all"
                                href="register.php">Sign up</a>
                        </p>
                    </div>
                </div>
            </main>


        </div>
    </div>
    <script>
        const toggleBtn = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password-input');
        
        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                const icon = this.querySelector('.material-symbols-outlined');
                icon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
            });
        }
    </script>
</body>

</html>