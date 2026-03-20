/**
 * AquaCRM – App JavaScript
 * Pure vanilla JS. No jQuery.
 * Provides: sidebar helpers, AJAX utilities, form helpers,
 *           notification/toast system, date formatting.
 */

'use strict';

/* ============================================================
   Toast / Notification System
   ============================================================ */
const Toast = (() => {
    let container = null;

    function getContainer() {
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    /**
     * Show a toast notification.
     * @param {string} message
     * @param {'success'|'error'|'warning'|'info'} type
     * @param {number} duration  ms before auto-dismiss (0 = sticky)
     */
    function show(message, type = 'info', duration = 4500) {
        const icons = {
            success: '<svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            error:   '<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            warning: '<svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
            info:    '<svg class="w-5 h-5 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        };

        const el = document.createElement('div');
        el.className = `toast ${type}`;
        el.innerHTML = `
            ${icons[type] || icons.info}
            <p class="flex-1 text-sm text-slate-700">${escapeHtml(message)}</p>
            <button class="text-slate-400 hover:text-slate-600 ml-2 text-lg leading-none" aria-label="Dismiss">&times;</button>
        `;

        el.querySelector('button').addEventListener('click', () => dismiss(el));
        getContainer().appendChild(el);

        if (duration > 0) {
            setTimeout(() => dismiss(el), duration);
        }

        return el;
    }

    function dismiss(el) {
        el.style.opacity = '0';
        el.style.transform = 'translateX(100%)';
        el.style.transition = 'opacity 0.25s, transform 0.25s';
        setTimeout(() => el.remove(), 260);
    }

    return { show, success: m => show(m, 'success'), error: m => show(m, 'error'), warning: m => show(m, 'warning'), info: m => show(m, 'info') };
})();

window.Toast = Toast;

/* ============================================================
   AJAX Helpers
   ============================================================ */
const Http = (() => {
    /**
     * Generic fetch wrapper with JSON handling.
     * @param {string} url
     * @param {RequestInit} options
     * @returns {Promise<any>}
     */
    async function request(url, options = {}) {
        const defaultHeaders = { 'X-Requested-With': 'XMLHttpRequest' };

        // Automatically add CSRF token from meta tag if present
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (csrfMeta) {
            defaultHeaders['X-CSRF-Token'] = csrfMeta.getAttribute('content');
        }

        const mergedOptions = {
            ...options,
            headers: { ...defaultHeaders, ...(options.headers || {}) },
        };

        const response = await fetch(url, mergedOptions);

        if (!response.ok) {
            const text = await response.text();
            throw new Error(text || `HTTP ${response.status}`);
        }

        const contentType = response.headers.get('Content-Type') || '';
        return contentType.includes('application/json') ? response.json() : response.text();
    }

    function get(url, params = {}) {
        const qs = new URLSearchParams(params).toString();
        return request(qs ? `${url}?${qs}` : url);
    }

    function post(url, data = {}) {
        if (data instanceof FormData) {
            return request(url, { method: 'POST', body: data });
        }
        return request(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });
    }

    return { get, post };
})();

window.Http = Http;

/* ============================================================
   Form Helpers
   ============================================================ */
const FormHelper = (() => {
    /**
     * Serialize a form element to a plain object.
     * @param {HTMLFormElement} form
     * @returns {Object}
     */
    function serialize(form) {
        const data = {};
        new FormData(form).forEach((value, key) => {
            if (Object.prototype.hasOwnProperty.call(data, key)) {
                if (!Array.isArray(data[key])) data[key] = [data[key]];
                data[key].push(value);
            } else {
                data[key] = value;
            }
        });
        return data;
    }

    /**
     * Display validation errors on a form.
     * Expects error objects keyed by field name.
     * @param {HTMLFormElement} form
     * @param {Object} errors  { fieldName: 'Error message' }
     */
    function showErrors(form, errors) {
        // Clear previous errors
        form.querySelectorAll('.field-error').forEach(el => el.remove());
        form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error', 'border-red-500'));

        Object.entries(errors).forEach(([field, message]) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (!input) return;

            input.classList.add('border-red-500');

            const errorEl = document.createElement('p');
            errorEl.className = 'field-error text-xs text-red-500 mt-1';
            errorEl.textContent = message;
            input.closest('.form-group') ? input.closest('.form-group').appendChild(errorEl)
                                         : input.insertAdjacentElement('afterend', errorEl);
        });
    }

    /**
     * Set a button to loading state.
     * @param {HTMLButtonElement} btn
     * @param {string} loadingText
     * @returns {Function} restore – call to reset the button
     */
    function setLoading(btn, loadingText = 'Please wait…') {
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin w-4 h-4 mr-2 inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>${escapeHtml(loadingText)}`;
        return () => { btn.disabled = false; btn.innerHTML = original; };
    }

    return { serialize, showErrors, setLoading };
})();

window.FormHelper = FormHelper;

/* ============================================================
   Date Formatting Helpers
   ============================================================ */
const DateUtil = (() => {
    const PAD = n => String(n).padStart(2, '0');

    /**
     * Format a Date (or ISO string) to a human-readable string.
     * @param {Date|string} date
     * @param {'short'|'long'|'datetime'} format
     */
    function format(date, fmt = 'short') {
        const d = date instanceof Date ? date : new Date(date);
        if (isNaN(d)) return '';

        const day   = PAD(d.getDate());
        const month = PAD(d.getMonth() + 1);
        const year  = d.getFullYear();
        const hh    = PAD(d.getHours());
        const mm    = PAD(d.getMinutes());

        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        if (fmt === 'long')     return `${day} ${months[d.getMonth()]} ${year}`;
        if (fmt === 'datetime') return `${day}/${month}/${year} ${hh}:${mm}`;
        return `${day}/${month}/${year}`;
    }

    /**
     * Return a relative time string like "3 hours ago".
     * @param {Date|string} date
     */
    function relative(date) {
        const d    = date instanceof Date ? date : new Date(date);
        const diff = Math.floor((Date.now() - d.getTime()) / 1000);

        if (diff < 60)          return 'just now';
        if (diff < 3600)        return `${Math.floor(diff / 60)} min ago`;
        if (diff < 86400)       return `${Math.floor(diff / 3600)} hr ago`;
        if (diff < 2592000)     return `${Math.floor(diff / 86400)} days ago`;
        return format(d, 'long');
    }

    return { format, relative };
})();

window.DateUtil = DateUtil;

/* ============================================================
   Sidebar toggle (supplement Alpine.js for keyboard / focus)
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    // Auto-dismiss flash messages after 5 s (if not using Alpine)
    document.querySelectorAll('[data-auto-dismiss]').forEach(el => {
        const delay = parseInt(el.dataset.autoDismiss, 10) || 5000;
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 420);
        }, delay);
    });

    // Confirm-before-navigate links
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', e => {
            if (!window.confirm(el.dataset.confirm || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });

    // Relative timestamps
    document.querySelectorAll('[data-timestamp]').forEach(el => {
        const ts = el.dataset.timestamp;
        if (ts) el.textContent = DateUtil.relative(ts);
    });
});

/* ============================================================
   Utility: HTML escape
   ============================================================ */
function escapeHtml(str) {
    return String(str)
        .replace(/&/g,  '&amp;')
        .replace(/</g,  '&lt;')
        .replace(/>/g,  '&gt;')
        .replace(/"/g,  '&quot;')
        .replace(/'/g,  '&#039;');
}

window.escapeHtml = escapeHtml;
