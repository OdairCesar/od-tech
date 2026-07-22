const STORAGE_KEY = 'cookie_consent';

function loadGoogleAnalyticsScript(measurementId) {
    const script = document.createElement('script');
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${measurementId}`;
    document.head.appendChild(script);
}

export function initCookieConsent() {
    const measurementId = window.GA_MEASUREMENT_ID;

    if (!measurementId || window.localStorage.getItem(STORAGE_KEY)) {
        return;
    }

    const banner = document.querySelector('[data-cookie-consent]');

    if (!banner) {
        return;
    }

    banner.classList.remove('hidden');

    banner.querySelector('[data-cookie-accept]')?.addEventListener('click', () => {
        window.localStorage.setItem(STORAGE_KEY, 'accepted');
        banner.classList.add('hidden');
        loadGoogleAnalyticsScript(measurementId);
    });

    banner.querySelector('[data-cookie-reject]')?.addEventListener('click', () => {
        window.localStorage.setItem(STORAGE_KEY, 'rejected');
        banner.classList.add('hidden');
    });
}
