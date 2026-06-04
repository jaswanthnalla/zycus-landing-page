(function () {
    'use strict';

    var PERSONAL = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'aol.com'];
    var NAME_RE = /^[a-zA-Z\s\-']+$/;
    var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function ga(event, params) {
        if (typeof window.gtag === 'function') {
            window.gtag('event', event, params || {});
        }
    }

    function setError(field, message) {
        var errorEl = document.getElementById('err-' + field.id.replace('zycus-', ''));
        if (errorEl) {
            errorEl.textContent = message || '';
        }
        if (message) {
            field.classList.add('zycus-invalid');
        } else {
            field.classList.remove('zycus-invalid');
        }
    }

    function validateField(field) {
        var name = field.name;
        var value = (field.value || '').trim();

        if (field.required && !value) {
            setError(field, errorFor(name, 'required'));
            return false;
        }

        if (name === 'full_name') {
            if (value.length < 2 || value.length > 50 || !NAME_RE.test(value)) {
                setError(field, 'Please enter your full name');
                return false;
            }
        }

        if (name === 'email') {
            if (!EMAIL_RE.test(value)) {
                setError(field, 'Please enter a valid work email');
                return false;
            }
            var domain = value.split('@')[1].toLowerCase();
            if (PERSONAL.indexOf(domain) !== -1) {
                setError(field, 'Please use your work email address');
                return false;
            }
        }

        if (name === 'company' && (value.length < 2 || value.length > 100)) {
            setError(field, 'Please enter your company name');
            return false;
        }

        if (name === 'message' && value.length > 500) {
            setError(field, 'Message must be 500 characters or fewer');
            return false;
        }

        setError(field, '');
        return true;
    }

    function errorFor(name, type) {
        if (type !== 'required') return 'This field is required';
        switch (name) {
            case 'full_name': return 'Please enter your full name';
            case 'email': return 'Please enter a valid work email';
            case 'company': return 'Please enter your company name';
            case 'job_title': return 'Please select your job title';
            case 'country': return 'Please select your country';
            default: return 'This field is required';
        }
    }

    function validateForm(form) {
        var fields = form.querySelectorAll('input[required], select[required], textarea');
        var valid = true;
        for (var i = 0; i < fields.length; i++) {
            if (fields[i].name === 'website') continue;
            if (!validateField(fields[i])) {
                valid = false;
            }
        }
        return valid;
    }

    function refreshSubmitState(form, button) {
        var fields = form.querySelectorAll('input[required], select[required]');
        var ok = true;
        for (var i = 0; i < fields.length; i++) {
            if (!fields[i].value.trim()) ok = false;
        }
        button.disabled = !ok;
    }

    function initForm() {
        var form = document.getElementById('zycus-demo');
        if (!form) return;

        var button = form.querySelector('.zycus-submit');
        var alertBox = form.querySelector('.zycus-form-alert');
        var counter = document.getElementById('zycus-message-count');
        var messageField = document.getElementById('zycus-message');

        if (counter && messageField) {
            var updateCount = function () {
                counter.textContent = messageField.value.length + '/500';
            };
            messageField.addEventListener('input', updateCount);
            updateCount();
        }

        var fields = form.querySelectorAll('input, select, textarea');
        for (var i = 0; i < fields.length; i++) {
            (function (field) {
                if (field.name === 'website') return;
                field.addEventListener('blur', function () {
                    validateField(field);
                });
                field.addEventListener('input', function () {
                    if (field.classList.contains('zycus-invalid')) {
                        validateField(field);
                    }
                    refreshSubmitState(form, button);
                });
                field.addEventListener('change', function () {
                    refreshSubmitState(form, button);
                });
                field.addEventListener('focus', function () {
                    ga('form_field_focus', { field_name: field.name, section: 'demo_form' });
                });
            })(fields[i]);
        }

        refreshSubmitState(form, button);

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!validateForm(form)) {
                ga('form_error', { error_type: 'validation_failed' });
                alertBox.className = 'zycus-form-alert is-error';
                alertBox.textContent = 'Please fix the errors above.';
                var firstInvalid = form.querySelector('.zycus-invalid');
                if (firstInvalid) firstInvalid.focus();
                return;
            }

            if (typeof window.ZycusLanding === 'undefined') {
                alertBox.className = 'zycus-form-alert is-error';
                alertBox.textContent = 'Configuration error. Please refresh the page.';
                return;
            }

            button.disabled = true;
            button.classList.add('is-loading');
            button.querySelector('.zycus-btn-label').textContent = 'Submitting';
            alertBox.className = 'zycus-form-alert';
            alertBox.textContent = '';

            var data = new FormData(form);
            data.append('action', 'zycus_demo_submit');
            data.append('nonce', window.ZycusLanding.nonce);
            data.append('source', 'landing_page');

            fetch(window.ZycusLanding.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: data
            })
                .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
                .then(function (res) {
                    if (res.body && res.body.success) {
                        ga('form_submit', {
                            form_type: 'demo_request',
                            job_title: data.get('job_title'),
                            country: data.get('country')
                        });
                        alertBox.className = 'zycus-form-alert is-success';
                        alertBox.textContent = res.body.message;
                        form.reset();
                        for (var j = 0; j < fields.length; j++) {
                            fields[j].classList.remove('zycus-invalid');
                            setError(fields[j], '');
                        }
                        refreshSubmitState(form, button);
                        if (counter) counter.textContent = '0/500';
                    } else {
                        ga('form_error', { error_type: 'submission_failed' });
                        alertBox.className = 'zycus-form-alert is-error';
                        alertBox.textContent = (res.body && res.body.message) || 'Something went wrong. Please try again.';
                        if (res.body && res.body.errors) {
                            Object.keys(res.body.errors).forEach(function (key) {
                                var fld = form.querySelector('[name="' + key + '"]');
                                if (fld) setError(fld, res.body.errors[key]);
                            });
                        }
                    }
                })
                .catch(function () {
                    ga('form_error', { error_type: 'network_error' });
                    alertBox.className = 'zycus-form-alert is-error';
                    alertBox.textContent = 'Network error. Please check your connection.';
                })
                .then(function () {
                    button.classList.remove('is-loading');
                    button.querySelector('.zycus-btn-label').textContent = 'Get Your Demo Now';
                    refreshSubmitState(form, button);
                });
        });
    }

    function initCounters() {
        var metrics = document.querySelectorAll('.zycus-metric-value');
        if (!metrics.length) return;

        if (!('IntersectionObserver' in window)) {
            // Fallback: set all values immediately
            for (var i = 0; i < metrics.length; i++) {
                var m = metrics[i];
                m.textContent = m.getAttribute('data-target') + (m.getAttribute('data-suffix') || '');
            }
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var el = entry.target;
                if (el.dataset.animated) return;
                el.dataset.animated = '1';
                var target = parseInt(el.getAttribute('data-target'), 10) || 0;
                var suffix = el.getAttribute('data-suffix') || '';
                var duration = 2000;
                var start = performance.now();

                function step(now) {
                    var t = Math.min(1, (now - start) / duration);
                    var eased = 1 - Math.pow(1 - t, 3);
                    el.textContent = Math.round(target * eased) + suffix;
                    if (t < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
                observer.unobserve(el);
            });
        }, { threshold: 0.4 });

        for (var i = 0; i < metrics.length; i++) {
            observer.observe(metrics[i]);
        }
    }

    function initSmoothScroll() {
        document.addEventListener('click', function (e) {
            var target = e.target.closest('a[href^="#"]');
            if (!target) return;
            var id = target.getAttribute('href').slice(1);
            if (!id) return;
            var dest = document.getElementById(id);
            if (!dest) return;
            e.preventDefault();
            dest.scrollIntoView({ behavior: 'smooth', block: 'start' });

            if (target.classList.contains('zycus-btn')) {
                ga('cta_click', {
                    button_location: target.dataset.zycusTrack || 'unknown',
                    button_text: target.textContent.trim(),
                    section: target.dataset.zycusTrack || 'unknown'
                });
            }

            if (id === 'zycus-demo-form') {
                ga('scroll_to_form', { section: 'demo_form' });
            }
        });
    }

    function initStickyCta() {
        var sticky = document.getElementById('zycus-sticky');
        var hero = document.querySelector('.zycus-hero');
        if (!sticky || !hero) return;

        var dismissed = false;
        try {
            dismissed = window.sessionStorage && window.sessionStorage.getItem('zycusStickyDismissed') === '1';
        } catch (e) { /* sessionStorage blocked */ }
        if (dismissed) return;

        sticky.removeAttribute('hidden');
        sticky.hidden = false;

        var close = sticky.querySelector('.zycus-sticky-close');
        if (close) {
            close.addEventListener('click', function () {
                sticky.classList.remove('is-visible');
                try {
                    if (window.sessionStorage) {
                        window.sessionStorage.setItem('zycusStickyDismissed', '1');
                    }
                } catch (e) { /* ignore */ }
            });
        }

        var ticking = false;
        function update() {
            ticking = false;
            var heroBottom = hero.getBoundingClientRect().bottom;
            if (heroBottom <= 0) {
                sticky.classList.add('is-visible');
            } else {
                sticky.classList.remove('is-visible');
            }
        }
        function onScroll() {
            if (ticking) return;
            ticking = true;
            (window.requestAnimationFrame || function (cb) { setTimeout(cb, 16); })(update);
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll, { passive: true });
        update();
    }

    function initScrollTracking() {
        var form = document.getElementById('zycus-demo-form');
        if (!form || !('IntersectionObserver' in window)) return;
        var fired = false;
        var obs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting && !fired) {
                    fired = true;
                    ga('scroll_to_form', { section: 'demo_form', scroll_depth: 50 });
                    obs.disconnect();
                }
            });
        }, { threshold: 0.25 });
        obs.observe(form);
    }

    function initHeroCtaTracking() {
        var btns = document.querySelectorAll('.zycus-hero [data-zycus-track]');
        for (var i = 0; i < btns.length; i++) {
            (function (btn) {
                btn.addEventListener('click', function () {
                    ga('cta_click', {
                        button_location: 'hero',
                        button_text: btn.textContent.trim(),
                        section: 'hero'
                    });
                });
            })(btns[i]);
        }
    }

    function ready(fn) {
        if (document.readyState !== 'loading') return fn();
        document.addEventListener('DOMContentLoaded', fn);
    }

    ready(function () {
        initForm();
        initCounters();
        initSmoothScroll();
        initStickyCta();
        initScrollTracking();
        initHeroCtaTracking();
    });
})();