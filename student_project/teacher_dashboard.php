<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        h2 { text-align: center; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        .toggle-btns, .filter-section { text-align: center; margin-bottom: 20px; }
        button, select { padding: 10px; margin: 5px; cursor: pointer; }
        .table-view, .card-view { display: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #c2185b; color: white; }
        .delete-btn { background: red; color: white; border: none; padding: 5px 10px; cursor: pointer; }
        .delete-btn:hover { background: darkred; }
        .card { background: #fff; padding: 15px; border-radius: 10px; box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1); margin-bottom: 15px; }
        .stats { display: flex; justify-content: space-around; margin-bottom: 20px; }
        .stat-box { padding: 10px; background: #c2185b; color: white; border-radius: 10px; width: 30%; text-align: center; }
        .chart-container { display: flex; justify-content: center; margin-top: 20px; }
    </style>
</head>
<body>
    <h2>Teacher Dashboard</h2>
    <div class="container">
        <div style="text-align: right; margin-bottom: 10px;">
            <button onclick="logout()" style="background: #c2185b; color: white; border: none; padding: 10px; cursor: pointer; border-radius: 5px;">Logout</button>
        </div>

        <div class="stats">
            <div class="stat-box">Already Placed: <span id="placedCount">0</span></div>
            <div class="stat-box">Sitting for Placement: <span id="placementCount">0</span></div>
            <div class="stat-box">Higher Studies: <span id="studyCount">0</span></div>
        </div>

        <div class="chart-container">
            <canvas id="studentChart"></canvas>
        </div>

        <div class="filter-section">
            <label for="statusFilter">Filter by Status:</label>
            <select id="statusFilter" onchange="fetchStudents()">
                <option value="all">All</option>
                <option value="Already Placed">Already Placed</option>
                <option value="Sitting for Placement">Sitting for Placement</option>
                <option value="Higher Study">Higher Studies</option>
            </select>
        </div>

        <div class="toggle-btns">
            <button onclick="showView('table')">Table View</button>
            <button onclick="showView('card')">Card View</button>
        </div>

        <div id="tableView" class="table-view">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Roll No</th>
                        <th>Email</th>
                        <th>CGPA</th>
                        <th>Status</th>
                        <th>Offer Letter</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody"></tbody>
            </table>
        </div>

        <div id="cardView" class="card-view"></div>
    </div>

    <script>
        let studentChart;

        function showView(view) {
            document.getElementById('tableView').style.display = view === 'table' ? 'block' : 'none';
            document.getElementById('cardView').style.display = view === 'card' ? 'block' : 'none';
        }

        function fetchStudents() {
            let statusFilter = document.getElementById("statusFilter").value;

            fetch('fetch_students.php')
                .then(response => response.json())
                .then(data => {
                    let tableBody = document.getElementById('studentTableBody');
                    let cardContainer = document.getElementById('cardView'); 

                    let placedCount = 0, placementCount = 0, studyCount = 0;
                    tableBody.innerHTML = '';
                    cardContainer.innerHTML = '';

                    data.forEach(student => {
                        if (!student.cgpa) student.cgpa = {}; // Ensure CGPA data exists

                        if (statusFilter !== "all" && student.status !== statusFilter) return;

                        if (student.status === "Already Placed") placedCount++;
                        if (student.status === "Sitting for Placement") placementCount++;
                        if (student.status === "Higher Study") studyCount++;

                        let row = `<tr>
                            <td>${student.name}</td>
                            <td>${student.rollno}</td>
                            <td>${student.email}</td>
                            <td>${student.cgpa.sem1}, ${student.cgpa.sem2}, ${student.cgpa.sem3}, ${student.cgpa.sem4}, 
                                ${student.cgpa.sem5}, ${student.cgpa.sem6}, ${student.cgpa.sem7}, ${student.cgpa.sem8}</td>
                            <td>${student.status}</td>
                            <td>
                                ${student.offer_letter ? `<a href="download.php?file=${student.offer_letter}">Download</a>` : 'N/A'}
                            </td>
                            <td><button class="delete-btn" onclick="deleteStudent(${student.id})">Delete</button></td>
                        </tr>`;
                        tableBody.innerHTML += row;
                        let card = `
                        <div class="card">
                            <h3>${student.name}</h3>
                            <p><strong>Roll No:</strong> ${student.rollno}</p>
                            <p><strong>Email:</strong> ${student.email}</p>
                            <p><strong>CGPA:</strong> ${student.cgpa.sem1 || 'N/A'}, ${student.cgpa.sem2 || 'N/A'}, ${student.cgpa.sem3 || 'N/A'}, 
                                ${student.cgpa.sem4 || 'N/A'}, ${student.cgpa.sem5 || 'N/A'}, ${student.cgpa.sem6 || 'N/A'}, 
                                ${student.cgpa.sem7 || 'N/A'}, ${student.cgpa.sem8 || 'N/A'}</p>
                            <p><strong>Status:</strong> ${student.status}</p>
                            <p><strong>Offer Letter:</strong> ${student.offer_letter ? `<a href="download.php?file=${student.offer_letter}">Download</a>` : 'N/A'}</p>
                            <button class="delete-btn" onclick="deleteStudent(${student.id})">Delete</button>
                        </div>`;
                        cardContainer.innerHTML += card;
                    });

                    updateStats(placedCount, placementCount, studyCount);
                })
                .catch(error => {
                    document.getElementById('cardView').innerHTML = "<p style='color:red; text-align:center;'>Error fetching data</p>";
                    document.getElementById('studentTableBody').innerHTML = "<tr><td colspan='7' style='color:red; text-align:center;'>Error fetching data</td></tr>";
                });
        }


        function updateStats(placed, placement, study) {
            document.getElementById("placedCount").innerText = placed;
            document.getElementById("placementCount").innerText = placement;
            document.getElementById("studyCount").innerText = study;

            updateChart(placed, placement, study);
        }

        function updateChart(placed, placement, study) {
            let ctx = document.getElementById('studentChart').getContext('2d');
            if (studentChart) studentChart.destroy(); 

            studentChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Already Placed', 'Sitting for Placement', 'Higher Studies'],
                    datasets: [{
                        label: 'Students Distribution',
                        data: [placed, placement, study],
                        backgroundColor: ['#4CAF50', '#FF9800', '#2196F3']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        function deleteStudent(id) {
            console.log("Deleting student with ID:", id);  
            if (confirm("Are you sure you want to delete this student?")) {
                fetch('delete_student.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Server Response:", data); 
                    if (data.success) {
                        alert("Student deleted successfully!");
                        fetchStudents(); 
                    } else {
                        alert("Error deleting student: " + (data.error || "Unknown error"));
                    }
                })
                .catch(error => console.error("Request failed:", error));
                alert("Request failed: " + error.message);
            }
        }


        window.onload = function() {
            showView('table');
            fetchStudents();
        };
        function logout() {
            window.location.href = "index.html"; 
        }

    </script>
</body>
</html>
