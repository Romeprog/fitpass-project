@extends('layouts.app')

@section('title', 'Workouts')
@section('page', 'workouts')
@section('body_class', 'tracker')

@section('content')
    <main>
        <section class="page-hero">
            <div class="container">
                <p class="eyebrow">Individual Workouts</p>
                <h1 class="page-title">Pick a workout. <span>Check off every set.</span></h1>
                <p class="page-sub">Open any workout to see each exercise with its sets and reps. Tick them off as you
                    train, then save the session to your history.</p>
                <div class="stats-row">
                    <div class="stat"><b id="sWeek">0</b><span>This week</span></div>
                    <div class="stat"><b id="sTotal">0</b><span>Total workouts</span></div>
                    <div class="stat"><b id="sStreak">0</b><span>Day streak</span></div>
                    <div class="stat"><b id="sToday">0</b><span>Done today</span></div>
                </div>
            </div>
        </section>

        <section class="section" id="programs">
            <div class="container">
                <div class="filters" id="filters" role="group" aria-label="Filter workouts"></div>
                <div class="cards-grid" id="workoutGrid"></div>
            </div>
        </section>

        <section class="section alt" id="history">
            <div class="container">
                <div class="section-head">
                    <h2>Workout History</h2>
                    <button class="btn-ghost" id="clearHistory" type="button">Clear history</button>
                </div>
                <ul class="history-list" id="historyList"></ul>
                <p class="empty" id="historyEmpty">No workouts saved yet. Start one above and it will show up here.</p>
            </div>
        </section>
    </main>

    <!-- WORKOUT SESSION MODAL -->
    <div class="modal-overlay" id="sessionOverlay">
        <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="sessionTitle">
            <button class="modal-close" id="sessionClose" aria-label="Close" type="button">✕</button>
            <div class="modal-header session-header">
                <h3 id="sessionTitle"></h3>
                <p id="sessionDesc"></p>
            </div>
            <div class="progress-wrap">
                <div class="progress-label"><span>Progress</span><span id="progressText">0 / 0 exercises</span></div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
            </div>
            <ul class="exercise-list" id="exerciseList"></ul>
            <div class="modal-actions">
                <button class="btn-green" id="finishBtn" type="button">Finish &amp; Save Workout</button>
            </div>
        </div>
    </div>

    <!-- EXERCISE DEMO MODAL -->
    <div class="modal-overlay demo-overlay" id="demoOverlay">
        <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="demoTitle">
            <button class="modal-close" id="demoClose" aria-label="Close demo" type="button">✕</button>
            <div class="modal-header session-header">
                <h3 id="demoTitle"></h3>
                <p id="demoDetail"></p>
            </div>
            <div class="demo-media" id="demoMedia"></div>
            <div class="demo-actions">
                <a class="demo-link" id="demoSearch" href="#" target="_blank" rel="noopener">▶ Watch a video on
                    YouTube</a>
            </div>
        </div>
    </div>
@endsection