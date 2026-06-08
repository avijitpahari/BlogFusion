<?php
require_once dirname(__DIR__) . '/config.php';
include BASE_PATH . 'include/db.php';
include BASE_PATH . 'include/data_fetch.php';

$slug = $_GET['slug'] ?? '';

$post = single_post_slug($slug);

if (!$post) {
  die("Post not found");
}
?>

<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <link rel="icon" type="image/png" href="<?php echo defined('BASE_URL') ? BASE_URL : '/BlogFusion/'; ?>upload/site_image/logo2.png" />
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>Blog Fusion - The Future of Creative Development</title>
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800;900&display=swap"
    rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
    rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "surface-bright": "#fef7ff",
            "on-primary-fixed-variant": "#5a00c6",
            "surface-container": "#f3ebfa",
            "on-primary-fixed": "#25005a",
            "on-error-container": "#93000a",
            "background": "#fef7ff",
            "surface-container-highest": "#e8dfee",
            "secondary": "#4b41e1",
            "on-tertiary-fixed": "#3e0022",
            "on-error": "#ffffff",
            "primary": "#630ed4",
            "on-background": "#1d1a24",
            "on-secondary-fixed": "#0f0069",
            "inverse-surface": "#332f39",
            "on-surface-variant": "#4a4455",
            "tertiary-container": "#bf2076",
            "surface": "#fef7ff",
            "on-surface": "#1d1a24",
            "inverse-primary": "#d2bbff",
            "error-container": "#ffdad6",
            "secondary-fixed-dim": "#c3c0ff",
            "primary-container": "#7c3aed",
            "surface-container-high": "#ede5f4",
            "on-tertiary-fixed-variant": "#8c0053",
            "error": "#ba1a1a",
            "primary-fixed": "#eaddff",
            "on-secondary": "#ffffff",
            "outline": "#7b7487",
            "on-primary-container": "#ede0ff",
            "tertiary": "#9b005c",
            "surface-container-lowest": "#ffffff",
            "surface-variant": "#e8dfee",
            "tertiary-fixed": "#ffd9e4",
            "surface-tint": "#732ee4",
            "outline-variant": "#ccc3d8",
            "tertiary-fixed-dim": "#ffb0cd",
            "on-secondary-container": "#fffbff",
            "secondary-container": "#645efb",
            "primary-fixed-dim": "#d2bbff",
            "secondary-fixed": "#e2dfff",
            "on-secondary-fixed-variant": "#3323cc",
            "on-primary": "#ffffff",
            "on-tertiary-container": "#ffdde7",
            "inverse-on-surface": "#f6eefc",
            "on-tertiary": "#ffffff",
            "surface-container-low": "#f9f1ff",
            "surface-dim": "#dfd7e6"
          },
          borderRadius: {
            DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem",
            "2xl": "1rem", "3xl": "1.5rem", full: "9999px"
          },
          fontFamily: {
            headline: ["Public Sans"], display: ["Public Sans"],
            body: ["Public Sans"], label: ["Public Sans"]
          }
        }
      }
    }
  </script>
  <style>
    body {
      font-family: 'Public Sans', sans-serif;
    }

    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    .dark body {
      background: #1d1a24;
      color: #ede5f4;
    }

    .dark .bg-surface-bright {
      background: #1d1a24 !important;
    }

    .dark .bg-surface-container-low {
      background: #2a2534 !important;
    }

    .dark .bg-surface-container-high {
      background: #332f3d !important;
    }

    .dark .bg-surface-container-highest\/40 {
      background: rgba(50, 45, 62, 0.7) !important;
    }

    .dark .text-on-surface {
      color: #ede5f4 !important;
    }

    .dark .text-on-surface-variant {
      color: #ccc3d8 !important;
    }

    .dark .border-outline-variant\/10 {
      border-color: rgba(100, 90, 120, 0.25) !important;
    }

    .dark .border-outline-variant\/20 {
      border-color: rgba(100, 90, 120, 0.3) !important;
    }

    .dark .code-container {
      background: #12101a;
    }

    .dark .bg-white {
      background: #2a2534 !important;
    }

    .dark .bg-zinc-50 {
      background: #1a1723 !important;
    }

    .dark .text-zinc-900 {
      color: #ede5f4 !important;
    }

    .dark .text-zinc-500 {
      color: #9990a8 !important;
    }

    .dark .bg-surface-container-high {
      background: #332f3d !important;
    }

    .dark nav {
      background: rgba(29, 26, 36, 0.85) !important;
    }

    .dark .search-results {
      background: #2a2534;
      border-color: rgba(100, 90, 120, 0.3);
    }

    .dark .search-result-item:hover {
      background: #332f3d;
    }

    .dark .modal-bg {
      background: rgba(0, 0, 0, 0.6) !important;
    }

    .dark .modal-box {
      background: #2a2534 !important;
    }

    .dark textarea {
      background: transparent;
      color: #ede5f4;
    }

    .dark input[type=text] {
      background: rgba(50, 45, 62, 0.5);
      color: #ede5f4;
    }

    .dark .bg-surface-container {
      background: #241f30 !important;
    }

    .dark .hover\:bg-surface-container:hover {
      background: #2a2534 !important;
    }

    .code-container {
      background: #1d1a24;
      color: #d2bbff;
    }

    .no-scrollbar::-webkit-scrollbar {
      display: none;
    }

    /* Search dropdown */
    .search-results {
      position: absolute;
      top: calc(100% + 8px);
      left: 0;
      right: 0;
      background: white;
      border-radius: 12px;
      border: 1px solid rgba(204, 195, 216, 0.4);
      box-shadow: 0 8px 32px rgba(99, 14, 212, 0.1);
      z-index: 100;
      overflow: hidden;
      display: none;
    }

    .search-results.show {
      display: block;
    }

    .search-result-item {
      padding: 10px 14px;
      cursor: pointer;
      transition: background 0.15s;
      font-size: 13px;
      border-bottom: 1px solid rgba(204, 195, 216, 0.2);
    }

    .search-result-item:last-child {
      border-bottom: none;
    }

    .search-result-item:hover {
      background: #f9f1ff;
    }

    .search-result-item .tag {
      font-size: 10px;
      color: #9b005c;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }

    /* Modal */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.4);
      z-index: 200;
      display: none;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(4px);
    }

    .modal-overlay.show {
      display: flex;
    }

    .modal-box {
      background: white;
      border-radius: 24px;
      padding: 32px;
      max-width: 480px;
      width: 90%;
      box-shadow: 0 24px 80px rgba(99, 14, 212, 0.15);
      animation: modalIn 0.2s ease;
    }

    @keyframes modalIn {
      from {
        transform: scale(0.95) translateY(8px);
        opacity: 0;
      }

      to {
        transform: scale(1) translateY(0);
        opacity: 1;
      }
    }

    /* Toast */
    .toast {
      position: fixed;
      bottom: 24px;
      left: 50%;
      transform: translateX(-50%) translateY(80px);
      background: #1d1a24;
      color: #eaddff;
      padding: 12px 24px;
      border-radius: 999px;
      font-size: 14px;
      font-weight: 600;
      z-index: 300;
      transition: transform 0.3s ease;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    }

    .toast.show {
      transform: translateX(-50%) translateY(0);
    }

    .dark .toast {
      background: #eaddff;
      color: #1d1a24;
    }

    /* Reaction count */
    .reaction-btn {
      position: relative;
    }

    .reaction-btn .count {
      position: absolute;
      top: -6px;
      right: -6px;
      background: #630ed4;
      color: white;
      font-size: 10px;
      font-weight: 700;
      border-radius: 999px;
      min-width: 18px;
      height: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 4px;
      opacity: 0;
      transform: scale(0);
      transition: all 0.2s;
    }

    .reaction-btn.active .count {
      opacity: 1;
      transform: scale(1);
    }

    .reaction-btn.active {
      background: rgba(99, 14, 212, 0.12) !important;
      transform: scale(1.1);
    }

    /* Comment like */
    .comment-like.liked {
      color: #630ed4 !important;
    }

    /* Category tabs */
    .cat-tab {
      cursor: pointer;
      transition: all 0.2s;
    }

    .cat-tab.active {
      color: #630ed4;
      font-weight: 700;
      border-bottom: 2px solid #630ed4;
    }

    /* Notification dot */
    .notif-dot {
      width: 8px;
      height: 8px;
      background: #630ed4;
      border-radius: 50%;
      position: absolute;
      top: 0;
      right: 0;
    }

    /* Reading progress */
    #progress-bar {
      transition: width 0.1s linear;
    }

    /* Tooltip */
    [data-tooltip] {
      position: relative;
    }

    [data-tooltip]::after {
      content: attr(data-tooltip);
      position: absolute;
      bottom: calc(100% + 6px);
      left: 50%;
      transform: translateX(-50%);
      background: #1d1a24;
      color: white;
      font-size: 11px;
      padding: 4px 10px;
      border-radius: 6px;
      white-space: nowrap;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.2s;
    }

    [data-tooltip]:hover::after {
      opacity: 1;
    }

    /* Comment reply indent */
    .reply-comment {
      margin-left: 52px;
      border-left: 2px solid rgba(99, 14, 212, 0.2);
      padding-left: 16px;
    }

    /* Scroll behavior */
    html {
      scroll-behavior: smooth;
    }

    /* ─── SCROLL REVEAL ─── */
    [data-reveal] {
      opacity: 0;
      transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
      will-change: opacity, transform;
    }

    [data-reveal="fade-up"] {
      transform: translateY(40px);
    }

    [data-reveal="fade-down"] {
      transform: translateY(-40px);
    }

    [data-reveal="fade-left"] {
      transform: translateX(-40px);
    }

    [data-reveal="fade-right"] {
      transform: translateX(40px);
    }

    [data-reveal="zoom-in"] {
      transform: scale(0.88);
    }

    [data-reveal="zoom-up"] {
      transform: scale(0.92) translateY(30px);
    }

    [data-reveal="flip"] {
      transform: rotateX(15deg) translateY(30px);
      transform-origin: top center;
    }

    [data-reveal].revealed {
      opacity: 1;
      transform: none;
    }

    /* Stagger children inside a reveal group */
    [data-reveal-group]>* {
      opacity: 0;
      transform: translateY(32px);
      transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
      will-change: opacity, transform;
    }

    [data-reveal-group].revealed>*:nth-child(1) {
      transition-delay: 0s;
    }

    [data-reveal-group].revealed>*:nth-child(2) {
      transition-delay: 0.08s;
    }

    [data-reveal-group].revealed>*:nth-child(3) {
      transition-delay: 0.16s;
    }

    [data-reveal-group].revealed>*:nth-child(4) {
      transition-delay: 0.24s;
    }

    [data-reveal-group].revealed>*:nth-child(5) {
      transition-delay: 0.32s;
    }

    [data-reveal-group].revealed>*:nth-child(6) {
      transition-delay: 0.40s;
    }

    [data-reveal-group].revealed>* {
      opacity: 1;
      transform: none;
    }

    /* Skip animations if user prefers reduced motion */
    @media (prefers-reduced-motion: reduce) {

      [data-reveal],
      [data-reveal-group]>* {
        transition: none !important;
        opacity: 1 !important;
        transform: none !important;
      }
    }

    /* Views live pulse */
    @keyframes pulse {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: 0.5;
      }
    }

    .live-dot {
      width: 6px;
      height: 6px;
      background: #630ed4;
      border-radius: 50%;
      display: inline-block;
      animation: pulse 2s infinite;
    }
  </style>
