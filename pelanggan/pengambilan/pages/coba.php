<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Pickup Calendar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .calendar-container {
            width: 1097px;
            height: 400px;
            flex-shrink: 0;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .calendar-title {
            font-size: 24px;
            font-weight: bold;
        }
        .calendar-nav {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .calendar-nav select {
            padding: 5px 10px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .calendar-nav button {
            padding: 5px 15px;
            background: #4285f4;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .calendar-nav button:hover {
            background: #3367d6;
        }
        .calendar {
            width: 100%;
            height: calc(100% - 60px);
            border-collapse: collapse;
        }
        .calendar th {
            background: #f2f2f2;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        .calendar td {
            padding: 5px;
            border: 1px solid #ddd;
            vertical-align: top;
            height: 80px;
            width: 14.28%;
            position: relative;
        }
        .calendar .date {
            font-weight: bold;
            margin-bottom: 5px;
            text-align: right;
            padding: 2px 5px;
        }
        .calendar .other-month {
            color: #aaa;
            background-color: #f9f9f9;
        }
        .calendar .today {
            background-color: #e6f7ff;
        }
        .calendar .today .date {
            background-color: #4285f4;
            color: white;
            border-radius: 50%;
            display: inline-block;
            width: 24px;
            height: 24px;
            text-align: center;
            line-height: 24px;
        }
        .pickup-schedule {
            font-size: 12px;
            background-color: #fffacd;
            padding: 3px;
            margin: 2px 0;
            border-radius: 3px;
            cursor: pointer;
            position: relative;
        }
        .pickup-schedule:hover {
            background-color: #ffeeba;
        }
        .pickup-schedule .delete-btn {
            position: absolute;
            right: 2px;
            top: 2px;
            color: #888;
            cursor: pointer;
            font-size: 10px;
            visibility: hidden;
        }
        .pickup-schedule:hover .delete-btn {
            visibility: visible;
        }
        .add-schedule-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #4285f4;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            cursor: pointer;
            display: none;
        }
        td:hover .add-schedule-btn {
            display: block;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 100;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 300px;
        }
        .modal h3 {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .modal-buttons button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .modal-buttons .save-btn {
            background: #4285f4;
            color: white;
        }
        .modal-buttons .cancel-btn {
            background: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="calendar-container">
        <div class="calendar-header">
            <div class="calendar-title">Pickup Schedule Calendar</div>
            <div class="calendar-nav">
                <button id="prevMonth"><</button>
                <select id="monthSelect"></select>
                <select id="yearSelect"></select>
                <button id="nextMonth">></button>
                <button id="todayBtn">Today</button>
            </div>
        </div>
        <table class="calendar">
            <thead>
                <tr>
                    <th>Mon</th>
                    <th>Tue</th>
                    <th>Wed</th>
                    <th>Thu</th>
                    <th>Fri</th>
                    <th>Sat</th>
                    <th>Sun</th>
                </tr>
            </thead>
            <tbody id="calendarBody">
                <!-- Calendar will be generated by JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Modal for adding/editing schedules -->
    <div class="modal" id="scheduleModal">
        <div class="modal-content">
            <h3 id="modalTitle">Add Pickup Schedule</h3>
            <div class="form-group">
                <label for="scheduleTime">Time:</label>
                <input type="time" id="scheduleTime" required>
            </div>
            <div class="form-group">
                <label for="scheduleName">Name:</label>
                <input type="text" id="scheduleName" placeholder="Enter name" required>
            </div>
            <div class="modal-buttons">
                <button class="cancel-btn" id="cancelBtn">Cancel</button>
                <button class="save-btn" id="saveBtn">Save</button>
            </div>
        </div>
    </div>

    <script>
        // Database simulation (in a real app, this would be server-side)
        let pickupSchedules = [
            { id: 1, date: '2025-06-05', time: '07:00', name: 'Sti Aminoh' },
            { id: 2, date: '2025-06-09', time: '08:00', name: 'Ardi Hasanudin' },
            { id: 3, date: '2025-06-19', time: '13:00', name: 'Andi Barokah' },
            { id: 4, date: '2025-06-21', time: '10:00', name: 'Chita Andini' }
        ];

        let currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();
        let selectedDate = null;
        let editingSchedule = null;

        // Initialize the calendar
        document.addEventListener('DOMContentLoaded', function() {
            initMonthYearSelectors();
            renderCalendar();
            setupEventListeners();
        });

        function initMonthYearSelectors() {
            const monthSelect = document.getElementById('monthSelect');
            const yearSelect = document.getElementById('yearSelect');
            
            // Populate months
            const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            
            months.forEach((month, index) => {
                const option = document.createElement('option');
                option.value = index;
                option.textContent = month;
                if (index === currentMonth) option.selected = true;
                monthSelect.appendChild(option);
            });
            
            // Populate years (10 years range)
            for (let year = currentYear - 5; year <= currentYear + 5; year++) {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                if (year === currentYear) option.selected = true;
                yearSelect.appendChild(option);
            }
        }

        function renderCalendar() {
            const calendarBody = document.getElementById('calendarBody');
            calendarBody.innerHTML = '';
            
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const daysInMonth = lastDay.getDate();
            const firstDayOfWeek = firstDay.getDay();
            
            // Adjust firstDayOfWeek to make Monday the first day (0=Sunday, 1=Monday, etc.)
            const startDay = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1;
            
            let date = 1;
            let dayOfWeek = 0;
            let isDone = false;
            
            while (!isDone) {
                const row = document.createElement('tr');
                
                for (let i = 0; i < 7; i++) {
                    const cell = document.createElement('td');
                    
                    if (date > daysInMonth && dayOfWeek >= 0) {
                        // Days from next month
                        const nextMonthDate = date - daysInMonth;
                        cell.classList.add('other-month');
                        cell.innerHTML = `<div class="date">${nextMonthDate}</div>`;
                        date++;
                    } else if (dayOfWeek < startDay && date === 1) {
                        // Days from previous month
                        const prevMonth = new Date(currentYear, currentMonth, 0);
                        const prevMonthDays = prevMonth.getDate();
                        const prevMonthDate = prevMonthDays - (startDay - dayOfWeek - 1);
                        cell.classList.add('other-month');
                        cell.innerHTML = `<div class="date">${prevMonthDate}</div>`;
                    } else {
                        // Current month days
                        const cellDate = new Date(currentYear, currentMonth, date);
                        
                        // Check if this is today
                        if (cellDate.toDateString() === new Date().toDateString()) {
                            cell.classList.add('today');
                        }
                        
                        cell.innerHTML = `<div class="date">${date}</div>`;
                        
                        // Add pickup schedules for this date
                        const formattedDate = formatDate(cellDate);
                        const schedules = pickupSchedules.filter(s => s.date === formattedDate);
                        
                        schedules.forEach(schedule => {
                            const scheduleElement = document.createElement('div');
                            scheduleElement.className = 'pickup-schedule';
                            scheduleElement.dataset.id = schedule.id;
                            scheduleElement.innerHTML = `
                                ${schedule.time} ${schedule.name}
                                <span class="delete-btn" onclick="deleteSchedule(event, ${schedule.id})">✕</span>
                            `;
                            cell.appendChild(scheduleElement);
                        });
                        
                        // Add button to add new schedule
                        const addBtn = document.createElement('button');
                        addBtn.className = 'add-schedule-btn';
                        addBtn.innerHTML = '+';
                        addBtn.onclick = (e) => {
                            e.stopPropagation();
                            openAddScheduleModal(cellDate);
                        };
                        cell.appendChild(addBtn);
                        
                        // Add click handler to select date
                        cell.onclick = () => {
                            selectedDate = cellDate;
                        };
                        
                        date++;
                    }
                    
                    row.appendChild(cell);
                    dayOfWeek++;
                }
                
                calendarBody.appendChild(row);
                
                if (date > daysInMonth) {
                    isDone = true;
                }
            }
        }

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function setupEventListeners() {
            // Month/year selectors
            document.getElementById('monthSelect').addEventListener('change', function() {
                currentMonth = parseInt(this.value);
                renderCalendar();
            });
            
            document.getElementById('yearSelect').addEventListener('change', function() {
                currentYear = parseInt(this.value);
                renderCalendar();
            });
            
            // Navigation buttons
            document.getElementById('prevMonth').addEventListener('click', function() {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                updateSelectors();
                renderCalendar();
            });
            
            document.getElementById('nextMonth').addEventListener('click', function() {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                updateSelectors();
                renderCalendar();
            });
            
            document.getElementById('todayBtn').addEventListener('click', function() {
                currentDate = new Date();
                currentMonth = currentDate.getMonth();
                currentYear = currentDate.getFullYear();
                updateSelectors();
                renderCalendar();
            });
            
            // Modal buttons
            document.getElementById('cancelBtn').addEventListener('click', closeModal);
            document.getElementById('saveBtn').addEventListener('click', saveSchedule);
        }

        function updateSelectors() {
            document.getElementById('monthSelect').value = currentMonth;
            document.getElementById('yearSelect').value = currentYear;
        }

        function openAddScheduleModal(date) {
            selectedDate = date;
            editingSchedule = null;
            document.getElementById('modalTitle').textContent = 'Add Pickup Schedule';
            document.getElementById('scheduleTime').value = '';
            document.getElementById('scheduleName').value = '';
            document.getElementById('scheduleModal').style.display = 'flex';
        }

        function openEditScheduleModal(schedule) {
            editingSchedule = schedule;
            selectedDate = new Date(schedule.date);
            document.getElementById('modalTitle').textContent = 'Edit Pickup Schedule';
            document.getElementById('scheduleTime').value = schedule.time;
            document.getElementById('scheduleName').value = schedule.name;
            document.getElementById('scheduleModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('scheduleModal').style.display = 'none';
        }

        function saveSchedule() {
            const time = document.getElementById('scheduleTime').value;
            const name = document.getElementById('scheduleName').value;
            
            if (!time || !name) {
                alert('Please fill in all fields');
                return;
            }
            
            if (editingSchedule) {
                // Update existing schedule
                editingSchedule.time = time;
                editingSchedule.name = name;
            } else {
                // Add new schedule
                const newId = pickupSchedules.length > 0 
                    ? Math.max(...pickupSchedules.map(s => s.id)) + 1 
                    : 1;
                
                pickupSchedules.push({
                    id: newId,
                    date: formatDate(selectedDate),
                    time: time,
                    name: name
                });
            }
            
            closeModal();
            renderCalendar();
            
            // In a real app, you would save to database here
            console.log('Schedules updated:', pickupSchedules);
        }

        function deleteSchedule(event, id) {
            event.stopPropagation();
            if (confirm('Are you sure you want to delete this schedule?')) {
                pickupSchedules = pickupSchedules.filter(s => s.id !== id);
                renderCalendar();
                
                // In a real app, you would delete from database here
                console.log('Schedule deleted. Updated schedules:', pickupSchedules);
            }
        }
    </script>
</body>
</html>