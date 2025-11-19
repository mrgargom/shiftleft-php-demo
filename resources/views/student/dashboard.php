<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Student Dashboard</h1>
            <p class="mt-2 text-sm text-gray-700">Welcome back, <?php echo htmlspecialchars($student['name']); ?>!</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="/student/advisors" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Book Appointment
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
                <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
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

    <!-- Upcoming Appointments -->
    <div class="mt-8 bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">My Appointments</h3>
            <a href="/student/appointments" class="text-sm text-indigo-600 hover:text-indigo-500">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Advisor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by booking an appointment with an advisor.</p>
                                <div class="mt-6">
                                    <a href="/student/advisors" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        Find Advisor
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (array_slice($appointments, 0, 5) as $appointment): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($appointment['advisor_name']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($appointment['department']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo date('M d, Y', strtotime($appointment['date'])); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo date('g:i A', strtotime($appointment['time'])); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars(substr($appointment['purpose'], 0, 40)) . (strlen($appointment['purpose']) > 40 ? '...' : ''); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'confirmed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'declined' => 'bg-red-100 text-red-800',
                                        'completed' => 'bg-blue-100 text-blue-800'
                                    ];
                                    $colorClass = $statusColors[$appointment['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $colorClass; ?>">
                                        <?php echo ucfirst(htmlspecialchars($appointment['status'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <?php if ($appointment['status'] === 'pending' || $appointment['status'] === 'confirmed'): ?>
                                        <form method="POST" action="/student/appointments/cancel" class="inline" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notifications -->
    <?php if (!empty($notifications)): ?>
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Notifications</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>
                    <li class="px-6 py-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-900"><?php echo htmlspecialchars($notification['message']); ?></p>
                                <p class="mt-1 text-sm text-gray-500"><?php echo date('M d, Y g:i A', strtotime($notification['timestamp'])); ?></p>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
