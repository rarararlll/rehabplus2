const searchInput = document.getElementById('patientSearch');
const rows = document.querySelectorAll('#patientsTable tbody tr');

if (searchInput) {
    searchInput.addEventListener('keyup', function () {
        const search = this.value.toLowerCase();

        rows.forEach((row) => {
            row.style.display = row.innerText.toLowerCase().includes(search) ? '' : 'none';
        });
    });
}

const chartData = window.rehabPlusDashboardData || {};
const recoveryChart = document.getElementById('recoveryChart');

if (recoveryChart && chartData.recoveryLabels && chartData.recoveryValues) {
    new Chart(recoveryChart, {
        type: 'line',
        data: {
            labels: chartData.recoveryLabels,
            datasets: [{
                label: 'Recovery Progress',
                data: chartData.recoveryValues,
                borderColor: '#14b8a6',
                backgroundColor: 'rgba(20,184,166,.15)',
                fill: true,
                tension: .4,
            }],
        },
    });
}

const conditionChart = document.getElementById('conditionChart');

if (conditionChart && chartData.conditionLabels && chartData.conditionValues) {
    new Chart(conditionChart, {
        type: 'doughnut',
        data: {
            labels: chartData.conditionLabels,
            datasets: [{
                data: chartData.conditionValues,
                backgroundColor: ['#14b8a6', '#0ea5e9', '#8b5cf6', '#f59e0b'],
            }],
        },
    });
}

const themeToggle = document.getElementById('themeToggle');
const applyTheme = (dark) => {
    document.documentElement.classList.toggle('dark-mode', dark);
    document.body.classList.toggle('dark-mode', dark);
    themeToggle.innerHTML = dark
        ? '<i class="bi bi-sun-fill"></i>'
        : '<i class="bi bi-moon-fill"></i>';
};

if (themeToggle) {
    applyTheme(localStorage.getItem('rehabplus-theme') === 'dark');
    themeToggle.addEventListener('click', () => {
        const dark = !document.body.classList.contains('dark-mode');
        localStorage.setItem('rehabplus-theme', dark ? 'dark' : 'light');
        applyTheme(dark);
    });
}