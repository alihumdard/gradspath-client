@impersonating
  <div class="gp-impersonation-banner" role="alert" aria-live="polite">
    <div class="gp-impersonation-banner__inner">
      <div class="gp-impersonation-banner__info">
        <span class="gp-impersonation-banner__badge">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
          Admin Impersonation Mode
        </span>
        <span class="gp-impersonation-banner__text">
          Viewing portal as <strong>{{ auth()->user()->name ?? auth()->user()->email }}</strong>
          <span class="gp-impersonation-banner__role">({{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'User') }})</span>
        </span>
      </div>
      <div class="gp-impersonation-banner__actions">
        <a href="{{ route('impersonate.leave') }}" class="gp-impersonation-banner__leave-btn">
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          Return to Admin Portal
        </a>
      </div>
    </div>
  </div>

  <style>
    .gp-impersonation-banner {
      position: sticky;
      top: 0;
      left: 0;
      right: 0;
      z-index: 99999;
      background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
      color: #ffffff;
      border-bottom: 2px solid #6366f1;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
      padding: 0.625rem 1.25rem;
      font-family: inherit;
      font-size: 0.875rem;
      animation: gpBannerSlideDown 0.3s ease-out forwards;
    }

    @keyframes gpBannerSlideDown {
      from { transform: translateY(-100%); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    .gp-impersonation-banner__inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      max-width: 1440px;
      margin: 0 auto;
      flex-wrap: wrap;
    }

    .gp-impersonation-banner__info {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      flex-wrap: wrap;
    }

    .gp-impersonation-banner__badge {
      display: inline-flex;
      align-items: center;
      gap: 0.375rem;
      background: rgba(245, 158, 11, 0.2);
      border: 1px solid rgba(245, 158, 11, 0.5);
      color: #fbbf24;
      font-weight: 700;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      padding: 0.2rem 0.55rem;
      border-radius: 9999px;
    }

    .gp-impersonation-banner__text {
      color: #e0e7ff;
      font-size: 0.875rem;
    }

    .gp-impersonation-banner__text strong {
      color: #ffffff;
      font-weight: 600;
    }

    .gp-impersonation-banner__role {
      color: #a5b4fc;
      font-size: 0.8125rem;
    }

    .gp-impersonation-banner__leave-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: #f59e0b;
      color: #111827;
      font-weight: 600;
      font-size: 0.8125rem;
      padding: 0.4rem 0.875rem;
      border-radius: 0.5rem;
      text-decoration: none;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 2px 6px rgba(245, 158, 11, 0.35);
      white-space: nowrap;
    }

    .gp-impersonation-banner__leave-btn:hover {
      background: #fbbf24;
      color: #000000;
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(245, 158, 11, 0.5);
    }

    .gp-impersonation-banner__leave-btn:active {
      transform: translateY(0);
    }

    @media (max-width: 640px) {
      .gp-impersonation-banner__inner {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }
      .gp-impersonation-banner__actions {
        width: 100%;
      }
      .gp-impersonation-banner__leave-btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
@endImpersonating
