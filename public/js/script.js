const FP = (() => {
    const SK = 'fitpass_session', UK = 'fitpass_users';
    const read = (store, k, fb) => { try { const v = JSON.parse(store.getItem(k)); return v == null ? fb : v; } catch (e) { return fb; } };
    const write = (store, k, v) => { try { store.setItem(k, JSON.stringify(v)); return true; } catch (e) { return false; } };
    const pad = n => String(n).padStart(2, '0');
    const defaults = () => ({ goals: { plan: 'balanced', cal: 2200, p: 150, c: 265, f: 70 }, foods: {}, water: {}, workouts: [], myFoods: [] });
    return {
        session() { return read(sessionStorage, SK, null) || read(localStorage, SK, null); },
        login(username, remember) {
            this.logout();
            write(remember ? localStorage : sessionStorage, SK, { username });
        },
        logout() { try { sessionStorage.removeItem(SK); localStorage.removeItem(SK); } catch (e) { } },
        requireLogin() {
            if (!this.session()) { location.replace('/login'); return false; }
            return true;
        },
        users() { return read(localStorage, UK, []); },
        saveUsers(list) { return write(localStorage, UK, list); },
        data() {
            const s = this.session();
            const d = read(localStorage, 'fitpass_data_' + (s ? s.username.toLowerCase() : 'guest'), {});
            return Object.assign(defaults(), d);
        },
        save(d) {
            const s = this.session();
            return write(localStorage, 'fitpass_data_' + (s ? s.username.toLowerCase() : 'guest'), d);
        },
        key(date) { return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()); },
        today() { return this.key(new Date()); },
        parse(k) { const [y, m, d] = k.split('-').map(Number); return new Date(y, m - 1, d); },
        addDays(k, n) { const d = this.parse(k); d.setDate(d.getDate() + n); return this.key(d); },
        esc(s) { return String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c])); },
        totals(entries) {
            return entries.reduce((t, e) => ({ cal: t.cal + e.cal, p: t.p + e.p, c: t.c + e.c, f: t.f + e.f }), { cal: 0, p: 0, c: 0, f: 0 });
        },
        weekCount(workouts) {
            const now = new Date();
            const start = new Date(now.getFullYear(), now.getMonth(), now.getDate() - ((now.getDay() + 6) % 7));
            const sk = this.key(start);
            return workouts.filter(w => w.date >= sk).length;
        },
        streak(workouts) {
            const days = new Set(workouts.map(w => w.date));
            const cur = new Date();
            if (!days.has(this.key(cur))) cur.setDate(cur.getDate() - 1);
            let n = 0;
            while (days.has(this.key(cur))) { n++; cur.setDate(cur.getDate() - 1); }
            return n;
        }
    };
})();



/* ── Shared: navbar, scroll effects, logout ── */
function initNav() {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');
    const navbar = document.getElementById('navbar');

    hamburger.addEventListener('click', () => {
        const open = navMenu.classList.toggle('active');
        hamburger.setAttribute('aria-expanded', open);
    });
    navMenu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
        navMenu.classList.remove('active');
        hamburger.setAttribute('aria-expanded', 'false');
    }));

    const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 50);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    const logoutBtn = document.getElementById('logoutBtn');   // the landing page has no logout button
    if (logoutBtn) logoutBtn.addEventListener('click', () => FP.logout());

    const els = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver(entries => {
            let n = 0;
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.style.transitionDelay = (n++ * 0.08) + 's';
                    e.target.classList.add('visible');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.12 });
        els.forEach(el => io.observe(el));
    } else {
        els.forEach(el => el.classList.add('visible'));
    }
}

/* ── Page: home ── */
/* home.html */
function initHome() {
    let toastTimer;
    function toast(msg, isError) {
        let t = document.getElementById('toast');
        if (!t) { t = document.createElement('div'); t.id = 'toast'; t.className = 'toast'; t.setAttribute('role', 'status'); document.body.appendChild(t); }
        t.textContent = msg;
        t.classList.toggle('error', !!isError);
        t.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => t.classList.remove('show'), 2600);
    }


    (function () {
        const d = FP.data();
        const today = FP.today();
        const t = FP.totals(d.foods[today] || []);
        const set = (id, v) => { document.getElementById(id).textContent = v; };

        set('gCal', Math.round(t.cal));
        set('gCalGoal', d.goals.cal);
        set('gPro', Math.round(t.p));
        set('gProGoal', d.goals.p);
        const meals = (d.foods[today] || []).length;
        set('gMeals', meals);
        set('gWeek', FP.weekCount(d.workouts));

        const left = d.goals.cal - Math.round(t.cal);
        if (t.cal > 0) set('gCalNote', left >= 0 ? left + ' kcal left today' : Math.abs(left) + ' kcal over goal');
        const st = FP.streak(d.workouts);
        if (st > 0) set('gStreak', st + '-day streak. Keep it going!');
        if (meals > 0) set('gMealsNote', meals === 1 ? 'Nice start. Keep logging!' : 'Great job tracking today');
    })();


}

