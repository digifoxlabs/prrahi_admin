<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Prrahi</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    :root {
      --bg-deep: #0b1228;
      --bg-mid: #1e293b;
      --accent-cyan: #06b6d4;
      --accent-blue: #3b82f6;
      --accent-amber: #f59e0b;
    }

    .mesh-background {
      background:
        radial-gradient(circle at 15% 20%, rgba(34, 211, 238, 0.32), transparent 45%),
        radial-gradient(circle at 85% 18%, rgba(96, 165, 250, 0.3), transparent 40%),
        radial-gradient(circle at 50% 90%, rgba(251, 191, 36, 0.24), transparent 35%),
        linear-gradient(160deg, var(--bg-deep), var(--bg-mid));
    }

    .glass-card {
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(191, 219, 254, 0.35);
      background: linear-gradient(145deg, rgba(30, 41, 59, 0.78), rgba(51, 65, 85, 0.58));
      box-shadow: 0 12px 32px rgba(15, 23, 42, 0.34);
      transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .welcome-panel {
      background: linear-gradient(155deg, rgba(51, 65, 85, 0.42), rgba(30, 41, 59, 0.32));
      box-shadow: 0 24px 55px rgba(15, 23, 42, 0.42);
    }

    .welcome-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid rgba(186, 230, 253, 0.45);
      background: rgba(8, 47, 73, 0.35);
      color: #bae6fd;
      letter-spacing: 0.06em;
    }

    .role-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1.25rem;
      align-items: stretch;
    }

    @media (min-width: 768px) {
      .role-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }
    }

    .role-card {
      min-height: 210px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .glass-card::before {
      content: "";
      position: absolute;
      inset: -30%;
      opacity: 0;
      background: linear-gradient(120deg, transparent 35%, rgba(255, 255, 255, 0.3) 50%, transparent 65%);
      transform: translateX(-38%) rotate(12deg);
      transition: opacity 0.35s ease;
      pointer-events: none;
    }

    .glass-card:hover {
      transform: translateY(-7px);
      border-color: rgba(125, 211, 252, 0.78);
      box-shadow: 0 18px 42px rgba(14, 116, 144, 0.38);
    }

    .glass-card:hover::before {
      opacity: 1;
      animation: sheen 0.85s ease;
    }

    @keyframes sheen {
      from { transform: translateX(-42%) rotate(12deg); }
      to { transform: translateX(42%) rotate(12deg); }
    }

    /* Keep the full page visible on shorter laptop screens (e.g. 15-inch). */
    @media (min-width: 1024px) and (max-height: 820px) {
      .welcome-main {
        align-items: flex-start;
        padding-top: 3.5rem;
        padding-bottom: 0.5rem;
      }

      .welcome-panel {
        padding: 3rem;
      }

      .welcome-logo {
        height: 4.75rem;
        width: 6.5rem;
      }

      .welcome-title {
        margin-top: 0.35rem;
        font-size: 1.65rem;
        line-height: 1.2;
      }

      .welcome-subtitle {
        margin-top: 0.2rem;
        font-size: 0.8rem;
        line-height: 1.25;
      }

      .welcome-grid {
        margin-top: 0.75rem;
        gap: 1.0rem;
      }

      .welcome-card {
        padding: 0.75rem;
      }

      .role-card {
        min-height: 180px;
      }

      .welcome-card-title {
        margin-top: 0.4rem;
        font-size: 0.95rem;
      }

      .welcome-card-text {
        margin-top: 0.25rem;
        font-size: 0.72rem;
        line-height: 1.2;
      }

      .welcome-card-cta {
        margin-top: 0.55rem;
        font-size: 0.72rem;
      }
    }

    @media (min-width: 1024px) and (max-height: 720px) {
      .welcome-subtitle,
      .welcome-card-text {
        display: true;
      }

      .welcome-grid {
        margin-top: 0.5rem;
      }
    }
  </style>
</head>
<body class="mesh-background min-h-screen text-slate-100 lg:h-screen lg:overflow-hidden">
  <main class="welcome-main mx-auto flex min-h-screen w-full max-w-6xl items-center px-4 py-10 sm:px-8 lg:h-screen lg:px-10 lg:py-2">
    <section class="welcome-panel w-full rounded-3xl border border-slate-300/30 bg-slate-800/35 p-6 backdrop-blur-md sm:p-8 lg:p-5">
      <div class="text-center">
        <span class="welcome-badge rounded-full px-3 py-1 text-[10px] font-semibold sm:text-xs">SECURE ACCESS PORTAL</span>
        <img
          src="{{ asset('images/logo/logo.png') }}"
          alt="Prrahi Logo"
          class="welcome-logo mx-auto mt-2 h-24 w-36 object-contain sm:h-28 sm:w-44 md:h-32 md:w-48 lg:h-20 lg:w-36"
        />
        <h1 class="welcome-title mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl md:text-5xl lg:text-3xl">Welcome to Prrahi</h1>
        <p class="welcome-subtitle mx-auto mt-1 max-w-2xl text-sm text-slate-100/95 sm:text-base lg:text-sm">
          Select your role below and continue to your workspace.
        </p>
      </div>

      <div class="role-grid welcome-grid mt-5 lg:mx-auto lg:max-w-4xl lg:gap-3">
        <a
          href="/admin/login"
          class="role-card welcome-card glass-card group rounded-2xl p-5 sm:p-4 lg:p-4"
          aria-label="Administrator login"
        >
          <div>
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-400/20 text-cyan-300 ring-1 ring-cyan-300/35 lg:h-9 lg:w-9">A</span>
            <h2 class="welcome-card-title mt-2 text-xl font-semibold text-white lg:text-base">Administrator</h2>
            <p class="welcome-card-text mt-1 text-sm text-slate-200 lg:text-xs">Manage users, security, and system-level configuration.</p>
          </div>
          <span class="welcome-card-cta mt-3 inline-flex items-center text-sm font-medium lg:mt-2 lg:text-xs text-cyan-300 transition group-hover:translate-x-1">
            Enter Portal <span class="ml-2">&rarr;</span>
          </span>
        </a>

        <a
          href="/sales/login"
          class="role-card welcome-card glass-card group rounded-2xl p-5 sm:p-4 lg:p-4"
          aria-label="Sales login"
        >
          <div>
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-blue-400/20 text-blue-300 ring-1 ring-blue-300/35 lg:h-9 lg:w-9">S</span>
            <h2 class="welcome-card-title mt-2 text-xl font-semibold text-white lg:text-base">Sales</h2>
            <p class="welcome-card-text mt-1 text-sm text-slate-200 lg:text-xs">Track orders, activity, and performance across customers.</p>
          </div>
          <span class="welcome-card-cta mt-3 inline-flex items-center text-sm font-medium lg:mt-2 lg:text-xs text-blue-300 transition group-hover:translate-x-1">
            Enter Portal <span class="ml-2">&rarr;</span>
          </span>
        </a>

        <a
          href="/distributor/login"
          class="role-card welcome-card glass-card group rounded-2xl p-5 sm:p-4 lg:p-4"
          aria-label="Distributor login"
        >
          <div>
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-400/20 text-amber-300 ring-1 ring-amber-300/35 lg:h-9 lg:w-9">D</span>
            <h2 class="welcome-card-title mt-2 text-xl font-semibold text-white lg:text-base">Distributor</h2>
            <p class="welcome-card-text mt-1 text-sm text-slate-200 lg:text-xs">Handle products, stock movement, and delivery workflows.</p>
          </div>
          <span class="welcome-card-cta mt-3 inline-flex items-center text-sm font-medium lg:mt-2 lg:text-xs text-amber-300 transition group-hover:translate-x-1">
            Enter Portal <span class="ml-2">&rarr;</span>
          </span>
        </a>
      </div>
    </section>
  </main>
</body>
</html>
