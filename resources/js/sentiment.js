let sentimentChartInstance = null;
let sentimentBarChartInstance = null;
let sentiment24hTrendInstance = null;

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function renderDoughnutChart(range = 'this_month') {
  const chartEl   = document.getElementById('sentimentChart');
  const legendEl  = document.getElementById('custom-legend');
  const noDataEl  = document.getElementById('noDataMessage');
  const noDataTxt = document.getElementById('noDataText'); // opsional, kalau ada

  // === Saat ganti waktu: sembunyikan & hancurkan chart lama ===
  if (window.sentimentChartInstance) {
    window.sentimentChartInstance.destroy();
    window.sentimentChartInstance = null;
  }
  chartEl.style.display  = 'none';
  legendEl.style.display = 'none';
  legendEl.innerHTML     = '';
  // noDataMessage jangan diubah di sini; biar ditentukan setelah fetch

  fetch(`/overview/sentiment-data?range=${range}`)
    .then(res => res.json())
    .then(data => {
      // --- kondisi: tidak ada data/response error ---
      if (!Array.isArray(data) || data.length === 0 || data.message) {
        if (noDataTxt) noDataTxt.textContent = 'Data Tidak Ditemukan!';
        noDataEl.style.display = 'block';   // tampilkan ONLY noDataMessage
        return;
      }

      const total = data.reduce((acc, item) => acc + (item?.total || 0), 0);
      if (total === 0) {
        if (noDataTxt) noDataTxt.textContent = 'Data tersedia, tapi semua total = 0';
        noDataEl.style.display = 'block';   // tampilkan ONLY noDataMessage
        return;
      }

      // --- ada data: sembunyikan noDataMessage, tampilkan chart + legend ---
      noDataEl.style.display = 'none';
      chartEl.style.display  = 'block';
      legendEl.style.display = 'flex';

      const colorMap = { positive:'#0FAF62', neutral:'#7B878C', negative:'#E84646', default:'#ccc' };
      const standardize = s => {
        const str = (s || '').toLowerCase();
        if (str === 'positif') return 'positive';
        if (str === 'netral')  return 'neutral';
        if (str === 'negatif') return 'negative';
        return str;
        };

      const raw = data
        .filter(i => i?.sentimen != null)
        .map(i => ({ sentiment: standardize(i.sentimen), total: i.total || 0 }));

      // gabungkan jika ada label duplikat
      const labelMap = {};
      raw.forEach(({ sentiment, total }) => {
        labelMap[sentiment] = (labelMap[sentiment] || 0) + total;
      });

      const labels = Object.keys(labelMap);
      const values = labels.map(k => labelMap[k]);
      const colors = labels.map(l => colorMap[l] || colorMap.default);

      const ctx = chartEl.getContext('2d');
      window.sentimentChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels.map(capitalize),
          datasets: [{
            data: values,
            backgroundColor: colors,
            borderColor: '#fff',
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
                label: (context) => {
                  const val = context.parsed || 0;
                  const percent = ((val / total) * 100).toFixed(1);
                  return `${context.label}: ${val} (${percent}%)`;
                }
              }
            }
          }
        }
      });

      // render legend
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
        legendEl.appendChild(item);
      });
    })
    .catch(() => {
      // error fetch → tampilkan ONLY noDataMessage
      if (noDataTxt) noDataTxt.textContent = 'Data Tidak Ditemukan!';
      noDataEl.style.display = 'block';
      chartEl.style.display  = 'none';
      legendEl.style.display = 'none';
      legendEl.innerHTML     = '';
    });
}

function render24HourTrendChart() {
    fetch('/overview/chart-24h-sentiment')
        .then(res => res.json())
        .then(data => {
            const sentimentData = {
                positive: { '0:00': 0, '4:00': 0, '8:00': 0, '12:00': 0, '16:00': 0, '20:00': 0 },
                negative: { '0:00': 0, '4:00': 0, '8:00': 0, '12:00': 0, '16:00': 0, '20:00': 0 },
                neutral:  { '0:00': 0, '4:00': 0, '8:00': 0, '12:00': 0, '16:00': 0, '20:00': 0 }
            };

            data.forEach(item => {
                const jam = item.hour;
                if (sentimentData.positive[jam] !== undefined) {
                    sentimentData.positive[jam] += item.positive || 0;
                    sentimentData.neutral[jam]  += item.neutral  || 0;
                    sentimentData.negative[jam] += item.negative || 0;
                }
            });

            const labels = ['0:00', '4:00', '8:00', '12:00', '16:00', '20:00'];
            const datasetConfig = {
                positive: { label: 'Positive', borderColor: '#0FAF62', backgroundColor: 'rgba(15, 175, 98, 0.2)' },
                negative: { label: 'Negative', borderColor: '#E84646', backgroundColor: 'rgba(232, 70, 70, 0.2)' },
                neutral:  { label: 'Neutral',  borderColor: '#7B878C', backgroundColor: 'rgba(123, 135, 140, 0.2)' }
            };

            const datasets = Object.keys(sentimentData).map(key => ({
                label: datasetConfig[key].label,
                data: labels.map(jam => sentimentData[key][jam]),
                borderColor: datasetConfig[key].borderColor,
                backgroundColor: datasetConfig[key].backgroundColor,
                fill: true,
                tension: 0.3
            }));

            if (sentiment24hTrendInstance) sentiment24hTrendInstance.destroy();
            const ctx = document.getElementById('sentiment24hChart')?.getContext('2d');
            if (!ctx) return;

            sentiment24hTrendInstance = new Chart(ctx, {
                type: 'line',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true } },
                    scales: {
                        x: { display: true },
                        y: { beginAtZero: true, max: 25, min: 0, ticks: { stepSize: 5 } }
                    }
                }
            });
        })
        .catch(() => {});
}

