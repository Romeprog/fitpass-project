@extends('layouts.app')

@section('title', 'Diet Tracker')
@section('page', 'diet')
@section('body_class', 'tracker')

@section('content')
    <main>
        <section class="page-hero">
            <div class="container">
                <p class="eyebrow">Diet Tracker</p>
                <h1 class="page-title">Log every meal. <span>Hit your targets.</span></h1>
                <p class="page-sub">Add what you eat, see your calories and macros update, and compare your day to your
                    own goals.</p>
                <div class="date-nav">
                    <button type="button" id="prevDay" aria-label="Previous day">‹</button>
                    <span class="date-label" id="dateLabel">Today</span>
                    <button type="button" id="nextDay" aria-label="Next day">›</button>
                    <input type="date" id="datePick" aria-label="Pick a date">
                    <button type="button" id="goToday">Today</button>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <!-- SUMMARY -->
                <div class="panel summary">
                    <div>
                        <div class="ring-wrap">
                            <svg viewBox="0 0 120 120" class="ring" aria-hidden="true">
                                <circle class="ring-bg" cx="60" cy="60" r="52" />
                                <circle class="ring-fg" id="ringFg" cx="60" cy="60" r="52" transform="rotate(-90 60 60)" />
                            </svg>
                            <div class="ring-text">
                                <b id="calNow">0</b>
                                <span>of <span id="calGoal">0</span> kcal</span>
                                <div class="ring-note" id="ringNote"></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="macro">
                            <div class="macro-top"><span>Protein</span><span><b id="proNow">0</b> / <span
                                        id="proGoal">0</span> g</span></div>
                            <div class="bar"><i class="p" id="proBar"></i></div>
                        </div>
                        <div class="macro">
                            <div class="macro-top"><span>Carbs</span><span><b id="carbNow">0</b> / <span
                                        id="carbGoal">0</span> g</span></div>
                            <div class="bar"><i class="c" id="carbBar"></i></div>
                        </div>
                        <div class="macro">
                            <div class="macro-top"><span>Fats</span><span><b id="fatNow">0</b> / <span id="fatGoal">0</span>
                                    g</span></div>
                            <div class="bar"><i class="f" id="fatBar"></i></div>
                        </div>
                    </div>
                    <div class="extra">
                        <h4>Training this week</h4>
                        <p class="extra-count"><b id="wkCount">0</b> workouts completed</p>
                        <p class="extra-count"><b id="wkStreak">0</b> day streak</p>
                    </div>
                </div>

                <div class="layout">
                    <!-- MEAL LOG -->
                    <div class="panel">
                        <h3>Meals</h3>
                        <div id="mealLog"></div>
                    </div>

                    <div class="stack">
                        <!-- ADD FOOD -->
                        <div class="panel">
                            <h3>Add Food</h3>
                            <form id="foodForm" class="form-grid" novalidate>
                                <div class="field full">
                                    <label for="quick">Quick add (optional)</label>
                                    <select id="quick"></select>
                                    <button type="button" class="btn-red" id="removeSaved" hidden>Remove from My
                                        Foods</button>
                                </div>
                                <div class="field">
                                    <label for="meal">Meal</label>
                                    <select id="meal">
                                        <option value="breakfast">Breakfast</option>
                                        <option value="lunch">Lunch</option>
                                        <option value="dinner">Dinner</option>
                                        <option value="snack">Snack</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="servings">Servings</label>
                                    <input type="number" id="servings" value="1" min="0.25" step="0.25">
                                </div>
                                <div class="field full">
                                    <label for="fName">Food name</label>
                                    <input type="text" id="fName" maxlength="60" placeholder="e.g. Chicken adobo" required>
                                </div>
                                <div class="field">
                                    <label for="fCal">Calories (kcal)</label>
                                    <input type="number" id="fCal" min="0" step="1" placeholder="0" required>
                                </div>
                                <div class="field">
                                    <label for="fPro">Protein (g)</label>
                                    <input type="number" id="fPro" min="0" step="0.1" placeholder="0">
                                </div>
                                <div class="field">
                                    <label for="fCarb">Carbs (g)</label>
                                    <input type="number" id="fCarb" min="0" step="0.1" placeholder="0">
                                </div>
                                <div class="field">
                                    <label for="fFat">Fats (g)</label>
                                    <input type="number" id="fFat" min="0" step="0.1" placeholder="0">
                                </div>
                                <div class="field full">
                                    <label class="save-row" for="saveMine"><input type="checkbox" id="saveMine"> Save to
                                        my foods for next time</label>
                                </div>
                                <div class="field full"><button type="submit" class="btn-red">Add to Log</button></div>
                            </form>
                            <p class="hint">Quick-add values are approximate. Edit any number before adding.</p>
                        </div>

                        <!-- GOALS -->
                        <div class="panel">
                            <h3>My Daily Goals</h3>
                            <div class="form-grid">
                                <div class="field full">
                                    <label for="planSel">Nutrition plan</label>
                                    <select id="planSel"></select>
                                </div>
                                <div class="field"><label for="gCalIn">Calories</label><input type="number" id="gCalIn"
                                        min="500" step="50"></div>
                                <div class="field"><label for="gProIn">Protein (g)</label><input type="number" id="gProIn"
                                        min="0"></div>
                                <div class="field"><label for="gCarbIn">Carbs (g)</label><input type="number" id="gCarbIn"
                                        min="0"></div>
                                <div class="field"><label for="gFatIn">Fats (g)</label><input type="number" id="gFatIn"
                                        min="0"></div>
                            </div>
                            <div class="btn-row">
                                <button type="button" class="btn-ghost" id="viewPlan">View Sample Day</button>
                                <button type="button" class="btn-red" id="saveGoals" style="flex:1">Save Goals</button>
                            </div>
                            <p class="hint" id="goalCurrent"></p>
                        </div>
                    </div>
                </div>

                <!-- WEEKLY CHART -->
                <div class="panel">
                    <h3>Last 7 Days</h3>
                    <div class="chart" id="chart"></div>
                    <p class="hint">Calories per day. The dashed line is your goal. Click a bar to open that day.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- PLAN MODAL -->
    <div class="modal-overlay" id="planOverlay">
        <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="planTitle">
            <button class="modal-close" id="planClose" aria-label="Close" type="button">✕</button>
            <div class="modal-header plan-header">
                <h3 id="planTitle"></h3>
                <p id="planDesc"></p>
            </div>
            <div class="modal-stats" id="planStats"></div>
            <div class="modal-meals">
                <h5>Sample Day</h5>
                <ul id="planMeals"></ul>
            </div>
            <div class="modal-actions"><button class="btn-red" id="usePlan" type="button">Use this plan for my
                    goals</button></div>
        </div>
    </div>
@endsection