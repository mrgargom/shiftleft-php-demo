<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Advisor Dashboard</h1>
            <p class="mt-2 text-sm text-gray-700">Welcome back, <?php echo htmlspecialchars($advisor['name']); ?>!</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="/advisor/availability/create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                Set Availability
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Appointments</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900"><?php echo $stats['total'] ?? 0; ?></dd>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Pending Requests</dt>
                <dd class="mt-1 text-3xl font-semibold text-yellow-600"><?php echo $stats['pending'] ?? 0; ?></dd>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Confirmed</dt>
                <dd class="mt-1 text-3xl font-semibold text-green-600"><?php echo $stats['confirmed'] ?? 0; ?></dd>
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                <dd class="mt-1 text-3xl font-semibold text-blue-600"><?php echo $stats['completed'] ?? 0; ?></dd>
            </div>
        </div>
    </div>

    <!-- Pending Requests -->
    <?php if (!empty($pendingAppointments)): ?>
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Pending Appointment Requests</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($pendingAppointments as $appointment): ?>
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($appointment['student_name']); ?></h4>
                                <div class="mt-2 flex items-center text-sm text-gray-500 space-x-6">
                                    <span><?php echo date('M d, Y', strtotime($appointment['date'])); ?> at <?php echo date('g:i A', strtotime($appointment['time'])); ?></span>
                                    <span><?php echo htmlspecialchars($appointment['major']); ?> - <?php echo htmlspecialchars($appointment['year_level']); ?></span>
                                </div>
                                <p class="mt-2 text-sm text-gray-900"><strong>Purpose:</strong> <?php echo htmlspecialchars($appointment['purpose']); ?></p>
                            </div>
                            <div class="ml-6 flex gap-2">
                                <form method="POST" action="/advisor/appointments/confirm">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                        Confirm
                                    </button>
                                </form>
                                <form method="POST" action="/advisor/appointments/decline" onsubmit="return confirm('Decline this appointment?');">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Decline
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Upcoming Confirmed Appointments -->
    <div class="mt-8 bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Upcoming Appointments</h3>
            <a href="/advisor/appointments" class="text-sm text-indigo-600 hover:text-indigo-500">View all</a>
        </div>
        <ul class="divide-y divide-gray-200">
            <?php if (empty($confirmedAppointments)): ?>
                <li class="px-6 py-12 text-center text-gray-500">No upcoming appointments</li>
            <?php else: ?>
                <?php foreach (array_slice($confirmedAppointments, 0, 5) as $appointment): ?>
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($appointment['student_name']); ?></h4>
                                <div class="mt-2 flex items-center text-sm text-gray-500 space-x-6">
                                    <span><?php echo date('M d, Y', strtotime($appointment['date'])); ?> at <?php echo date('g:i A', strtotime($appointment['time'])); ?></span>
                                    <span><?php echo $appointment['duration']; ?> minutes</span>
                                </div>
                                <p class="mt-2 text-sm text-gray-900"><?php echo htmlspecialchars($appointment['purpose']); ?></p>
                            </div>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Confirmed
                            </span>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
