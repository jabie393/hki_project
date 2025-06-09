function initAdminPage() {
    // Pastikan chartData tersedia
    if (typeof chartData !== 'undefined') {
        initCharts(chartData);
    }
    /**
 * Fungsi untuk inisialisasi semua chart
 * @param {Object} data - Data untuk chart
 */
    function initCharts(data) {
        initBarChart(data.tahunLabels, data.tahunData);
        initPieChart(data.jenisLabels, data.jenisData);
    }

    /* Bar Chart (Hak Cipta per Tahun) */
    function initBarChart(labels, dataset) {
        new Chart(document.getElementById('chartTahun'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah',
                    data: dataset,
                    backgroundColor: '#004080b7',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    /* Pie Chart (Distribusi Jenis Hak Cipta) */
    function initPieChart(labels, dataset) {
        new Chart(document.getElementById('chartJenis'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: dataset,
                    backgroundColor: [
                        '#0074D9', '#FF4136', '#2ECC40', '#FF851B',
                        '#B10DC9', '#FFDC00', '#39CCCC', '#AAAAAA'
                    ]
                }]
            },
            options: { responsive: true }
        });
    }

}