function renderBarChart(range = 'this_month') {
  const canvasEl   = document.getElementById('sentimentBarChart');
  const noDataEl   = document.getElementById('noDataMessagebar');

  // Reset: hancurkan chart lama & sembunyikan canvas, biarkan noData ditentukan setelah fetch
  if (window.sentimentBarChartInstance) {
    window.sentimentBarChartInstance.destroy();
    window.sentimentBarChartInstance = null;
  }
  canvasEl.style.display = 'none';
  noDataEl.style.display = 'none';

  fetch(`/api/sentiment-bar-data?range=${range}`)
    .then(res => res.json())
    .then(data => {
      // Validasi dasar
      if (!Array.isArray(data) || data.length === 0 || data.message) {
        noDataEl.style.display = 'block';
        return;
      }

      // Group per source
      const grouped = {};
      let grandTotal = 0;
      data.forEach(item => {
        const src = item.source;
        if (!src) return;
        if (!grouped[src]) grouped[src] = { positif: 0, netral: 0, negatif: 0 };
        const p = item.positif || 0;
        const n = item.netral  || 0;
        const g = item.negatif || 0;
        grouped[src].positif += p;
        grouped[src].netral  += n;
        grouped[src].negatif += g;
        grandTotal += p + n + g;
      });

      // Jika setelah agregasi total tetap 0 → no data
      if (grandTotal === 0) {
        noDataEl.style.display = 'block';
        return;
      }

      const labels      = Object.keys(grouped);
      const positifData = labels.map(src => grouped[src].positif);
      const netralData  = labels.map(src => grouped[src].netral);
      const negatifData = labels.map(src => grouped[src].negatif);

      // Render chart
      const ctx = canvasEl.getContext('2d');
      window.sentimentBarChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            { label: 'Positive', data: positifData, backgroundColor: '#0FAF62', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.6 },
            { label: 'Neutral',  data: netralData,  backgroundColor: '#7B878C', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.6 },
            { label: 'Negative', data: negatifData, backgroundColor: '#E84646', borderRadius: 4, barPercentage: 0.6, categoryPercentage: 0.6 }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            tooltip: {
              callbacks: {
                label: (ctx) => `${ctx.dataset.label}: ${ctx.raw}`
              }
            }
          },
          scales: {
            x: { ticks: { font: { size: 12 } } },
            y: { beginAtZero: true, ticks: { stepSize: 50 } }
          }
        }
      });

      // Tampilkan canvas, sembunyikan no-data
      noDataEl.style.display = 'none';
      canvasEl.style.display = 'block';
    })
    .catch(() => {
      // Error fetch → tampilkan only no-data
      if (window.sentimentBarChartInstance) {
        window.sentimentBarChartInstance.destroy();
        window.sentimentBarChartInstance = null;
      }
      canvasEl.style.display = 'none';
      noDataEl.style.display = 'block';
    });
}


document.addEventListener('DOMContentLoaded', function () {
    // Doughnut dropdown handler
    document.querySelectorAll('.range-option-doughnut').forEach(item => {
        item.addEventListener('click', e => {
            e.preventDefault();
            const range = e.target.dataset.range;
            document.getElementById('sentimentDropdownBtn').innerHTML =
                `${e.target.innerText}<svg class="w-2.5 h-2.5 ms-1.5" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>`;
            renderDoughnutChart(range);
        });
    });

    // Bar chart dropdown handler
    document.querySelectorAll('.range-option-bar').forEach(item => {
        item.addEventListener('click', e => {
            e.preventDefault();
            const range = e.target.dataset.range;
            document.getElementById('barDropdownBtn').innerHTML =
                `${e.target.innerText}<svg class="w-2.5 h-2.5 ms-1.5" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>`;
            renderBarChart(range);
        });
    });

    renderDoughnutChart();
    renderBarChart();
    render24HourTrendChart();
});