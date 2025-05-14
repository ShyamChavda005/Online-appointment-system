<?php
include_once("../../config.php");
$conn = connection();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$puname = $_SESSION['user'];

// getting particular patients record that in session
$query2 = mysqli_query($conn, "SELECT * FROM patients where username='$puname'");
$patient = mysqli_fetch_assoc($query2);
$pid = $patient["patient_id"];

// Fetch available years
$yearQuery = "SELECT DISTINCT YEAR(appointment_date) AS year FROM appointments WHERE patient_id = $pid ORDER BY year ASC";
$yearResult = $conn->query($yearQuery);
$years = [];
while ($row = $yearResult->fetch_assoc()) {
    $years[] = $row['year'];
}

$defaultYear = in_array(2025, $years) ? 2025 : ($years[0] ?? date('Y')); // Default to 2025 if available

// Fetch appointments per day
$dailyQuery = "SELECT DATE(appointment_date) AS day, COUNT(*) AS total FROM appointments WHERE patient_id = $pid GROUP BY DATE(appointment_date)";
$dailyResult = $conn->query($dailyQuery);
$dailyData = [];
while ($row = $dailyResult->fetch_assoc()) {
    $dailyData[$row['day']] = $row['total'];
}

// Fetch appointments per week
$weeklyQuery = "SELECT YEARWEEK(appointment_date) AS week, COUNT(*) AS total FROM appointments WHERE patient_id = $pid GROUP BY YEARWEEK(appointment_date)";
$weeklyResult = $conn->query($weeklyQuery);
$weeklyData = [];
while ($row = $weeklyResult->fetch_assoc()) {
    $weeklyData[$row['week']] = $row['total'];
}

// Fetch appointments per month
$appointmentData = [];
foreach ($years as $year) {
    $monthlyQuery = "SELECT MONTH(appointment_date) AS month, COUNT(*) AS total FROM appointments WHERE YEAR(appointment_date) = $year AND patient_id = $pid GROUP BY MONTH(appointment_date)";
    $monthlyResult = $conn->query($monthlyQuery);
    $monthlyData = array_fill(1, 12, 0);
    while ($row = $monthlyResult->fetch_assoc()) {
        $monthlyData[$row['month']] = $row['total'];
    }
    $appointmentData[$year] = $monthlyData;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .chart-container {
            height: 100%;
            width: 90%;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #ddd;
            margin: auto;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            padding: 15px 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .header-container h2 {
            margin: 0;
            font-size: 20px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-container h2 i {
            color: #007bff;
        }

        .filter-group {
            display: flex;
            gap: 15px;
        }

        .filter-group label {
            font-size: 14px;
            color: #555;
            font-weight: 600;
        }

        .form-select {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #fff;
            min-width: 200px;
            text-align: left;
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .filter-group {
                flex-direction: column;
                width: 100%;
            }

            .form-select {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4"><i class="fas fa-chart-line text-primary"></i> Your Appointment Statistics </h2>

        <div class="d-flex flex-wrap justify-content-center gap-3 mb-3 align-items-center">
            <div class="d-flex align-items-center gap-2">
                <label for="yearSelect" class="fw-bold"><i class="fas fa-calendar-alt text-primary"></i> Year:</label>
                <select id="yearSelect" class="form-select shadow-none w-auto">
                    <?php foreach ($years as $year): ?>
                        <option value="<?= $year ?>" <?= ($year == $defaultYear) ? 'selected' : '' ?>><?= $year ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex align-items-center gap-2">
                <label for="monthRangeSelect" class="fw-bold"><i class="fas fa-calendar text-success"></i> Range:</label>
                <select id="monthRangeSelect" class="form-select shadow-none w-auto">
                    <option value="6">Last 6 Months</option>
                    <option value="12" selected>Last 12 Months</option>
                </select>
            </div>

            <div class="d-flex align-items-center gap-2">
                <label for="timeFrameSelect" class="fw-bold"><i class="fas fa-clock text-danger"></i> View:</label>
                <select id="timeFrameSelect" class="form-select shadow-none w-auto">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly" selected>Monthly</option>
                </select>
            </div>
        </div>


        <div class="chart-container">
            <canvas id="appointmentChart"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('appointmentChart').getContext('2d');
        const appointmentData = <?= json_encode($appointmentData) ?>;
        const dailyData = <?= json_encode($dailyData) ?>;
        const weeklyData = <?= json_encode($weeklyData) ?>;

        let selectedYear = "<?= $defaultYear ?>";
        let selectedMonthRange = 12;
        let selectedTimeFrame = "monthly";

        function getChartLabels() {
            if (selectedTimeFrame === "daily") {
                return Object.keys(dailyData).slice(-30);
            } else if (selectedTimeFrame === "weekly") {
                return Object.keys(weeklyData).slice(-12);
            } else {
                return ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"].slice(-selectedMonthRange);
            }
        }

        function getChartData() {
            if (selectedTimeFrame === "daily") {
                return Object.values(dailyData).slice(-30);
            } else if (selectedTimeFrame === "weekly") {
                return Object.values(weeklyData).slice(-12);
            } else {
                return Object.values(appointmentData[selectedYear]).slice(-selectedMonthRange);
            }
        }

        let chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: getChartLabels(),
                datasets: [{
                    label: 'Total Appointments',
                    data: getChartData(),
                    backgroundColor: "rgba(54, 162, 235, 0.6)",
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
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
                plugins: {
                    legend: {
                        display: false
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
                animation: {
                    duration: 1000,
                    easing: 'easeInOutCubic'
                }
            }
        });

        function updateChart() {
            selectedYear = document.getElementById("yearSelect").value;
            selectedMonthRange = parseInt(document.getElementById("monthRangeSelect").value);
            selectedTimeFrame = document.getElementById("timeFrameSelect").value;

            chart.data.labels = getChartLabels();
            chart.data.datasets[0].data = getChartData();
            chart.update();
        }

        document.getElementById("yearSelect").addEventListener("change", updateChart);
        document.getElementById("monthRangeSelect").addEventListener("change", updateChart);
        document.getElementById("timeFrameSelect").addEventListener("change", updateChart);
    </script>

</body>

</html>