/* ── Page: workouts ── */
/* workouts.html */
function initWorkouts() {
    let toastTimer;
    function toast(msg, isError) {
        let t = document.getElementById('toast');
        if (!t) { t = document.createElement('div'); t.id = 'toast'; t.className = 'toast'; t.setAttribute('role', 'status'); document.body.appendChild(t); }
        t.textContent = msg;
        t.classList.toggle('error', !!isError);
        t.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => t.classList.remove('show'), 2600);
    }


    const workoutPlans = {
        arm: {
            title: 'Arm Workout', cat: 'strength', level: 'Intermediate', minutes: 25,
            desc: 'Build strength and definition in your biceps, triceps, and forearms.',
            exercises: [
                { name: 'Diamond Push Up', detail: '10 Reps • 3 Sets' },
                { name: 'Push Up', detail: '10 Reps • 3 Sets' },
                { name: 'Push Up & Rotation', detail: '12 Reps • 2 Sets' },
                { name: 'Burpees', detail: '16 Reps • 2 Sets' },
                { name: 'Tricep Dips', detail: '12 Reps • 3 Sets' },
                { name: 'Triceps Push Up', detail: '14 Reps • 2 Sets' },
                { name: 'Plank Shoulder Taps', detail: '12 Reps • 3 Sets' }
            ]
        },
        chest: {
            title: 'Chest Workout', cat: 'strength', level: 'Intermediate', minutes: 25,
            desc: 'Strengthen and sculpt your pectoral muscles with targeted push variations.',
            exercises: [
                { name: 'Push Up', detail: '10 Reps • 3 Sets' },
                { name: 'Wide Push Up', detail: '12 Reps • 2 Sets' },
                { name: 'Diamond Push Up', detail: '12 Reps • 3 Sets' },
                { name: 'Decline Push Up', detail: '10 Reps • 2 Sets' },
                { name: 'Incline Push Up', detail: '10 Reps • 2 Sets' },
                { name: 'Archer Push Up', detail: '10 Reps • 2 Sets' },
                { name: 'Bench Press', detail: '10 Reps • 2 Sets' }
            ]
        },
        leg: {
            title: 'Leg Workout', cat: 'strength', level: 'Intermediate', minutes: 30,
            desc: 'Power up your quads, hamstrings, and glutes with lower body exercises.',
            exercises: [
                { name: 'Squat', detail: '12 Reps • 2 Sets' },
                { name: 'Reverse Lunges', detail: '12 Reps • 3 Sets' },
                { name: 'Step Up', detail: '15 Reps • 3 Sets' },
                { name: 'Curtsy Lunges', detail: '14 Reps • 2 Sets' },
                { name: 'Lunges', detail: '10 Reps • 2 Sets' },
                { name: 'Bulgarian Split Squat', detail: '10 Reps • 3 Sets' },
                { name: 'Jump Squats', detail: '14 Reps • 2 Sets' }
            ]
        },
        shoulder: {
            title: 'Shoulder Workout', cat: 'strength', level: 'Intermediate', minutes: 25,
            desc: 'Develop strong, defined shoulders with focused deltoid exercises.',
            exercises: [
                { name: 'Lateral Raise', detail: '10 Reps • 3 Sets' },
                { name: 'Pike Push Up', detail: '12 Reps • 3 Sets' },
                { name: 'Decline Push Up', detail: '10 Reps • 3 Sets' },
                { name: 'Incline Push Up', detail: '10 Reps • 3 Sets' },
                { name: 'Plank To Downward Dog', detail: '12 Reps • 3 Sets' },
                { name: 'Shoulder Taps', detail: '20 Reps • 3 Sets' },
                { name: 'Tricep Dips', detail: '12 Reps • 3 Sets' }
            ]
        },
        abs: {
            title: 'Abs Workout', cat: 'core', level: 'Beginner', minutes: 20,
            desc: 'Tone and tighten your core with effective abdominal exercises.',
            exercises: [
                { name: 'Sit Up', detail: '13 Reps • 3 Sets' },
                { name: 'Leg Raise', detail: '12 Reps • 3 Sets' },
                { name: 'Plank', detail: '2:00 minutes • 2 Sets' },
                { name: 'Russian Twist', detail: '10 Reps • 3 Sets' },
                { name: 'Bicycle Crunches', detail: '14 Reps • 2 Sets' },
                { name: 'Heel Touch', detail: '20 Reps • 3 Sets' },
                { name: 'V-Up', detail: '16 Reps • 2 Sets' }
            ]
        },
        back: {
            title: 'Back Workout', cat: 'strength', level: 'Advanced', minutes: 30,
            desc: 'Build a wider, stronger back with pull-focused and posterior chain movements.',
            exercises: [
                { name: 'Pull Ups', detail: '12 Reps • 3 Sets' },
                { name: 'Australian Pull-Ups', detail: '14 Reps • 2 Sets' },
                { name: 'Reverse Snow Angels', detail: '15 Reps • 3 Sets' },
                { name: 'Back Widow', detail: '12 Reps • 3 Sets' },
                { name: 'Scapula Push Up', detail: '10 Reps • 2 Sets' },
                { name: 'Superman Hold', detail: '50 seconds • 2 Sets' },
                { name: 'Resistance Band Deadlift', detail: '10 Reps • 3 Sets' }
            ]
        },
        cardio: {
            title: 'Cardio Workout', cat: 'cardio', level: 'Beginner', minutes: 25,
            desc: 'Boost your heart rate and burn calories with energetic cardio exercises.',
            exercises: [
                { name: 'Jumping Jacks', detail: '45 seconds • 3 Sets' },
                { name: 'High Knees', detail: '40 seconds • 3 Sets' },
                { name: 'Jump Rope', detail: '60 seconds • 3 Sets' },
                { name: 'Butt Kicks', detail: '40 seconds • 3 Sets' },
                { name: 'Mountain Climbers', detail: '30 seconds • 3 Sets' },
                { name: 'Box Step-Ups', detail: '15 Reps • 3 Sets' },
                { name: 'Running in Place', detail: '60 seconds • 2 Sets' }
            ]
        },
        fullbody: {
            title: 'Full Body Workout', cat: 'full', level: 'Intermediate', minutes: 35,
            desc: 'Hit every muscle group with a complete full-body training session.',
            exercises: [
                { name: 'Burpees', detail: '12 Reps • 3 Sets' },
                { name: 'Squat to Press', detail: '12 Reps • 3 Sets' },
                { name: 'Push Up to Row', detail: '10 Reps • 3 Sets' },
                { name: 'Reverse Lunge with Twist', detail: '12 Reps • 2 Sets' },
                { name: 'Plank Up-Down', detail: '10 Reps • 3 Sets' },
                { name: 'Glute Bridge', detail: '15 Reps • 3 Sets' },
                { name: 'Dead Bug', detail: '10 Reps • 2 Sets' }
            ]
        },
        hiit: {
            title: 'HIIT Workout', cat: 'cardio', level: 'Advanced', minutes: 20,
            desc: 'Max-intensity intervals to torch fat and skyrocket your metabolism.',
            exercises: [
                { name: 'Burpees', detail: '20s on • 10s rest • 4 Sets' },
                { name: 'Jump Squats', detail: '20s on • 10s rest • 4 Sets' },
                { name: 'Explosive Push Ups', detail: '20s on • 10s rest • 3 Sets' },
                { name: 'Sprint in Place', detail: '20s on • 10s rest • 4 Sets' },
                { name: 'Plank to Push Up', detail: '20s on • 10s rest • 3 Sets' },
                { name: 'Lateral Bounds', detail: '20s on • 10s rest • 3 Sets' },
                { name: 'Tuck Jumps', detail: '20s on • 10s rest • 4 Sets' }
            ]
        }
    };

    const categories = [
        { id: 'all', label: 'All' }, { id: 'strength', label: 'Strength' }, { id: 'core', label: 'Core' },
        { id: 'cardio', label: 'Cardio & HIIT' }, { id: 'full', label: 'Full Body' }
    ];

    let data = FP.data();
    let activeCat = 'all';
    let current = null;      // { id, checks: [] }

    const $ = id => document.getElementById(id);

    /* ── stats + cards + history ── */
    function renderStats() {
        const today = FP.today();
        $('sWeek').textContent = FP.weekCount(data.workouts);
        $('sTotal').textContent = data.workouts.length;
        $('sStreak').textContent = FP.streak(data.workouts);
        $('sToday').textContent = data.workouts.filter(w => w.date === today).length;
    }

    function renderFilters() {
        $('filters').innerHTML = categories.map(c =>
            `<button type="button" class="chip${c.id === activeCat ? ' active' : ''}" data-cat="${c.id}" aria-pressed="${c.id === activeCat}">${c.label}</button>`
        ).join('');
    }

    function renderCards() {
        const today = FP.today();
        const doneToday = new Set(data.workouts.filter(w => w.date === today).map(w => w.plan));
        $('workoutGrid').innerHTML = Object.entries(workoutPlans)
            .filter(([, p]) => activeCat === 'all' || p.cat === activeCat)
            .map(([id, p]) => `
                <article class="w-card" id="card-${id}">
                    <div class="w-head">
                        <h3>${p.title}</h3>
                        <span class="badge">${doneToday.has(id) ? '✓ Done today' : p.level}</span>
                    </div>
                    <div class="w-body">
                        <p class="w-desc">${p.desc}</p>
                        <div class="w-meta"><span>🏋️ ${p.exercises.length} exercises</span><span>⏱ ~${p.minutes} min</span></div>
                        <button class="btn-green" type="button" data-start="${id}">Start Workout</button>
                    </div>
                </article>`).join('');
    }

    function renderHistory() {
        const list = [...data.workouts].sort((a, b) => b.ts - a.ts).slice(0, 30);
        $('historyEmpty').style.display = list.length ? 'none' : 'block';
        $('clearHistory').style.display = list.length ? '' : 'none';
        $('historyList').innerHTML = list.map(w => {
            const full = w.done === w.total;
            const when = FP.parse(w.date).toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' });
            return `<li class="history-item${full ? '' : ' partial'}">
                <div class="history-main"><b>${FP.esc(w.title)}</b><span>${when} · ${full ? 'Completed' : 'Partially Completed'}</span></div>
                <div class="history-score">${w.done}/${w.total}</div>
                ${full || !workoutPlans[w.plan] ? '' : `<button class="btn-continue" type="button" data-continue="${w.ts}">Continue</button>`}
                <button class="icon-btn" type="button" data-del="${w.ts}" aria-label="Delete this entry">🗑</button>
            </li>`;
        }).join('');
    }

    function renderAll() { renderStats(); renderCards(); renderHistory(); }

    /* ── Modal ── */
    function renderSession() {
        const plan = workoutPlans[current.id];
        const done = current.checks.filter(Boolean).length;
        const total = plan.exercises.length;
        $('exerciseList').innerHTML = plan.exercises.map((e, i) => `
            <li class="ex-item">
                <button type="button" class="ex-row${current.checks[i] ? ' done' : ''}" data-ex="${i}" aria-pressed="${!!current.checks[i]}">
                    <span class="check">${current.checks[i] ? '✓' : ''}</span>
                    <span class="ex-name">${e.name}</span><span class="ex-detail">${e.detail}</span>
                </button>
                <button type="button" class="ex-help" data-demo="${i}" aria-label="How to do ${e.name}" title="How to do this exercise">🎬</button>
            </li>`).join('');
        $('progressText').textContent = done + ' / ' + total + ' exercises';
        $('progressFill').style.width = (done / total * 100) + '%';
    }

    function openWorkout(id, entry) {
        const plan = workoutPlans[id];
        if (!plan) return;
        current = { id, checks: plan.exercises.map(() => false), resumeTs: null };
        if (entry) {
            current.resumeTs = entry.ts;
            // restore saved progress (older entries only stored a count, so tick the first N)
            current.checks = Array.isArray(entry.checks) && entry.checks.length === plan.exercises.length
                ? entry.checks.map(Boolean)
                : plan.exercises.map((_, i) => i < entry.done);
        }
        $('sessionTitle').textContent = plan.title;
        $('sessionDesc').textContent = plan.desc;
        renderSession();
        $('sessionOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
        $('sessionClose').focus();
    }

    function closeWorkout() {
        closeDemo();
        $('sessionOverlay').classList.remove('active');
        document.body.style.overflow = '';
        current = null;
        if (location.hash) history.replaceState(null, '', location.pathname + location.search);
    }

    /* ── Exercise demo ──
       Put a short clip for each exercise in  images/exercises/  named after the exercise,
       lowercase with hyphens, e.g.  images/exercises/diamond-push-up.mp4
       MP4 is tried first, then GIF. To use a different file, add
       demo: '/images/exercises/my-file.mp4'  (.mp4, .webm or .gif) to that exercise. */
    let demoReturn = null;
    let demoToken = 0;

    function demoSlug(name) {
        return name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }

    function showDemoEmpty(media, tried) {
        media.textContent = '';
        const box = document.createElement('div');
        box.className = 'demo-empty';
        const strong = document.createElement('strong');
        strong.textContent = 'Demo coming soon';
        box.appendChild(strong);
        box.appendChild(document.createTextNode('No demo for this exercise yet. Tap the button below to watch a video instead.'));
        const small = document.createElement('small');
        small.style.cssText = 'display:block;margin-top:10px;opacity:.6;word-break:break-all';
        small.textContent = 'Looked for: ' + tried.join(' or ');
        box.appendChild(small);
        media.appendChild(box);
    }

    function loadDemo(media, e, token) {
        const slug = demoSlug(e.name);
        const urls = e.demo ? [e.demo] : ['/images/exercises/' + slug + '.mp4', '/images/exercises/' + slug + '.gif'];
        const tried = [];
        const next = () => {
            if (token !== demoToken) return;
            if (!urls.length) { console.warn('Demo not loaded:', tried.join(', ')); showDemoEmpty(media, tried); return; }
            const url = urls.shift();
            tried.push('/' + url);
            const isVideo = /\.(mp4|webm)(\?.*)?$/i.test(url);
            let el;
            if (isVideo) {
                el = document.createElement('video');
                el.muted = true; el.loop = true; el.autoplay = true; el.playsInline = true;
                el.setAttribute('playsinline', ''); el.setAttribute('aria-label', 'How to do ' + e.name);
                el.addEventListener('loadeddata', () => {
                    if (token !== demoToken) return;
                    media.textContent = '';
                    media.appendChild(el);
                    const p = el.play(); if (p && p.catch) p.catch(() => { });
                });
            } else {
                el = new Image();
                el.alt = 'How to do ' + e.name;
                el.addEventListener('load', () => {
                    if (token !== demoToken) return;
                    media.textContent = '';
                    media.appendChild(el);
                });
            }
            el.addEventListener('error', next);
            el.src = url;
        };
        next();
    }

    function openDemo(i, trigger) {
        if (!current) return;
        const e = workoutPlans[current.id].exercises[i];
        $('demoTitle').textContent = e.name;
        $('demoDetail').textContent = e.detail;
        $('demoSearch').href = 'https://www.youtube.com/results?search_query=' + encodeURIComponent(e.name + ' exercise proper form');

        const media = $('demoMedia');
        media.textContent = '';
        const loading = document.createElement('div');
        loading.className = 'demo-empty';
        loading.textContent = 'Loading demo…';
        media.appendChild(loading);
        loadDemo(media, e, ++demoToken);

        demoReturn = trigger || null;
        $('demoOverlay').classList.add('active');
        $('demoClose').focus();
    }

    function closeDemo() {
        demoToken++;
        $('demoOverlay').classList.remove('active');
        $('demoMedia').textContent = '';
        if (demoReturn && document.body.contains(demoReturn)) demoReturn.focus();
        demoReturn = null;
    }

    function finishWorkout() {
        if (!current) return;
        const plan = workoutPlans[current.id];
        const done = current.checks.filter(Boolean).length;
        if (done === 0) { toast('Check off at least one exercise first.', true); return; }
        const record = { plan: current.id, title: plan.title, done, total: plan.exercises.length, minutes: plan.minutes, date: FP.today(), ts: Date.now(), checks: current.checks.slice() };
        const idx = current.resumeTs ? data.workouts.findIndex(w => w.ts === current.resumeTs) : -1;
        if (idx >= 0) data.workouts[idx] = record;   // continuing: update the same entry, don't duplicate
        else data.workouts.push(record);
        if (!FP.save(data)) { toast('Could not save. Browser storage is unavailable.', true); return; }
        const complete = done === plan.exercises.length;
        closeWorkout();
        renderAll();
        toast(complete ? 'Workout Complete. Great Job! 💪' : 'Workout Saved.');
    }

    /* ── events ── */
    $('filters').addEventListener('click', e => {
        const b = e.target.closest('[data-cat]');
        if (!b) return;
        activeCat = b.dataset.cat;
        renderFilters();
        renderCards();
    });
    $('workoutGrid').addEventListener('click', e => {
        const b = e.target.closest('[data-start]');
        if (b) openWorkout(b.dataset.start);
    });
    $('exerciseList').addEventListener('click', e => {
        const h = e.target.closest('[data-demo]');
        if (h) { openDemo(Number(h.dataset.demo), h); return; }
        const b = e.target.closest('[data-ex]');
        if (!b || !current) return;
        const i = Number(b.dataset.ex);
        current.checks[i] = !current.checks[i];
        renderSession();
        const again = $('exerciseList').querySelector(`[data-ex="${i}"]`);
        if (again) again.focus();
    });
    $('finishBtn').addEventListener('click', finishWorkout);
    $('sessionClose').addEventListener('click', closeWorkout);
    $('sessionOverlay').addEventListener('click', e => { if (e.target === $('sessionOverlay')) closeWorkout(); });
    $('demoClose').addEventListener('click', closeDemo);
    $('demoOverlay').addEventListener('click', e => { if (e.target === $('demoOverlay')) closeDemo(); });
    document.addEventListener('keydown', e => {
        if (e.key !== 'Escape') return;
        if ($('demoOverlay').classList.contains('active')) closeDemo();
        else if (current) closeWorkout();
    });

    $('historyList').addEventListener('click', e => {
        const c = e.target.closest('[data-continue]');
        if (c) {
            const entry = data.workouts.find(w => String(w.ts) === c.dataset.continue);
            if (entry) openWorkout(entry.plan, entry);
            return;
        }
        const b = e.target.closest('[data-del]');
        if (!b) return;
        data.workouts = data.workouts.filter(w => String(w.ts) !== b.dataset.del);
        FP.save(data);
        renderAll();
    });
    $('clearHistory').addEventListener('click', () => {
        if (!confirm('Delete all saved workouts?')) return;
        data.workouts = [];
        FP.save(data);
        renderAll();
    });

    /* Footer links like /workouts#arm open that workout directly */
    function openFromHash() {
        const id = location.hash.slice(1);
        if (workoutPlans[id]) { activeCat = 'all'; renderFilters(); renderCards(); openWorkout(id); }
    }
    window.addEventListener('hashchange', openFromHash);

    renderFilters();
    renderAll();
    openFromHash();


}

