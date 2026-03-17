
<?php include "../include/session.php";
include "../include/db.php";
include "../config.php";
global $conn;
$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
$image=$data['profile_image'];
?>


<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Blog Fusion | Insights into Tech, Programming &amp; Business</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap"
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
</head>





<body class="bg-background-light dark:bg-background-dark min-h-screen">
    <?php include "../include/navbar.php";?>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == "login_success"){ ?>

            <div id="alertBox" class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] w-full max-w-sm px-4">
                <div
                    class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-lg">
                    <span class="material-symbols-outlined text-green-500">check_circle</span>
                    <p class="text-sm font-semibold">Login successful</p>
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
    <main>
        <!-- HERO SECTION -->
        <section class="relative py-12 lg:py-24 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative z-10 @container">
                    <div
                        class="flex flex-col items-center text-center gap-8 py-12 px-6 rounded-3xl bg-slate-900 dark:bg-slate-800/50 shadow-2xl relative overflow-hidden">
                        <!-- Background Pattern Decor -->
                        <div class="absolute inset-0 opacity-20 pointer-events-none bg-[radial-gradient(circle_at_50%_50%,_var(--tw-gradient-stops))] from-primary via-secondary to-accent"
                            data-alt="Abstract colorful gradient pattern background"></div>
                        <div class="flex flex-col gap-4 max-w-3xl relative z-10">
                            <span
                                class="inline-flex items-center self-center px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase bg-primary/20 text-primary mb-2">
                                Discover the Future
                            </span>
                            <h1 class="text-4xl md:text-6xl font-black text-white leading-tight tracking-tight">
                                Welcome to the <span
                                    class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-secondary to-accent">Blog
                                    Website</span>
                            </h1>
                            <p class="text-lg text-slate-300 font-normal leading-relaxed max-w-2xl mx-auto">
                                Explore expert insights, deep dives into cutting-edge technology, programming tutorials,
                                and modern business strategies to stay ahead in the digital world.
                            </p>
                        </div>
                        <div class="flex flex-wrap justify-center gap-4 relative z-10">
                            <button
                                class="h-12 px-8 rounded-xl bg-primary text-white font-bold hover:scale-105 transition-transform shadow-xl shadow-primary/30 flex items-center gap-2">
                                Explore Posts <span class="material-symbols-outlined">trending_flat</span>
                            </button>
                            <button
                                class="h-12 px-8 rounded-xl bg-white/10 text-white font-bold backdrop-blur-sm border border-white/20 hover:bg-white/20 transition-all">
                                Join Newsletter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- LATEST POSTS -->
        <section class="py-12 bg-white dark:bg-slate-900/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2 tracking-tight">Latest
                            Stories</h2>
                        <div class="h-1.5 w-20 bg-primary rounded-full"></div>
                    </div>
                    <a class="text-primary font-bold flex items-center gap-1 hover:gap-2 transition-all" href="#">
                        View All <span class="material-symbols-outlined">chevron_right</span>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Post 1 -->
                    <article
                        class="group flex flex-col bg-background-light dark:bg-slate-800 rounded-2xl overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all">
                        <div class="relative aspect-video overflow-hidden">
                            <img class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-500"
                                data-alt="Artificial intelligence circuit board futuristic visual"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAr7YXcv7tazLnhV8wJ0cT4RShm4iWAlU4GHDSFlC9eAilIJt2mWM_VgI1Y9okFiNEyLG-yRGAvwT-DNt6WDb6ceWEhk1HUSuJkQitc9ejgpS5CjMtsyXOguMEm-H6L39NiQmU0dG-kH5a79Hb4d6dHFd_W1IKC4LCacVa_ZRZJpu27RcdGeBMdvk4GbaEwd076rIV3Mu_ELkuPin2NE3uQArzM5rhA2IPo9diL4nyOUmFjU9mPA6nbd1KS3baW1Viu1_1ahFQah4Y" />
                            <div class="absolute top-4 left-4">
                                <span
                                    class="px-3 py-1 bg-primary text-white text-xs font-bold rounded-lg uppercase">Tech</span>
                            </div>
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <div
                                class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs mb-3 font-medium">
                                <span class="material-symbols-outlined text-sm">calendar_today</span> Oct 24, 2023
                                <span class="mx-1">•</span>
                                <span class="material-symbols-outlined text-sm">schedule</span> 5 min read
                            </div>
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white mb-3 group-hover:text-primary transition-colors">
                                The Unstoppable Evolution of Generative AI</h3>
                            <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-2 mb-6">Explore how LLMs are
                                reshaping creative workflows and what the future holds for human-AI collaboration.</p>
                            <div
                                class="mt-auto flex items-center justify-between border-t border-slate-200 dark:border-slate-700 pt-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                        <img class="w-full h-full object-cover" data-alt="Author profile picture male"
                                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuD-Z8bULXzFVTuF2XXRxC_YC40ai2I17FvfS6atlzBf9P01sBM-NCXJtUaGpDaaQBOTMwMtDTrMjWnLcON_-dJEDE7pLxvAWqMQPCLTZocZRJkfSpffdkb_B5woy7Uxa8JqUVnxxtWkxjLZkWcWp7vC9PU2bKr5AZvQc0wow1SwNh29lIFfXof1gdV-ZxA7DPann9tkiD_V0FZ3bVAdLIAj-M60wD1oQqR53YRlQi5DlKyMHMpqRkuipD8dTh8hQGtO3l3bAKj43Js" />
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Alex
                                        Rivers</span>
                                </div>
                                <button class="text-primary">
                                    <span class="material-symbols-outlined">bookmark</span>
                                </button>
                            </div>
                        </div>
                    </article>
                    <!-- Post 2 -->
                    <article
                        class="group flex flex-col bg-background-light dark:bg-slate-800 rounded-2xl overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all">
                        <div class="relative aspect-video overflow-hidden">
                            <img class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-500"
                                data-alt="React logo and code on a dark screen"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAwmV65BpjFy5inoP5f1CEIrpLeYyEMInFjMj41DTYurS9qOkdVJcPtZvvlxGGE2tYrLZkiLXvsRkc2yx6cUlPzS5rQocHL6eouNFDMj_Xf652fSenHWopbODSARuAoyuGNmyID5Fgi4rT-gmOoLRnCg1FtwAnExMALu5eNJRtLOMU0qb41nxJQDRkdvzYiArFpR8eZ8H25_vJkpIH7H2Zmi3LmPoZQzaLdiZS_FtRR3e6v2-tLWuK7SJISDIbwBZi9Y0OiBLf5lb8" />
                            <div class="absolute top-4 left-4">
                                <span
                                    class="px-3 py-1 bg-secondary text-white text-xs font-bold rounded-lg uppercase">Programming</span>
                            </div>
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <div
                                class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs mb-3 font-medium">
                                <span class="material-symbols-outlined text-sm">calendar_today</span> Oct 22, 2023
                                <span class="mx-1">•</span>
                                <span class="material-symbols-outlined text-sm">schedule</span> 8 min read
                            </div>
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white mb-3 group-hover:text-primary transition-colors">
                                Mastering React Server Components</h3>
                            <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-2 mb-6">A deep dive into
                                Next.js 14 and how server-side rendering is evolving for better performance.</p>
                            <div
                                class="mt-auto flex items-center justify-between border-t border-slate-200 dark:border-slate-700 pt-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                        <img class="w-full h-full object-cover" data-alt="Author profile picture female"
                                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuD2Gex8xYZvyeyv2akKrLdCbt2SLD0_AC-RxPkP0jauUoyGL_RXmIyq7BQeg8oP2rHaJ_QpsYpdcJGuTut1wgDKttuOY2hCOw6L5I0-M6AGD4IJ6cp9D84EaNphopWVB7F3Fz2g3PuZ1Lsto8ZhY9OxbbiTSBt9-WDGF_le6AczetKZxrYBKEeg8ZruYSZ7dquRPq88xw1ghjcdS1HyVsTmIJtHq5SnPlwUnolPXSdsV6d1ExiAnf1mHY2GPBGj8AeTOP4aLTq1-6M" />
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Sarah Chen</span>
                                </div>
                                <button class="text-primary">
                                    <span class="material-symbols-outlined">bookmark</span>
                                </button>
                            </div>
                        </div>
                    </article>
                    <!-- Post 3 -->
                    <article
                        class="group flex flex-col bg-background-light dark:bg-slate-800 rounded-2xl overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all">
                        <div class="relative aspect-video overflow-hidden">
                            <img class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-500"
                                data-alt="Business meeting around a large table"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuA7V2XZWdKMmDIDysH8-zmC21VKTAUVEfFDffj77Vp-prSMCngpe_7WK1hxFGU4OOswsMC6rAEx6X0ldxNnVdfnw__UTB19h5BWM_SUOyb3wd2HetcrEPlxpEAhayXd0TOFhidCqe4BsSKrvauBQcUgOkPtZ66miRu216htCQzELko5J_Kx74fo1BGsd7TYliSa0W2mr9sOWgYXgCFxUX5e8FvlsZghjQAWyiRLVbKQjOPf3CIWo0h5CHwe_S9eXrsWlLeTk1hWkXg" />
                            <div class="absolute top-4 left-4">
                                <span
                                    class="px-3 py-1 bg-accent text-white text-xs font-bold rounded-lg uppercase">Business</span>
                            </div>
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <div
                                class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs mb-3 font-medium">
                                <span class="material-symbols-outlined text-sm">calendar_today</span> Oct 20, 2023
                                <span class="mx-1">•</span>
                                <span class="material-symbols-outlined text-sm">schedule</span> 6 min read
                            </div>
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white mb-3 group-hover:text-primary transition-colors">
                                Scaling Your Startup in 2024</h3>
                            <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-2 mb-6">Key metrics every
                                founder should track when moving from Seed to Series A funding rounds.</p>
                            <div
                                class="mt-auto flex items-center justify-between border-t border-slate-200 dark:border-slate-700 pt-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                        <img class="w-full h-full object-cover" data-alt="Author profile picture male"
                                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBIkaoNJNBrZFzkRjURpYcGP0toJGVGe5TSfWQ9NRWiS9vEci6iv1VZQ0WUH4RPmyCdv8XnkuGx1DBG6vFeOibhcdknE0kyQnrVg41x30ouo8mdLA0vfvzrdpahY9VjXlsYxBxcXuw58bfV11o-lKU_eHHlqYCyGSlU0rX1nSNWPZ9H5HaKMLtPvuYWSeUZtyCZOkHB_GbWdU-A00wRx0nsLrk0y2QPvo7t26QBBDF_lLcH3JUQlgxeQVk4Np9Ja3EP4NIGE68NosI" />
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Mark
                                        Thompson</span>
                                </div>
                                <button class="text-primary">
                                    <span class="material-symbols-outlined">bookmark</span>
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>
        <!-- CATEGORIES SECTION -->
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-4 tracking-tight">Explore by Topic
                    </h2>
                    <p class="text-slate-600 dark:text-slate-400 max-w-xl mx-auto">Dive into our most popular categories
                        to find the content that matters most to your journey.</p>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <a class="group relative flex flex-col items-center justify-center p-8 rounded-2xl bg-white dark:bg-slate-800 shadow-lg border border-slate-100 dark:border-slate-700 hover:border-primary transition-all overflow-hidden"
                        href="#">
                        <div class="absolute inset-0 bg-primary/5 group-hover:bg-primary/10 transition-colors"></div>
                        <span class="material-symbols-outlined text-4xl text-primary mb-4 relative z-10">biotech</span>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white relative z-10">Technology</h4>
                        <p class="text-slate-500 dark:text-slate-400 text-xs relative z-10">142 Articles</p>
                    </a>
                    <a class="group relative flex flex-col items-center justify-center p-8 rounded-2xl bg-white dark:bg-slate-800 shadow-lg border border-slate-100 dark:border-slate-700 hover:border-secondary transition-all overflow-hidden"
                        href="#">
                        <div class="absolute inset-0 bg-secondary/5 group-hover:bg-secondary/10 transition-colors">
                        </div>
                        <span
                            class="material-symbols-outlined text-4xl text-secondary mb-4 relative z-10">terminal</span>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white relative z-10">Programming</h4>
                        <p class="text-slate-500 dark:text-slate-400 text-xs relative z-10">86 Articles</p>
                    </a>
                    <a class="group relative flex flex-col items-center justify-center p-8 rounded-2xl bg-white dark:bg-slate-800 shadow-lg border border-slate-100 dark:border-slate-700 hover:border-accent transition-all overflow-hidden"
                        href="#">
                        <div class="absolute inset-0 bg-accent/5 group-hover:bg-accent/10 transition-colors"></div>
                        <span class="material-symbols-outlined text-4xl text-accent mb-4 relative z-10">insights</span>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white relative z-10">Business</h4>
                        <p class="text-slate-500 dark:text-slate-400 text-xs relative z-10">64 Articles</p>
                    </a>
                    <a class="group relative flex flex-col items-center justify-center p-8 rounded-2xl bg-white dark:bg-slate-800 shadow-lg border border-slate-100 dark:border-slate-700 hover:border-slate-400 transition-all overflow-hidden"
                        href="#">
                        <div class="absolute inset-0 bg-slate-500/5 group-hover:bg-slate-500/10 transition-colors">
                        </div>
                        <span
                            class="material-symbols-outlined text-4xl text-slate-600 dark:text-slate-400 mb-4 relative z-10">palette</span>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white relative z-10">Design</h4>
                        <p class="text-slate-500 dark:text-slate-400 text-xs relative z-10">52 Articles</p>
                    </a>
                </div>
            </div>
        </section>
        <!-- POPULAR POSTS -->
        <section class="py-16 bg-slate-900 dark:bg-slate-950 text-white overflow-hidden relative">
            <div
                class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-96 h-96 bg-primary/20 blur-[100px] rounded-full">
            </div>
            <div
                class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-96 h-96 bg-accent/10 blur-[100px] rounded-full">
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                    <div class="flex items-center gap-4">
                        <span class="material-symbols-outlined text-4xl text-accent">trending_up</span>
                        <h2 class="text-3xl font-black tracking-tight">Trending Now</h2>
                    </div>
                    <div class="flex gap-2">
                        <button
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center border border-white/10 transition-all">
                            <span class="material-symbols-outlined">arrow_back</span>
                        </button>
                        <button
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center border border-white/10 transition-all">
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Popular horizontal card 1 -->
                    <div
                        class="group flex flex-col sm:flex-row gap-6 p-4 rounded-3xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all">
                        <div class="w-full sm:w-48 h-48 rounded-2xl overflow-hidden shrink-0">
                            <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                data-alt="Cybersecurity digital blue security background"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB6xULtovp3PHJKT6UE9bRim75PBUMToKgp-lvDgbHuamNObMZcbYpsFqs_Eau5aQYP-GXGV6EkGFVLZU3YkmsxTqCuC-WzZNNWSPv7PcZ9TY167HCa8dCu8Vr4QrDcat6GIiYzffl2VUxb6oapSGwIOoWb5RypFpUTUq5G7x33guHDY9iI6rPzMRUWlKEuYY7jh8oJHLA2GQaUZMfcwZxUmhnXvb6Kd51GjOb720rPFmP4JJiXiQzhZ_Eq2AyaUcfWtF9emRCKeFk" />
                        </div>
                        <div class="flex flex-col justify-center py-2">
                            <div class="flex items-center gap-2 text-accent text-xs font-bold uppercase mb-2">
                                <span class="material-symbols-outlined text-xs">verified</span> Featured
                            </div>
                            <h3 class="text-xl font-bold mb-3 group-hover:text-primary transition-colors">Why
                                Cybersecurity is the Next Frontier for Web Developers</h3>
                            <p class="text-slate-400 text-sm mb-4 line-clamp-2">Learn the fundamental security
                                principles every modern developer needs to secure their applications.</p>
                            <div class="flex items-center gap-4 text-slate-500 text-xs">
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined text-sm">visibility</span> 12.4k views</span>
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined text-sm">comment</span> 84 comments</span>
                            </div>
                        </div>
                    </div>
                    <!-- Popular horizontal card 2 -->
                    <div
                        class="group flex flex-col sm:flex-row gap-6 p-4 rounded-3xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-all">
                        <div class="w-full sm:w-48 h-48 rounded-2xl overflow-hidden shrink-0">
                            <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                data-alt="Remote workspace with laptop and coffee"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDUfz20RMPQxRAfgsLn37_5lqaFQfMW-HMrA4ToHkrOpPh_mmI2j5txVme3xbW3yiQ7xMbytBwIirEjlhGaJ5hGyICwgmSW7qgcr0XEIM-e-c_MIldULDkXMgB1MVMea-R0J62u7rDyV_St7Y366Q1WJWZ8qWsEU4B81XVTOHza7y0lO0emXG8lagFs3be-LQKQluR1JgSAvPco72aXzRXtIYbzVNJqZY5PlLkLNN-AE-ImRRbCQ6UmCh7xiUgadBRWPtUK-44HLgw" />
                        </div>
                        <div class="flex flex-col justify-center py-2">
                            <div class="flex items-center gap-2 text-accent text-xs font-bold uppercase mb-2">
                                <span class="material-symbols-outlined text-xs">local_fire_department</span> Hot Topic
                            </div>
                            <h3 class="text-xl font-bold mb-3 group-hover:text-primary transition-colors">The Psychology
                                of Remote Productivity</h3>
                            <p class="text-slate-400 text-sm mb-4 line-clamp-2">How to build a mental framework that
                                maintains high performance without the office structure.</p>
                            <div class="flex items-center gap-4 text-slate-500 text-xs">
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined text-sm">visibility</span> 9.8k views</span>
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined text-sm">comment</span> 42 comments</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
     <!-- FOOTER 
    <footer class="bg-white dark:bg-background-dark border-t border-slate-200 dark:border-slate-800 pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                Branding 
                <div class="flex flex-col gap-6">
                    <div class="flex items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white">
                            <span class="material-symbols-outlined">auto_awesome</span>
                        </div>
                        <span
                            class="text-xl font-black tracking-tight text-slate-900 dark:text-white uppercase">Blog<span
                                class="text-primary">Fusion</span></span>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">
                        Empowering readers through high-quality insights and stories that inspire innovation in the tech
                        and business world.
                    </p>
                    <div class="flex gap-4">
                        <a class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all"
                            href="#">
                            <span class="material-symbols-outlined">public</span>
                        </a>
                        <a class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all"
                            href="#">
                            <span class="material-symbols-outlined">alternate_email</span>
                        </a>
                        <a class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all"
                            href="#">
                            <span class="material-symbols-outlined">rss_feed</span>
                        </a>
                    </div>
                </div>
                Navigation
                <div>
                    <h5 class="text-slate-900 dark:text-white font-bold mb-6">Quick Links</h5>
                    <ul class="flex flex-col gap-4 text-sm font-medium text-slate-500 dark:text-slate-400">
                        <li><a class="hover:text-primary transition-colors" href="#">Home</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Latest Posts</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Categories</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">About Us</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Contact</a></li>
                    </ul>
                </div>
                Categories
                <div>
                    <h5 class="text-slate-900 dark:text-white font-bold mb-6">Popular Topics</h5>
                    <ul class="flex flex-col gap-4 text-sm font-medium text-slate-500 dark:text-slate-400">
                        <li><a class="hover:text-primary transition-colors" href="#">AI &amp; Machine Learning</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Web Development</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Startup Funding</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">User Experience</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Cloud Architecture</a></li>
                    </ul>
                </div>
                Newsletter
                <div>
                    <h5 class="text-slate-900 dark:text-white font-bold mb-6">Weekly Insights</h5>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">Get the latest articles and hand-picked
                        resources delivered to your inbox.</p>
                    <form class="flex flex-col gap-3">
                        <input
                            class="h-11 rounded-xl border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 text-sm focus:ring-2 focus:ring-primary/50"
                            placeholder="Email address" type="email" />
                        <button
                            class="h-11 rounded-xl bg-primary text-white font-bold hover:bg-primary/90 transition-all"
                            type="submit">Subscribe Now</button>
                    </form>
                </div>
            </div>
            Bottom Footer
            <div
                class="pt-8 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-slate-500 dark:text-slate-400 text-xs">
                    © 2023 Blog Fusion. All rights reserved. Built with love for creators.
                </p>
                <div class="flex gap-6 text-xs text-slate-500 dark:text-slate-400">
                    <a class="hover:text-primary" href="#">Privacy Policy</a>
                    <a class="hover:text-primary" href="#">Terms of Service</a>
                    <a class="hover:text-primary" href="#">Cookie Settings</a>
                </div>
            </div>
        </div>
    </footer> -->
    <?php include "../include/footer.php"?>
</body>

</html>