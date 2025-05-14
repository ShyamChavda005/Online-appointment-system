<?php
// Establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthcare";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch yearly payment data
$yearlyData = [];
$query = "SELECT YEAR(create_at) as year, MONTH(create_at) as month, SUM(amount) as total 
          FROM payments 
          GROUP BY YEAR(create_at), MONTH(create_at)"; 
$result = $conn->query(query: $query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $yearlyData[$row['year']][$row['month']] = $row['total'];
    }
}

// Fill months with zero if no data exists
foreach ($yearlyData as $year => $months) {
    for ($i = 1; $i <= 12; $i++) {
        if (!isset($yearlyData[$year][$i])) {
            $yearlyData[$year][$i] = 0;
        }
    }
    ksort($yearlyData[$year]); // Ensure months are sorted
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Statistics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container2 {
            height: 100%;
            width: 90%;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #ddd;
            margin: auto;
        }

        .dropdown-container {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4"><i class="fas fa-chart-line text-primary"></i> Payment Statistics </h2>

        <div class="dropdown-container">
            <label for="yearSelect" class="fw-bold d-inline"><i class="fas fa-calendar-alt text-primary"></i> Year:</label>
            <select id="yearSelect" onchange="updateChart()" class="form-select shadow-none w-auto d-inline ms-2">
                <?php foreach (array_keys($yearlyData) as $year): ?>
                    <option value="<?= $year ?>"><?= $year ?></option>
                <?php endforeach; ?>
            </select>
        </div>


        <div class="chart-container2">
            <canvas id="paymentChart"></canvas>
        </div>
    </div>

    <script>
        const ctx2 = document.getElementById('paymentChart').getContext('2d');

        // Data dynamically fetched from PHP
        const yearlyData = <?= json_encode($yearlyData) ?>;

        const data = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                    type: 'bar', // Bar chart dataset
                    label: 'Total Payments (Bar)',
                    data: Object.values(yearlyData[Object.keys(yearlyData)[0]]), // Default year
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                },
                {
                    type: 'line', // Line chart dataset
                    label: 'Total Payments (Line)',
                    data: Object.values(yearlyData[Object.keys(yearlyData)[0]]), // Default year
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: false
                }
            ]
        };

        const options = {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    backgroundColor: "rgba(0,0,0,0.8)",
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 12
                    },
                    padding: 10
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: "rgba(0,0,0,0.1)"
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutCubic'
            }
        };

        let myChart = new Chart(ctx2, {
            type: 'bar',
            data: data,
            options: options
        });

        // Function to update the chart based on selected year
        function updateChart() {
            const selectedYear = document.getElementById('yearSelect').value;
            myChart.data.datasets[0].data = Object.values(yearlyData[selectedYear]);
            myChart.data.datasets[1].data = Object.values(yearlyData[selectedYear]);
            myChart.update();
        }
    </script>
</body>

</html>