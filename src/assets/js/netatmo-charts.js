/**
 * Mini measurement charts for the module widgets.
 *
 * Replaces the former inline Alpine.js miniChart/miniBarChart components,
 * which cannot run on CSP-protected hosts (inline scripts are blocked, and
 * Alpine's expression evaluator needs 'unsafe-eval'). Each chart is driven
 * purely by data attributes:
 *
 *   <div data-mini-chart
 *        data-chart-type="line|bar"
 *        data-module-id="…"
 *        data-metric="Temperature"
 *        data-color="#ef4444"
 *        data-unit="°C">
 *       <canvas data-chart-canvas></canvas>
 *       <div data-chart-loading>…</div>
 *   </div>
 *
 * The station uuid is read from <body data-station-uuid="…">.
 */
(function () {
    'use strict';

    function smoothData(data, windowSize) {
        if (data.length < windowSize) return data;

        const smoothed = [];
        const halfWindow = Math.floor(windowSize / 2);

        for (let i = 0; i < data.length; i++) {
            let sum = 0;
            let count = 0;

            for (let j = Math.max(0, i - halfWindow); j <= Math.min(data.length - 1, i + halfWindow); j++) {
                sum += data[j];
                count++;
            }

            smoothed.push(sum / count);
        }

        return smoothed;
    }

    function hourLabels(timestamps) {
        return timestamps.map((t) => new Date(t).getHours() + ':00');
    }

    function createLineChart(canvas, timestamps, values, color, unit) {
        const labels = hourLabels(timestamps);

        const minVal = Math.min(...values);
        const maxVal = Math.max(...values);
        const range = maxVal - minVal;
        const padding = range * 0.15;

        const minAxis = minVal - padding;
        const maxAxis = maxVal + padding;

        const cubicInterpolationMode = (unit === '%' || unit === ' dB') ? 'monotone' : false;
        const tension = (unit === '%' || unit === ' dB') ? 0 : 0.5;

        const ctx = canvas.getContext('2d');

        // For temperature charts, use blue color when below 0
        let borderColor = color;
        let backgroundColor = color + '20';

        if (unit === '°C' && maxVal < 0) {
            // All values below 0, use cool blue
            borderColor = '#3b82f6';
            backgroundColor = '#3b82f620';
        } else if (unit === '°C' && minVal < 0 && maxVal >= 0) {
            // Mixed values, create gradient
            const gradient = ctx.createLinearGradient(0, canvas.height, 0, 0);
            const zeroPoint = (0 - minAxis) / (maxAxis - minAxis);
            gradient.addColorStop(0, '#3b82f6'); // Blue at bottom (cold)
            gradient.addColorStop(zeroPoint, '#3b82f6');
            gradient.addColorStop(zeroPoint, color); // Original color at 0°C
            gradient.addColorStop(1, color); // Original color at top (warm)
            borderColor = gradient;

            const bgGradient = ctx.createLinearGradient(0, canvas.height, 0, 0);
            bgGradient.addColorStop(0, '#3b82f620');
            bgGradient.addColorStop(zeroPoint, '#3b82f620');
            bgGradient.addColorStop(zeroPoint, color + '20');
            bgGradient.addColorStop(1, color + '20');
            backgroundColor = bgGradient;
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    borderColor: borderColor,
                    backgroundColor: backgroundColor,
                    borderWidth: 1.5,
                    tension: tension,
                    cubicInterpolationMode: cubicInterpolationMode,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: unit === '°C' && maxVal < 0 ? '#3b82f6' : color,
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 8,
                        titleFont: { size: 10 },
                        bodyFont: { size: 11 },
                        displayColors: false,
                        callbacks: {
                            label: (context) => `${context.parsed.y.toFixed(1)}${unit}`
                        }
                    }
                },
                scales: {
                    x: { display: false },
                    y: {
                        display: false,
                        min: minAxis,
                        max: maxAxis,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    function createBarChart(canvas, timestamps, values, color, unit) {
        const labels = hourLabels(timestamps);

        const minVal = Math.min(...values);
        const maxVal = Math.max(...values);
        const range = maxVal - minVal;
        const padding = range * 0.1;

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: color + '60',
                    borderColor: color,
                    borderWidth: 1,
                    borderRadius: 2,
                    hoverBackgroundColor: color + 'A0',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 8,
                        titleFont: { size: 10 },
                        bodyFont: { size: 11 },
                        displayColors: false,
                        callbacks: {
                            label: (context) => `${context.parsed.y.toFixed(0)}${unit}`
                        }
                    }
                },
                scales: {
                    x: { display: false },
                    y: {
                        display: false,
                        min: Math.max(0, minVal - padding),
                        max: maxVal + padding,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    async function initChart(el, stationUuid) {
        const { moduleId, metric, color, unit = '', chartType } = el.dataset;
        const canvas = el.querySelector('[data-chart-canvas]');
        const loading = el.querySelector('[data-chart-loading]');

        try {
            const response = await fetch(`/api/netatmo/stations/${stationUuid}/modules/${moduleId}/measurements?period=1day&scale=1hour`);
            const data = await response.json();

            if (data.error) {
                console.error('API error for mini chart:', data);
                return;
            }

            if (canvas && data.measurements && data.measurements.data[metric]) {
                let values = data.measurements.data[metric];

                // Convert km/h to m/s for wind speed
                if (unit === ' m/s' && metric === 'WindStrength') {
                    values = values.map((v) => v / 3.6);
                }

                if (chartType === 'bar') {
                    createBarChart(canvas, data.measurements.timestamps, values, color, unit);
                } else {
                    // Apply moving average smoothing for humidity and slow-changing metrics
                    if (unit === '%' || unit === ' dB') {
                        values = smoothData(values, 3);
                    }

                    createLineChart(canvas, data.measurements.timestamps, values, color, unit);
                }
            }
        } catch (error) {
            console.error('Failed to load mini chart:', error);
        } finally {
            if (loading) {
                loading.classList.add('hidden');
            }
        }
    }

    function init() {
        const stationUuid = document.body.dataset.stationUuid;
        if (!stationUuid || typeof Chart === 'undefined') return;

        document.querySelectorAll('[data-mini-chart]').forEach((el) => initChart(el, stationUuid));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
