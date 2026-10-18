<!DOCTYPE html>
<html lang="en" data-page="landing">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitPass – Workouts, meals and goals in one place</title>
    <meta name="description"
        content="FitPass gives you guided workouts, a diet tracker with daily calorie and macro goals, and a streak that keeps you consistent.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600;700&family=Barlow:wght@400;500;600;700;900&family=Barlow+Condensed:wght@500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <header>
        <nav class="navbar" id="navbar">
            <div class="nav-container">
                <a href="{{ url('/') }}" class="logo">Fit<span>Pass</span></a>
                <ul class="nav-menu" id="nav-menu">
                    <li><a class="nav-link" href="#features">Features</a></li>
                    <li><a class="nav-link" href="#how">How it works</a></li>
                    <li><a class="btn-signin" href="{{ route('login') }}">Sign In</a></li>
                    <li><a class="btn-logout" href="{{ route('register') }}">Create Account</a></li>
                </ul>
                <button class="hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="wrap">
                <div>
                    <span class="eyebrow"><i></i>Train. Eat. Stay consistent.</span>
                    <h1>Your workouts and meals, in <em>one place.</em></h1>
                    <p class="lead">Follow guided workouts, track calories and macros against your daily goals, and
                        keep a streak going. FitPass keeps it all together.</p>
                    <div class="actions">
                        <a href="{{ route('register') }}" class="btn">Get Started Free<svg class="ico"
                                viewBox="0 0 24 24">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg></a>
                        <a href="{{ route('login') }}" class="btn ghost">Sign In</a>
                    </div>
                    <ul class="perks">
                        <li><svg class="ico" viewBox="0 0 24 24">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                            </svg>Guided workouts</li>
                        <li><svg class="ico" viewBox="0 0 24 24">
                                <path d="M21.21 15.89A10 10 0 1 1 8 2.83" />
                                <path d="M22 12A10 10 0 0 0 12 2v10z" />
                            </svg>Calorie and macro tracking</li>
                        <li><svg class="ico" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <circle cx="12" cy="12" r="6" />
                                <circle cx="12" cy="12" r="2" />
                            </svg>Daily streaks</li>
                    </ul>
                </div>

                <div class="visual">
                    <div class="float a" aria-hidden="true">
                        <span class="dot"><svg class="ico" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12" />
                            </svg></span>
                        <div><b>Workout Complete</b><span>Arm workout · 25 min</span></div>
                    </div>

                    <div class="today" aria-label="Example of the FitPass daily summary">
                        <div class="today-head">
                            <h3>Your day at a glance</h3>
                            <span class="tag">Example</span>
                        </div>
                        <div class="ring-row">
                            <div class="ring">
                                <svg viewBox="0 0 120 120">
                                    <circle class="track" cx="60" cy="60" r="52" />
                                    <circle class="fill" cx="60" cy="60" r="52" />
                                </svg>
                                <b>1,640<small>of 2,200 kcal</small></b>
                            </div>
                            <div class="ring-text">
                                <strong>560 kcal left</strong>
                                <span>You’re on track for today’s goal.</span>
                            </div>
                        </div>
                        <div class="macro">
                            <div>Protein <span>112 / 150 g</span></div>
                            <div class="bar"><i style="--w: 75%; --c: var(--green)"></i></div>
                        </div>
                        <div class="today-foot">
                            <div><b>3</b><span>workouts this week</span></div>
                            <div><b>5 days</b><span>current streak</span></div>
                        </div>
                    </div>

                    <div class="float b" aria-hidden="true">
                        <span class="dot"><svg class="ico" viewBox="0 0 24 24">
                                <path d="M12 2c1 4 5 5 5 10a5 5 0 0 1-10 0c0-2 1-3 2-4 0 2 1 3 2 3 0-3-1-5 1-9z" />
                            </svg></span>
                        <div><b>5 Day Streak</b><span>Keep it going</span></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="block" id="features">
            <div class="wrap">
                <div class="head">
                    <span class="kicker">Features</span>
                    <h2>Everything you need to stay on track</h2>
                    <p>Train, eat and check your progress without switching between apps.</p>
                </div>

                <div class="features">
                    <article class="panel tall">
                        <div class="tile"><svg class="ico" viewBox="0 0 24 24">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                            </svg></div>
                        <h3>Guided workouts</h3>
                        <p>Pick a workout, see every exercise with its reps and sets, and follow along from the first
                            move to the last.</p>
                        <div class="chips">
                            <span class="chip red">Arm workout</span>
                            <span class="chip">Intermediate</span>
                            <span class="chip">25 min</span>
                        </div>
                        <ul class="exercises">
                            <li class="done"><span class="ok"><svg viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg></span>Diamond push up <em>10 reps, 3 sets</em></li>
                            <li class="done"><span class="ok"><svg viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg></span>Push up <em>10 reps, 3 sets</em></li>
                            <li><span class="ok"><svg viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg></span>Burpees <em>16 reps, 2 sets</em></li>
                            <li><span class="ok"><svg viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg></span>Tricep dips <em>12 reps, 3 sets</em></li>
                            <li><span class="ok"><svg viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg></span>Plank shoulder taps <em>12 reps, 3 sets</em></li>
                        </ul>
                    </article>

                    <article class="panel">
                        <div class="tile"><svg class="ico" viewBox="0 0 24 24">
                                <path d="M21.21 15.89A10 10 0 1 1 8 2.83" />
                                <path d="M22 12A10 10 0 0 0 12 2v10z" />
                            </svg></div>
                        <h3>Diet tracker</h3>
                        <p>Log what you eat and watch calories, protein, carbs and fats fill up against your goals.</p>
                        <div class="macro">
                            <div>Protein <span>112 / 150 g</span></div>
                            <div class="bar"><i style="--w: 75%; --c: var(--green)"></i></div>
                        </div>
                        <div class="macro">
                            <div>Carbs <span>180 / 265 g</span></div>
                            <div class="bar"><i style="--w: 68%; --c: var(--amber)"></i></div>
                        </div>
                        <div class="macro">
                            <div>Fats <span>41 / 70 g</span></div>
                            <div class="bar"><i style="--w: 58%; --c: var(--blue)"></i></div>
                        </div>
                    </article>

                    <article class="panel">
                        <div class="tile"><svg class="ico" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <circle cx="12" cy="12" r="6" />
                                <circle cx="12" cy="12" r="2" />
                            </svg></div>
                        <h3>Goals and streaks</h3>
                        <p>Choose a nutrition plan or set your own daily targets, then keep the streak alive.</p>
                        <div class="week" aria-label="Example week with five training days">
                            <span class="on">M</span><span class="on">T</span><span class="on">W</span><span
                                class="on">T</span><span class="on">F</span><span>S</span><span>S</span>
                        </div>
                    </article>
                </div>

                <div class="strip">
                    <div>
                        <svg class="ico" viewBox="0 0 24 24">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                        </svg>
                        <b>Follow along</b>
                        <span>Every exercise listed with reps and sets.</span>
                    </div>
                    <div>
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M21.21 15.89A10 10 0 1 1 8 2.83" />
                            <path d="M22 12A10 10 0 0 0 12 2v10z" />
                        </svg>
                        <b>Know your numbers</b>
                        <span>Calories and macros against daily goals.</span>
                    </div>
                    <div>
                        <svg class="ico" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <circle cx="12" cy="12" r="6" />
                            <circle cx="12" cy="12" r="2" />
                        </svg>
                        <b>Set your targets</b>
                        <span>Pick a plan or enter your own goals.</span>
                    </div>
                    <div>
                        <svg class="ico" viewBox="0 0 24 24">
                            <path d="M12 2c1 4 5 5 5 10a5 5 0 0 1-10 0c0-2 1-3 2-4 0 2 1 3 2 3 0-3-1-5 1-9z" />
                        </svg>
                        <b>Stay consistent</b>
                        <span>Daily streaks that reward showing up.</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="block how" id="how" style="padding-top: 40px">
            <div class="wrap">
                <div class="head">
                    <span class="kicker">How it works</span>
                    <h2>Start in three steps</h2>
                </div>
                <ol class="steps">
                    <li>
                        <h3>Create your account</h3>
                        <p>Sign up with a username, your email and a password. It takes under a minute.</p>
                    </li>
                    <li>
                        <h3>Set your goals</h3>
                        <p>Pick a nutrition plan and your daily calorie and macro targets.</p>
                    </li>
                    <li>
                        <h3>Train and log</h3>
                        <p>Complete workouts, log your meals and keep your streak going.</p>
                    </li>
                </ol>
            </div>
        </section>

        <section class="cta">
            <div class="wrap">
                <div class="box">
                    <div>
                        <h2>Ready for today’s session?</h2>
                        <p>Create your account and log your first workout.</p>
                    </div>
                    <a href="{{ route('register') }}" class="btn">Create Account<svg class="ico" viewBox="0 0 24 24">
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <polyline points="12 5 19 12 12 19" />
                        </svg></a>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3>Fit<span>Pass</span></h3>
                    <p>Build A Stronger, Healthier You</p>
                </div>
                <div class="footer-links">
                    <div class="footer-column">
                        <h4>Quick Links</h4>
                        <ul>
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><a href="{{ route('about') }}">About Us</a></li>
                            <li><a href="{{ route('workouts') }}">Workouts</a></li>
                            <li><a href="{{ route('diet') }}">Diet Tracker</a></li>
                        </ul>
                    </div>
                    <div class="footer-column workout-categories">
                        <h4>Workout Categories</h4>
                        <ul>
                            <li><a href="{{ route('workouts') }}#arm">Arm</a></li>
                            <li><a href="{{ route('workouts') }}#chest">Chest</a></li>
                            <li><a href="{{ route('workouts') }}#leg">Leg</a></li>
                            <li><a href="{{ route('workouts') }}#shoulder">Shoulder</a></li>
                            <li><a href="{{ route('workouts') }}#abs">Abs</a></li>
                            <li><a href="{{ route('workouts') }}#back">Back</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h4>Follow Us</h4>
                        <ul>
                            <li><a href="#">Facebook</a></li>
                            <li><a href="#">Instagram</a></li>
                            <li><a href="#">YouTube</a></li>
                            <li><a href="#">Tiktok</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© {{ date('Y') }} FitPass. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>