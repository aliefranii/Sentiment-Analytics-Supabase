let sentimentChartInstance = null;
let sentimentBarChartInstance = null;
let sentiment24hTrendInstance = null;

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function renderDoughnutChart(range = 'this_month') {
    fetch(`/overview/sentiment-data?range=${range}`)
        .then(res => res.json())
        .then(data => {
            if (!data || data.message || data.length === 0) {
                document.getElementById('sentimentChart').style.display = 'none';
                document.getElementById('custom-legend').style.display = 'none';
                document.getElementById('noDataMessage').style.display = 'block';
                return;
            }

            document.getElementById('sentimentChart').style.display = 'block';
            document.getElementById('custom-legend').style.display = 'flex';
            document.getElementById('noDataMessage').style.display = 'none';

            const total = data.reduce((acc, item) => acc + item.total, 0);

            if (total === 0) {
                document.getElementById('noDataMessage').textContent = 'Data tersedia, tapi semua total = 0';
                document.getElementById('noDataMessage').style.display = 'block';
                document.getElementById('sentimentChart').style.display = 'none';
                document.getElementById('custom-legend').style.display = 'none';
                return;
            }

            const colorMap = {
                positive: '#0FAF62',
                neutral: '#7B878C',
                negative: '#E84646',
                default: '#ccc'
            };

            const standardizeSentiment = (s) => {
                const str = s.toLowerCase();
                if (str === 'positif') return 'positive';
                if (str === 'netral') return 'neutral';
                if (str === 'negatif') return 'negative';
                return str;
            };

            const rawData = data
                .filter(item => item.sentimen)
                .map(item => ({
                    sentiment: standardizeSentiment(item.sentimen),
                    total: item.total
                }));

            const labelMap = Object.fromEntries(
                rawData.map(item => [item.sentiment, item.total])
            );

            const labels = Object.keys(labelMap);
            const values = labels.map(key => labelMap[key]);
            const colors = labels.map(label => colorMap[label] || colorMap.default);

            if (sentimentChartInstance) {
                sentimentChartInstance.destroy();
            }

            const ctx = document.getElementById('sentimentChart')?.getContext('2d');
            if (!ctx) return;

            sentimentChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels.map(capitalize),
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const val = context.parsed;
                                    const percent = ((val / total) * 100).toFixed(1);
                                    return `${context.label}: ${val} (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });

            const legendContainer = document.getElementById('custom-legend');
            legendContainer.innerHTML = '';

            labels.forEach((label, i) => {
                const percent = ((values[i] / total) * 100).toFixed(0);
                const item = document.createElement('div');
                item.className = 'legend-item flex items-center gap-2 text-sm text-gray-700 font-medium';

                const dot = document.createElement('span');
                dot.className = 'legend-color w-3 h-3 rounded-full inline-block';
                dot.style.backgroundColor = colors[i];

                const text = document.createElement('span');
                text.textContent = `${capitalize(label)} : ${percent}%`;

                item.appendChild(dot);
                item.appendChild(text);
                legendContainer.appendChild(item);
            });
        })
        .catch(error => {
            document.getElementById('sentimentChart').style.display = 'none';
            document.getElementById('custom-legend').style.display = 'none';
            document.getElementById('noDataMessage').style.display = 'block';
            document.getElementById('noDataMessage').textContent = 'Terjadi kesalahan saat memuat data.';
        });
}

function render24HourTrendChart() {
    fetch('/overview/chart-24h-sentiment')
        .then(res => res.json())
        .then(data => {
            console.log('Data diterima:', data); // Verifikasi data yang diterima

            // Inisialisasi sentimentData dengan nilai 0 untuk setiap jam
            const sentimentData = {
                positive: { '0:00': 0, '4:00': 0, '8:00': 0, '12:00': 0, '16:00': 0, '20:00': 0 },
                negative: { '0:00': 0, '4:00': 0, '8:00': 0, '12:00': 0, '16:00': 0, '20:00': 0 },
                neutral: { '0:00': 0, '4:00': 0, '8:00': 0, '12:00': 0, '16:00': 0, '20:00': 0 }
            };

            // Memproses data yang diterima
            data.forEach(item => {
                const jam = item.hour;
                const positiveCount = item.positive || 0; // Menggunakan angka atau 0 jika tidak ada
                const neutralCount = item.neutral || 0;
                const negativeCount = item.negative || 0;

                // Memastikan jam valid
                if (sentimentData.positive[jam] !== undefined) {
                    sentimentData.positive[jam] += positiveCount;
                    sentimentData.neutral[jam] += neutralCount;
                    sentimentData.negative[jam] += negativeCount;
                } else {
                    console.warn(`Jam ${jam} tidak ditemukan dalam sentimentData`);
                }
            });

            console.log('Formatted sentimentData:', sentimentData); // Verifikasi data yang diproses

            const labels = ['0:00', '4:00', '8:00', '12:00', '16:00', '20:00'];

            const datasetConfig = {
                positive: { label: 'Positive', borderColor: '#0FAF62', backgroundColor: 'rgba(15, 175, 98, 0.2)' },
                negative: { label: 'Negative', borderColor: '#E84646', backgroundColor: 'rgba(232, 70, 70, 0.2)' },
                neutral: { label: 'Neutral', borderColor: '#7B878C', backgroundColor: 'rgba(123, 135, 140, 0.2)' }
            };

            // Menangani nilai yang hilang dan memastikan setiap jam terisi data
            const datasets = Object.entries(sentimentData).map(([key, value]) => {
                const dataForSentiment = labels.map(jam => value[jam] || 0); // Set default 0 jika data tidak ada

                console.log(`${key} data:`, dataForSentiment); // Debugging data yang akan digunakan

                return {
                    label: datasetConfig[key].label,
                    data: dataForSentiment,
                    borderColor: datasetConfig[key].borderColor,
                    backgroundColor: datasetConfig[key].backgroundColor,
                    fill: true,
                    tension: 0.3
                };
            });

            console.log('Chart data:', {
                labels,
                positiveData: sentimentData.positive,
                negativeData: sentimentData.negative,
                neutralData: sentimentData.neutral
            });

            const ctx = document.getElementById('sentiment24hChart')?.getContext('2d');
            if (!ctx) {
                console.error('Konteks canvas tidak ditemukan');
                return;
            }

            // Hancurkan chart sebelumnya jika ada
            if (sentiment24hTrendInstance) {
                sentiment24hTrendInstance.destroy();
            }

            sentiment24hTrendInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true },
                        title: {
                            display: false,
                            text: 'Trend Sentimen 24 Jam'
                        }
                    },
                    scales: {
                        x: {
                            title: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            max: 25,
                            min: 0,
                            ticks: { stepSize: 5 },
                            title: { display: false }
                        }
                    }
                }
            });
        })
        .catch(err => {
            console.error('Error memuat data trend sentiment 24 jam:', err);
        });
}

function renderBarChart(range = 'this_month') {
    fetch(`/api/sentiment-bar-data?range=${range}`)
        .then(res => res.json())
        .then(data => {
            // Cek apakah data yang diterima adalah objek dengan pesan
            if (data.message) {
                document.getElementById('sentimentBarChart').style.display = 'none';
                document.getElementById('noDataMessagebar').style.display = 'block';
                return; // Keluar dari fungsi jika ada pesan
            }

            // Cek apakah data yang diterima sudah benar (array)
            if (!Array.isArray(data)) {
                document.getElementById('sentimentBarChart').style.display = 'none';
                document.getElementById('noDataMessagebar').style.display = 'block';
                return;
            }

            // Tangani jika tidak ada data
            if (data.length === 0) {
                document.getElementById('sentimentBarChart').style.display = 'none';
                document.getElementById('noDataMessagebar').style.display = 'block';
                return;
            }

            document.getElementById('sentimentBarChart').style.display = 'block';
            document.getElementById('noDataMessagebar').style.display = 'none';

            const grouped = {};

            // Mengelompokkan data berdasarkan 'source' dan 'sentimen'
            data.forEach(item => {
                if (!item.source || (item.positif === undefined && item.netral === undefined && item.negatif === undefined)) {
                    return; // Skip jika tidak ada data sama sekali untuk sentimen
                }

                const source = item.source;
                const positif = item.positif || 0; // Anggap 0 jika tidak ada data
                const netral = item.netral || 0;   // Anggap 0 jika tidak ada data
                const negatif = item.negatif || 0; // Anggap 0 jika tidak ada data

                // Inisialisasi grup jika belum ada
                if (!grouped[source]) {
                    grouped[source] = { positif: 0, netral: 0, negatif: 0 };
                }

                // Tambahkan data untuk masing-masing sentimen
                grouped[source].positif += positif;
                grouped[source].netral += netral;
                grouped[source].negatif += negatif;
            });

            // Ambil labels (source)
            const labels = Object.keys(grouped);
            const positifData = labels.map(src => grouped[src].positif);
            const netralData = labels.map(src => grouped[src].netral);
            const negatifData = labels.map(src => grouped[src].negatif);

            // Hancurkan chart sebelumnya jika ada
            if (sentimentBarChartInstance) {
                sentimentBarChartInstance.destroy();
            }

            const ctx = document.getElementById('sentimentBarChart')?.getContext('2d');
            if (!ctx) {
                return;
            }

            // Buat chart baru
            sentimentBarChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Positive',
                            data: positifData,
                            backgroundColor: '#0FAF62',
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.6,
                        },
                        {
                            label: 'Neutral',
                            data: netralData,
                            backgroundColor: '#7B878C',
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.6,
                        },
                        {
                            label: 'Negative',
                            data: negatifData,
                            backgroundColor: '#E84646',
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `${context.dataset.label}: ${context.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { font: { size: 12 } }
                        },
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 25,
                            ticks: { stepSize: 5 }
                        }
                    }
                }
            });
        })
        .catch(err => {
            document.getElementById('sentimentBarChart').style.display = 'none';
            document.getElementById('noDataMessagebar').style.display = 'block';
        });
}

document.addEventListener('DOMContentLoaded', function () {
    // Doughnut dropdown handler
    const doughnutItems = document.querySelectorAll('.range-option-doughnut');
    const doughnutBtn = document.getElementById('sentimentDropdownBtn');

    doughnutItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const selectedRange = this.dataset.range;

            doughnutBtn.innerHTML = `
                ${this.innerText}
                <svg class="w-2.5 h-2.5 ms-1.5" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="m1 1 4 4 4-4" />
                </svg>
            `;
            renderDoughnutChart(selectedRange);
        });
    });

    // Bar chart dropdown handler
    const barItems = document.querySelectorAll('.range-option-bar');
    const barBtn = document.getElementById('barDropdownBtn');

    barItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const selectedRange = this.dataset.range;

            barBtn.innerHTML = `
                ${this.innerText}
                <svg class="w-2.5 h-2.5 ms-1.5" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="m1 1 4 4 4-4" />
                </svg>
            `;
            renderBarChart(selectedRange);
        });
    });

    // Default load
    renderDoughnutChart();
    renderBarChart();
    render24HourTrendChart();
});

