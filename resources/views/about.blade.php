@extends('layouts.app')

@section('title', 'About Us')
@section('page', 'about')

@section('content')
    <main>
        <!-- HERO BANNER -->
        <section class="about-hero" id="about">
            <div class="hero-inner">
                <div class="hero-left">
                    <div class="hero-label">Who We Are</div>
                    <h1 class="hero-title">
                        Built For<br>
                        <em>Real Life.</em>
                    </h1>
                    <p class="hero-desc">
                        FitPass is your all-in-one fitness companion. Follow individual workouts exercise by exercise,
                        log what you eat every day, and watch your progress build without the pressure of rigid
                        commitments.
                    </p>
                </div>

                <div class="hero-right">
                    <div class="stat-card">
                        <div class="stat-number">Every <span>Workout</span></div>
                        <div class="stat-label">Exercise by exercise, set by set</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">Daily <span>Diet Log</span></div>
                        <div class="stat-label">Calories and macros against your goals</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">Your <span>Progress</span></div>
                        <div class="stat-label">History, weekly totals and streaks</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- VALUES -->
        <section class="values-section">
            <div class="container">
                <div class="section-label reveal">What Drives Us</div>
                <h2 class="section-heading reveal">Our Core <span>Values</span></h2>

                <div class="values-grid">
                    <div class="value-card reveal">
                        <div class="value-num">01</div>
                        <span class="value-icon">⚡</span>
                        <div class="value-title">Flexibility First</div>
                        <p class="value-text">No long-term lock-ins. Work out whenever and however fits your schedule.
                            Your life, your rules.</p>
                    </div>
                    <div class="value-card reveal">
                        <div class="value-num">02</div>
                        <span class="value-icon">🎯</span>
                        <div class="value-title">Goal-Driven</div>
                        <p class="value-text">Set your own calorie and macro targets, pick a workout, and track every
                            step toward strength, stamina, or just feeling better.</p>
                    </div>
                    <div class="value-card reveal">
                        <div class="value-num">03</div>
                        <span class="value-icon">🔥</span>
                        <div class="value-title">Zero Pressure</div>
                        <p class="value-text">We believe fitness should feel empowering, not stressful. FitPass meets
                            you exactly where you are today.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- QUOTE BANNER -->
        <section class="quote-banner">
            <p class="quote-text reveal">
                Fitness isn't a <span>destination</span>. It's the way you show up for yourself, every single day.
            </p>
            <p class="quote-author reveal">— FitPass</p>
        </section>

        <!-- FEATURES ROW -->
        <section class="features-section">
            <div class="container">
                <div class="features-row">
                    <div class="features-left">
                        <div class="section-label reveal-left">Why FitPass</div>
                        <h2 class="section-heading reveal-left" style="transition-delay: 0.15s;">Fitness <span>On
                                Your</span> Terms</h2>
                        <p class="features-body reveal-left" style="transition-delay: 0.3s;">
                            We built FitPass to make working out and eating well easier and more flexible than ever.
                            Open any workout to see every exercise, check them off as you train, and log your meals to
                            see how your day adds up. Whether you're into strength training or just getting back into
                            shape, FitPass helps you stay consistent without long-term commitments.
                        </p>
                        <ul class="feature-list reveal-left" style="transition-delay: 0.35s;">
                            <li>Individual workouts with sets, reps and a rest timer</li>
                            <li>Daily calorie and macro tracking against your own goals</li>
                            <li>Nine nutrition plans to set your targets from</li>
                            <li>Workout history, weekly totals and streaks</li>
                            <li>Works for beginners and seasoned athletes alike</li>
                        </ul>
                    </div>

                    <div class="features-right reveal-right">
                        <div class="img-block">
                            <img src="{{ asset('images/pushup.png') }}"
                                alt="A man doing a push-up on a mat in his living room">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection