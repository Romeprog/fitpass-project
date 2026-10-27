<header>
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="logo">Fit<span>Pass</span></a>
            <ul class="nav-menu" id="nav-menu">
                <li><a href="{{ route('home') }}"
                        class="nav-link{{ request()->routeIs('home') ? ' active' : '' }}">Home</a></li>
                <li><a href="{{ route('about') }}"
                        class="nav-link{{ request()->routeIs('about') ? ' active' : '' }}">About Us</a></li>
                <li><a href="{{ route('workouts') }}"
                        class="nav-link{{ request()->routeIs('workouts') ? ' active' : '' }}">Workouts</a></li>
                <li><a href="{{ route('diet') }}" class="nav-link{{ request()->routeIs('diet') ? ' active' : '' }}">Diet
                        Tracker</a></li>
                <li><a href="{{ route('account') }}"
                        class="nav-link{{ request()->routeIs('account') ? ' active' : '' }}">Account</a></li>
                <li class="nav-user" id="navUser">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    <span>{{ auth()->user()->name }}</span>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-logout" id="logoutBtn">Log Out</button>
                    </form>
                </li>
            </ul>
            <button class="hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>
</header>