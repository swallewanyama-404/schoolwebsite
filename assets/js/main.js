// MAIN.JS — Navigation toggle & AJAX news loader

// Mobile nav toggle
const burger = document.getElementById('burger');
const navLinks = document.getElementById('nav-links');

if (burger) {
    burger.addEventListener('click', () => {
        navLinks.classList.toggle('open');
    });
}

// AJAX — Load latest news on homepage without page refresh
async function loadNews() {
    const container = document.getElementById('news-feed');
    if (!container) return; // Only runs if #news-feed exists on the page

    try {
        const res = await fetch(`${window.BASE_URL}api/news.php?limit=3`);
        if (!res.ok) {
            throw new Error(`HTTP error! Status: ${res.status}`);
        }
        const data = await res.json();
        if (data.length === 0) {
            container.innerHTML = '<p style="text-align:center;grid-column:1/-1;color:var(--gray-400);">No news available yet.</p>';
            return;
        }

        container.innerHTML = '';
        data.forEach(item => {
            const div = document.createElement('div');
            div.className = 'news-card';
            const dateStr = item.published_at
                ? new Date(item.published_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
                : '';
            div.innerHTML = `
                <div class="news-card-img">📰</div>
                <div class="news-card-body">
                    <h3>${item.title}</h3>
                    <p>${(item.body || '').substring(0, 100)}...</p>
                    ${dateStr ? `<p class="news-card-meta">${dateStr}</p>` : ''}
                </div>
            `;
            container.appendChild(div);
        });
    } catch (err) {
        console.error('Failed to load news:', err);
        container.innerHTML = '<p style="text-align:center;grid-column:1/-1;color:var(--gray-400);">Unable to load news right now.</p>';
    }
}

loadNews();
