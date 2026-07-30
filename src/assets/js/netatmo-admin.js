/**
 * Vanilla replacements for the small Alpine.js behaviours in the admin views.
 *
 * Alpine cannot run on CSP-protected hosts (its expression evaluator needs
 * 'unsafe-eval', and the x-data expressions were inline), so each behaviour is
 * a data-attribute hook instead:
 *
 *   [data-sidebar-toggle] / [data-sidebar] / [data-sidebar-overlay]
 *       Mobile sidebar open/close, with click-outside-to-close.
 *   [data-dismiss-alert]
 *       Removes the closest [role="alert"] flash message.
 *   [data-modal-open="name"] / [data-modal="name"] / [data-modal-close]
 *       Delete-confirmation modals. Clicking the backdrop closes too.
 *   [data-expand-toggle] + [data-expand-target]
 *       Collapsible sections (scoped to the closest [data-expandable]).
 *   [data-auto-fetch-url] + [data-loading-row]
 *       Station cards that trigger a first data fetch, then reload the page.
 *   [data-tab-button="name"] / [data-tab-panel="name"]
 *       The station form's tab navigation.
 *   [data-switch="field"] / [data-switch-show="field"]
 *       Toggle switches bound to a hidden input, revealing extra content.
 *   [data-token-manager] and data-token-* hooks
 *       The API-token set/change/remove flow on the station form.
 *   [data-copy-text]
 *       Copies the attribute value to the clipboard.
 *   [data-confirm-submit="message"]
 *       Asks for confirmation before submitting the form.
 */
