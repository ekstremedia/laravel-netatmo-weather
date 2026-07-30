/**
 * Static Tailwind build for the package's Blade views.
 *
 * The views were originally styled against the Tailwind Play CDN with this
 * exact color palette injected via an inline `tailwind.config` script. That
 * CDN script cannot run on CSP-protected hosts (script-src without the CDN
 * host blocks it), so the CSS is now compiled ahead of time into
 * src/assets/css/netatmo-weather.css and published as a static asset.
 *
 * Rebuild after any view change: `npm run build:css`
 */
module.exports = {
    content: ['./src/resources/views/**/*.blade.php'],
    safelist: [
        // index.blade.php builds these from a PHP match() on module type, so
        // the class scanner never sees the assembled names.
        'text-purple-400',
        'text-cyan-400',
        'text-emerald-400',
        'text-sky-400',
        'text-violet-400',
        // Added at runtime by netatmo-admin.js, never literal in the views.
        'hidden',
        'rotate-180',
        'translate-x-0',
        'translate-x-1',
        'translate-x-7',
        'bg-gray-600',
    ],
    theme: {
        extend: {
            colors: {
                netatmo: {
                    purple: '#8b5cf6',
                    deep: '#6d28d9',
                    dark: '#5b21b6',
                },
                dark: {
                    bg: '#0f0a1f',
                    surface: '#1a1332',
                    elevated: '#251b47',
                    border: '#3d2e6b',
                },
                weather: {
                    warm: '#f59e0b',
                    cool: '#06b6d4',
                },
            },
        },
    },
    plugins: [],
};
