<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Blog Fusion Dashboard</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-tint": "#732ee4",
                        "surface-container-lowest": "#ffffff",
                        "outline": "#7b7487",
                        "on-primary": "#ffffff",
                        "surface-container": "#f3ebfa",
                        "surface": "#fef7ff",
                        "primary-fixed-dim": "#d2bbff",
                        "secondary-container": "#645efb",
                        "primary-fixed": "#eaddff",
                        "on-tertiary": "#ffffff",
                        "on-surface-variant": "#4a4455",
                        "tertiary-fixed": "#ffd9e4",
                        "on-primary-container": "#ede0ff",
                        "on-error": "#ffffff",
                        "tertiary": "#9b005c",
                        "on-error-container": "#93000a",
                        "background": "#fef7ff",
                        "secondary-fixed": "#e2dfff",
                        "secondary-fixed-dim": "#c3c0ff",
                        "inverse-primary": "#d2bbff",
                        "inverse-on-surface": "#f6eefc",
                        "primary": "#630ed4",
                        "secondary": "#4b41e1",
                        "tertiary-fixed-dim": "#ffb0cd",
                        "on-tertiary-fixed-variant": "#8c0053",
                        "surface-bright": "#fef7ff",
                        "inverse-surface": "#332f39",
                        "error-container": "#ffdad6",
                        "on-secondary-fixed": "#0f0069",
                        "error": "#ba1a1a",
                        "on-secondary-fixed-variant": "#3323cc",
                        "surface-variant": "#e8dfee",
                        "on-secondary-container": "#fffbff",
                        "on-primary-fixed": "#25005a",
                        "outline-variant": "#ccc3d8",
                        "on-primary-fixed-variant": "#5a00c6",
                        "on-secondary": "#ffffff",
                        "surface-container-low": "#f9f1ff",
                        "on-tertiary-fixed": "#3e0022",
                        "primary-container": "#7c3aed",
                        "on-surface": "#1d1a24",
                        "on-background": "#1d1a24",
                        "tertiary-container": "#bf2076",
                        "surface-dim": "#dfd7e6",
                        "surface-container-high": "#ede5f4",
                        "surface-container-highest": "#e8dfee",
                        "on-tertiary-container": "#ffdde7"
                    },
                    fontFamily: {
                        "headline": ["Public Sans"],
                        "body": ["Public Sans"],
                        "label": ["Public Sans"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body { font-family: 'Public Sans', sans-serif; }
        
        /* Mobile Sidebar Toggle Logic */
        #sidebar-overlay.active { display: block; }
        #sidebar.mobile-active { transform: translateX(0); }
    </style>
</head>
<body class="bg-background text-on-background min-h-screen flex">
<!-- Sidebar Overlay (Mobile) -->
<div class="fixed inset-0 bg-on-surface/50 z-40 hidden md:hidden transition-opacity duration-300" id="sidebar-overlay" onclick="toggleSidebar()"></div>
<!-- Sidebar Navigation Drawer -->
<aside class="fixed left-0 top-0 z-50 h-screen w-72 bg-surface-container-low py-8 flex flex-col gap-2 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out" id="sidebar">
<div class="px-8 mb-10 flex items-center justify-between">
<span class="text-2xl font-black text-[#630ed4] tracking-tighter">Blog Fusion</span>
<button class="md:hidden text-primary p-1" onclick="toggleSidebar()">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<nav class="flex flex-col gap-1 px-4">
<a class="bg-[#eaddff] text-[#5a00c6] rounded-full px-6 py-3 flex items-center gap-4 transition-all duration-300" href="#">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
<span class="text-sm font-medium tracking-wide uppercase">Dashboard</span>
</a>
<a class="text-on-surface-variant hover:bg-surface-container-high rounded-full px-6 py-3 flex items-center gap-4 transition-all duration-300" href="#">
<span class="material-symbols-outlined">add_box</span>
<span class="text-sm font-medium tracking-wide uppercase">Add Post</span>
</a>
<a class="text-on-surface-variant hover:bg-surface-container-high rounded-full px-6 py-3 flex items-center gap-4 transition-all duration-300" href="#">
<span class="material-symbols-outlined">article</span>
<span class="text-sm font-medium tracking-wide uppercase">My Posts</span>
</a>
<a class="text-on-surface-variant hover:bg-surface-container-high rounded-full px-6 py-3 flex items-center gap-4 transition-all duration-300" href="#">
<span class="material-symbols-outlined">forum</span>
<span class="text-sm font-medium tracking-wide uppercase">Comments</span>
</a>
<a class="text-on-surface-variant hover:bg-surface-container-high rounded-full px-6 py-3 flex items-center gap-4 transition-all duration-300" href="#">
<span class="material-symbols-outlined">account_circle</span>
<span class="text-sm font-medium tracking-wide uppercase">Profile</span>
</a>
<div class="mt-auto pt-10">
<a class="text-on-surface-variant hover:bg-surface-container-high rounded-full px-6 py-3 flex items-center gap-4 transition-all duration-300" href="#">
<span class="material-symbols-outlined">logout</span>
<span class="text-sm font-medium tracking-wide uppercase">Logout</span>
</a>
</div>
</nav>
</aside>
<!-- Main Content Area -->
<main class="flex-1 md:ml-72 min-h-screen">
<!-- TopAppBar -->
<header class="fixed top-0 right-0 left-0 md:left-72 z-40 bg-[#fef7ff]/70 backdrop-blur-xl shadow-[0_32px_32px_rgba(29,26,36,0.06)] h-16 flex items-center justify-between px-6">
<div class="flex items-center gap-4">
<!-- Mobile Menu Trigger -->
<button class="md:hidden flex items-center justify-center w-10 h-10 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-colors" onclick="toggleSidebar()">
<span class="material-symbols-outlined">menu</span>
</button>
<h1 class="text-xl font-bold text-on-surface tracking-tighter">Dashboard</h1>
</div>
<div class="flex items-center gap-2 md:gap-4">
<div class="hidden sm:flex bg-surface-container-highest rounded-lg px-3 py-1.5 items-center gap-2">
<span class="material-symbols-outlined text-on-surface-variant text-lg">search</span>
<input class="bg-transparent border-none focus:ring-0 text-sm w-48 text-on-surface-variant placeholder:text-on-surface-variant/60" placeholder="Search insights..." type="text"/>
</div>
<button class="text-primary hover:bg-[#f3ebfa] p-2 rounded-full transition-colors">
<span class="material-symbols-outlined">dark_mode</span>
</button>
<div class="h-10 w-10 rounded-full bg-primary-container flex items-center justify-center text-on-primary font-bold overflow-hidden">
<img alt="User" class="w-full h-full object-cover" data-alt="Portrait of a male professional" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBahPq2yRzfjYEHmMJVAjHJ-L7pbK5Yo6vd07wOqPJpxpvdG1bAUhP91a9AfblKeGPpvefjP2UQb-EuCEXsNxOZQY9MxHsGnvQHMiTIYEbaRQECGC8oeiR1ilGbgPQ2OkZ_Wjx88Vp4AihxbM3daNZV4AwrX299WMvyrtDR75URxzr9V3C5iq6IawXW1DGWHzXLxVngonTpF4kvw9ELXEhZ7tbcj6qf0uhKpBdM3zXG_tkTxXSNsIvxrVYWPUlC8s77QnPEz3Stvxk"/>
</div>
</div>
</header>
<!-- Content Canvas -->
<div class="pt-24 px-6 pb-12 max-w-7xl mx-auto space-y-12">
<!-- Hero Stats Bento Grid -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-6">
<!-- Stat Card 1 -->
<div class="bg-surface-container rounded-xl p-8 flex flex-col justify-between group hover:shadow-[0_32px_32px_rgba(29,26,36,0.04)] transition-all duration-300 relative overflow-hidden">
<div class="absolute -right-4 -top-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
<span class="material-symbols-outlined text-9xl">article</span>
</div>
<div>
<span class="text-label text-on-surface-variant text-xs font-bold tracking-widest uppercase mb-2 block">Total Posts</span>
<h2 class="text-5xl font-extrabold text-primary tracking-tight">124</h2>
</div>
<div class="flex justify-end mt-4">
<span class="bg-tertiary/10 text-tertiary text-xs px-2 py-1 rounded-full font-bold">+12% this month</span>
</div>
</div>
<!-- Stat Card 2 -->
<div class="bg-surface-container-low rounded-xl p-8 flex flex-col justify-between group hover:shadow-[0_32px_32px_rgba(29,26,36,0.04)] transition-all duration-300 relative overflow-hidden">
<div class="absolute -right-4 -top-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
<span class="material-symbols-outlined text-9xl">forum</span>
</div>
<div>
<span class="text-label text-on-surface-variant text-xs font-bold tracking-widest uppercase mb-2 block">Total Comments</span>
<h2 class="text-5xl font-extrabold text-secondary tracking-tight">1,842</h2>
</div>
<div class="flex justify-end mt-4">
<span class="bg-secondary/10 text-secondary text-xs px-2 py-1 rounded-full font-bold">+5.2k new</span>
</div>
</div>
<!-- Stat Card 3 -->
<div class="bg-surface-container-highest rounded-xl p-8 flex flex-col justify-between group hover:shadow-[0_32px_32px_rgba(29,26,36,0.04)] transition-all duration-300 relative overflow-hidden">
<div class="absolute -right-4 -top-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
<span class="material-symbols-outlined text-9xl">visibility</span>
</div>
<div>
<span class="text-label text-on-surface-variant text-xs font-bold tracking-widest uppercase mb-2 block">Page Views</span>
<h2 class="text-5xl font-extrabold text-[#1d1a24] tracking-tight">42.5k</h2>
</div>
<div class="flex justify-end mt-4">
<span class="bg-primary/10 text-primary text-xs px-2 py-1 rounded-full font-bold">Top 5% category</span>
</div>
</div>
</section>
<!-- Main Layout: Recent Posts & Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
<!-- Recent Posts (2/3 width) -->
<div class="lg:col-span-2 space-y-8">
<div class="flex items-center justify-between">
<h3 class="text-2xl font-bold tracking-tight">Recent Posts</h3>
<button class="text-primary font-semibold text-sm hover:underline">View all</button>
</div>
<div class="space-y-6">
<!-- Post Item 1 -->
<div class="group bg-surface-container-lowest rounded-xl overflow-hidden flex flex-col sm:flex-row items-center hover:shadow-[0_20px_40px_rgba(29,26,36,0.05)] transition-all duration-300">
<div class="w-full sm:w-48 h-32 overflow-hidden">
<img alt="Tech post" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" data-alt="Close up of a keyboard and coffee" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD5ydmcFOjag2vIIyfKjCLCreSa5dyP4Cv0N91Jda2ZJnDM6Dmz1qU4Dew3Ieq1wc2MjtrWbzI-gx3BNPIRFEoV7Jvyx1fOnoXKLv3ubxJ6JJlwdtTOkX95v8hruvRv7jhMZrPMsJahQHwUqhi7QDOOW4CltdcpgqVfafeWj24utfB9nGL8PCKuRhNG_f0g_jXCve1yNXMHkZBQupkSUGiTgXUzoVWW3wK0aE2E_3Ln9bbboO0sU2zPXShvEAQDHsNU9VAdXzDA24s"/>
</div>
<div class="p-6 flex-1">
<div class="flex items-center gap-2 mb-2">
<span class="text-[10px] font-black uppercase tracking-widest text-tertiary">Technology</span>
<span class="w-1 h-1 rounded-full bg-outline-variant"></span>
<span class="text-[10px] text-on-surface-variant font-medium">2 hours ago</span>
</div>
<h4 class="text-lg font-bold text-on-surface leading-tight mb-2">The Future of AI in Editorial Design</h4>
<p class="text-sm text-on-surface-variant line-clamp-1">Exploring how generative models are reshaping the way we think about layout...</p>
</div>
<div class="px-6 py-4 sm:py-0">
<span class="material-symbols-outlined text-outline-variant group-hover:text-primary transition-colors">arrow_forward_ios</span>
</div>
</div>
<!-- Post Item 2 -->
<div class="group bg-surface-container-lowest rounded-xl overflow-hidden flex flex-col sm:flex-row items-center hover:shadow-[0_20px_40px_rgba(29,26,36,0.05)] transition-all duration-300">
<div class="w-full sm:w-48 h-32 overflow-hidden">
<img alt="Writing post" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" data-alt="Person writing in a notebook near a laptop" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDCMGUSszAmcTz7cTU6Xy_Zll0WmcaXzavPYRjZIXeX2yHftVK8_HpWGIY529ZWKXbc3nlyTyPzPbIo3AaVniasbJ9vqDvTsYTGAVz-nK2TJm82sEP-z5PEOeaimrdsptTpkxJHvDNLLbfdS3MWx24pKHuKBwrWhjnK0SGbb5Id3dqeS1HS2TtavOqH5Ly_KZ4UXASlKNOH2ozlgX_ZWfo6vJdih3Nm9E073DAKKpWjIbJOMv01oC83PevSAyOLY3JlAIkv2KEvVjs"/>
</div>
<div class="p-6 flex-1">
<div class="flex items-center gap-2 mb-2">
<span class="text-[10px] font-black uppercase tracking-widest text-secondary">Lifestyle</span>
<span class="w-1 h-1 rounded-full bg-outline-variant"></span>
<span class="text-[10px] text-on-surface-variant font-medium">1 day ago</span>
</div>
<h4 class="text-lg font-bold text-on-surface leading-tight mb-2">Mindful Writing: Finding Your Flow</h4>
<p class="text-sm text-on-surface-variant line-clamp-1">Practical tips for staying creative in a world full of digital noise...</p>
</div>
<div class="px-6 py-4 sm:py-0">
<span class="material-symbols-outlined text-outline-variant group-hover:text-primary transition-colors">arrow_forward_ios</span>
</div>
</div>
<!-- Post Item 3 -->
<div class="group bg-surface-container-lowest rounded-xl overflow-hidden flex flex-col sm:flex-row items-center hover:shadow-[0_20px_40px_rgba(29,26,36,0.05)] transition-all duration-300">
<div class="w-full sm:w-48 h-32 overflow-hidden">
<img alt="Work post" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" data-alt="High angle view of a tidy office desk" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAvh0DWjl0U7hivkBReOUMeI_DiVY2PPTEGviR79K5r3Y-gUdCOesMhDbtRvTVxeSlF93fQO9HXoko4QapdswRRjnH5WgFCjwSyoCFmM-XH8Vmeo-q6qF_oddgIwlWIvzaJZSJt-ltXeMCGoVax7p4FdaRBuC2maTrAwUIictFVbEBysxjFmqEvmmI227tkI-dtFlvLb9v3YEt6NoIWe7Dc1XotA5lZdbrly5s-JZuyC9RDjUpnJbfb2ti001xi3sgXHCCGXQnLqyQ"/>
</div>
<div class="p-6 flex-1">
<div class="flex items-center gap-2 mb-2">
<span class="text-[10px] font-black uppercase tracking-widest text-primary">Business</span>
<span class="w-1 h-1 rounded-full bg-outline-variant"></span>
<span class="text-[10px] text-on-surface-variant font-medium">3 days ago</span>
</div>
<h4 class="text-lg font-bold text-on-surface leading-tight mb-2">Monetizing Your Niche Blog in 2024</h4>
<p class="text-sm text-on-surface-variant line-clamp-1">Beyond ads: building a community that supports your passion projects...</p>
</div>
<div class="px-6 py-4 sm:py-0">
<span class="material-symbols-outlined text-outline-variant group-hover:text-primary transition-colors">arrow_forward_ios</span>
</div>
</div>
</div>
</div>
<!-- Activity Feed (1/3 width) -->
<div class="space-y-8">
<h3 class="text-2xl font-bold tracking-tight">Activity Feed</h3>
<div class="relative space-y-6 before:absolute before:left-[19px] before:top-4 before:bottom-0 before:w-px before:bg-outline-variant/30">
<!-- Activity Item 1 -->
<div class="relative flex gap-4">
<div class="relative z-10 w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-xl">comment</span>
</div>
<div class="flex-1">
<p class="text-sm text-on-surface leading-snug"><span class="font-bold">Sarah Jenkins</span> commented on <span class="text-primary font-medium italic">"The Future of AI"</span></p>
<p class="text-xs text-on-surface-variant mt-1">"This perspective is so fresh! I never thought..."</p>
<span class="text-[10px] text-outline mt-2 block uppercase font-bold tracking-tighter">12m ago</span>
</div>
</div>
<!-- Activity Item 2 -->
<div class="relative flex gap-4">
<div class="relative z-10 w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-xl">thumb_up</span>
</div>
<div class="flex-1">
<p class="text-sm text-on-surface leading-snug"><span class="font-bold">Alex Rivera</span> liked your post</p>
<span class="text-[10px] text-outline mt-2 block uppercase font-bold tracking-tighter">45m ago</span>
</div>
</div>
<!-- Activity Item 3 -->
<div class="relative flex gap-4">
<div class="relative z-10 w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-tertiary">
<span class="material-symbols-outlined text-xl">person_add</span>
</div>
<div class="flex-1">
<p class="text-sm text-on-surface leading-snug"><span class="font-bold">New Subscriber</span> joined your newsletter</p>
<span class="text-[10px] text-outline mt-2 block uppercase font-bold tracking-tighter">3h ago</span>
</div>
</div>
<!-- Activity Item 4 -->
<div class="relative flex gap-4">
<div class="relative z-10 w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-[#1d1a24]">
<span class="material-symbols-outlined text-xl">trending_up</span>
</div>
<div class="flex-1">
<p class="text-sm text-on-surface leading-snug">Weekly traffic goal <span class="font-bold">reached!</span></p>
<span class="text-[10px] text-outline mt-2 block uppercase font-bold tracking-tighter">Yesterday</span>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
<!-- Floating Action Button -->
<button class="fixed bottom-8 right-8 w-14 h-14 bg-gradient-to-br from-primary to-primary-container text-on-primary rounded-xl shadow-[0_12px_24px_rgba(99,14,212,0.3)] flex items-center justify-center hover:scale-105 active:scale-95 transition-all duration-200 z-30">
<span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">add</span>
</button>
<script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('mobile-active');
            overlay.classList.toggle('hidden');
            overlay.classList.toggle('active');
        }
    </script>
</body></html>