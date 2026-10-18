@extends('layouts.app')

@section('title', 'Home')
@section('page', 'home')

@section('content')
    <main>
        <!-- HERO -->
        <section class="home-section" id="home">
            <div class="home-content">
                <div class="hero-label">Your Fitness Journey Starts Here</div>
                <h1 class="main-heading">Build A <em>Stronger,</em><br>Healthier You</h1>
                <p class="tagline">If you want something you've never had,
                    <br>you must be willing to do something you've never done.
                </p>
                <div class="hero-ctas">
                    <a href="{{ route('workouts') }}" class="cta-btn">Start a Workout</a>
                    <a href="{{ route('diet') }}" class="cta-btn-outline">Track My Diet</a>
                </div>
            </div>
        </section>

        <!-- TODAY AT A GLANCE -->
        <section class="today-section" id="today">
            <div class="container">
                <p class="eyebrow reveal">Today at a glance</p>
                <h2 class="section-title reveal">Welcome back, {{ Str::before(trim(auth()->user()->name), ' ') }}</h2>
                <div class="glance-grid">
                    <a href="{{ route('diet') }}" class="glance-card reveal">
                        <div class="glance-label">Calories today</div>
                        <div class="glance-value"><span id="gCal">0</span> <small>/ <span id="gCalGoal">0</span>
                                kcal</small></div>
                        <div class="glance-note" id="gCalNote">Log your first meal</div>
                    </a>
                    <a href="{{ route('diet') }}" class="glance-card reveal">
                        <div class="glance-label">Protein today</div>
                        <div class="glance-value"><span id="gPro">0</span> <small>/ <span id="gProGoal">0</span>
                                g</small></div>
                        <div class="glance-note">Fuel your recovery</div>
                    </a>
                    <a href="{{ route('workouts') }}" class="glance-card green reveal">
                        <div class="glance-label">Workouts this week</div>
                        <div class="glance-value"><span id="gWeek">0</span></div>
                        <div class="glance-note" id="gStreak">Start your streak today</div>
                    </a>
                    <a href="{{ route('diet') }}" class="glance-card green reveal">
                        <div class="glance-label">Meals logged today</div>
                        <div class="glance-value"><span id="gMeals">0</span> <small>foods</small></div>
                        <div class="glance-note" id="gMealsNote">Nothing logged yet</div>
                    </a>
                </div>
            </div>
        </section>

        <!-- WHY FITPASS -->
        <section class="why-section">
            <div class="container">
                <p class="eyebrow reveal">Why Choose Us</p>
                <h2 class="section-title reveal">Everything You Need<br>to Succeed</h2>
                <p class="section-subtitle reveal">FitPass puts every workout and every meal in one place, so you can
                    see
                    exactly what you did and what to do next.</p>
                <div class="features-grid">
                    <div class="feature-card reveal">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M6.5 6.5h11M6.5 17.5h11M3 9v6M6 7v10M18 7v10M21 9v6" />
                            </svg>
                        </div>
                        <h3>Individual Workouts</h3>
                        <p>Open any workout, see every exercise with its sets and reps, and check each one off as you
                            go.</p>
                    </div>
                    <div class="feature-card reveal">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M18 8h1a4 4 0 0 1 0 8h-1" />
                                <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z" />
                                <line x1="6" y1="1" x2="6" y2="4" />
                                <line x1="10" y1="1" x2="10" y2="4" />
                                <line x1="14" y1="1" x2="14" y2="4" />
                            </svg>
                        </div>
                        <h3>Daily Diet Tracking</h3>
                        <p>Log meals and watch your calories, protein, carbs and fats against your own daily goals.</p>
                    </div>
                    <div class="feature-card reveal">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                            </svg>
                        </div>
                        <h3>Track Your Progress</h3>
                        <p>Your workout history, weekly totals and streaks are saved, so you can see the work add up.
                        </p>
                    </div>
                    <div class="feature-card reveal">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                <path d="M3 9h18M9 21V9" />
                            </svg>
                        </div>
                        <h3>Nutrition Plans</h3>
                        <p>Pick from nine nutrition plans, from muscle gain to keto, and use one to set your daily
                            targets.</p>
                    </div>
                    <div class="feature-card reveal">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                        </div>
                        <h3>Safe &amp; Consistent</h3>
                        <p>Structured routines built to form healthy habits safely and keep you consistent long-term.
                        </p>
                    </div>
                    <div class="feature-card reveal">
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                        <h3>No Long Commitments</h3>
                        <p>No rigid contracts. Train at your own pace and pick up right where you left off.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA BANNER -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-inner">
                    <h2 class="reveal">Ready to Build a <span>Stronger</span> You?</h2>
                    <p class="reveal">Start your fitness journey today. Pick a workout, log your meals, and let FitPass
                        keep count.</p>
                    <div class="cta-row reveal">
                        <a href="{{ route('workouts') }}" class="cta-btn">Browse Workouts</a>
                        <a href="{{ route('diet') }}" class="cta-btn-outline">Open Diet Tracker</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection