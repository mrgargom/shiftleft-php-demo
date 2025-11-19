<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900">My Appointments</h1>
    <div class="mt-6 bg-white shadow rounded-lg p-4">
        <form method="GET" action="/advisor/appointments">
            <label for="status" class="block text-sm font-medium text-gray-700">Filter by Status</label>
            <select id="status" name="status" onchange="this.form.submit()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">All Statuses</option>
                <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="confirmed" <?php echo (isset($_GET['status']) && $_GET['status'] === 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
            </select>
        </form>
    </div>
    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            <?php if (empty($appointments)): ?>
                <li class="px-6 py-12 text-center text-gray-500">No appointments found</li>
            <?php else: ?>
                <?php foreach ($appointments as $appointment): ?>
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($appointment['student_name']); ?></h4>
                                <div class="mt-2 flex items-center text-sm text-gray-500 space-x-6">
                                    <span><?php echo date('M d, Y', strtotime($appointment['date'])); ?> at <?php echo date('g:i A', strtotime($appointment['time'])); ?></span>
                                    <span><?php echo htmlspecialchars($appointment['major']); ?></span>
                                </div>
                                <p class="mt-2 text-sm text-gray-900"><strong>Purpose:</strong> <?php echo htmlspecialchars($appointment['purpose']); ?></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php 
                                    echo match($appointment['status']) {
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'confirmed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'declined' => 'bg-red-100 text-red-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                ?>">
                                    <?php echo ucfirst(htmlspecialchars($appointment['status'])); ?>
                                </span>
                                <?php if ($appointment['status'] === 'pending'): ?>
                                    <form method="POST" action="/advisor/appointments/confirm" class="inline">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                        <button type="submit" class="text-green-600 hover:text-green-900 text-sm">Confirm</button>
                                    </form>
                                    <form method="POST" action="/advisor/appointments/decline" class="inline">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Decline</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
