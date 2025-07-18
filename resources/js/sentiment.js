let sentimentChartInstance = null;
let sentimentBarChartInstance = null;
let sentiment24hTrendInstance = null;

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// ==== DOUGHNUT CHART ====
function renderDoughnutChart(range = 'this_month') {
    fetch(`/overview/sentiment-data?range=${range}`)
        .then(res => res.json())
        .then(data => {
            // Memeriksa apakah ada pesan 'no data'
            if (data.message) {
                // Menyembunyikan canvas dan custom legend, serta menampilkan pesan jika tidak ada data
                document.getElementById('sentimentChart').style.display = 'none';  // Menyembunyikan canvas
                document.getElementById('custom-legend').style.display = 'none';  // Menyembunyikan legend
                document.getElementById('noDataMessage').style.display = 'block';  // Menampilkan pesan
                return;  // Menghentikan eksekusi lebih lanjut jika tidak ada data
            }

            // Menyembunyikan pesan, menampilkan canvas dan custom legend jika ada data
            document.getElementById('sentimentChart').style.display = 'block';  // Menampilkan canvas
            document.getElementById('custom-legend').style.display = 'flex';  // Menampilkan legend
            document.getElementById('noDataMessage').style.display = 'none';  // Menyembunyikan pesan

            const total = data.reduce((acc, item) => acc + item.total, 0);
            const colorMap = {
                positive: '#0FAF62',
                neutral: '#7B878C',
                negative: '#E84646'
            };

            const rawData = data.map(item => ({
                sentiment: item.sentiment.toLowerCase(),
                total: item.total
            }));

            const labelMap = Object.fromEntries(
                rawData.map(item => [item.sentiment, item.total])
            );

            const desiredOrder = ['positive', 'neutral', 'negative'];
            const labels = desiredOrder.filter(key => labelMap[key] !== undefined);
            const values = labels.map(key => labelMap[key]);
            const colors = labels.map(label => colorMap[label] || '#ccc');

            // Destroy chart instance jika sudah ada
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

            // Update custom legend
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
            console.error('Error loading sentiment data:', error);
        });
}

// ==== 24H SENTIMENT TREND ==== 
function render24HourTrendChart() {
    fetch('/overview/chart-24h-sentiment')
        .then(res => res.json())
        .then(data => {
            // Mengelompokkan data berdasarkan sentimen
            const sentimentData = {
                positive: {},
                negative: {},
                neutral: {}
            };

            data.forEach(item => {
                const jam = item.jam;
                const sentiment = item.sentiment.toLowerCase();
                const count = item.total;

                if (sentimentData[sentiment]) {
                    sentimentData[sentiment][jam] = count;
                }
            });

            // Labels untuk x-axis: Jam per 4 jam (00:00, 04:00, 08:00, ...)
            const labels = ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'];

            // Dataset untuk sentimen positif, negatif, netral
            const datasetConfig = {
                positive: { label: 'Positive', borderColor: '#0FAF62', backgroundColor: 'rgba(15, 175, 98, 0.2)' },
                negative: { label: 'Negative', borderColor: '#E84646', backgroundColor: 'rgba(232, 70, 70, 0.2)' },
                neutral: { label: 'Neutral', borderColor: '#7B878C', backgroundColor: 'rgba(123, 135, 140, 0.2)' }
            };

            const datasets = Object.entries(sentimentData).map(([key, value]) => ({
                label: datasetConfig[key].label,
                data: labels.map(jam => value[jam] || 0),  // Ambil data untuk jam per 4 jam
                borderColor: datasetConfig[key].borderColor,
                backgroundColor: datasetConfig[key].backgroundColor,
                fill: true,  // Enable filling below the line
                tension: 0.3
            }));

            const ctx = document.getElementById('sentiment24hChart')?.getContext('2d');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,  // Label jam per 4 jam
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        title: {
                            display: false,
                            text: '24-Hour Sentiment Trend'
                        }
                    },
                    scales: {
                        x: {
                            title: { display: false, text: 'Time (4-Hour Intervals)' }
                        },
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 25,
                            ticks: { stepSize: 5 },
                            title: { display: false, text: 'Jumlah Sentimen' }
                        }
                    }
                }
            });
        })
        .catch(err => {
            console.error('Error loading 24h sentiment trend:', err);
        });
}

// ==== BAR CHART ==== 
function renderBarChart(range = 'this_month') {
    fetch(`/api/sentiment-bar-data?range=${range}`)
        .then(res => res.json())
        .then(data => {
            // Memeriksa jika tidak ada data
            if (data.message) {
                // Menyembunyikan canvas dan custom legend, serta menampilkan pesan jika tidak ada data
                document.getElementById('sentimentBarChart').style.display = 'none';  // Menyembunyikan canvas
                document.getElementById('noDataMessagebar').style.display = 'block';  // Menampilkan pesan no data
                return;  // Menghentikan eksekusi lebih lanjut jika tidak ada data
            }

            // Menyembunyikan pesan, menampilkan canvas jika ada data
            document.getElementById('sentimentBarChart').style.display = 'block';  // Menampilkan canvas
            document.getElementById('noDataMessagebar').style.display = 'none';  // Menyembunyikan pesan no data

            // Grouping the data by source and sentiment
            const grouped = {};
            data.forEach(item => {
                const source = item.source;
                const sentiment = item.sentiment.toLowerCase();
                if (!grouped[source]) {
                    grouped[source] = { positive: 0, neutral: 0, negative: 0 };
                }
                grouped[source][sentiment] = item.total;
            });

            const labels = Object.keys(grouped);
            const positiveData = labels.map(src => grouped[src].positive || 0);
            const neutralData = labels.map(src => grouped[src].neutral || 0);
            const negativeData = labels.map(src => grouped[src].negative || 0);

            if (sentimentBarChartInstance) {
                sentimentBarChartInstance.destroy();
            }

            const ctx = document.getElementById('sentimentBarChart')?.getContext('2d');
            if (!ctx) return;

            sentimentBarChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Positive',
                            data: positiveData,
                            backgroundColor: '#0FAF62',
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.5,
                        },
                        {
                            label: 'Neutral',
                            data: neutralData,
                            backgroundColor: '#7B878C',
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.5,
                        },
                        {
                            label: 'Negative',
                            data: negativeData,
                            backgroundColor: '#E84646',
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.5,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
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
            console.error('Error loading sentiment bar data:', err);
            // Handle error when API fails
            document.getElementById('sentimentBarChart').style.display = 'none';  // Menyembunyikan canvas
            document.getElementById('noDataMessage').style.display = 'block';  // Menampilkan pesan error
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