</head>

<body class="bg-surface-bright text-on-surface">
  
  <!-- TOAST -->
  <div class="toast" id="toast"></div>

  <!-- SHARE MODAL -->
  <div class="modal-overlay" id="shareModal">
    <div class="modal-box">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-black tracking-tight text-on-surface">Share this article</h3>
        <button onclick="closeShare()"
          class="w-9 h-9 flex items-center justify-center rounded-full bg-surface-container-high hover:bg-surface-container transition-colors">
          <span class="material-symbols-outlined text-on-surface-variant">close</span>
        </button>
      </div>
      <div class="flex flex-col gap-3 mb-6">
        <div class="flex items-center gap-3 p-3 rounded-xl bg-surface-container-low">
          <input id="shareUrl" type="text" value="https://blogfusion.io/luminous-editor-2024" readonly
            class="flex-1 bg-transparent border-none text-sm text-on-surface-variant outline-none focus:ring-0" />
          <button onclick="copyShareLink()"
            class="px-4 py-1.5 bg-primary text-white text-xs font-bold rounded-lg hover:bg-primary-container transition-colors">Copy</button>
        </div>
      </div>
      <div class="grid grid-cols-3 gap-3">
        <button onclick="shareToTwitter()"
          class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-surface-container-low hover:bg-surface-container transition-colors">
          <span class="text-2xl">𝕏</span>
          <span class="text-xs font-bold text-on-surface-variant">Twitter / X</span>
        </button>
        <button onclick="shareToLinkedIn()"
          class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-surface-container-low hover:bg-surface-container transition-colors">
          <span class="text-2xl">in</span>
          <span class="text-xs font-bold text-on-surface-variant">LinkedIn</span>
        </button>
        <button onclick="copyShareLink()"
          class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-surface-container-low hover:bg-surface-container transition-colors">
          <span class="material-symbols-outlined text-primary">link</span>
          <span class="text-xs font-bold text-on-surface-variant">Copy Link</span>
        </button>
      </div>
    </div>
  </div>

  <!-- NAV -->
  <!-- <nav
    class="fixed top-0 w-full z-50 bg-white/70 dark:bg-zinc-950/70 backdrop-blur-xl shadow-2xl shadow-violet-900/5 transition-shadow duration-300"
    id="mainNav">
    <div class="flex justify-between items-center h-16 px-6 max-w-7xl mx-auto">
      <div class="text-2xl font-black tracking-tighter text-violet-700 dark:text-violet-400 cursor-pointer"
        onclick="scrollToTop()">Blog Fusion</div>
      <div class="hidden md:flex gap-8 items-center font-public-sans tracking-tight">
        <a class="cat-tab text-zinc-600 dark:text-zinc-400 font-medium hover:text-violet-500 transition-colors pb-1"
          data-cat="home" onclick="filterCategory('home', this)" href="#">Home</a>
        <a class="cat-tab active text-violet-700 dark:text-violet-400 font-bold pb-1" data-cat="latest"
          onclick="filterCategory('latest', this)" href="#">Latest</a>
        <a class="cat-tab text-zinc-600 dark:text-zinc-400 font-medium hover:text-violet-500 transition-colors pb-1"
          data-cat="design" onclick="filterCategory('design', this)" href="#">Categories</a>
        <a class="cat-tab text-zinc-600 dark:text-zinc-400 font-medium hover:text-violet-500 transition-colors pb-1"
          data-cat="popular" onclick="filterCategory('popular', this)" href="#">Popular</a>
      </div>
      <div class="flex items-center gap-3"> -->
  <!-- Search -->
  <!--- <div class="hidden sm:block relative">
          <input id="searchInput" onInput="handleSearch(this)" onFocus="handleSearch(this)" onBlur="hideSearch()"
            class="bg-surface-container-highest/50 border-none rounded-xl px-4 py-1.5 focus:ring-2 ring-primary w-48 text-sm outline-none transition-all focus:w-60"
            placeholder="Search..." type="text" />
          <div class="search-results" id="searchResults"></div>
        </div>-->
  <!-- Dark mode toggle -->
  <!-- <button onclick="toggleDark()" id="darkBtn" 
          class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors">
          <span class="material-symbols-outlined text-violet-700 dark:text-violet-400 text-xl"
            id="darkIcon">dark_mode</span>
        </button> -->
  <!-- Notification bell -->
  <!-- <button onclick="showNotifications()" 
          class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors relative">
          <span class="material-symbols-outlined text-violet-700 dark:text-violet-400 text-xl">notifications</span>
          <span class="notif-dot"></span>
        </button> -->
  <!-- Profile -->
  <!-- <button onclick="toggleProfile()" class="relative">
          <span
            class="material-symbols-outlined text-violet-700 dark:text-violet-400 cursor-pointer active:scale-95 transition-transform text-3xl">account_circle</span>
        </button> -->
  <!-- Profile dropdown -->
  <!---<div id="profileMenu"
          class="hidden absolute top-16 right-4 bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl border border-outline-variant/20 p-2 w-48 z-50"
          style="position:fixed;top:64px;right:16px;">
          <div class="px-3 py-2 mb-1">
            <p class="font-bold text-sm text-on-surface">Guest Reader</p>
            <p class="text-xs text-on-surface-variant">Not signed in</p>
          </div>
          <hr class="border-outline-variant/20 mb-1" />
          <button onclick="showToast('Profile feature coming soon!')"
            class="w-full text-left px-3 py-2 rounded-xl hover:bg-surface-container text-sm text-on-surface transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-base">person</span> Profile
          </button>
          <button onclick="showToast('Settings coming soon!')"
            class="w-full text-left px-3 py-2 rounded-xl hover:bg-surface-container text-sm text-on-surface transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-base">settings</span> Settings
          </button>
          <button onclick="showToast('Sign in feature coming soon!')"
            class="w-full text-left px-3 py-2 rounded-xl hover:bg-surface-container text-sm text-primary font-semibold transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-base">login</span> Sign In
          </button>
        </div>
      </div>
    </div>
  </nav> -->

  <!-- POST HEADER -->
  <header class="pt-20 pb-12 px-6 max-w-7xl mx-auto">
    <button onclick="window.location.href='<?=BASE_URL?>pages/home.php?model=blog'"
      class="flex items-center gap-2 mb-5 md:mb-6 text-sm font-bold text-primary hover:text-primary-container transition-colors">
      <span class="material-symbols-outlined text-base">arrow_back</span> Back to Blogs
    </button>
    <div class="flex flex-col items-center text-center space-y-6">
      <span data-reveal="zoom-in"
        class="px-4 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed-variant text-label font-bold tracking-widest uppercase text-xs cursor-pointer hover:opacity-80 transition-opacity"
        onclick="filterCategory('design', document.querySelector('[data-cat=design]'))">Innovation &amp; Design</span>
      <h1 data-reveal="fade-up" style="transition-delay:0.1s"
        class="text-4xl md:text-6xl font-black tracking-tighter text-on-surface max-w-4xl leading-[1.1]">
        <?= htmlspecialchars($post['title']) ?>
      </h1>
      <div data-reveal="fade-up" style="transition-delay:0.22s" class="flex items-center gap-4 pt-4">
        <div
          class="w-12 h-12 rounded-full bg-surface-container-highest overflow-hidden ring-4 ring-surface-container-low">
          <img alt="Julian Vance" class="w-full h-full object-cover" src="<?= BASE_URL . $post['image'] ?>" />
        </div>
        <div class="text-left">
          <div class="font-bold text-on-surface">Julian Vance</div>
          <div class="text-on-surface-variant text-sm flex items-center gap-2 flex-wrap">
            October 24, 2024 • 8 min read •
            <span class="flex items-center gap-1.5">
              <span class="live-dot"></span>
              <span class="text-primary font-semibold" id="viewCounter">1,247 Views</span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- FEATURED IMAGE -->
  <section class="max-w-7xl mx-auto px-0 md:px-6 mb-16">
    <div data-reveal="zoom-up" class="w-full h-[614px] md:rounded-3xl overflow-hidden shadow-2xl shadow-primary/10">
      <img alt="Abstract digital architecture with violet and indigo glass layers" class="w-full h-full object-cover"
        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAtFvKhm16_jqvL35k2eac0ocd2RKhaREdgTGj80OP8oBIvwG--Ezx8tj9nLjk-G66ELQm8bGMub8oxzFaMjQaac7q_z7j0LVaGLSB41eQqd5-9TTIhP63aX1Yd1hOTRAQgklzmq1BjN0D25PfuEsjNWHW6cjBCFvm6VSLqcRDPxspPzxvhQz5UQKA4BJo5kQSMTKStV6uaYo42788FxKbHc_GX0l0iVNa1Ew3MklewzPowJsOWctTifCGcltMpjL4r_GT8dIvDfvQ" />
    </div>
  </section>

  <!-- MAIN CONTENT -->
  <main class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 relative">

    <!-- SIDEBAR -->
    <aside class="lg:col-span-4 order-2 lg:order-2 space-y-8">
      <div class="sticky top-24 space-y-8">


        <!-- Table of Contents -->
        <!-- <div data-reveal="fade-left" class="p-8 rounded-3xl bg-surface-container-low border border-outline-variant/10">
          <h3 class="text-label font-black tracking-widest uppercase text-xs mb-6 text-primary">On This Page</h3>
          <ul class="space-y-4 text-body-md" id="tocList">
            <li><a class="toc-link text-primary font-bold hover:text-primary-container transition-colors"
                href="#intro">The Death of the Border</a></li>
            <li><a class="toc-link text-on-surface-variant hover:text-primary transition-colors"
                href="#philosophy">Tonal Hierarchy Principles</a></li>
            <li><a class="toc-link text-on-surface-variant hover:text-primary transition-colors"
                href="#execution">Implementing Luminous Design</a></li>
            <li><a class="toc-link text-on-surface-variant hover:text-primary transition-colors" href="#summary">The
                Future of UI Depth</a></li>
          </ul> -->
        <!-- Reading time estimate -->
        <!-- <div class="mt-6 pt-6 border-t border-outline-variant/20">
            <div class="flex items-center justify-between text-xs text-on-surface-variant">
              <span>Reading progress</span>
              <span id="readingPct">0%</span>
            </div>
            <div class="mt-2 h-1.5 bg-surface-container-highest rounded-full overflow-hidden">
              <div id="inlineProgress" class="h-full bg-primary rounded-full transition-all duration-300"
                style="width:0%"></div>
            </div>
          </div> -->
        <!-- </div> -->

        <!-- Author Card -->
        <div class="p-8 rounded-3xl bg-surface-container-highest/40 backdrop-blur-sm border border-outline-variant/10">
          <h3 class="text-label font-black tracking-widest uppercase text-xs mb-4 text-on-surface-variant">About the
            Author</h3>
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full overflow-hidden bg-surface-container-highest">
              <img alt="Julian Vance" class="w-full h-full object-cover"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD2gxUOLgVoovM02PWBM6--mon1J9dmEjYp7aGw2s-OSkTcyFp1NOPnQKnTEnd7UkvE-s3MAzYFjiknfLRbDHbM6m7iKrx2F11SCUnq2ElpwdhjY95J4vLPw6dzDL9X38FB8OHbQvUvudgvatp6KMPh6jb7NLz-IwBb1A9JEUKJiMkUhOP8HsKDtFa0TdgGde301KYJZtvGeSwNRhpfCfqG9BtvrQEuumBp8lf389vMQg3TtfTAq3f4gtctunnRtXvjpcBN9baGDv4" />
            </div>
            <div>
              <p class="font-bold text-sm text-on-surface">Julian Vance</p>
              <p class="text-xs text-on-surface-variant">Lead UI Architect</p>
            </div>
          </div>
          <p class="text-on-surface text-sm leading-relaxed mb-6">Julian specializes in high-fidelity design systems and
            emotional digital experiences. 12 articles published.</p>
          <div class="flex gap-3">
            <button id="followBtn" onclick="toggleFollow()"
              class="flex-1 py-3 bg-white text-primary font-bold rounded-xl shadow-sm hover:shadow-md transition-all active:scale-95 text-sm">
              Follow Julian
            </button>
            <button onclick="showToast('Message feature coming soon!')"
              class="w-12 h-12 flex items-center justify-center rounded-xl bg-surface-container-high hover:bg-surface-container transition-colors">
              <span class="material-symbols-outlined text-on-surface-variant text-base">mail</span>
            </button>
          </div>
          <p class="text-xs text-on-surface-variant text-center mt-3" id="followerCount">1,840 followers</p>
        </div>

        <!-- Popular Posts -->
        <div data-reveal="fade-left" style="transition-delay:0.1s" class="space-y-6">
          <div class="flex justify-between items-center px-2">
            <h3 class="text-label font-black tracking-widest uppercase text-xs text-on-surface-variant">Most Read Today
            </h3>
            <button onclick="showToast('Loading all articles...')"
              class="text-xs text-primary font-bold hover:underline">See all</button>
          </div>
          <div class="space-y-4">
            <div
              class="group flex gap-4 items-center p-2 rounded-2xl hover:bg-surface-container transition-colors cursor-pointer"
              onclick="showToast('Opening: Neo-Retro article')">
              <div class="w-16 h-16 shrink-0 rounded-xl overflow-hidden bg-zinc-200">
                <img alt="Retro computing hardware" class="w-full h-full object-cover"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuDJOb1U5qgISEAqJrdPCqkwODrYZRJOPzeB_wyL7E5DczepKaYcZyD95tdcTlCcXu2EhiUIDwWe8yWlJGfS1bFEzh0xTfnPWIYhzbdUwKyfWEg7mHmSPzWKtFhFgi-1DEnRivM0sFWpdsSuqe-mAzHYCi90jhYkR5nxVP0OGU3nz6YJuRz1m53EBDuTSvAg5TdUQYpazFOJHnOLlsDBP_jbVUYT5l54cuEPOix--poT4vLzYzQqDzn9f_sufhWSP5DLf75LURfjItc" />
              </div>
              <div>
                <h4 class="font-bold text-sm text-on-surface group-hover:text-primary transition-colors leading-tight">
                  Neo-Retro: The Return of Pixels</h4>
                <span class="text-[10px] text-tertiary font-bold uppercase tracking-widest">Design Trends</span>
                <p class="text-xs text-on-surface-variant mt-0.5">2.1k views • 6 min</p>
              </div>
            </div>
            <div
              class="group flex gap-4 items-center p-2 rounded-2xl hover:bg-surface-container transition-colors cursor-pointer"
              onclick="showToast('Opening: AI & Ethics article')">
              <div class="w-16 h-16 shrink-0 rounded-xl overflow-hidden bg-zinc-200">
                <img alt="Data network visualization" class="w-full h-full object-cover"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuCOmIewBU5N5pnHs1kztsP-Zbq39eN3bydOPc_30jP4QgBHrd9QPiDSOzXKxQ8AMja6yjxLNTGavL0ORnwm0m4jjHY1z73dKkPtkQaAHnqB2TkzYskot_CStSYXbQ-NPyHdsq5J7Yy-YCZ_Pz50zcVnHtf7l5W_2CRlvgPOT9tRuNxujr7dWYkKCI-Za2pLYeFyXQAFxKynr9vE2ylkWWvbGl4szKxrpmrzrhIvXWwjfvee7X0EaoP4qWd7f5qtsp4d0P-DG3N8DAI" />
              </div>
              <div>
                <h4 class="font-bold text-sm text-on-surface group-hover:text-primary transition-colors leading-tight">
                  AI &amp; Ethics: The Designer's Role</h4>
                <span class="text-[10px] text-tertiary font-bold uppercase tracking-widest">Technology</span>
                <p class="text-xs text-on-surface-variant mt-0.5">3.4k views • 10 min</p>
              </div>
            </div>
            <div
              class="group flex gap-4 items-center p-2 rounded-2xl hover:bg-surface-container transition-colors cursor-pointer"
              onclick="showToast('Opening: Typography Systems article')">
              <div
                class="w-16 h-16 shrink-0 rounded-xl overflow-hidden bg-gradient-to-br from-violet-500 to-fuchsia-600 flex items-center justify-center">
                <span class="text-white text-2xl font-black">Aa</span>
              </div>
              <div>
                <h4 class="font-bold text-sm text-on-surface group-hover:text-primary transition-colors leading-tight">
                  Typography Systems for Scale</h4>
                <span class="text-[10px] text-tertiary font-bold uppercase tracking-widest">Typography</span>
                <p class="text-xs text-on-surface-variant mt-0.5">1.8k views • 7 min</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Newsletter signup -->
        <div data-reveal="fade-left" style="transition-delay:0.2s"
          class="p-6 rounded-3xl bg-gradient-to-br from-primary to-secondary-container text-white">
          <h3 class="font-black text-base mb-2">Stay in the loop</h3>
          <p class="text-sm text-primary-fixed mb-4 opacity-90">Weekly design insights delivered to your inbox.</p>
          <div class="flex gap-2">
            <input id="newsletterEmail" type="email" placeholder="your@email.com"
              class="flex-1 bg-white/20 border border-white/30 rounded-xl px-3 py-2 text-sm text-white placeholder-white/60 outline-none focus:bg-white/30 transition-colors" />
            <button onclick="subscribeNewsletter()"
              class="px-4 py-2 bg-white text-primary font-bold rounded-xl text-sm hover:shadow-lg transition-all active:scale-95 whitespace-nowrap">
              Subscribe
            </button>
          </div>
        </div>

      </div>
    </aside>

    <!-- ARTICLE -->
    <article class="lg:col-span-8 order-1 lg:order-1">
      <div class="prose prose-violet max-w-none space-y-8 text-on-surface leading-[1.8] text-lg">
        <?= $post['content'] ?>
      </div>

      <!-- REACTION BAR -->
      <section data-reveal="fade-up"
        class="mt-16 py-8 border-y border-outline-variant/20 flex flex-wrap items-center justify-between gap-6">
        <div class="flex items-center gap-4">
          <span class="text-sm font-bold text-on-surface-variant uppercase tracking-widest">How do you feel?</span>
          <div class="flex gap-2">
            <button
              class="reaction-btn w-12 h-12 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 transition-all text-xl"
              onclick="toggleReaction(this,'🔥')" data-tooltip="On fire">
              🔥<span class="count">0</span>
            </button>
            <button
              class="reaction-btn w-12 h-12 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 transition-all text-xl"
              onclick="toggleReaction(this,'💡')" data-tooltip="Insightful">
              💡<span class="count">0</span>
            </button>
            <button
              class="reaction-btn w-12 h-12 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 transition-all text-xl"
              onclick="toggleReaction(this,'❤️')" data-tooltip="Love it">
              ❤️<span class="count">0</span>
            </button>
            <button
              class="reaction-btn w-12 h-12 flex items-center justify-center rounded-full bg-surface-container-high hover:scale-110 transition-all text-xl"
              onclick="toggleReaction(this,'👏')" data-tooltip="Applause">
              👏<span class="count">0</span>
            </button>
          </div>
          <span class="text-sm text-on-surface-variant" id="reactionSummary"></span>
        </div>
        <div class="flex items-center gap-4">
          <button onclick="openShare()"
            class="flex items-center gap-2 px-6 py-2.5 rounded-full border border-outline-variant text-sm font-bold text-on-surface hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-lg">share</span> Share
          </button>
          <button id="saveBtn" onclick="toggleSave()"
            class="flex items-center gap-2 px-6 py-2.5 rounded-full bg-on-background text-white text-sm font-bold shadow-lg shadow-black/10 hover:scale-105 transition-transform">
            <span class="material-symbols-outlined text-lg" id="saveIcon">bookmark</span>
            <span id="saveText">Save</span>
          </button>
        </div>
      </section>

      <!-- COMMENTS SECTION -->
      <section data-reveal="fade-up" class="mt-16 space-y-8" id="commentsSection">
        <div class="flex items-center justify-between">
          <h3 class="text-2xl font-black tracking-tight">Community Thoughts (<span id="commentCount">34</span>)</h3>
          <div class="flex gap-2">
            <button onclick="sortComments('top')" id="sortTop"
              class="px-4 py-1.5 rounded-full text-xs font-bold bg-primary text-white transition-colors">Top</button>
            <button onclick="sortComments('new')" id="sortNew"
              class="px-4 py-1.5 rounded-full text-xs font-bold bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors">Newest</button>
          </div>
        </div>

        <!-- Comment Input -->
        <div class="flex gap-4 p-6 rounded-3xl bg-surface-container-low">
          <div class="w-10 h-10 rounded-full bg-primary-fixed shrink-0 flex items-center justify-center">
            <span class="text-primary font-black text-sm">G</span>
          </div>
          <div class="flex-1 space-y-4">
            <textarea id="commentInput"
              class="w-full bg-transparent border-none focus:ring-0 text-on-surface p-0 placeholder:text-on-surface-variant/40 resize-none h-20 outline-none text-sm leading-relaxed"
              placeholder="Join the discussion... Share your thoughts on luminous design."
              oninput="updateCharCount(this)"></textarea>
            <div class="flex justify-between items-center">
              <div class="flex gap-3 items-center">
                <span class="text-xs text-on-surface-variant/50" id="charCount">0 / 500</span>
                <button onclick="insertEmoji()"
                  class="text-on-surface-variant hover:text-on-surface transition-colors text-sm">😊</button>
              </div>
              <div class="flex gap-2">
                <button onclick="clearComment()"
                  class="px-4 py-2 text-on-surface-variant text-sm font-medium hover:text-on-surface transition-colors">Clear</button>
                <button onclick="postComment()"
                  class="px-6 py-2 bg-primary text-white font-bold rounded-xl text-sm shadow-lg shadow-primary/20 hover:bg-primary-container transition-all active:scale-95">
                  Post Comment
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Comments list -->
        <div id="commentsList" class="space-y-6"></div>

        <!-- Load more -->
        <button id="loadMoreBtn" onclick="loadMoreComments()"
          class="w-full py-4 rounded-2xl border border-outline-variant/30 text-sm font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors">
          Load 5 more comments
        </button>
      </section>
    </article>
  </main>

  <!-- RELATED POSTS -->
  <section class="max-w-7xl mx-auto px-6 py-24">
    <div class="flex justify-between items-center mb-10">
      <h3 class="text-label font-black tracking-widest uppercase text-xs text-on-surface-variant">More from Blog Fusion
      </h3>
      <button onclick="showToast('Loading all articles...')" class="text-xs text-primary font-bold hover:underline">View
        all →</button>
    </div>
    <div data-reveal-group class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="group cursor-pointer" onclick="showToast('Opening: Mastering Micro-Interactions')">
        <div class="aspect-video rounded-3xl overflow-hidden bg-surface-container-high mb-6 relative">
          <img alt="Designer workspace"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCbNfgt09MUpuSsCbc5wcg_EOISUrXoTiBKk8h_TVbZD2bCNwEGy-K8jJGEk8KNrPDhW9n4T0c_TW-UzTgbt6Jx4vyx284DTze6Fd2QXOU1KDTDLHhn7vtsKloNuUxz8pP88tgiepgKdC0cKWDQ4EXiKNvzFC2uXrOsYlR-t2QlN5qcAKEGiTfwyXI5R7QfxbDpV475z71f8RyBMH7C-5qE57jo7fV0kUM49r5yq2pdu52QwFROA5IWTT5l-Vx-Vucn1iljyNvsz-w" />
          <div
            class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
            <span class="text-white text-sm font-bold">Read article →</span>
          </div>
        </div>
        <span class="text-[10px] text-tertiary font-bold uppercase tracking-widest">UX Design</span>
        <h4 class="text-xl font-bold text-on-surface group-hover:text-primary transition-colors leading-tight mt-1">
          Mastering Micro-Interactions: A Guide for 2025</h4>
        <div class="mt-2 text-sm text-on-surface-variant flex items-center gap-3">
          By Sarah Chen • 5 min read
          <button onclick="event.stopPropagation(); bookmarkPost('micro-interactions')"
            class="hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-base">bookmark_border</span>
          </button>
        </div>
      </div>
      <div class="group cursor-pointer" onclick="showToast('Opening: Server-Side Rendering article')">
        <div class="aspect-video rounded-3xl overflow-hidden bg-surface-container-high mb-6 relative">
          <img alt="Code editor dark mode"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuATNhuyZ3TCC4ErkYrEcSjn38fwsn3EACVWnehPHjARn-zPgcz9yyjOs4kb9-0aXeSaa_VwJglAzfVmZLYKi8fIE_PsGkYVyWrpPE4FAXT-8lyCA4-CofbdeyJ_EAgZyFNwQ9XENHmYwpqSls1lof0o__m6-gOhJtP4-xZJy2JYRg6wbyZC_HobfHJvnPEaI580gN4fHSdTs4S9Y9OkfBWbwJM_VDujc1CiiL99u5mGvRWzDyCeKqcOHSltShFHz1B2ZPVdMHP5_0c" />
          <div
            class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
            <span class="text-white text-sm font-bold">Read article →</span>
          </div>
        </div>
        <span class="text-[10px] text-tertiary font-bold uppercase tracking-widest">Engineering</span>
        <h4 class="text-xl font-bold text-on-surface group-hover:text-primary transition-colors leading-tight mt-1">The
          Rise of Server-Side Rendering in Modern Frameworks</h4>
        <div class="mt-2 text-sm text-on-surface-variant flex items-center gap-3">
          By Marcus Wright • 12 min read
          <button onclick="event.stopPropagation(); bookmarkPost('ssr')" class="hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-base">bookmark_border</span>
          </button>
        </div>
      </div>
      <div class="group cursor-pointer" onclick="showToast('Opening: Securing the Modern Stack article')">
        <div class="aspect-video rounded-3xl overflow-hidden bg-surface-container-high mb-6 relative">
          <img alt="Cybersecurity concept"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCIJVSVfW5vVtzZaR7ld9qcs8mMTqLIPfVR5x8fnxiK5OnILKNv-_42ROvqpZjQFCi4bmuepQLgc2w3PIOBlBzaIwAkiO_a1Ig78j2HmERyJN39cUUCVaURCK2pZtkaxzum1nqOOUNzz2SVcFe8i5Qvq7MaH9_TXSEcwS5uEknEeL17ohSX6vvkMlDOGCb43bHBf1MtASXc07Q6QEOXsyhw5HzHyKtaPmaZpCFTAcacvhI1xGCkgZZqLqKos0JJ4oLsnJRrX1q6UVE" />
          <div
            class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
            <span class="text-white text-sm font-bold">Read article →</span>
          </div>
        </div>
        <span class="text-[10px] text-tertiary font-bold uppercase tracking-widest">Security</span>
        <h4 class="text-xl font-bold text-on-surface group-hover:text-primary transition-colors leading-tight mt-1">
          Securing the Modern Stack: 5 Core Vulnerabilities</h4>
        <div class="mt-2 text-sm text-on-surface-variant flex items-center gap-3">
          By Dr. Amara Okafor • 15 min read
          <button onclick="event.stopPropagation(); bookmarkPost('security')"
            class="hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-base">bookmark_border</span>
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="bg-zinc-50 dark:bg-zinc-900 w-full rounded-t-3xl mt-12">
    <div class="flex flex-col md:flex-row justify-between items-center py-12 px-8 max-w-7xl mx-auto">
      <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-8 md:mb-0">Blog Fusion</div>
      <div class="flex gap-8 items-center mb-8 md:mb-0 font-public-sans uppercase tracking-widest text-xs">
        <a class="text-zinc-500 hover:text-violet-400 transition-colors opacity-80 hover:opacity-100" href="#">About</a>
        <a class="text-zinc-500 hover:text-violet-400 transition-colors opacity-80 hover:opacity-100"
          href="#">Privacy</a>
        <a class="text-zinc-500 hover:text-violet-400 transition-colors opacity-80 hover:opacity-100" href="#">Terms</a>
        <a class="text-zinc-500 hover:text-violet-400 transition-colors opacity-80 hover:opacity-100"
          href="#">Contact</a>
      </div>
      <div class="text-zinc-500 text-sm">© 2024 Blog Fusion. The Luminous Editor.</div>
    </div>
  </footer>

  <!-- READING PROGRESS BAR -->
  <!-- <div class="fixed top-16 left-0 h-1 bg-gradient-to-r from-primary to-tertiary z-[60]" id="progress-bar"
    style="width:0%;transition:width 0.1s linear;"></div> -->

  <!-- BACK TO TOP -->
  <button id="backToTop" onclick="scrollToTop()"
    class="fixed bottom-8 right-8 w-12 h-12 bg-primary text-white rounded-full shadow-lg shadow-primary/30 flex items-center justify-center hover:scale-110 transition-all opacity-0 pointer-events-none z-50">
    <span class="material-symbols-outlined">arrow_upward</span>
  </button>

  <script>
    /* ─── DATA ─── */
    const allPosts = [
      { title: "The Luminous Editor", cat: "design", tag: "Innovation & Design", views: 1247 },
      { title: "Neo-Retro: The Return of Pixels", cat: "design", tag: "Design Trends", views: 2100 },
      { title: "AI & Ethics: The Designer's Role", cat: "technology", tag: "Technology", views: 3400 },
      { title: "Mastering Micro-Interactions", cat: "design", tag: "UX Design", views: 890 },
      { title: "Server-Side Rendering in Modern Frameworks", cat: "technology", tag: "Engineering", views: 1560 },
      { title: "Securing the Modern Stack", cat: "technology", tag: "Security", views: 2200 },
      { title: "Typography Systems for Scale", cat: "design", tag: "Typography", views: 1800 },
      { title: "Color Theory for Digital Interfaces", cat: "design", tag: "Design Trends", views: 950 },
      { title: "The State of WebAssembly 2024", cat: "technology", tag: "Engineering", views: 1100 },
    ];

    let commentData = [
      { id: 1, author: "Elena S.", avatar: "E", time: "2 hours ago", text: "This perspective on tonal depth is exactly what I've been trying to articulate to my clients. The 'box' fatigue is real. Great read!", likes: 12, liked: false, replies: [] },
      {
        id: 2, author: "Marco T.", avatar: "M", time: "4 hours ago", text: "I've been using this approach for 6 months now and my clients love it. The 'tonal hierarchy' terminology alone changed how I present design decisions.", likes: 8, liked: false, replies: [
          { id: 21, author: "Sarah K.", avatar: "S", time: "3 hours ago", text: "Same here! Especially the no-divider rule. Once you explain the 'whitespace as separator' concept it just clicks.", likes: 3, liked: false }
        ]
      },
      { id: 3, author: "Priya N.", avatar: "P", time: "6 hours ago", text: "The code snippet is perfect. I'll be refactoring our design tokens this week using this exact color naming convention. Thanks Julian!", likes: 21, liked: false, replies: [] },
      {
        id: 4, author: "James W.", avatar: "J", time: "8 hours ago", text: "Slightly disagree on the 'no borders at all' stance. There are accessibility cases where borders provide essential visual cues that color alone can't replicate for low-vision users.", likes: 15, liked: false, replies: [
          { id: 41, author: "Julian Vance", avatar: "JV", time: "7 hours ago", text: "Completely valid point, James. I should have emphasized this is a guideline, not a blanket rule. Accessibility should always override aesthetic preference.", likes: 19, liked: false, isAuthor: true }
        ]
      },
    ];

    let displayedComments = 3;
    let isFollowing = false;
    let isSaved = false;
    let isDark = false;
    let reactions = { '🔥': 0, '💡': 0, '❤️': 0, '👏': 0 };
    let nextCommentId = 100;
    let replyingTo = null;
    let sortMode = 'top';
    let views = 1247;

    /* ─── INIT ─── */
    window.onload = () => {
      renderComments();
      simulateViews();
    };

    /* ─── DARK MODE ─── */
    function toggleDark() {
      isDark = !isDark;
      document.documentElement.classList.toggle('dark', isDark);
      document.getElementById('darkIcon').textContent = isDark ? 'light_mode' : 'dark_mode';
    }

    /* ─── SCROLL ─── */
    // window.onscroll = () => {
    //   const scrollTop = document.body.scrollTop || document.documentElement.scrollTop;
    //   const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    //   const scrolled = (scrollTop / height) * 100;
    //   const pct = Math.round(scrolled);

    //   document.getElementById('progress-bar').style.width = scrolled + '%';
    //   document.getElementById('inlineProgress').style.width = scrolled + '%';
    //   document.getElementById('readingPct').textContent = pct + '%';

    //   const nav = document.getElementById('mainNav');
    //   nav.style.boxShadow = scrollTop > 50 ? '0 4px 24px rgba(99,14,212,0.08)' : '';

    //   const backBtn = document.getElementById('backToTop');
    //   if (scrollTop > 600) { backBtn.style.opacity = '1'; backBtn.style.pointerEvents = 'auto'; }
    //   else { backBtn.style.opacity = '0'; backBtn.style.pointerEvents = 'none'; }

    //   updateToc();
    // };

    function scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /* ─── TOC ─── */
    function updateToc() {
      const sections = ['intro', 'philosophy', 'execution', 'summary'];
      let active = sections[0];
      sections.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        if (el.getBoundingClientRect().top < 120) active = id;
      });
      document.querySelectorAll('.toc-link').forEach(a => {
        const isActive = a.getAttribute('href') === '#' + active;
        a.className = isActive
          ? 'toc-link text-primary font-bold hover:text-primary-container transition-colors'
          : 'toc-link text-on-surface-variant hover:text-primary transition-colors';
      });
    }

    /* ─── SEARCH ─── */
    function handleSearch(input) {
      const q = input.value.toLowerCase().trim();
      const box = document.getElementById('searchResults');
      if (!q) { box.classList.remove('show'); return; }
      const results = allPosts.filter(p => p.title.toLowerCase().includes(q) || p.tag.toLowerCase().includes(q));
      if (!results.length) {
        box.innerHTML = '<div class="search-result-item text-on-surface-variant">No results for "' + escapeHtml(q) + '"</div>';
      } else {
        box.innerHTML = results.map(p => `
      <div class="search-result-item" onmousedown="event.preventDefault(); showToast('Opening: ${escapeHtml(p.title)}'); document.getElementById('searchInput').value=''; document.getElementById('searchResults').classList.remove('show');">
        <div class="font-semibold text-on-surface">${escapeHtml(p.title)}</div>
        <div class="tag">${escapeHtml(p.tag)} • ${p.views.toLocaleString()} views</div>
      </div>`).join('');
      }
      box.classList.add('show');
    }

    function hideSearch() {
      setTimeout(() => document.getElementById('searchResults').classList.remove('show'), 150);
    }

    /* ─── CATEGORY FILTER ─── */
    function filterCategory(cat, el) {
      if (!el) return;
      document.querySelectorAll('.cat-tab').forEach(t => {
        t.className = 'cat-tab text-zinc-600 dark:text-zinc-400 font-medium hover:text-violet-500 transition-colors pb-1';
      });
      el.className = 'cat-tab active text-violet-700 dark:text-violet-400 font-bold pb-1';
      showToast(cat === 'home' ? 'Showing all posts' : cat === 'popular' ? 'Showing popular posts' : 'Filtering by category...');
      return false;
    }

    /* ─── REACTIONS ─── */
    function toggleReaction(btn, emoji) {
      const isActive = btn.classList.contains('active');
      if (isActive) {
        reactions[emoji]--;
        btn.classList.remove('active');
      } else {
        reactions[emoji]++;
        btn.classList.add('active');
        btn.style.transform = 'scale(1.2)';
        setTimeout(() => btn.style.transform = '', 300);
      }
      const count = btn.querySelector('.count');
      count.textContent = reactions[emoji];

      // Update summary
      const parts = Object.entries(reactions).filter(([, v]) => v > 0).map(([e, v]) => `${e} ${v}`);
      document.getElementById('reactionSummary').textContent = parts.length ? parts.join('  ') : '';
    }

    /* ─── SHARE ─── */
    function openShare() { document.getElementById('shareModal').classList.add('show'); }
    function closeShare() { document.getElementById('shareModal').classList.remove('show'); }
    document.getElementById('shareModal').addEventListener('click', e => { if (e.target === document.getElementById('shareModal')) closeShare(); });

    function copyShareLink() {
      navigator.clipboard.writeText('https://blogfusion.io/luminous-editor-2024').catch(() => { });
      showToast('Link copied to clipboard! 🔗');
      closeShare();
    }

    function shareToTwitter() {
      showToast('Opening Twitter / X...');
      closeShare();
    }
    function shareToLinkedIn() {
      showToast('Opening LinkedIn...');
      closeShare();
    }

    /* ─── SAVE ─── */
    function toggleSave() {
      isSaved = !isSaved;
      const btn = document.getElementById('saveBtn');
      const icon = document.getElementById('saveIcon');
      const text = document.getElementById('saveText');
      if (isSaved) {
        icon.style.fontVariationSettings = "'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24";
        text.textContent = 'Saved';
        btn.classList.add('bg-primary');
        btn.classList.remove('bg-on-background');
        showToast('Article saved to your library! 📚');
      } else {
        icon.style.fontVariationSettings = "'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24";
        text.textContent = 'Save';
        btn.classList.remove('bg-primary');
        btn.classList.add('bg-on-background');
        showToast('Removed from your library');
      }
    }

    function bookmarkPost(slug) {
      showToast('Post bookmarked! 📌');
    }

    /* ─── FOLLOW ─── */
    function toggleFollow() {
      isFollowing = !isFollowing;
      const btn = document.getElementById('followBtn');
      const count = document.getElementById('followerCount');
      let current = parseInt(count.textContent.replace(/,/g, '').split(' ')[0]);
      if (isFollowing) {
        btn.textContent = '✓ Following';
        btn.style.background = '#eaddff';
        btn.style.color = '#630ed4';
        count.textContent = (current + 1).toLocaleString() + ' followers';
        showToast('You\'re now following Julian Vance!');
      } else {
        btn.textContent = 'Follow Julian';
        btn.style.background = '';
        btn.style.color = '';
        count.textContent = current.toLocaleString() + ' followers';
        showToast('Unfollowed Julian Vance');
      }
    }

    /* ─── COMMENTS ─── */
    function renderComments() {
      const list = document.getElementById('commentsList');
      let sorted = [...commentData];
      if (sortMode === 'top') sorted.sort((a, b) => b.likes - a.likes);
      else sorted.sort((a, b) => b.id - a.id);

      const shown = sorted.slice(0, displayedComments);
      list.innerHTML = shown.map(c => renderComment(c)).join('');

      const loadBtn = document.getElementById('loadMoreBtn');
      if (displayedComments >= sorted.length) {
        loadBtn.style.display = 'none';
      } else {
        loadBtn.style.display = '';
        loadBtn.textContent = `Load ${Math.min(5, sorted.length - displayedComments)} more comments`;
      }
    }

    function renderComment(c) {
      const repliesHtml = (c.replies || []).map(r => `
    <div class="reply-comment mt-4 flex gap-3">
      <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center text-sm font-black ${r.isAuthor ? 'bg-primary text-white' : 'bg-primary-fixed text-primary'}">
        ${r.avatar}
      </div>
      <div class="flex-1">
        <div class="flex items-center gap-2 mb-1">
          <span class="font-bold text-on-surface text-sm">${escapeHtml(r.author)}</span>
          ${r.isAuthor ? '<span class="px-2 py-0.5 rounded-full bg-primary text-white text-[10px] font-bold">Author</span>' : ''}
          <span class="text-xs text-on-surface-variant">${r.time}</span>
        </div>
        <p class="text-on-surface-variant text-sm leading-relaxed">${escapeHtml(r.text)}</p>
        <button onclick="likeComment(${r.id}, true, ${c.id})" class="comment-like flex items-center gap-1 text-xs font-bold text-on-surface-variant hover:text-on-surface mt-2 transition-colors ${r.liked ? 'liked' : ''}">
          <span class="material-symbols-outlined text-[14px]">${r.liked ? 'thumb_up' : 'thumb_up'}</span> ${r.likes}
        </button>
      </div>
    </div>`).join('');

      return `
    <div class="flex gap-4 p-2" id="comment-${c.id}">
      <div class="w-10 h-10 rounded-full shrink-0 flex items-center justify-center text-sm font-black ${c.isAuthor ? 'bg-primary text-white' : 'bg-primary-fixed text-primary'}">
        ${c.avatar}
      </div>
      <div class="flex-1 space-y-2">
        <div class="flex items-center gap-2">
          <span class="font-bold text-on-surface">${escapeHtml(c.author)}</span>
          ${c.isAuthor ? '<span class="px-2 py-0.5 rounded-full bg-primary text-white text-[10px] font-bold">Author</span>' : ''}
          <span class="text-xs text-on-surface-variant">${c.time}</span>
          ${c.id >= 100 ? '<span class="px-2 py-0.5 rounded-full bg-primary-fixed text-primary text-[10px] font-bold">You</span>' : ''}
        </div>
        <p class="text-on-surface-variant text-sm leading-relaxed">${escapeHtml(c.text)}</p>
        <div class="flex items-center gap-4 pt-1">
          <button onclick="startReply(${c.id})" class="text-xs font-bold text-primary hover:underline transition-colors">Reply</button>
          <button onclick="likeComment(${c.id}, false, null)" class="comment-like flex items-center gap-1 text-xs font-bold text-on-surface-variant hover:text-on-surface transition-colors ${c.liked ? 'liked' : ''}">
            <span class="material-symbols-outlined text-[14px]">thumb_up</span> <span id="likes-${c.id}">${c.likes}</span>
          </button>
          ${c.id >= 100 ? `<button onclick="deleteComment(${c.id})" class="text-xs font-bold text-error hover:underline transition-colors">Delete</button>` : ''}
        </div>
        ${repliesHtml}
        <div id="reply-box-${c.id}" class="hidden mt-4">
          <div class="flex gap-3">
            <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-black text-xs shrink-0">G</div>
            <div class="flex-1">
              <textarea id="reply-input-${c.id}" class="w-full bg-surface-container-low border border-outline-variant/20 rounded-xl px-4 py-3 text-sm text-on-surface outline-none focus:ring-2 ring-primary resize-none h-16" placeholder="Write a reply..."></textarea>
              <div class="flex justify-end gap-2 mt-2">
                <button onclick="cancelReply(${c.id})" class="px-4 py-1.5 text-on-surface-variant text-sm font-medium hover:text-on-surface transition-colors">Cancel</button>
                <button onclick="postReply(${c.id})" class="px-5 py-1.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-container transition-colors">Reply</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>`;
    }

    function postComment() {
      const input = document.getElementById('commentInput');
      const text = input.value.trim();
      if (!text) { showToast('Please write something first!'); return; }
      if (text.length > 500) { showToast('Comment is too long (max 500 chars)'); return; }

      const newComment = {
        id: nextCommentId++, author: 'Guest Reader', avatar: 'G',
        time: 'just now', text, likes: 0, liked: false, replies: []
      };
      commentData.unshift(newComment);
      displayedComments++;
      updateCommentCount(1);
      input.value = '';
      document.getElementById('charCount').textContent = '0 / 500';
      renderComments();
      showToast('Comment posted! 💬');
      setTimeout(() => document.getElementById('comment-' + newComment.id)?.scrollIntoView({ behavior: 'smooth', block: 'center' }), 100);
    }

    function postReply(commentId) {
      const input = document.getElementById('reply-input-' + commentId);
      const text = input.value.trim();
      if (!text) return;
      const comment = commentData.find(c => c.id === commentId);
      if (!comment) return;
      comment.replies.push({ id: nextCommentId++, author: 'Guest Reader', avatar: 'G', time: 'just now', text, likes: 0, liked: false });
      updateCommentCount(1);
      renderComments();
      showToast('Reply posted!');
    }

    function startReply(id) {
      document.querySelectorAll('[id^=reply-box-]').forEach(el => el.classList.add('hidden'));
      document.getElementById('reply-box-' + id)?.classList.remove('hidden');
      document.getElementById('reply-input-' + id)?.focus();
    }

    function cancelReply(id) {
      document.getElementById('reply-box-' + id)?.classList.add('hidden');
    }

    function likeComment(commentId, isReply, parentId) {
      if (isReply) {
        const parent = commentData.find(c => c.id === parentId);
        const reply = parent?.replies.find(r => r.id === commentId);
        if (!reply) return;
        reply.liked = !reply.liked;
        reply.likes += reply.liked ? 1 : -1;
      } else {
        const comment = commentData.find(c => c.id === commentId);
        if (!comment) return;
        comment.liked = !comment.liked;
        comment.likes += comment.liked ? 1 : -1;
      }
      renderComments();
    }

    function deleteComment(id) {
      commentData = commentData.filter(c => c.id !== id);
      if (displayedComments > 0) displayedComments--;
      updateCommentCount(-1);
      renderComments();
      showToast('Comment deleted');
    }

    function sortComments(mode) {
      sortMode = mode;
      displayedComments = 3;
      document.getElementById('sortTop').className = mode === 'top'
        ? 'px-4 py-1.5 rounded-full text-xs font-bold bg-primary text-white transition-colors'
        : 'px-4 py-1.5 rounded-full text-xs font-bold bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors';
      document.getElementById('sortNew').className = mode === 'new'
        ? 'px-4 py-1.5 rounded-full text-xs font-bold bg-primary text-white transition-colors'
        : 'px-4 py-1.5 rounded-full text-xs font-bold bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors';
      renderComments();
    }

    function loadMoreComments() {
      displayedComments += 5;
      renderComments();
    }

    function updateCommentCount(delta) {
      const el = document.getElementById('commentCount');
      el.textContent = parseInt(el.textContent) + delta;
    }

    function updateCharCount(el) {
      const len = el.value.length;
      document.getElementById('charCount').textContent = len + ' / 500';
      if (len > 450) document.getElementById('charCount').style.color = '#ba1a1a';
      else document.getElementById('charCount').style.color = '';
    }

    function clearComment() {
      document.getElementById('commentInput').value = '';
      document.getElementById('charCount').textContent = '0 / 500';
    }

    function insertEmoji() {
      const emojis = ['😊', '🎨', '✨', '💜', '🔥', '👍', '💡', '🚀'];
      const e = emojis[Math.floor(Math.random() * emojis.length)];
      const input = document.getElementById('commentInput');
      input.value += e;
      updateCharCount(input);
    }

    /* ─── CODE COPY ─── */
    function copyCode(btn) {
      const code = btn.closest('.code-container').querySelector('code').innerText;
      navigator.clipboard.writeText(code).catch(() => { });
      const orig = btn.innerHTML;
      btn.innerHTML = '<span class="material-symbols-outlined text-sm">check</span> Copied!';
      btn.classList.add('text-green-400');
      setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('text-green-400'); }, 2000);
    }

    /* ─── PROFILE DROPDOWN ─── */
    function toggleProfile() {
      const menu = document.getElementById('profileMenu');
      menu.classList.toggle('hidden');
      document.addEventListener('click', function handler(e) {
        if (!menu.contains(e.target)) {
          menu.classList.add('hidden');
          document.removeEventListener('click', handler);
        }
      }, { once: false });
    }

    /* ─── TOAST ─── */
    let toastTimer;
    function showToast(msg) {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.classList.add('show');
      clearTimeout(toastTimer);
      toastTimer = setTimeout(() => t.classList.remove('show'), 3000);
    }

    /* ─── NOTIFICATIONS ─── */
    function showNotifications() {
      showToast('You have 3 new replies on your comments!');
      document.querySelector('.notif-dot').style.display = 'none';
    }

    /* ─── NEWSLETTER ─── */
    function subscribeNewsletter() {
      const email = document.getElementById('newsletterEmail').value.trim();
      if (!email || !email.includes('@')) { showToast('Please enter a valid email address'); return; }
      document.getElementById('newsletterEmail').value = '';
      showToast('Subscribed! Welcome to Blog Fusion 🎉');
    }

    /* ─── VIEWS SIMULATION ─── */
    function simulateViews() {
      setInterval(() => {
        if (Math.random() < 0.3) {
          views++;
          document.getElementById('viewCounter').textContent = views.toLocaleString() + ' Views';
        }
      }, 5000);
    }

    /* ─── UTILS ─── */
    function escapeHtml(str) {
      return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    /* ─── SCROLL REVEAL ─── */
    (function () {
      const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
            revealObserver.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

      const groupObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
            groupObserver.unobserve(entry.target);
          }
        });
      }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

      function initReveal() {
        document.querySelectorAll('[data-reveal]').forEach(el => revealObserver.observe(el));
        document.querySelectorAll('[data-reveal-group]').forEach(el => groupObserver.observe(el));
      }

      // Re-run after dynamic comment rendering
      window.initScrollReveal = initReveal;
      initReveal();
    })();
  </script>
</body>

</html>