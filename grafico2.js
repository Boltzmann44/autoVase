var ctx2 = document.getElementById('waterChart').getContext('2d');
var myChart2 = new Chart(ctx2, {
    type: 'doughnut',
    data: {
        datasets: [{
            label: [''],
            data: [waterLevel, 100-waterLevel],
            backgroundColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 255, 255, 1)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(205, 207, 209, 1)'
            ],
            borderWidth: 1
        }]

    },
    options: {
        responsive: true,
        mantainAspectRatio: false
    }
});