// ===== Global instances =====
let sentimentChartInstance = null;
let sentimentBarChartInstance = null;
let sentiment24hTrendInstance = null;

// ===== Helpers =====
function capitalize(str = '') {
  return str.charAt(0).toUpperCase() + str.slice(1);
}
function show(el, disp = 'block') { if (el) el.style.display = disp; }
function hide(el) { if (el) el.style.display = 'none'; }

// ===== Doughnut =====
function renderDoughnutChart(range = 'this_month') {
  const chartEl   = document.getElementById('sentimentChart');
  const legendEl  = document.getElementById('custom-legend');
  const noDataEl  = document.getElementById('noDataMessage');
  const noDataTxt = document.getElementById('noDataText'); // opsional

  if (!chartEl || !legendEl || !noDataEl) {
    console.warn('[Doughnut] Missing element', { chart: !!chartEl, legend: !!legendEl, nodata: !!noDataEl });
    return;
  }

  // reset saat ganti waktu
  if (sentimentChartInstance) { sentimentChartInstance.destroy(); sentimentChartInstance = null; }
  hide(chartEl);
  hide(legendEl);
  legendEl.innerHTML = '';
  // noDataEl diputuskan setelah fetch

  fetch(`/overview/sentiment-data?range=${range}`)
    .then(res => res.json())
    .then(data => {
      // empty/invalid
      if (!Array.isArray(data) || data.length === 0 || data.message) {
        if (noDataTxt) noDataTxt.textContent = 'Data Tidak Ditemukan!';
        show(noDataEl);
        return;
      }

      const total = data.reduce((acc, item) => acc + (item?.total || 0), 0);
      if (total === 0) {
        if (noDataTxt) noDataTxt.textContent = 'Data tersedia, tapi semua total = 0';
        show(noDataEl);
        return;
      }

      // ada data → render
      hide(noDataEl);
      show(chartEl);
      show(legendEl, 'flex');

      const colorMap = { positive:'#0FAF62', neutral:'#7B878C', negative:'#E84646', default:'#ccc' };
      const standardize = (s='') => {
        const t = s.toLowerCase();
        if (t === 'positif') return 'positive';
        if (t === 'netral')  return 'neutral';
        if (t === 'negatif') return 'negative';
        return t;
      };

      const raw = data
        .filter(i => i?.sentimen != null)
        .map(i => ({ sentiment: standardize(i.sentimen), total: i.total || 0 }));

      // gabung per label
      const labelMap = {};
      raw.forEach(({ sentiment, total }) => { labelMap[sentiment] = (labelMap[sentiment] || 0) + total; });

      const labels = Object.keys(labelMap);
      const values = labels.map(k => labelMap[k]);
      const colors = labels.map(l => colorMap[l] || colorMap.default);

      const ctx = chartEl.getContext('2d');
      sentimentChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels.map(capitalize),
          datasets: [{ data: values, backgroundColor: colors, borderColor: '#fff', borderWidth: 2 }]
        },
        options: {
          responsive: true, maintainAspectRatio: false, cutout: '70%',
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

      // Legend
      labels.forEach((label, i) => {
        const percent = ((values[i] / total) * 100).toFixed(0);
        const item = document.createElement('div');
        item.className = 'legend-item flex items-center gap-2 text-sm text-gray-700 font-medium';
        const dot = document.createElement('span');
        dot.className = 'legend-color w-3 h-3 rounded-full inline-block';
        dot.style.backgroundColor = colors[i];
        const text = document.createElement('span');
        text.textContent = `${capitalize(label)} : ${percent}%`;
        item.appendChild(dot); item.appendChild(text);
        legendEl.appendChild(item);
      });
    })
    .catch(() => {
      if (noDataTxt) noDataTxt.textContent = 'Data Tidak Ditemukan!';
      show(noDataEl);
      hide(chartEl); hide(legendEl); legendEl.innerHTML = '';
    });
}

