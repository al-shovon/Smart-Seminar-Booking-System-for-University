function filterSeminars() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('.seminar-card');
    let found = 0;

    cards.forEach(card => {
        const title = card.getAttribute('data-title').toLowerCase();
        const speaker = card.getAttribute('data-speaker').toLowerCase();
        const venue = card.getAttribute('data-venue').toLowerCase();

        if (title.includes(query) || speaker.includes(query) || venue.includes(query)) {
            card.style.display = 'block';
            found++;
        } else {
            card.style.display = 'none';
        }
    });

    const noResults = document.getElementById('noResults');
    if (noResults) {
        noResults.style.display = found === 0 ? 'block' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });
});

function filterSeminars() {
    const query = document.getElementById('searchInput')
                          .value.toLowerCase();
    const cards = document.querySelectorAll('.seminar-card');
    let found   = 0;

    cards.forEach(card => {
        const title   = card.getAttribute('data-title').toLowerCase();
        const speaker = card.getAttribute('data-speaker').toLowerCase();
        const venue   = card.getAttribute('data-venue').toLowerCase();

        const match = title.includes(query)
                   || speaker.includes(query)
                   || venue.includes(query);

        card.style.display = match ? 'block' : 'none';
        if (match) found++;
    });

    const noRes = document.getElementById('noResults');
    if (noRes) noRes.style.display = found === 0 ? 'block' : 'none';
}

// ── Auto-hide Alerts ───────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

    // Alert auto hide
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.6s, transform 0.6s';
            alert.style.opacity    = '0';
            alert.style.transform  = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 600);
        }, 4000);
    });

    // Seat bar animate on load
    document.querySelectorAll('.seat-bar-fill').forEach(bar => {
        const target = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => { bar.style.width = target; }, 200);
    });

    // Stat cards counter animation
    document.querySelectorAll('.stat-info h3').forEach(el => {
        const target = parseInt(el.innerText.replace('%',''));
        if (isNaN(target)) return;
        const suffix = el.innerText.includes('%') ? '%' : '';
        let current  = 0;
        const step   = Math.ceil(target / 40);
        const timer  = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.innerText = current + suffix;
        }, 30);
    });

    // Active nav link highlight
    const path  = window.location.pathname;
    document.querySelectorAll('.nav-links a').forEach(link => {
        if (link.getAttribute('href') &&
            path.includes(link.getAttribute('href').split('/').pop())) {
            link.style.color = '#e8a020';
        }
    });

});

// ── Confirm Delete ─────────────────────────────────────────
function confirmDelete(name) {
    return confirm(`Are you sure you want to delete "${name}"?\nThis action cannot be undone.`);
}

// ── Print Booking ──────────────────────────────────────────
function printBooking() {
    window.print();
}
