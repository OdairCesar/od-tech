function initMobileMenu() {
    const toggle = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');

    if (!toggle || !menu) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isOpen = menu.classList.toggle('flex');
        menu.classList.toggle('hidden', !isOpen);
        toggle.setAttribute('aria-expanded', String(isOpen));
    });

    menu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            menu.classList.add('hidden');
            menu.classList.remove('flex');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });
}

function initRevealOnScroll() {
    const revealEls = document.querySelectorAll('[data-reveal]');

    if (revealEls.length === 0) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -60px 0px' },
    );

    revealEls.forEach((el) => observer.observe(el));
}

function initCarousels() {
    document.querySelectorAll('[data-carousel]').forEach((carousel) => {
        const track = carousel.querySelector('[data-carousel-track]');
        const prev = carousel.querySelector('[data-carousel-prev]');
        const next = carousel.querySelector('[data-carousel-next]');

        if (!track || !prev || !next) {
            return;
        }

        const scrollByAmount = () => track.clientWidth * 0.85;

        prev.addEventListener('click', () => track.scrollBy({ left: -scrollByAmount(), behavior: 'smooth' }));
        next.addEventListener('click', () => track.scrollBy({ left: scrollByAmount(), behavior: 'smooth' }));

        const pagination = carousel.parentElement?.querySelector('[data-carousel-pagination]');
        const items = Array.from(track.children);
        const dots = pagination ? Array.from(pagination.querySelectorAll('[data-carousel-dot]')) : [];

        if (dots.length === 0 || items.length === 0) {
            return;
        }

        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                const index = Number(dot.dataset.carouselDot);
                items[index]?.scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' });
            });
        });

        const setActiveDot = () => {
            const trackRect = track.getBoundingClientRect();
            let closestIndex = 0;
            let closestDistance = Infinity;

            items.forEach((item, index) => {
                const distance = Math.abs(item.getBoundingClientRect().left - trackRect.left);
                if (distance < closestDistance) {
                    closestDistance = distance;
                    closestIndex = index;
                }
            });

            dots.forEach((dot, index) => dot.classList.toggle('is-active', index === closestIndex));
        };

        track.addEventListener('scroll', () => {
            window.clearTimeout(track._paginationTimeout);
            track._paginationTimeout = window.setTimeout(setActiveDot, 100);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initRevealOnScroll();
    initCarousels();
});