/* ── Page: diet ── */
/* diet.html */
function initDiet() {
    let toastTimer;
    function toast(msg, isError) {
        let t = document.getElementById('toast');
        if (!t) { t = document.createElement('div'); t.id = 'toast'; t.className = 'toast'; t.setAttribute('role', 'status'); document.body.appendChild(t); }
        t.textContent = msg;
        t.classList.toggle('error', !!isError);
        t.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => t.classList.remove('show'), 2600);
    }


    const nutritionPlans = {
        'muscle-gain': {
            title: 'Muscle Gain Plan', desc: 'High protein and calorie surplus to support muscle growth.',
            stats: { Calories: '2800-3200', Protein: '180-200g', Carbs: '350-400g', Fats: '80-100g' },
            meals: ['🍳 Breakfast: Oatmeal with protein powder, banana, almonds', '🍎 Snack: Greek yogurt with berries', '🥗 Lunch: Grilled chicken, brown rice, vegetables', '🥤 Snack: Protein shake, apple', '🍽 Dinner: Salmon, sweet potato, broccoli', '🌙 Before Bed: Cottage cheese']
        },
        'weight-loss': {
            title: 'Weight Loss Plan', desc: 'Lower calories to help burn fat while maintaining nutrients.',
            stats: { Calories: '1500-1800', Protein: '120-150g', Carbs: '150-180g', Fats: '50-60g' },
            meals: ['🍳 Breakfast: Egg whites, spinach, whole grain toast', '🍎 Snack: Apple with almond butter', '🥗 Lunch: Turkey breast, quinoa, mixed greens', '🥤 Snack: Protein smoothie', '🍽 Dinner: Grilled fish, asparagus, cauliflower rice', '🌙 Before Bed: Casein protein shake']
        },
        'balanced': {
            title: 'Balanced Nutrition', desc: 'Balanced macros to maintain a healthy body weight.',
            stats: { Calories: '2000-2400', Protein: '140-160g', Carbs: '250-280g', Fats: '65-75g' },
            meals: ['🍳 Breakfast: Whole grain pancakes, eggs, berries', '🍎 Snack: Mixed nuts and dried fruit', '🥗 Lunch: Chicken stir-fry, brown rice, vegetables', '🥤 Snack: Hummus with veggie sticks', '🍽 Dinner: Lean beef, baked potato, green beans', '🌙 Before Bed: Greek yogurt']
        },
        'performance': {
            title: 'Performance Plan', desc: 'Higher carbs and protein for energy, endurance, and recovery.',
            stats: { Calories: '2400-2800', Protein: '140-160g', Carbs: '300-350g', Fats: '70-90g' },
            meals: ['🍳 Breakfast: Eggs, toast, and fruit', '🍎 Snack: Protein bar', '🥗 Lunch: Chicken breast with rice and vegetables', '🥤 Snack: Protein shake', '🍽 Dinner: Salmon with sweet potatoes', '🌙 Before Bed: Greek yogurt']
        },
        'high-protein': {
            title: 'High Protein Plan', desc: 'Extra protein intake to support muscle repair and strength development.',
            stats: { Calories: '2200-2500', Protein: '180-220g', Carbs: '180-220g', Fats: '60-80g' },
            meals: ['🍳 Breakfast: Eggs, oats, protein shake', '🍎 Snack: Peanut butter toast', '🥗 Lunch: Chicken breast, brown rice, broccoli', '🥤 Snack: Protein smoothie', '🍽 Dinner: Lean steak, sweet potato', '🌙 Before Bed: Greek yogurt']
        },
        'fasting': {
            title: 'Fasting Plan', desc: 'Timed eating windows that help improve metabolism and fat burning.',
            stats: { Calories: '1800-2000', Protein: '130-150g', Carbs: '150-180g', Fats: '60-70g' },
            meals: ['🍳 Breakfast: Eggs, avocado, whole grain toast', '🍎 Snack: Protein shake', '🥗 Lunch: Grilled chicken, quinoa, vegetables', '🥤 Snack: Greek yogurt with berries', '🍽 Dinner: Salmon with brown rice and broccoli']
        },
        'keto': {
            title: 'Keto Plan', desc: 'Low-carb, high-fat eating to shift your body into a fat-burning ketosis state.',
            stats: { Calories: '1800-2200', Protein: '120-140g', Carbs: '20-50g', Fats: '130-160g' },
            meals: ['🍳 Breakfast: Scrambled eggs with butter, bacon, avocado', '🍎 Snack: Almonds and string cheese', '🥗 Lunch: Tuna salad with olive oil over leafy greens', '🥤 Snack: Boiled eggs with salt', '🍽 Dinner: Ribeye steak, roasted zucchini, garlic butter', '🌙 Before Bed: Peanut butter with celery sticks']
        },
        'plant-based': {
            title: 'Plant-Based Plan', desc: 'Whole-food, plant-powered meals that fuel performance and support overall wellness.',
            stats: { Calories: '1900-2300', Protein: '100-130g', Carbs: '280-320g', Fats: '55-70g' },
            meals: ['🍳 Breakfast: Oatmeal with chia seeds, berries, and almond butter', '🍎 Snack: Edamame with sea salt', '🥗 Lunch: Lentil and quinoa bowl with roasted veggies', '🥤 Snack: Smoothie with spinach, banana, plant protein', '🍽 Dinner: Tofu stir-fry with brown rice and broccoli', '🌙 Before Bed: Soy milk with a handful of walnuts']
        },
        'cut-lean': {
            title: 'Cut & Lean Plan', desc: 'Strategic calorie cycling to shed body fat while preserving hard-earned lean muscle.',
            stats: { Calories: '1600-2000', Protein: '160-190g', Carbs: '120-160g', Fats: '45-60g' },
            meals: ['🍳 Breakfast: Egg white omelette, spinach, whole grain toast', '🍎 Snack: Rice cakes with cottage cheese', '🥗 Lunch: Grilled turkey, mixed greens, light vinaigrette', '🥤 Snack: Whey protein shake with water', '🍽 Dinner: Tilapia, steamed asparagus, cauliflower mash', '🌙 Before Bed: Casein protein shake']
        }
    };

    /* Turn each plan's ranges (e.g. "2800-3200") into a single daily target (the midpoint). */
    const mid = s => { const n = String(s).match(/\d+/g).map(Number); return Math.round(n.reduce((a, b) => a + b, 0) / n.length); };
    Object.values(nutritionPlans).forEach(p => {
        p.goal = { cal: mid(p.stats.Calories), p: mid(p.stats.Protein), c: mid(p.stats.Carbs), f: mid(p.stats.Fats) };
    });

    /* Approximate nutrition per serving for quick-add. */
    const defaultFoods = [
        { name: 'Egg (1 large)', cal: 78, p: 6, c: 0.6, f: 5 },
        { name: 'Oatmeal (1 cup cooked)', cal: 158, p: 6, c: 27, f: 3 },
        { name: 'Whole grain bread (1 slice)', cal: 80, p: 4, c: 14, f: 1 },
        { name: 'Banana (1 medium)', cal: 105, p: 1.3, c: 27, f: 0.4 },
        { name: 'Apple (1 medium)', cal: 95, p: 0.5, c: 25, f: 0.3 },
        { name: 'Greek yogurt (170 g)', cal: 100, p: 17, c: 6, f: 0.7 },
        { name: 'Cottage cheese (1 cup)', cal: 220, p: 25, c: 8, f: 10 },
        { name: 'Whey protein shake (1 scoop)', cal: 120, p: 24, c: 3, f: 1.5 },
        { name: 'Chicken breast (100 g, grilled)', cal: 165, p: 31, c: 0, f: 3.6 },
        { name: 'Lean steak (100 g)', cal: 180, p: 27, c: 0, f: 7 },
        { name: 'Salmon (100 g)', cal: 208, p: 20, c: 0, f: 13 },
        { name: 'Tuna, canned in water (100 g)', cal: 116, p: 26, c: 0, f: 1 },
        { name: 'Tofu (100 g)', cal: 76, p: 8, c: 1.9, f: 4.8 },
        { name: 'White rice (1 cup cooked)', cal: 205, p: 4.3, c: 45, f: 0.4 },
        { name: 'Brown rice (1 cup cooked)', cal: 216, p: 5, c: 45, f: 1.8 },
        { name: 'Pasta (1 cup cooked)', cal: 220, p: 8, c: 43, f: 1.3 },
        { name: 'Sweet potato (1 medium)', cal: 103, p: 2.3, c: 24, f: 0.2 },
        { name: 'Lentils (1 cup cooked)', cal: 230, p: 18, c: 40, f: 0.8 },
        { name: 'Broccoli (1 cup)', cal: 55, p: 3.7, c: 11, f: 0.6 },
        { name: 'Avocado (½ fruit)', cal: 120, p: 1.5, c: 6, f: 11 },
        { name: 'Almonds (28 g)', cal: 164, p: 6, c: 6, f: 14 },
        { name: 'Peanut butter (1 tbsp)', cal: 94, p: 4, c: 3, f: 8 }
    ];

    /* Shared food list: managed on the admin page, falls back to the built-in list. */
    const foodDb = (() => {
        try {
            const list = JSON.parse(localStorage.getItem('fitpass_admin_foods'));
            if (Array.isArray(list) && list.length && list.every(f => f && typeof f.name === 'string' && isFinite(f.cal))) {
                return list.map(f => ({ name: f.name, cal: +f.cal || 0, p: +f.p || 0, c: +f.c || 0, f: +f.f || 0 }));
            }
        } catch (e) { }
        return defaultFoods;
    })();

    const mealNames = { breakfast: '🍳 Breakfast', lunch: '🥗 Lunch', dinner: '🍽 Dinner', snack: '🍎 Snack' };
    const CIRC = 2 * Math.PI * 52;

    let data = FP.data();
    let viewDate = FP.today();
    let modalPlan = null;

    const $ = id => document.getElementById(id);
    const round = (n, d = 0) => { const m = Math.pow(10, d); return Math.round(n * m) / m; };
    const entriesFor = k => data.foods[k] || [];
    const persist = () => { if (!FP.save(data)) toast('Could not save. Browser storage is unavailable.', true); };

    function dateText(k) {
        const t = FP.today();
        if (k === t) return 'Today';
        if (k === FP.addDays(t, -1)) return 'Yesterday';
        if (k === FP.addDays(t, 1)) return 'Tomorrow';
        return FP.parse(k).toLocaleDateString(undefined, { weekday: 'long', month: 'short', day: 'numeric' });
    }

    /* ── summary ── */
    function renderSummary() {
        const g = data.goals;
        const t = FP.totals(entriesFor(viewDate));
        $('dateLabel').textContent = dateText(viewDate);
        $('datePick').value = viewDate;

        $('calNow').textContent = round(t.cal);
        $('calGoal').textContent = g.cal;
        $('goalCurrent').textContent = 'Current goal: ' + g.cal + ' kcal · P ' + g.p + 'g · C ' + g.c + 'g · F ' + g.f + 'g';
        const pct = g.cal ? t.cal / g.cal : 0;
        const ring = $('ringFg');
        ring.style.strokeDashoffset = CIRC * (1 - Math.min(pct, 1));
        ring.classList.toggle('over', pct > 1);
        const left = round(g.cal - t.cal);
        const note = $('ringNote');
        note.textContent = t.cal === 0 ? '' : (left >= 0 ? left + ' kcal left' : Math.abs(left) + ' kcal over');
        note.classList.toggle('over', left < 0);

        [['pro', 'p'], ['carb', 'c'], ['fat', 'f']].forEach(([id, k]) => {
            $(id + 'Now').textContent = round(t[k]);
            $(id + 'Goal').textContent = g[k];
            $(id + 'Bar').style.width = Math.min(100, g[k] ? t[k] / g[k] * 100 : 0) + '%';
        });

        $('wkCount').textContent = FP.weekCount(data.workouts);
        $('wkStreak').textContent = FP.streak(data.workouts);
    }

    /* ── meal log ── */
    function renderMeals() {
        const list = entriesFor(viewDate);
        if (!list.length) {
            $('mealLog').innerHTML = '<p class="empty">Nothing logged for this day yet. Add your first meal with the form.</p>';
            return;
        }
        $('mealLog').innerHTML = Object.keys(mealNames).map(m => {
            const items = list.filter(e => e.meal === m);
            if (!items.length) return '';
            const sub = round(items.reduce((s, e) => s + e.cal, 0));
            return `<div class="meal-group">
                <div class="meal-title">${mealNames[m]}<span>${sub} kcal</span></div>
                ${items.map(e => `<div class="food-item">
                    <div class="food-main"><b>${FP.esc(e.name)}</b><span>P ${round(e.p, 1)}g · C ${round(e.c, 1)}g · F ${round(e.f, 1)}g</span></div>
                    <div class="food-cal">${round(e.cal)}</div>
                    <button class="icon-btn" type="button" data-del="${e.id}" aria-label="Remove ${FP.esc(e.name)}">🗑</button>
                </div>`).join('')}
            </div>`;
        }).join('');
    }

    /* ── 7-day chart ── */
    function renderChart() {
        const days = Array.from({ length: 7 }, (_, i) => FP.addDays(viewDate, i - 6));
        const cals = days.map(k => round(FP.totals(entriesFor(k)).cal));
        const goal = data.goals.cal;
        const max = Math.max(goal * 1.25, ...cals, 1);
        const plotH = 220 - 46; // chart height minus label space
        $('chart').innerHTML = days.map((k, i) => {
            const h = cals[i] / max * 100;
            const d = FP.parse(k);
            return `<button type="button" class="chart-col${k === viewDate ? ' selected' : ''}" data-day="${k}" aria-label="${dateText(k)}: ${cals[i]} kilocalories">
                <span class="chart-val">${cals[i] || ''}</span>
                <div class="chart-bar${cals[i] > goal ? ' over' : ''}" style="height:${h}%"></div>
                <span class="chart-day">${d.toLocaleDateString(undefined, { weekday: 'short' })}<br>${d.getDate()}</span>
            </button>`;
        }).join('') + `<div class="goal-line" style="bottom:calc(46px + ${goal / max} * (100% - 46px))"><span>Goal ${goal}</span></div>`;
    }

    function renderGoals() {
        const g = data.goals;
        $('planSel').value = g.plan;
        $('gCalIn').value = g.cal; $('gProIn').value = g.p; $('gCarbIn').value = g.c; $('gFatIn').value = g.f;
    }

    function renderAll() { renderSummary(); renderMeals(); renderChart(); }

    function setDate(k) { viewDate = k; renderAll(); }

    /* ── add food ── */
    function quickFood(v) {
        if (!v) return null;
        const list = v[0] === 'm' ? (data.myFoods || []) : foodDb;
        return list[Number(v.slice(1))] || null;
    }

    function renderQuick() {
        const mine = data.myFoods || [];
        const opt = (prefix, f, i) => `<option value="${prefix}${i}">${FP.esc(f.name)} · ${f.cal} kcal</option>`;
        $('quick').innerHTML = '<option value="">Custom food (type below)</option>' +
            (mine.length ? '<optgroup label="My foods">' + mine.map((f, i) => opt('m', f, i)).join('') + '</optgroup>' : '') +
            '<optgroup label="FitPass foods">' + foodDb.map((f, i) => opt('s', f, i)).join('') + '</optgroup>';
        $('removeSaved').hidden = true;
    }

    function applyQuick() {
        const v = $('quick').value;
        $('removeSaved').hidden = v[0] !== 'm';
        const f = quickFood(v);
        if (!f) return;
        const s = parseFloat($('servings').value) || 1;
        $('fName').value = f.name + (s !== 1 ? ' × ' + s : '');
        $('fCal').value = round(f.cal * s);
        $('fPro').value = round(f.p * s, 1);
        $('fCarb').value = round(f.c * s, 1);
        $('fFat').value = round(f.f * s, 1);
    }

    renderQuick();
    $('removeSaved').addEventListener('click', () => {
        const v = $('quick').value;
        if (v[0] !== 'm') return;
        const f = quickFood(v);
        data.myFoods.splice(Number(v.slice(1)), 1);
        persist();
        renderQuick();
        $('foodForm').reset();
        $('servings').value = 1;
        toast('Removed ' + (f ? f.name : 'food') + ' from my foods.');
    });
    $('quick').addEventListener('change', applyQuick);
    $('servings').addEventListener('input', applyQuick);

    $('foodForm').addEventListener('submit', e => {
        e.preventDefault();
        const name = $('fName').value.trim();
        const cal = parseFloat($('fCal').value);
        if (!name) { toast('Enter a food name.', true); $('fName').focus(); return; }
        if (!(cal >= 0)) { toast('Enter the calories for this food.', true); $('fCal').focus(); return; }
        const num = id => Math.max(0, parseFloat($(id).value) || 0);
        const entry = { id: Date.now() + '-' + Math.random().toString(36).slice(2, 6), meal: $('meal').value, name, cal, p: num('fPro'), c: num('fCarb'), f: num('fFat') };
        (data.foods[viewDate] = data.foods[viewDate] || []).push(entry);
        let saved = false;
        if ($('saveMine').checked) {
            /* store per single serving, and drop any "× N" added by quick add */
            const s = parseFloat($('servings').value) || 1;
            const base = name.replace(/\s*×\s*[\d.]+$/, '').trim() || name;
            const rec = { name: base, cal: round(cal / s), p: round(entry.p / s, 1), c: round(entry.c / s, 1), f: round(entry.f / s, 1) };
            data.myFoods = data.myFoods || [];
            const at = data.myFoods.findIndex(x => x.name.toLowerCase() === base.toLowerCase());
            if (at >= 0) data.myFoods[at] = rec; else data.myFoods.push(rec);
            saved = true;
        }
        persist();
        $('foodForm').reset();
        $('servings').value = 1;
        if (saved) renderQuick();
        $('removeSaved').hidden = true;
        renderAll();
        toast(saved ? 'Added ' + name + ' and saved to my foods' : 'Added ' + name);
    });

    $('mealLog').addEventListener('click', e => {
        const b = e.target.closest('[data-del]');
        if (!b) return;
        data.foods[viewDate] = entriesFor(viewDate).filter(x => x.id !== b.dataset.del);
        if (!data.foods[viewDate].length) delete data.foods[viewDate];
        persist();
        renderAll();
    });

    /* ── goals + plans ── */
    $('planSel').innerHTML = '<option value="custom">Custom goals</option>' +
        Object.entries(nutritionPlans).map(([id, p]) => `<option value="${id}">${p.title}</option>`).join('');

    function fillFromPlan(id) {
        const p = nutritionPlans[id];
        if (!p) return;
        $('gCalIn').value = p.goal.cal; $('gProIn').value = p.goal.p; $('gCarbIn').value = p.goal.c; $('gFatIn').value = p.goal.f;
    }
    $('planSel').addEventListener('change', () => fillFromPlan($('planSel').value));
    ['gCalIn', 'gProIn', 'gCarbIn', 'gFatIn'].forEach(id => $(id).addEventListener('input', () => { $('planSel').value = 'custom'; }));

    function saveGoals() {
        const cal = parseInt($('gCalIn').value, 10), p = parseInt($('gProIn').value, 10),
            c = parseInt($('gCarbIn').value, 10), f = parseInt($('gFatIn').value, 10);
        if (!(cal >= 500) || [p, c, f].some(v => !(v >= 0))) { toast('Enter valid goals (calories at least 500).', true); return; }
        data.goals = { plan: $('planSel').value, cal, p, c, f };
        persist();
        renderAll();
        toast('Goals Saved.');
    }
    $('saveGoals').addEventListener('click', saveGoals);

    function openPlan(id) {
        const plan = nutritionPlans[id];
        if (!plan) { toast('Choose a nutrition plan first.', true); return; }
        modalPlan = id;
        $('planTitle').textContent = plan.title;
        $('planDesc').textContent = plan.desc;
        $('planStats').innerHTML = Object.entries(plan.stats).map(([l, v]) =>
            `<div class="modal-stat-box"><span class="modal-stat-label">${l}</span><span class="modal-stat-value">${v}</span></div>`).join('');
        $('planMeals').innerHTML = plan.meals.map(m => `<li>${m}</li>`).join('');
        $('planOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
        $('planClose').focus();
    }
    function closePlan() {
        $('planOverlay').classList.remove('active');
        document.body.style.overflow = '';
        modalPlan = null;
    }
    $('viewPlan').addEventListener('click', () => openPlan($('planSel').value));
    $('planClose').addEventListener('click', closePlan);
    $('planOverlay').addEventListener('click', e => { if (e.target === $('planOverlay')) closePlan(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && modalPlan) closePlan(); });
    $('usePlan').addEventListener('click', () => {
        const id = modalPlan;
        $('planSel').value = id;
        fillFromPlan(id);
        closePlan();
        saveGoals();
    });

    /* ── date navigation ── */
    $('prevDay').addEventListener('click', () => setDate(FP.addDays(viewDate, -1)));
    $('nextDay').addEventListener('click', () => setDate(FP.addDays(viewDate, 1)));
    $('goToday').addEventListener('click', () => setDate(FP.today()));
    $('datePick').addEventListener('change', e => { if (e.target.value) setDate(e.target.value); });
    $('chart').addEventListener('click', e => {
        const b = e.target.closest('[data-day]');
        if (b) setDate(b.dataset.day);
    });

    renderGoals();
    renderAll();


}

