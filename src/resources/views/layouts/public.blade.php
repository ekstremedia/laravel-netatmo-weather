{{-- resources/views/layouts/public.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $weatherStation->station_name ?? config('netatmo-weather.name') }} - Weather Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
          integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        html, body {
            height: 100%;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1332;
        }

        ::-webkit-scrollbar-thumb {
            background: #6d28d9;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #8b5cf6;
        }

        /* Fixed background gradient */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(ellipse at top left, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                        radial-gradient(ellipse at bottom right, rgba(109, 40, 217, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'netatmo': {
                            'purple': '#8b5cf6',
                            'deep': '#6d28d9',
                            'dark': '#5b21b6',
                        },
                        'dark': {
                            'bg': '#0f0a1f',
                            'surface': '#1a1332',
                            'elevated': '#251b47',
                            'border': '#3d2e6b',
                        },
                        'weather': {
                            'warm': '#f59e0b',
                            'cool': '#06b6d4',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-bg min-h-screen text-slate-100">
    @yield('content')

    <script>
        // Mini chart component for module widgets
        window.miniChart = function(moduleId, dataType, color, unit) {
            return {
                loading: true,
                chart: null,

                init() {
                    // Wait for Chart.js
                    if (typeof Chart === 'undefined') {
                        setTimeout(() => this.init(), 100);
                        return;
                    }

                    this.loadData();
                },

                async loadData() {
                    try {
                        const response = await fetch(`/api/netatmo/stations/{{ $weatherStation->uuid }}/modules/${moduleId}/measurements?period=1day&scale=1hour`);
                        const data = await response.json();

                        if (data.error) {
                            console.error('API error for mini chart:', data);
                            this.loading = false;
                            return;
                        }

                        if (data.measurements && data.measurements.data[dataType]) {
                            let values = data.measurements.data[dataType];

                            // Convert km/h to m/s for wind speed
                            if (unit === ' m/s' && dataType === 'WindStrength') {
                                values = values.map(v => v / 3.6);
                            }

                            // Apply moving average smoothing for humidity and slow-changing metrics
                            if (unit === '%' || unit === ' dB') {
                                values = this.smoothData(values, 3);
                            }

                            this.createChart(data.measurements.timestamps, values, color, unit);
                        }
                    } catch (error) {
                        console.error('Failed to load mini chart:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                smoothData(data, windowSize) {
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
                },

                createChart(timestamps, values, color, unit) {
                    const canvas = this.$refs.canvas;
                    if (!canvas) return;

                    const labels = timestamps.map(t => {
                        const date = new Date(t);
                        return date.getHours() + ':00';
                    });

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

                    this.chart = new Chart(ctx, {
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
            }
        };

        // Mini bar chart component for noise levels
        window.miniBarChart = function(moduleId, dataType, color, unit) {
            return {
                loading: true,
                chart: null,

                init() {
                    // Wait for Chart.js
                    if (typeof Chart === 'undefined') {
                        setTimeout(() => this.init(), 100);
                        return;
                    }

                    this.loadData();
                },

                async loadData() {
                    try {
                        const response = await fetch(`/api/netatmo/stations/{{ $weatherStation->uuid }}/modules/${moduleId}/measurements?period=1day&scale=1hour`);
                        const data = await response.json();

                        if (data.error) {
                            console.error('API error for mini bar chart:', data);
                            this.loading = false;
                            return;
                        }

                        if (data.measurements && data.measurements.data[dataType]) {
                            this.createBarChart(data.measurements.timestamps, data.measurements.data[dataType], color, unit);
                        }
                    } catch (error) {
                        console.error('Failed to load mini bar chart:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                createBarChart(timestamps, values, color, unit) {
                    const canvas = this.$refs.canvas;
                    if (!canvas) return;

                    const labels = timestamps.map(t => {
                        const date = new Date(t);
                        return date.getHours() + ':00';
                    });

                    const minVal = Math.min(...values);
                    const maxVal = Math.max(...values);
                    const range = maxVal - minVal;
                    const padding = range * 0.1;

                    const ctx = canvas.getContext('2d');
                    this.chart = new Chart(ctx, {
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
            }
        };
    </script>
</body>
</html>