(function () {
    'use strict';

    function initSidebar() {
        const sidebar = document.querySelector('[data-sidebar]');
        const overlay = document.querySelector('[data-sidebar-overlay]');
        const toggle = document.querySelector('[data-sidebar-toggle]');

        if (!sidebar || !toggle) return;

        const close = () => {
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            if (overlay) overlay.classList.add('hidden');
        };

        const open = () => {
            sidebar.classList.add('translate-x-0');
            sidebar.classList.remove('-translate-x-full');
            if (overlay) overlay.classList.remove('hidden');
        };

        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            sidebar.classList.contains('translate-x-0') ? close() : open();
        });

        if (overlay) overlay.addEventListener('click', close);

        // click.away equivalent: any click outside the sidebar closes it.
        document.addEventListener('click', (event) => {
            if (sidebar.classList.contains('translate-x-0') && !sidebar.contains(event.target)) {
                close();
            }
        });
    }

    function initAlerts() {
        document.querySelectorAll('[data-dismiss-alert]').forEach((button) => {
            button.addEventListener('click', () => {
                const alert = button.closest('[role="alert"]');
                if (alert) alert.remove();
            });
        });
    }

    function initModals() {
        document.querySelectorAll('[data-modal-open]').forEach((button) => {
            button.addEventListener('click', () => {
                const modal = document.querySelector(`[data-modal="${button.dataset.modalOpen}"]`);
                if (modal) modal.classList.remove('hidden');
            });
        });

        document.querySelectorAll('[data-modal]').forEach((modal) => {
            // Backdrop click closes; clicks inside the dialog do not bubble to it.
            modal.addEventListener('click', (event) => {
                if (event.target === modal) modal.classList.add('hidden');
            });

            modal.querySelectorAll('[data-modal-close]').forEach((button) => {
                button.addEventListener('click', () => modal.classList.add('hidden'));
            });
        });
    }

    function initExpanders() {
        document.querySelectorAll('[data-expand-toggle]').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const scope = toggle.closest('[data-expandable]');
                if (!scope) return;

                const target = scope.querySelector('[data-expand-target]');
                if (target) target.classList.toggle('hidden');

                const icon = toggle.querySelector('[data-expand-icon]');
                if (icon) icon.classList.toggle('rotate-180');
            });
        });
    }

    function initAutoFetch() {
        document.querySelectorAll('[data-auto-fetch-url]').forEach(async (card) => {
            try {
                const response = await fetch(card.dataset.autoFetchUrl);
                if (response.ok) {
                    // Reload the page to show the fetched data
                    window.location.reload();
                    return;
                }
            } catch (error) {
                console.error('Failed to fetch data:', error);
            }

            const loading = card.querySelector('[data-loading-row]');
            if (loading) loading.classList.add('hidden');
        });
    }

    function initTabs() {
        const buttons = document.querySelectorAll('[data-tab-button]');
        if (!buttons.length) return;

        const activeClasses = ['border-netatmo-purple', 'text-netatmo-purple'];
        const inactiveClasses = ['border-transparent', 'text-purple-400', 'hover:text-purple-300', 'hover:border-purple-500/30'];

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                buttons.forEach((other) => {
                    const isActive = other === button;
                    other.classList.remove(...(isActive ? inactiveClasses : activeClasses));
                    other.classList.add(...(isActive ? activeClasses : inactiveClasses));
                });

                document.querySelectorAll('[data-tab-panel]').forEach((panel) => {
                    panel.classList.toggle('hidden', panel.dataset.tabPanel !== button.dataset.tabButton);
                });
            });
        });
    }

    function initSwitches() {
        document.querySelectorAll('[data-switch]').forEach((button) => {
            const field = button.dataset.switch;
            const onClass = button.dataset.switchOnClass;
            const input = button.parentElement.querySelector(`input[name="${field}"]`);
            const knob = button.querySelector('[data-switch-knob]');

            button.addEventListener('click', () => {
                const on = button.dataset.switchState !== 'on';
                button.dataset.switchState = on ? 'on' : 'off';

                button.classList.toggle(onClass, on);
                button.classList.toggle('bg-gray-600', !on);
                if (knob) {
                    knob.classList.toggle('translate-x-7', on);
                    knob.classList.toggle('translate-x-1', !on);
                }
                if (input) input.value = on ? '1' : '0';

                document.querySelectorAll(`[data-switch-show="${field}"]`).forEach((el) => {
                    el.classList.toggle('hidden', !on);
                });
            });
        });
    }

    function initTokenManager() {
        const manager = document.querySelector('[data-token-manager]');
        if (!manager) return;

        const state = {
            hasToken: manager.dataset.hasToken === '1',
            editing: false,   // token input visible (was: generateToken)
            removing: false,  // remove confirmation visible (was: removeToken)
        };

        const tokenInput = manager.querySelector('#api_token');
        const removeFlag = manager.querySelector('#remove_api_token');

        const show = (el, visible) => el.classList.toggle('hidden', !visible);

        function render() {
            manager.querySelectorAll('[data-token-status]').forEach((el) => show(el, state.hasToken && !state.editing && !state.removing));
            manager.querySelectorAll('[data-token-set]').forEach((el) => show(el, !state.hasToken && !state.editing));
            manager.querySelectorAll('[data-token-remove-confirm]').forEach((el) => show(el, state.removing));
            manager.querySelectorAll('[data-token-input]').forEach((el) => show(el, state.editing));
            manager.querySelectorAll('[data-token-change-warning]').forEach((el) => show(el, state.hasToken));
            manager.querySelectorAll('[data-token-show-when-set]').forEach((el) => show(el, state.hasToken));
            manager.querySelectorAll('[data-token-show-when-unset]').forEach((el) => show(el, !state.hasToken));
        }

        function randomToken() {
            const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
            const bytes = new Uint8Array(48);
            crypto.getRandomValues(bytes);
            return Array.from(bytes, (b) => alphabet[b % alphabet.length]).join('');
        }

        manager.querySelectorAll('[data-token-action]').forEach((button) => {
            button.addEventListener('click', () => {
                switch (button.dataset.tokenAction) {
                    case 'change':
                        state.editing = true;
                        break;
                    case 'remove':
                        state.removing = true;
                        break;
                    case 'cancel-remove':
                        state.removing = false;
                        if (removeFlag) removeFlag.value = '';
                        break;
                    case 'confirm-remove':
                        state.hasToken = false;
                        state.removing = false;
                        state.editing = false;
                        if (tokenInput) tokenInput.value = '';
                        if (removeFlag) removeFlag.value = '1';
                        break;
                    case 'generate':
                        if (tokenInput) tokenInput.value = randomToken();
                        state.hasToken = true;
                        break;
                    case 'cancel-input':
                        state.editing = false;
                        if (tokenInput) tokenInput.value = '';
                        break;
                }
                render();
            });
        });
    }

    function initCopyButtons() {
        document.querySelectorAll('[data-copy-text]').forEach((button) => {
            button.addEventListener('click', () => {
                navigator.clipboard.writeText(button.dataset.copyText);
            });
        });
    }

    function initConfirmSubmit() {
        document.querySelectorAll('form[data-confirm-submit]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (!window.confirm(form.dataset.confirmSubmit)) {
                    event.preventDefault();
                }
            });
        });
    }

    function init() {
        initSidebar();
        initAlerts();
        initModals();
        initExpanders();
        initAutoFetch();
        initTabs();
        initSwitches();
        initTokenManager();
        initCopyButtons();
        initConfirmSubmit();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