// ===== 24H Trend =====
function render24HourTrendChart() {
  const canvasEl = document.getElementById('sentiment24hChart');
  const noDataEl = document.getElementById('noDataMessage24h'); // siapkan di HTML

  if (!canvasEl || !noDataEl) {
    console.warn('[24H] Missing element', { canvas: !!canvasEl, nodata: !!noDataEl });
    return;
  }

  // reset/destroy
  if (sentiment24hTrendInstance) { sentiment24hTrendInstance.destroy(); sentiment24hTrendInstance = null; }
  hide(canvasEl);
  hide(noDataEl);

  fetch('/overview/chart-24h-sentiment')
    .then(res => res.json())
    .then(data => {
      // bentuk data kosong/invalid
      if (!Array.isArray(data) || data.length === 0) { show(noDataEl); return; }

      // map jam ke bucket 0,4,8,12,16,20
      const sentimentData = {
        positive: { '0:00': 0, '4:00': 0, '8:00': 0, '12:00': 0, '16:00': 0, '20:00': 0 },
        negative: { '0:00': 0, '4:00': 0, '8:00': 0, '12:00': 0, '16:00': 0, '20:00': 0 },
        neutral:  { '0:00': 0, '4:00': 0, '8:00': 0, '12:00': 0, '16:00': 0, '20:00': 0 }
      };

      let grand = 0;
      data.forEach(item => {
        const jam = item.hour; // pastikan backend kirim '0:00' dst
        if (jam in sentimentData.positive) {
          const p = item.positive || 0, n = item.neutral || 0, g = item.negative || 0;
          sentimentData.positive[jam] += p;
          sentimentData.neutral[jam]  += n;
          sentimentData.negative[jam] += g;
          grand += p + n + g;
        }
      });

      if (grand === 0) { show(noDataEl); return; }

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
        fill: true, tension: 0.3
      }));

      const ctx = canvasEl.getContext('2d');
      sentiment24hTrendInstance = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: { legend: { display: true } },
          scales: {
            x: { display: true },
            y: { beginAtZero: true, ticks: { stepSize: 5 } } // biar adaptif; set max/stepSize kalau dibutuhkan
          }
        }
      });

      hide(noDataEl);
      show(canvasEl);
    })
    .catch(() => { show(noDataEl); hide(canvasEl); });
}

// ===== Bar =====
function renderBarChart(range = 'this_month') {
  const canvasEl = document.getElementById('sentimentBarChart');
  const noDataEl = document.getElementById('noDataMessagebar');

  if (!canvasEl || !noDataEl) {
    console.warn('[Bar] Missing element', { canvas: !!canvasEl, nodata: !!noDataEl });
    return;
  }

  // reset/destroy
  if (sentimentBarChartInstance) { sentimentBarChartInstance.destroy(); sentimentBarChartInstance = null; }
  hide(canvasEl);
  hide(noDataEl);

  fetch(`/api/sentiment-bar-data?range=${range}`)
    .then(res => res.json())
    .then(data => {
      if (!Array.isArray(data) || data.length === 0 || data.message) { show(noDataEl); return; }

      const grouped = {};
      let grandTotal = 0;
      data.forEach(item => {
        const src = item.source;
        if (!src) return;
        if (!grouped[src]) grouped[src] = { positif: 0, netral: 0, negatif: 0 };
        const p = item.positif || 0, n = item.netral || 0, g = item.negatif || 0;
        grouped[src].positif += p; grouped[src].netral += n; grouped[src].negatif += g;
        grandTotal += p + n + g;
      });

      if (grandTotal === 0) { show(noDataEl); return; }

      const labels      = Object.keys(grouped);
      const positifData = labels.map(src => grouped[src].positif);
      const netralData  = labels.map(src => grouped[src].netral);
      const negatifData = labels.map(src => grouped[src].negatif);

      const ctx = canvasEl.getContext('2d');
      sentimentBarChartInstance = new Chart(ctx, {
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
          responsive: true, maintainAspectRatio: false,
          plugins: { tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${ctx.raw}` } } },
          scales: { 
            x: { ticks: { font: { size: 12 } } },
            y: { beginAtZero: true, ticks: { stepSize: 50 } }
           }
        }
      });

      hide(noDataEl);
      show(canvasEl);
    })
    .catch(() => {
      if (sentimentBarChartInstance) { sentimentBarChartInstance.destroy(); sentimentBarChartInstance = null; }
      hide(canvasEl); show(noDataEl);
    });
}

// ===== Wiring =====
document.addEventListener('DOMContentLoaded', function () {
  // Doughnut dropdown
  document.querySelectorAll('.range-option-doughnut').forEach(item => {
    item.addEventListener('click', e => {
      e.preventDefault();
      const el = e.currentTarget;
      const range = el.dataset.range;
      const btn = document.getElementById('sentimentDropdownBtn');
      if (btn) {
        btn.innerHTML = `${el.innerText}<svg class="w-2.5 h-2.5 ms-1.5" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>`;
      }
      renderDoughnutChart(range);
    });
  });

  // Bar dropdown
  document.querySelectorAll('.range-option-bar').forEach(item => {
    item.addEventListener('click', e => {
      e.preventDefault();
      const el = e.currentTarget;
      const range = el.dataset.range;
      const btn = document.getElementById('barDropdownBtn');
      if (btn) {
        btn.innerHTML = `${el.innerText}<svg class="w-2.5 h-2.5 ms-1.5" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>`;
      }
      renderBarChart(range);
    });
  });

  // Panggil hanya jika elemennya ada di halaman
  if (document.getElementById('sentimentChart'))      renderDoughnutChart();
  if (document.getElementById('sentimentBarChart'))   renderBarChart();
  if (document.getElementById('sentiment24hChart'))   render24HourTrendChart();
});
