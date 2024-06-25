const ctx = document.getElementById('tempChart');

new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['', '', '', '', '', '','','','',''],
    datasets: [{
      label: 'Temperatura della stanza (C°)',
      data: temps,
      borderColor:[
          'rgb(46,94,78)'
      ],
      backgroundColor:[
          'rgb(46,94,78)'
      ],
      borderWidth: 2
    }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: false
      }
    },
    responsive: true,
  }
});

