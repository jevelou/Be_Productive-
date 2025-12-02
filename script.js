// For Sign in and Sign Up
const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});

// Add an event listener to each label with class "underline-on-click"
const labels = document.querySelectorAll('.underline-on-click');

labels.forEach(label => {
    label.addEventListener('click', () => {
        label.style.textDecoration = 'underline';
    });
});

// Function to toggle visibility of Terms & Conditions container
function toggleTermsAndConditions() {
    var container = document.getElementById("termsAndConditions");
    if (container.style.display === "none") {
        container.style.display = "block";
    } else {
        container.style.display = "none";
    }
}

// Function to handle the search functionality
function searchTasks(event) {
    event.preventDefault(); // Prevent form submission

    // Get the input value
    var searchValue = document.getElementById("searchInput").value.toLowerCase();

    // Get all task rows
    var taskRows = document.querySelectorAll("#taskTableBody tr");

    // Loop through each task row
    taskRows.forEach(function(row) {
        // Get the task name cell (second cell in each row)
        var taskNameCell = row.cells[1];
        
        // Get the task name text content
        var taskName = taskNameCell.textContent.toLowerCase();
        
        // Check if the task name contains the search value
        if (taskName.includes(searchValue)) {
            // If the task name matches the search value, display the row
            row.style.display = "";
        } else {
            // If the task name does not match the search value, hide the row
            row.style.display = "none";
        }
    });
}

// Function to hide form container
function hideForm(formId) {
    document.getElementById(formId).style.display = 'none';
}

// Function to show task form and hide goal form
function showTaskForm() {
    document.getElementById("taskForm").style.display = "block";
    document.getElementById("goalForm").style.display = "none";
}

// Function to show goal form and hide task form
function showGoalForm() {
    document.getElementById("taskForm").style.display = "none";
    document.getElementById("goalForm").style.display = "block";
}