/* ── Page: about ── */
/* about.html */
function initAbout() {
    let toastTimer;
    function toast(msg, isError) {
        let t = document.getElementById('toast');
        if (!t) { t = document.createElement('div'); t.id = 'toast'; t.className = 'toast'; t.setAttribute('role', 'status'); document.body.appendChild(t); }
        t.textContent = msg;
        t.classList.toggle('error', !!isError);
        t.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => t.classList.remove('show'), 2600);
    }


    document.querySelectorAll('.reveal-left, .reveal-right').forEach(el => {
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); } });
        }, { threshold: 0.15 });
        io.observe(el);
    });


}

/* ── Page: admin (has its own navbar code, so initNav is not used there) ── */
/* admin.html */
function initAdmin() {
    /* Starting data copied from workouts.html and diet.html */
    const workoutPlans = {
        arm: {
            title: 'Arm Workout', cat: 'strength', level: 'Intermediate', minutes: 25,
            desc: 'Build strength and definition in your biceps, triceps, and forearms.',
            exercises: [
                { name: 'Diamond Push Up', detail: '10 Reps • 3 Sets' },
                { name: 'Push Up', detail: '10 Reps • 3 Sets' },
                { name: 'Push Up & Rotation', detail: '12 Reps • 2 Sets' },
                { name: 'Burpees', detail: '16 Reps • 2 Sets' },
                { name: 'Tricep Dips', detail: '12 Reps • 3 Sets' },
                { name: 'Triceps Push Up', detail: '14 Reps • 2 Sets' },
                { name: 'Plank Shoulder Taps', detail: '12 Reps • 3 Sets' }
            ]
        },
        chest: {
            title: 'Chest Workout', cat: 'strength', level: 'Intermediate', minutes: 25,
            desc: 'Strengthen and sculpt your pectoral muscles with targeted push variations.',
            exercises: [
                { name: 'Push Up', detail: '10 Reps • 3 Sets' },
                { name: 'Wide Push Up', detail: '12 Reps • 2 Sets' },
                { name: 'Diamond Push Up', detail: '12 Reps • 3 Sets' },
                { name: 'Decline Push Up', detail: '10 Reps • 2 Sets' },
                { name: 'Incline Push Up', detail: '10 Reps • 2 Sets' },
                { name: 'Archer Push Up', detail: '10 Reps • 2 Sets' },
                { name: 'Bench Press', detail: '10 Reps • 2 Sets' }
            ]
        },
        leg: {
            title: 'Leg Workout', cat: 'strength', level: 'Intermediate', minutes: 30,
            desc: 'Power up your quads, hamstrings, and glutes with lower body exercises.',
            exercises: [
                { name: 'Squat', detail: '12 Reps • 2 Sets' },
                { name: 'Reverse Lunges', detail: '12 Reps • 3 Sets' },
                { name: 'Step Up', detail: '15 Reps • 3 Sets' },
                { name: 'Curtsy Lunges', detail: '14 Reps • 2 Sets' },
                { name: 'Lunges', detail: '10 Reps • 2 Sets' },
                { name: 'Bulgarian Split Squat', detail: '10 Reps • 3 Sets' },
                { name: 'Jump Squats', detail: '14 Reps • 2 Sets' }
            ]
        },
        shoulder: {
            title: 'Shoulder Workout', cat: 'strength', level: 'Intermediate', minutes: 25,
            desc: 'Develop strong, defined shoulders with focused deltoid exercises.',
            exercises: [
                { name: 'Lateral Raise', detail: '10 Reps • 3 Sets' },
                { name: 'Pike Push Up', detail: '12 Reps • 3 Sets' },
                { name: 'Decline Push Up', detail: '10 Reps • 3 Sets' },
                { name: 'Incline Push Up', detail: '10 Reps • 3 Sets' },
                { name: 'Plank To Downward Dog', detail: '12 Reps • 3 Sets' },
                { name: 'Shoulder Taps', detail: '20 Reps • 3 Sets' },
                { name: 'Tricep Dips', detail: '12 Reps • 3 Sets' }
            ]
        },
        abs: {
            title: 'Abs Workout', cat: 'core', level: 'Beginner', minutes: 20,
            desc: 'Tone and tighten your core with effective abdominal exercises.',
            exercises: [
                { name: 'Sit Up', detail: '13 Reps • 3 Sets' },
                { name: 'Leg Raise', detail: '12 Reps • 3 Sets' },
                { name: 'Plank', detail: '2:00 minutes • 2 Sets' },
                { name: 'Russian Twist', detail: '10 Reps • 3 Sets' },
                { name: 'Bicycle Crunches', detail: '14 Reps • 2 Sets' },
                { name: 'Heel Touch', detail: '20 Reps • 3 Sets' },
                { name: 'V-Up', detail: '16 Reps • 2 Sets' }
            ]
        },
        back: {
            title: 'Back Workout', cat: 'strength', level: 'Advanced', minutes: 30,
            desc: 'Build a wider, stronger back with pull-focused and posterior chain movements.',
            exercises: [
                { name: 'Pull Ups', detail: '12 Reps • 3 Sets' },
                { name: 'Australian Pull-Ups', detail: '14 Reps • 2 Sets' },
                { name: 'Reverse Snow Angels', detail: '15 Reps • 3 Sets' },
                { name: 'Back Widow', detail: '12 Reps • 3 Sets' },
                { name: 'Scapula Push Up', detail: '10 Reps • 2 Sets' },
                { name: 'Superman Hold', detail: '50 seconds • 2 Sets' },
                { name: 'Resistance Band Deadlift', detail: '10 Reps • 3 Sets' }
            ]
        },
        cardio: {
            title: 'Cardio Workout', cat: 'cardio', level: 'Beginner', minutes: 25,
            desc: 'Boost your heart rate and burn calories with energetic cardio exercises.',
            exercises: [
                { name: 'Jumping Jacks', detail: '45 seconds • 3 Sets' },
                { name: 'High Knees', detail: '40 seconds • 3 Sets' },
                { name: 'Jump Rope', detail: '60 seconds • 3 Sets' },
                { name: 'Butt Kicks', detail: '40 seconds • 3 Sets' },
                { name: 'Mountain Climbers', detail: '30 seconds • 3 Sets' },
                { name: 'Box Step-Ups', detail: '15 Reps • 3 Sets' },
                { name: 'Running in Place', detail: '60 seconds • 2 Sets' }
            ]
        },
        fullbody: {
            title: 'Full Body Workout', cat: 'full', level: 'Intermediate', minutes: 35,
            desc: 'Hit every muscle group with a complete full-body training session.',
            exercises: [
                { name: 'Burpees', detail: '12 Reps • 3 Sets' },
                { name: 'Squat to Press', detail: '12 Reps • 3 Sets' },
                { name: 'Push Up to Row', detail: '10 Reps • 3 Sets' },
                { name: 'Reverse Lunge with Twist', detail: '12 Reps • 2 Sets' },
                { name: 'Plank Up-Down', detail: '10 Reps • 3 Sets' },
                { name: 'Glute Bridge', detail: '15 Reps • 3 Sets' },
                { name: 'Dead Bug', detail: '10 Reps • 2 Sets' }
            ]
        },
        hiit: {
            title: 'HIIT Workout', cat: 'cardio', level: 'Advanced', minutes: 20,
            desc: 'Max-intensity intervals to torch fat and skyrocket your metabolism.',
            exercises: [
                { name: 'Burpees', detail: '20s on • 10s rest • 4 Sets' },
                { name: 'Jump Squats', detail: '20s on • 10s rest • 4 Sets' },
                { name: 'Explosive Push Ups', detail: '20s on • 10s rest • 3 Sets' },
                { name: 'Sprint in Place', detail: '20s on • 10s rest • 4 Sets' },
                { name: 'Plank to Push Up', detail: '20s on • 10s rest • 3 Sets' },
                { name: 'Lateral Bounds', detail: '20s on • 10s rest • 3 Sets' },
                { name: 'Tuck Jumps', detail: '20s on • 10s rest • 4 Sets' }
            ]
        }
    };

    const foodDb = [
        { name: 'Egg (1 large)', cal: 78, p: 6, c: 0.6, f: 5 },
        { name: 'Oatmeal (1 cup cooked)', cal: 158, p: 6, c: 27, f: 3 },
        { name: 'Whole grain bread (1 slice)', cal: 80, p: 4, c: 14, f: 1 },
        { name: 'Banana (1 medium)', cal: 105, p: 1.3, c: 27, f: 0.4 },
        { name: 'Apple (1 medium)', cal: 95, p: 0.5, c: 25, f: 0.3 },
        { name: 'Greek yogurt (170 g)', cal: 100, p: 17, c: 6, f: 0.7 },
        { name: 'Cottage cheese (1 cup)', cal: 220, p: 25, c: 8, f: 10 },
        { name: 'Whey protein shake (1 scoop)', cal: 120, p: 24, c: 3, f: 1.5 },
        { name: 'Chicken breast (100 g, grilled)', cal: 165, p: 31, c: 0, f: 3.6 },
        { name: 'Lean steak (100 g)', cal: 180, p: 27, c: 0, f: 7 },
        { name: 'Salmon (100 g)', cal: 208, p: 20, c: 0, f: 13 },
        { name: 'Tuna, canned in water (100 g)', cal: 116, p: 26, c: 0, f: 1 },
        { name: 'Tofu (100 g)', cal: 76, p: 8, c: 1.9, f: 4.8 },
        { name: 'White rice (1 cup cooked)', cal: 205, p: 4.3, c: 45, f: 0.4 },
        { name: 'Brown rice (1 cup cooked)', cal: 216, p: 5, c: 45, f: 1.8 },
        { name: 'Pasta (1 cup cooked)', cal: 220, p: 8, c: 43, f: 1.3 },
        { name: 'Sweet potato (1 medium)', cal: 103, p: 2.3, c: 24, f: 0.2 },
        { name: 'Lentils (1 cup cooked)', cal: 230, p: 18, c: 40, f: 0.8 },
        { name: 'Broccoli (1 cup)', cal: 55, p: 3.7, c: 11, f: 0.6 },
        { name: 'Avocado (½ fruit)', cal: 120, p: 1.5, c: 6, f: 11 },
        { name: 'Almonds (28 g)', cal: 164, p: 6, c: 6, f: 14 },
        { name: 'Peanut butter (1 tbsp)', cal: 94, p: 4, c: 3, f: 8 }
    ];

    const seedUsers = [
        { id: 1, name: 'Maria Santos', email: 'maria.santos@email.com', joined: '2026-05-12', last: '2026-10-16', status: 'active', plans: { arm: 6, leg: 4, abs: 5, cardio: 3 } },
        { id: 2, name: 'John Reyes', email: 'john.reyes@email.com', joined: '2026-05-28', last: '2026-10-17', status: 'active', plans: { chest: 8, arm: 5, back: 4, shoulder: 3 } },
        { id: 3, name: 'Angela Cruz', email: 'angela.cruz@email.com', joined: '2026-06-09', last: '2026-10-10', status: 'active', plans: { hiit: 7, cardio: 6, fullbody: 4 } },
        { id: 4, name: 'Mark Dela Peña', email: 'mark.dp@email.com', joined: '2026-07-03', last: '2026-09-02', status: 'suspended', plans: { leg: 3, chest: 2 } },
        { id: 5, name: 'Kristine Lim', email: 'kristine.lim@email.com', joined: '2026-07-21', last: '2026-10-15', status: 'active', plans: { abs: 9, fullbody: 5, cardio: 2 } },
        { id: 6, name: 'Paolo Garcia', email: 'paolo.garcia@email.com', joined: '2026-08-14', last: '2026-10-17', status: 'active', plans: { back: 6, shoulder: 5, arm: 3 } },
        { id: 7, name: 'Sofia Ramos', email: 'sofia.ramos@email.com', joined: '2026-09-05', last: '2026-10-13', status: 'active', plans: { cardio: 4, abs: 3 } },
        { id: 8, name: 'Daniel Torres', email: 'daniel.t@email.com', joined: '2026-10-02', last: '2026-10-12', status: 'active', plans: { arm: 2, chest: 1 } }
    ];

    /* Nutrition plans: the same nine plans members see on the diet page (daily targets) */
    const seedGoalPlans = {
        'muscle-gain': { title: 'Muscle Gain Plan', desc: 'High protein and calorie surplus to support muscle growth.', cal: 3000, p: 190, c: 375, f: 90 },
        'weight-loss': { title: 'Weight Loss Plan', desc: 'Lower calories to help burn fat while maintaining nutrients.', cal: 1650, p: 135, c: 165, f: 55 },
        'balanced': { title: 'Balanced Nutrition', desc: 'Balanced macros to maintain a healthy body weight.', cal: 2200, p: 150, c: 265, f: 70 },
        'performance': { title: 'Performance Plan', desc: 'Higher carbs and protein for energy, endurance, and recovery.', cal: 2600, p: 150, c: 325, f: 80 },
        'high-protein': { title: 'High Protein Plan', desc: 'Extra protein intake to support muscle repair and strength development.', cal: 2350, p: 200, c: 200, f: 70 },
        'fasting': { title: 'Fasting Plan', desc: 'Timed eating windows that help improve metabolism and fat burning.', cal: 1900, p: 140, c: 165, f: 65 },
        'keto': { title: 'Keto Plan', desc: 'Low-carb, high-fat eating to shift your body into a fat-burning ketosis state.', cal: 2000, p: 130, c: 35, f: 145 },
        'plant-based': { title: 'Plant-Based Plan', desc: 'Whole-food, plant-powered meals that fuel performance and support overall wellness.', cal: 2100, p: 115, c: 300, f: 63 },
        'cut-lean': { title: 'Cut & Lean Plan', desc: 'Strategic calorie cycling to shed body fat while preserving hard-earned lean muscle.', cal: 1800, p: 175, c: 140, f: 53 }
    };
    /* Which plan each starting member follows */
    const seedMemberPlan = { 1: 'weight-loss', 2: 'muscle-gain', 3: 'cut-lean', 4: 'balanced', 5: 'weight-loss', 6: 'high-protein', 7: 'plant-based', 8: 'performance' };

    const KEYS = { users: 'fitpass_admin_users', workouts: 'fitpass_admin_workouts', foods: 'fitpass_admin_foods', goalPlans: 'fitpass_admin_goalplans' };
    const read = (k, fb) => { try { const v = JSON.parse(localStorage.getItem(k)); return v == null ? fb : v; } catch (e) { return fb; } };
    const write = (k, v) => { try { localStorage.setItem(k, JSON.stringify(v)); return true; } catch (e) { return false; } };
    const $ = id => document.getElementById(id);
    const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const clone = o => JSON.parse(JSON.stringify(o));

    let users = read(KEYS.users, null) || clone(seedUsers);
    let plans = read(KEYS.workouts, null) || clone(workoutPlans);
    let foods = read(KEYS.foods, null) || clone(foodDb);
    let goalPlans = read(KEYS.goalPlans, null) || clone(seedGoalPlans);

    /* give the starting members their daily goals (members added later start with none) */
    function seedMemberGoals() {
        users.forEach(u => {
            const id = seedMemberPlan[u.id], p = seedGoalPlans[id];
            if (!u.goal && p) u.goal = { plan: id, cal: p.cal, p: p.p, c: p.c, f: p.f };
        });
    }
    seedMemberGoals();

    const catLabels = { strength: 'Strength', core: 'Core', cardio: 'Cardio & HIIT', full: 'Full Body' };
    const levels = ['Beginner', 'Intermediate', 'Advanced'];

    function toast(msg, isError) {
        let t = $('toast');
        if (!t) { t = document.createElement('div'); t.id = 'toast'; t.className = 'toast'; t.setAttribute('role', 'status'); document.body.appendChild(t); }
        t.textContent = msg;
        t.classList.toggle('error', !!isError);
        t.classList.add('show');
        clearTimeout(toast.t);
        toast.t = setTimeout(() => t.classList.remove('show'), 2400);
    }

    function persist() {
        const ok = write(KEYS.users, users) && write(KEYS.workouts, plans) && write(KEYS.foods, foods) && write(KEYS.goalPlans, goalPlans);
        if (!ok) toast('Could not save. Browser storage is unavailable.', true);
    }

    const fmtDate = k => new Date(k + 'T00:00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    const sessions = u => Object.values(u.plans || {}).reduce((a, b) => a + b, 0);
    const todayKey = () => { const d = new Date(); return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'); };
    const slug = s => s.toLowerCase().replace(/[^a-z0-9]+/g, '').slice(0, 20) || 'workout';

    /* ── bars helper ── */
    function bars(rows, green) {
        if (!rows.length) return '<p class="empty">No data yet.</p>';
        const max = Math.max(...rows.map(r => r.v), 1);
        return rows.map(r => `<div class="bar-row"><span class="lbl">${esc(r.l)}</span>
                <div class="bar-track"><div class="bar-fill${green ? ' green' : ''}" style="width:${(r.v / max) * 100}%"></div></div>
                <span class="val">${r.v}</span></div>`).join('');
    }

    function sessionsByPlan() {
        const totals = {};
        users.forEach(u => Object.entries(u.plans || {}).forEach(([k, v]) => { totals[k] = (totals[k] || 0) + v; }));
        return Object.entries(totals).map(([k, v]) => ({ l: plans[k] ? plans[k].title.replace(' Workout', '') : k, v })).sort((a, b) => b.v - a.v);
    }

    /* ── stats + overview + reports ── */
    function renderStats() {
        $('sUsers').textContent = users.length;
        $('sActive').textContent = users.filter(u => u.status === 'active').length;
        $('sWorkouts').textContent = Object.keys(plans).length;
        $('sFoods').textContent = foods.length;
    }

    function renderOverview() {
        $('ovPopular').innerHTML = bars(sessionsByPlan().slice(0, 5));
        const events = [];
        users.forEach(u => {
            events.push({ d: u.joined, t: `${esc(u.name)} joined FitPass`, warn: false });
            events.push({ d: u.last, t: `${esc(u.name)} finished a workout`, warn: u.status === 'suspended' });
        });
        events.sort((a, b) => b.d.localeCompare(a.d));
        $('ovActivity').innerHTML = events.slice(0, 6).map(e => `<li class="${e.warn ? 'warn' : ''}">${e.t}<span>${fmtDate(e.d)}</span></li>`).join('');
    }

    function renderReports() {
        $('rpWorkouts').innerHTML = bars(sessionsByPlan());
        const months = [];
        const now = new Date();
        for (let i = 5; i >= 0; i--) {
            const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
            const key = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
            months.push({ key, l: d.toLocaleDateString('en-US', { month: 'short', year: '2-digit' }), v: 0 });
        }
        users.forEach(u => { const m = months.find(x => u.joined.startsWith(x.key)); if (m) m.v++; });
        $('rpSignups').innerHTML = bars(months, true);
        $('rpStatus').innerHTML = bars([
            { l: 'Active', v: users.filter(u => u.status === 'active').length },
            { l: 'Suspended', v: users.filter(u => u.status !== 'active').length }
        ], true);
        $('rpLevels').innerHTML = bars(levels.map(l => ({ l, v: Object.values(plans).filter(p => p.level === l).length })), true);
    }

    /* ── users ── */
    function renderUsers() {
        const q = $('userSearch').value.trim().toLowerCase();
        const list = users.filter(u => !q || u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q));
        $('userRows').innerHTML = list.length ? list.map(u => `<tr>
                <td><b>${esc(u.name)}</b><span class="sub">${esc(u.email)}</span></td>
                <td>${fmtDate(u.joined)}</td>
                <td>${sessions(u)}</td>
                <td>${fmtDate(u.last)}</td>
                <td><span class="pill${u.status === 'active' ? '' : ' off'}">${u.status}</span></td>
                <td class="actions">
                    <button class="btn-mini" type="button" data-toggle="${u.id}">${u.status === 'active' ? 'Suspend' : 'Activate'}</button>
                    <button class="btn-mini danger" type="button" data-deluser="${u.id}">Delete</button>
                </td></tr>`).join('') : '<tr><td colspan="6" class="empty">No members found.</td></tr>';
    }

    /* ── workouts ── */
    function renderWorkouts() {
        const entries = Object.entries(plans);
        $('workoutRows').innerHTML = entries.length ? entries.map(([id, p]) => `<tr>
                <td><b>${esc(p.title)}</b><span class="sub">${esc(p.desc || '')}</span></td>
                <td>${esc(catLabels[p.cat] || p.cat)}</td>
                <td><span class="pill lvl">${esc(p.level)}</span></td>
                <td>${p.exercises.length} exercises</td>
                <td>~${p.minutes} min</td>
                <td class="actions">
                    <button class="btn-mini" type="button" data-editw="${esc(id)}">Edit</button>
                    <button class="btn-mini danger" type="button" data-delw="${esc(id)}">Delete</button>
                </td></tr>`).join('') : '<tr><td colspan="6" class="empty">No workouts yet.</td></tr>';
    }

    /* ── foods ── */
    function renderFoods() {
        const q = $('foodSearch').value.trim().toLowerCase();
        const rows = foods.map((f, i) => ({ f, i })).filter(x => !q || x.f.name.toLowerCase().includes(q));
        $('foodRows').innerHTML = rows.length ? rows.map(({ f, i }) => `<tr>
                <td><b>${esc(f.name)}</b></td>
                <td>${f.cal} kcal</td><td>${f.p} g</td><td>${f.c} g</td><td>${f.f} g</td>
                <td class="actions">
                    <button class="btn-mini" type="button" data-editf="${i}">Edit</button>
                    <button class="btn-mini danger" type="button" data-delf="${i}">Delete</button>
                </td></tr>`).join('') : '<tr><td colspan="6" class="empty">No foods found.</td></tr>';
    }

    /* ── goals: nutrition plans + member daily goals ── */
    const planTitle = id => goalPlans[id] ? goalPlans[id].title : 'Custom goals';
    function renderGoals() {
        const ids = Object.keys(goalPlans);
        $('goalPlanRows').innerHTML = ids.length ? ids.map(id => {
            const p = goalPlans[id];
            const n = users.filter(u => u.goal && u.goal.plan === id).length;
            return `<tr>
                <td><b>${esc(p.title)}</b><span class="sub">${esc(p.desc || '')}</span></td>
                <td>${p.cal} kcal</td><td>${p.p} g</td><td>${p.c} g</td><td>${p.f} g</td>
                <td>${n}</td>
                <td class="actions">
                    <button class="btn-mini" type="button" data-editgp="${esc(id)}">Edit</button>
                    <button class="btn-mini danger" type="button" data-delgp="${esc(id)}">Delete</button>
                </td></tr>`;
        }).join('') : '<tr><td colspan="7" class="empty">No nutrition plans yet.</td></tr>';

        const q = $('goalSearch').value.trim().toLowerCase();
        const rows = users.filter(u => !q || u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q));
        $('memberGoalRows').innerHTML = rows.length ? rows.map(u => {
            const g = u.goal;
            return `<tr>
                <td><b>${esc(u.name)}</b><span class="sub">${esc(u.email)}</span></td>
                ${g ? `<td><span class="pill lvl">${esc(planTitle(g.plan))}</span></td>
                <td>${g.cal} kcal</td><td>${g.p} g</td><td>${g.c} g</td><td>${g.f} g</td>`
                    : '<td colspan="5" class="empty" style="text-align:left">No goals set yet</td>'}
                <td class="actions">
                    <button class="btn-mini" type="button" data-editg="${u.id}">${g ? 'Edit' : 'Set goals'}</button>
                </td></tr>`;
        }).join('') : '<tr><td colspan="7" class="empty">No members found.</td></tr>';

        const counts = {};
        users.forEach(u => { if (u.goal) counts[planTitle(u.goal.plan)] = (counts[planTitle(u.goal.plan)] || 0) + 1; });
        $('rpGoals').innerHTML = bars(Object.entries(counts).map(([l, v]) => ({ l: l.replace(/ (Plan|Nutrition)$/, ''), v })).sort((a, b) => b.v - a.v), true);
    }

    function renderAll() { renderStats(); renderOverview(); renderUsers(); renderWorkouts(); renderFoods(); renderGoals(); renderReports(); }

    /* ── modal ── */
    let modalReturn = null;
    function openModal(title, sub, html, onSubmit) {
        $('mTitle').textContent = title;
        $('mSub').textContent = sub;
        $('mBody').innerHTML = html + `<div class="modal-actions">
                <button type="button" class="btn-cancel" id="mCancel">Cancel</button>
                <button type="button" class="btn-red" id="mSave">Save</button></div>`;
        modalReturn = document.activeElement;
        $('modal').classList.add('active');
        $('mCancel').addEventListener('click', closeModal);
        $('mSave').addEventListener('click', () => { if (onSubmit() !== false) closeModal(); });
        const first = $('mBody').querySelector('input,select,textarea');
        if (first) first.focus();
    }
    function closeModal() {
        $('modal').classList.remove('active');
        if (modalReturn && document.body.contains(modalReturn)) modalReturn.focus();
    }
    const val = id => $(id).value.trim();
    const num = id => Number($(id).value);

    function userForm() {
        openModal('Add Member', 'Create a new FitPass member account.', `
                <div class="field full"><label for="uName">Full name</label><input id="uName" maxlength="50"></div>
                <div class="field full"><label for="uEmail">Email</label><input id="uEmail" type="email" maxlength="80"></div>`, () => {
            const name = val('uName'), email = val('uEmail');
            if (!name || !/^\S+@\S+\.\S+$/.test(email)) { toast('Enter a name and a valid email.', true); return false; }
            if (users.some(u => u.email.toLowerCase() === email.toLowerCase())) { toast('That email is already registered.', true); return false; }
            users.push({ id: Date.now(), name, email, joined: todayKey(), last: todayKey(), status: 'active', plans: {} });
            persist(); renderAll(); toast('Member added.');
        });
    }

    function workoutForm(id) {
        const p = id ? plans[id] : { title: '', cat: 'strength', level: 'Beginner', minutes: 20, desc: '', exercises: [] };
        openModal(id ? 'Edit Workout' : 'Add Workout', 'One exercise per line, written as: Name | 10 Reps • 3 Sets', `
                <div class="field full"><label for="wTitle">Workout name</label><input id="wTitle" maxlength="40" value="${esc(p.title)}"></div>
                <div class="field"><label for="wCat">Category</label><select id="wCat">${Object.entries(catLabels).map(([k, v]) => `<option value="${k}"${p.cat === k ? ' selected' : ''}>${v}</option>`).join('')}</select></div>
                <div class="field"><label for="wLevel">Level</label><select id="wLevel">${levels.map(l => `<option${p.level === l ? ' selected' : ''}>${l}</option>`).join('')}</select></div>
                <div class="field"><label for="wMin">Minutes</label><input id="wMin" type="number" min="5" max="180" value="${p.minutes}"></div>
                <div class="field full"><label for="wDesc">Description</label><input id="wDesc" maxlength="140" value="${esc(p.desc || '')}"></div>
                <div class="field full"><label for="wEx">Exercises</label><textarea id="wEx">${esc(p.exercises.map(e => e.name + ' | ' + e.detail).join('\n'))}</textarea></div>`, () => {
            const title = val('wTitle');
            const exercises = $('wEx').value.split('\n').map(l => l.trim()).filter(Boolean).map(l => {
                const [n, ...rest] = l.split('|');
                return { name: n.trim(), detail: rest.join('|').trim() || '10 Reps • 3 Sets' };
            });
            if (!title || !exercises.length) { toast('Add a name and at least one exercise.', true); return false; }
            const minutes = Math.max(5, num('wMin') || 20);
            const rec = { title, cat: $('wCat').value, level: $('wLevel').value, minutes, desc: val('wDesc'), exercises };
            let key = id;
            if (!key) { key = slug(title); while (plans[key]) key += '1'; }
            plans[key] = rec;
            persist(); renderAll(); toast(id ? 'Workout updated.' : 'Workout added.');
        });
    }

    function foodForm(i) {
        const isEdit = i !== null;
        const f = isEdit ? foods[i] : { name: '', cal: 0, p: 0, c: 0, f: 0 };
        openModal(isEdit ? 'Edit Food' : 'Add Food', 'Nutrition values are per serving.', `
                <div class="field full"><label for="fName">Food name</label><input id="fName" maxlength="60" value="${esc(f.name)}"></div>
                <div class="field"><label for="fCal">Calories (kcal)</label><input id="fCal" type="number" min="0" value="${f.cal}"></div>
                <div class="field"><label for="fP">Protein (g)</label><input id="fP" type="number" min="0" step="0.1" value="${f.p}"></div>
                <div class="field"><label for="fC">Carbs (g)</label><input id="fC" type="number" min="0" step="0.1" value="${f.c}"></div>
                <div class="field"><label for="fF">Fats (g)</label><input id="fF" type="number" min="0" step="0.1" value="${f.f}"></div>`, () => {
            const name = val('fName');
            if (!name) { toast('Enter a food name.', true); return false; }
            const rec = { name, cal: Math.max(0, num('fCal')), p: Math.max(0, num('fP')), c: Math.max(0, num('fC')), f: Math.max(0, num('fF')) };
            if (isEdit) foods[i] = rec; else foods.push(rec);
            persist(); renderAll(); toast(isEdit ? 'Food updated.' : 'Food added.');
        });
    }

    function goalPlanForm(id) {
        const isEdit = !!id;
        const p = isEdit ? goalPlans[id] : { title: '', desc: '', cal: 2000, p: 140, c: 220, f: 65 };
        openModal(isEdit ? 'Edit Nutrition Plan' : 'Add Nutrition Plan', 'Daily targets that members can use as their goals.', `
                <div class="field full"><label for="gpTitle">Plan name</label><input id="gpTitle" maxlength="40" value="${esc(p.title)}"></div>
                <div class="field full"><label for="gpDesc">Short description</label><input id="gpDesc" maxlength="120" value="${esc(p.desc)}"></div>
                <div class="field"><label for="gpCal">Calories (kcal)</label><input id="gpCal" type="number" min="500" value="${p.cal}"></div>
                <div class="field"><label for="gpP">Protein (g)</label><input id="gpP" type="number" min="0" value="${p.p}"></div>
                <div class="field"><label for="gpC">Carbs (g)</label><input id="gpC" type="number" min="0" value="${p.c}"></div>
                <div class="field"><label for="gpF">Fats (g)</label><input id="gpF" type="number" min="0" value="${p.f}"></div>`, () => {
            const title = val('gpTitle');
            if (!title) { toast('Enter a plan name.', true); return false; }
            const rec = { title, desc: val('gpDesc'), cal: num('gpCal'), p: num('gpP'), c: num('gpC'), f: num('gpF') };
            if (!(rec.cal >= 500) || [rec.p, rec.c, rec.f].some(v => !(v >= 0))) { toast('Enter valid targets (calories at least 500).', true); return false; }
            let key = id;
            if (!key) {
                const base = slug(title); key = base;
                for (let n = 2; goalPlans[key]; n++) key = base + n;
            }
            goalPlans[key] = rec;
            persist(); renderAll(); toast(isEdit ? 'Plan updated.' : 'Plan added.');
        });
    }

    function memberGoalForm(userId) {
        const u = users.find(x => String(x.id) === String(userId));
        if (!u) return;
        const g = u.goal || { plan: 'balanced', cal: 2200, p: 150, c: 265, f: 70 };
        const opts = '<option value="custom"' + (goalPlans[g.plan] ? '' : ' selected') + '>Custom goals</option>' +
            Object.entries(goalPlans).map(([id, p]) => `<option value="${esc(id)}"${id === g.plan ? ' selected' : ''}>${esc(p.title)}</option>`).join('');
        openModal('Daily Goals', u.name, `
                <div class="field full"><label for="mgPlan">Nutrition plan</label><select id="mgPlan">${opts}</select></div>
                <div class="field"><label for="mgCal">Calories (kcal)</label><input id="mgCal" type="number" min="500" value="${g.cal}"></div>
                <div class="field"><label for="mgP">Protein (g)</label><input id="mgP" type="number" min="0" value="${g.p}"></div>
                <div class="field"><label for="mgC">Carbs (g)</label><input id="mgC" type="number" min="0" value="${g.c}"></div>
                <div class="field"><label for="mgF">Fats (g)</label><input id="mgF" type="number" min="0" value="${g.f}"></div>`, () => {
            const rec = { plan: val('mgPlan'), cal: num('mgCal'), p: num('mgP'), c: num('mgC'), f: num('mgF') };
            if (!(rec.cal >= 500) || [rec.p, rec.c, rec.f].some(v => !(v >= 0))) { toast('Enter valid goals (calories at least 500).', true); return false; }
            u.goal = rec;
            persist(); renderAll(); toast('Goals saved for ' + u.name + '.');
        });
        $('mgPlan').addEventListener('change', () => {
            const p = goalPlans[$('mgPlan').value];
            if (!p) return;
            $('mgCal').value = p.cal; $('mgP').value = p.p; $('mgC').value = p.c; $('mgF').value = p.f;
        });
        ['mgCal', 'mgP', 'mgC', 'mgF'].forEach(id => $(id).addEventListener('input', () => { $('mgPlan').value = 'custom'; }));
    }

    /* ── events ── */
    $('mClose').addEventListener('click', closeModal);
    $('modal').addEventListener('click', e => { if (e.target === $('modal')) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    $('addUserBtn').addEventListener('click', userForm);
    $('addWorkoutBtn').addEventListener('click', () => workoutForm(null));
    $('addFoodBtn').addEventListener('click', () => foodForm(null));
    $('addGoalPlanBtn').addEventListener('click', () => goalPlanForm(null));
    $('goalSearch').addEventListener('input', renderGoals);
    $('userSearch').addEventListener('input', renderUsers);
    $('foodSearch').addEventListener('input', renderFoods);

    document.addEventListener('click', e => {
        const t = e.target.closest('button');
        if (!t) return;
        if (t.dataset.toggle) {
            const u = users.find(x => String(x.id) === t.dataset.toggle);
            if (u) { u.status = u.status === 'active' ? 'suspended' : 'active'; persist(); renderAll(); toast(u.name + ' is now ' + u.status + '.'); }
        } else if (t.dataset.deluser) {
            const u = users.find(x => String(x.id) === t.dataset.deluser);
            if (u && confirm('Delete ' + u.name + '? This cannot be undone.')) { users = users.filter(x => x !== u); persist(); renderAll(); toast('Member deleted.'); }
        } else if (t.dataset.editw) {
            workoutForm(t.dataset.editw);
        } else if (t.dataset.delw) {
            const p = plans[t.dataset.delw];
            if (p && confirm('Delete "' + p.title + '"?')) { delete plans[t.dataset.delw]; persist(); renderAll(); toast('Workout deleted.'); }
        } else if (t.dataset.editgp) {
            goalPlanForm(t.dataset.editgp);
        } else if (t.dataset.delgp) {
            const p = goalPlans[t.dataset.delgp];
            if (p && confirm('Delete "' + p.title + '"? Members keep their current numbers.')) { delete goalPlans[t.dataset.delgp]; persist(); renderAll(); toast('Plan deleted.'); }
        } else if (t.dataset.editg) {
            memberGoalForm(t.dataset.editg);
        } else if (t.dataset.editf) {
            foodForm(Number(t.dataset.editf));
        } else if (t.dataset.delf) {
            const f = foods[Number(t.dataset.delf)];
            if (f && confirm('Delete "' + f.name + '"?')) { foods.splice(Number(t.dataset.delf), 1); persist(); renderAll(); toast('Food deleted.'); }
        }
    });

    $('exportBtn').addEventListener('click', () => {
        const q = v => '"' + String(v).replace(/"/g, '""') + '"';
        const rows = [['Name', 'Email', 'Joined', 'Last active', 'Workouts', 'Status']]
            .concat(users.map(u => [u.name, u.email, u.joined, u.last, sessions(u), u.status]));
        const blob = new Blob([rows.map(r => r.map(q).join(',')).join('\n')], { type: 'text/csv' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'fitpass-members.csv';
        a.click();
        URL.revokeObjectURL(a.href);
        toast('Members exported.');
    });

    $('resetBtn').addEventListener('click', () => {
        if (!confirm('Reset members, workouts, foods and nutrition plans back to the starting data?')) return;
        users = clone(seedUsers); plans = clone(workoutPlans); foods = clone(foodDb); goalPlans = clone(seedGoalPlans); seedMemberGoals();
        persist(); renderAll(); toast('Demo data reset.');
    });

    /* ── tabs ── */
    function showTab(name) {
        document.querySelectorAll('.tab').forEach(t => t.classList.toggle('active', t.id === 'tab-' + name));
        document.querySelectorAll('[data-tab]').forEach(a => a.classList.toggle('active', a.dataset.tab === name));
        if (name === 'reports') renderReports();
        try { history.replaceState(null, '', '#' + name); } catch (e) { /* blocked in sandboxed previews; safe to ignore */ }
    }
    document.querySelectorAll('[data-tab]').forEach(a => a.addEventListener('click', () => {
        showTab(a.dataset.tab);
        $('nav-menu').classList.remove('active');
        $('hamburger').setAttribute('aria-expanded', 'false');
    }));

    /* ── navbar ── */
    (function () {
        const hamburger = $('hamburger'), navMenu = $('nav-menu'), navbar = $('navbar');
        hamburger.addEventListener('click', () => {
            const open = navMenu.classList.toggle('active');
            hamburger.setAttribute('aria-expanded', open);
        });
        const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 50);
        window.addEventListener('scroll', onScroll);
        onScroll();
    })();

    renderAll();
    const start = location.hash.slice(1);
    if (['overview', 'users', 'workouts', 'foods', 'goals', 'reports'].includes(start)) showTab(start);

}

/* ── Page: register ── */
/* register.html */
function initRegister() {
    const pw = document.getElementById('password');
    const cpw = document.getElementById('confirmPassword');

    function checkLength() {
        pw.classList.toggle('match', pw.value.length >= 8);
    }

    function checkMatch() {
        if (!cpw.value) { cpw.classList.remove('match', 'no-match'); return; }
        const ok = cpw.value === pw.value;
        cpw.classList.toggle('match', ok);
        cpw.classList.toggle('no-match', !ok);
    }

    pw.addEventListener('input', () => { checkLength(); checkMatch(); });
    cpw.addEventListener('input', checkMatch);
}

/* ── Shared: show/hide password (eye icon) ── */
function initPasswordToggle() {
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.classList.toggle('showing', show);
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });
}

/* ── Start-up ── */
const PAGE_INIT = {
    home: initHome,
    workouts: initWorkouts,
    diet: initDiet,
    about: initAbout,
    admin: initAdmin,
    register: initRegister
    /* login only needs the shared password toggle.
       landing (landing.blade.php) only needs the shared initNav: its old inline
       hamburger / scroll script is now covered by it. */
};

function startPage() {
    const page = document.documentElement.dataset.page;
    initPasswordToggle();
    if (page !== 'admin' && document.getElementById('navbar')) initNav();
    if (PAGE_INIT[page]) PAGE_INIT[page]();
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', startPage);
else startPage();