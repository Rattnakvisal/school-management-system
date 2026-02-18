<nav style="background:#111827;color:#fff;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
    <div style="font-weight:700;">Student Panel</div>
    <div style="display:flex;align-items:center;gap:12px;">
        <span style="font-size:14px;opacity:.9;">
            {{ auth()->user()->name ?? 'Student' }}
        </span>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" style="background:#374151;color:#fff;border:0;padding:6px 10px;border-radius:6px;cursor:pointer;">
                Logout
            </button>
        </form>
    </div>
</nav>
