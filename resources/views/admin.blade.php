@extends('layouts.app')

@section('title', 'Admin')
@section('page', 'admin')

@section('nav')
    <header>
        <nav class="navbar" id="navbar">
            <div class="nav-container">
                <a href="{{ route('admin') }}" class="logo">Fit<span>Pass</span></a>
                <ul class="nav-menu" id="nav-menu">
                    <li><a class="nav-link active" data-tab="overview">Overview</a></li>
                    <li><a class="nav-link" data-tab="users">Users</a></li>
                    <li><a class="nav-link" data-tab="workouts">Workouts</a></li>
                    <li><a class="nav-link" data-tab="foods">Foods</a></li>
                    <li><a class="nav-link" data-tab="goals">Goals</a></li>
                    <li><a class="nav-link" data-tab="reports">Reports</a></li>
                    <li>
                        <form action="{{ route('admin.logout') }}" method="POST">
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
@endsection

@section('content')
    <main>
        <section class="page-hero">
            <div class="container">
                <p class="eyebrow">Admin Panel</p>
                <h1 class="page-title">Manage FitPass. <span>Keep it running.</span></h1>
                <p class="page-sub">Add and edit workouts and foods, manage member accounts, and see how people are
                    using
                    FitPass.</p>
                <div class="stats-row">
                    <div class="stat"><b id="sUsers">0</b><span>Total members</span></div>
                    <div class="stat"><b id="sActive">0</b><span>Active members</span></div>
                    <div class="stat"><b id="sWorkouts">0</b><span>Workouts offered</span></div>
                    <div class="stat"><b id="sFoods">0</b><span>Foods in database</span></div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">

                <!-- OVERVIEW -->
                <div class="tab active" id="tab-overview">
                    <div class="section-head">
                        <h2>Overview</h2>
                    </div>
                    <div class="grid-2">
                        <div class="panel">
                            <h3>Most popular workouts</h3>
                            <p class="note">Completed sessions across all members.</p>
                            <div id="ovPopular"></div>
                        </div>
                        <div class="panel">
                            <h3>Recent activity</h3>
                            <p class="note">Latest member sign-ups and sessions.</p>
                            <ul class="activity" id="ovActivity"></ul>
                        </div>
                    </div>
                </div>

                <!-- USERS -->
                <div class="tab" id="tab-users">
                    <div class="section-head">
                        <h2>Members</h2>
                        <div class="head-actions">
                            <input class="search" id="userSearch" type="search" placeholder="Search name or email">
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Joined</th>
                                    <th>Workouts</th>
                                    <th>Last active</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="userRows"></tbody>
                        </table>
                    </div>
                </div>

                <!-- WORKOUTS -->
                <div class="tab" id="tab-workouts">
                    <div class="section-head">
                        <h2>Workouts</h2>
                        <div class="head-actions">
                            <button class="cta-btn" id="addWorkoutBtn" type="button">+ Add Workout</button>
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Workout</th>
                                    <th>Category</th>
                                    <th>Level</th>
                                    <th>Exercises</th>
                                    <th>Time</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="workoutRows"></tbody>
                        </table>
                    </div>
                </div>

                <!-- FOODS -->
                <div class="tab" id="tab-foods">
                    <div class="section-head">
                        <h2>Food Database</h2>
                        <div class="head-actions">
                            <input class="search" id="foodSearch" type="search" placeholder="Search foods">
                            <button class="cta-btn" id="addFoodBtn" type="button">+ Add Food</button>
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Food</th>
                                    <th>Calories</th>
                                    <th>Protein</th>
                                    <th>Carbs</th>
                                    <th>Fats</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="foodRows"></tbody>
                        </table>
                    </div>
                </div>

                <!-- GOALS -->
                <div class="tab" id="tab-goals">
                    <div class="section-head">
                        <h2>Nutrition Plans</h2>
                        <div class="head-actions">
                            <button class="cta-btn" id="addGoalPlanBtn" type="button">+ Add Plan</button>
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Calories</th>
                                    <th>Protein</th>
                                    <th>Carbs</th>
                                    <th>Fats</th>
                                    <th>Members</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="goalPlanRows"></tbody>
                        </table>
                    </div>

                    <div class="section-head" style="margin-top:40px">
                        <h2>Member Daily Goals</h2>
                        <div class="head-actions">
                            <input class="search" id="goalSearch" type="search" placeholder="Search name or email">
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Plan</th>
                                    <th>Calories</th>
                                    <th>Protein</th>
                                    <th>Carbs</th>
                                    <th>Fats</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="memberGoalRows"></tbody>
                        </table>
                    </div>
                </div>

                <!-- REPORTS -->
                <div class="tab" id="tab-reports">
                    <div class="section-head">
                        <h2>Reports</h2>
                        <div class="head-actions">
                            <button class="btn-ghost" id="exportBtn" type="button">Export Members (CSV)</button>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="panel">
                            <h3>Sessions by workout</h3>
                            <p class="note">Which workouts members finish the most.</p>
                            <div id="rpWorkouts"></div>
                        </div>
                        <div class="panel">
                            <h3>New members by month</h3>
                            <p class="note">Sign-ups over the last 6 months.</p>
                            <div id="rpSignups"></div>
                        </div>
                        <div class="panel">
                            <h3>Account status</h3>
                            <p class="note">Active vs suspended members.</p>
                            <div id="rpStatus"></div>
                        </div>
                        <div class="panel">
                            <h3>Workouts by level</h3>
                            <p class="note">How the workout library is spread.</p>
                            <div id="rpLevels"></div>
                        </div>
                        <div class="panel">
                            <h3>Members by nutrition plan</h3>
                            <p class="note">Which nutrition plans members follow.</p>
                            <div id="rpGoals"></div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>

    <!-- MODAL -->
    <div class="modal-overlay" id="modal" role="dialog" aria-modal="true" aria-labelledby="mTitle">
        <div class="modal-box">
            <button class="modal-close" id="mClose" type="button" aria-label="Close">✕</button>
            <div class="modal-header">
                <h3 id="mTitle"></h3>
                <p id="mSub"></p>
            </div>
            <div class="modal-form" id="mBody"></div>
        </div>
    </div>
@endsection