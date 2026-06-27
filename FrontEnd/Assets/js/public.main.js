/**
 * public.main.js — JavaScript for Public Theme
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Navbar scroll effect
    const navbar = document.querySelector('.navbar-public');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // Cylinder chart animation on scroll
    const cylinderChart = document.querySelector('.cylinder-chart-container');
    if (cylinderChart) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCylinders();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });
        
        observer.observe(cylinderChart);
    }

    // Period select change handler
    const periodSelect = document.getElementById('periodSelect');
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            const period = this.value;
            updateChartData(period);
        });
    }
});

/**
 * Animate cylinder fills on scroll
 */
function animateCylinders() {
    const fills = document.querySelectorAll('.cylinder-fill');
    fills.forEach((fill, index) => {
        setTimeout(() => {
            fill.style.transition = 'height 1s cubic-bezier(0.4, 0, 0.2, 1)';
        }, index * 100);
    });
}

/**
 * Update chart data via AJAX
 */
function updateChartData(period) {
    // Placeholder — will be implemented when API is ready
    console.log('Fetching data for period:', period);
    
    // Example implementation:
    // fetch(API_BASE_URL + '/public/waste-habits?period=' + period)
    //     .then(res => res.json())
    //     .then(data => {
    //         updateCylinderHeights(data);
    //     });
}

/**
 * Update cylinder heights with new data
 */
function updateCylinderHeights(data) {
    const fills = document.querySelectorAll('.cylinder-fill');
    const values = document.querySelectorAll('.cylinder-value');
    const maxValue = Math.max(...Object.values(data)) || 1;
    
    const keys = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
    
    keys.forEach((key, index) => {
        if (fills[index] && values[index]) {
            const value = data[key] || 0;
            const height = Math.max((value / maxValue) * 200, 10);
            fills[index].style.height = height + 'px';
            values[index].textContent = value + ' kg';
        }
    });
}