// Function to handle form submission for adding a task
function addTask(event) {
    event.preventDefault(); // Prevent the default form submission behavior

    // Get form data
    var formData = new FormData(document.getElementById("addTaskForm"));

    // Send form data to add_task.php using AJAX
    fetch("add_task.php", {
        method: "POST",
        body: formData
    })
    .then(response => {
        if (response.ok) {
            // If the response is successful, show a success message or perform any other action
            window.location.href = "home.php?message=Goal added successfully!";
            // You can show a success message or update the UI here
        } else {
            // If the response is not successful, show an error message or perform any other action
            alert("Failed to add goal.");
            // You can show an error message or handle the error in another way
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Handle errors here
    });
}

// Function to handle form submission for adding a goal
function addGoal(event) {
    event.preventDefault(); // Prevent the default form submission behavior

    // Get form data
    var formData = new FormData(document.getElementById("addGoalForm"));

    // Send form data to add_goal.php using AJAX
    fetch("add_goal.php", {
        method: "POST",
        body: formData
    })
    .then(response => {
        if (response.ok) {
            // If the response is successful, show a success message or perform any other action
            window.location.href = "home.php?message=Goal added successfully!";
            // You can show a success message or update the UI here
        } else {
            // If the response is not successful, show an error message or perform any other action
            alert("Failed to add goal.");
            // You can show an error message or handle the error in another way
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Handle errors here
    });
}

// For the read, edit and delete button a task
function readTask(taskId) {
    // You can implement the logic here to read/display the details of the task
    console.log("Reading task with ID: " + taskId);
}

// Function to handle deleting a task
function deleteTask(taskId) {
    // You can implement the logic here to delete the task
    console.log("Deleting task with ID: " + taskId);
}

// Function to handle reading a goal
function readGoal(goalId) {
    // You can implement the logic here to read/display the details of the goal
    console.log("Reading goal with ID: " + goalId);
}

// Function to handle deleting a goal
function deleteGoal(goal_id) {
    if (confirm("Are you sure you want to delete this goal?")) {
        // Create a FormData object to send data to the server
        var formData = new FormData();
        formData.append('delete_goal_id', goal_id);

        // Send an AJAX request to delete_goal.php
        fetch('delete_goal.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data === "success") {
                // If deletion is successful, remove the row from the table
                var row = document.getElementById('goal-' + goal_id);
                if (row) {
                    row.parentNode.removeChild(row);
                }
            } else {
                // If there's an error, display a message to the user
                alert("Error deleting goal: " + data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("An error occurred while deleting the goal.");
        });
    }
}

// Function to handle deleting a task
function deleteTask(task_id) {
    if (confirm("Are you sure you want to delete this task?")) {
        // Create a FormData object to send data to the server
        var formData = new FormData();
        formData.append('delete_task_id', task_id);

        // Send an AJAX request to delete_task.php
        fetch('delete_task.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data === "success") {
                // If deletion is successful, remove the row from the table
                var row = document.getElementById('task-' + task_id);
                if (row) {
                    row.parentNode.removeChild(row);
                }
            } else {
                // If there's an error, display a message to the user
                alert("Error deleting task: " + data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("An error occurred while deleting the task.");
        });
    }
}

// Function to redirect to edit_goal.php with the goal_id parameter
function editGoal(goalId) {
    window.location.href = 'edit_goal.php?goal_id=' + goalId;
}

function redirectToReadGoal(goalId) {
    window.location.href = "read_goal.php?goal_id=" + goalId;
}

function redirectToReadTask(taskId) {
    window.location.href = "read_task.php?task_id=" + taskId;
}

// For Edit Task and Goal Button
function editTask(taskId) {
    window.location.href = "edit_task.php?task_id=" + taskId;
}

// For Read and Delete time log
function readTimeLog(logId) {
    window.location.href = "read_timelog.php?log_id=" + logId;
}

function deleteTimeLog(log_id) {
    if (confirm('Are you sure you want to delete this log?')) {
        var formData = new FormData();
        formData.append('delete_log_id', log_id);

        fetch('delete_timelog.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'success') {
                // If deletion is successful, remove the row from the table
                var row = document.getElementById('log-' + log_id);
                if (row) {
                    row.parentNode.removeChild(row);
                }
            } else {
                alert('Error deleting log: ' + data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the log.');
        });
    }
}

// For Search
function filterTable() {
    const searchInput = document.getElementById('search').value.toLowerCase();
    const taskTableBody = document.getElementById('taskTableBody');
    const goalTableBody = document.getElementById('goalTableBody');

    filterTableRows(taskTableBody, searchInput);
    filterTableRows(goalTableBody, searchInput);
}

function filterTableRows(tableBody, searchInput) {
    const rows = tableBody.getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let rowContainsSearchInput = false;
        for (let j = 0; j < cells.length; j++) {
            if (cells[j].innerText.toLowerCase().includes(searchInput)) {
                rowContainsSearchInput = true;
                break;
            }
        }
        rows[i].style.display = rowContainsSearchInput ? '' : 'none';
    }
}

// For Log Time 
function redirectToLogTime() {
    window.location.href = "log_time.php";
}

var startTime;
var timerInterval;

function startTimer() {
    startTime = new Date().getTime();
    timerInterval = setInterval(updateTimer, 1000); // Update every second
    document.getElementById('startTimerBtn').disabled = true;
}

function stopTimer() {
    clearInterval(timerInterval); // Stop the timer interval
    var endTime = new Date().getTime();
    var duration = Math.round((endTime - startTime) / 1000); // duration in seconds
    document.getElementById('duration').value = duration;
    document.getElementById('end_time').value = new Date().toISOString().slice(0, 19);
    document.getElementById('startTimerBtn').disabled = false;
}

function updateTimer() {
    var currentTime = new Date().getTime();
    var elapsedTime = currentTime - startTime;
    var hours = Math.floor(elapsedTime / (1000 * 60 * 60));
    var minutes = Math.floor((elapsedTime % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((elapsedTime % (1000 * 60)) / 1000);
    // Add leading zeros if needed
    hours = padZero(hours);
    minutes = padZero(minutes);
    seconds = padZero(seconds);
    // Update the timer display
    document.getElementById('timerDisplay').innerText = hours + ":" + minutes + ":" + seconds;
}

function padZero(num) {
    return (num < 10) ? "0" + num : num;
}

// Function to show and hide profile popup
function toggleProfilePopup() {
    const popup = document.getElementById("profilePopup");
    popup.style.display = (popup.style.display === "block") ? "none" : "block";
}

// Optional: hide popup when clicking outside
document.addEventListener("click", function(e) {
    const popup = document.getElementById("profilePopup");
    const icon = document.querySelector(".profile-container");

    if (!icon.contains(e.target) && !popup.contains(e.target)) {
        popup.style.display = "none";
    }
});

// For Chart and Reminder
function redirectToProductivityChart() {
    window.location.href = "chart.php";
}

function redirectToReminder() {
    window.location.href = "reminder.php";
}