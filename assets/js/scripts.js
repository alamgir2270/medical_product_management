// Form Validation
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});

// Dynamic Order Visualization (Sample Example)
const orderStats = document.getElementById('orderStats');
if (orderStats) {
    const ctx = orderStats.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pending', 'Processed', 'Delivered'],
            datasets: [{
                label: 'Order Status',
                data: [12, 19, 7],
                backgroundColor: ['#ff6384', '#36a2eb', '#4caf50'],
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                },
            },
        },
    });
}

// Sidebar Toggle for Admin/User Dashboard
const sidebarToggle = document.querySelector('#sidebarToggle');
if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        document.body.classList.toggle('sidebar-collapsed');
    });
}
