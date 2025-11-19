<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Book Appointment</h1>
            <p class="mt-2 text-sm text-gray-700">Schedule an appointment with <?php echo htmlspecialchars($advisor['name']); ?></p>
        </div>
    </div>

    <!-- Advisor Info Card -->
    <div class="mt-6 bg-white shadow rounded-lg p-6">
        <div class="flex items-center">
            <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                <span class="text-2xl font-medium text-indigo-600"><?php echo strtoupper(substr($advisor['name'], 0, 1)); ?></span>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($advisor['name']); ?></h3>
                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($advisor['department']); ?></p>
                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($advisor['office_location']); ?></p>
            </div>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="mt-6 bg-white shadow rounded-lg p-6">
        <form action="/student/appointments/store" method="POST" class="space-y-6">
            <input type="hidden" name="advisor_id" value="<?php echo $advisor['id']; ?>">

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Date *</label>
                    <input type="date" name="date" id="date" required min="<?php echo date('Y-m-d'); ?>"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="time" class="block text-sm font-medium text-gray-700">Time *</label>
                    <input type="time" name="time" id="time" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label for="duration" class="block text-sm font-medium text-gray-700">Duration (minutes) *</label>
                    <select name="duration" id="duration" required
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="30" selected>30 minutes</option>
                        <option value="45">45 minutes</option>
                        <option value="60">60 minutes</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose *</label>
                    <textarea name="purpose" id="purpose" rows="3" required
                              placeholder="e.g., Course selection, Career guidance, Academic planning..."
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>

                <div class="sm:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                    <textarea name="notes" id="notes" rows="2"
                              placeholder="Any additional information for the advisor..."
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>
            </div>

            <?php if (!empty($availabilities)): ?>
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Advisor Availability:</h4>
                    <ul class="text-xs text-blue-700 space-y-1">
                        <?php foreach (array_slice($availabilities, 0, 5) as $availability): ?>
                            <li>• <?php echo date('M d, Y', strtotime($availability['date'])); ?> - 
                                <?php echo date('g:i A', strtotime($availability['start_time'])); ?> to 
                                <?php echo date('g:i A', strtotime($availability['end_time'])); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="flex items-center justify-end gap-x-4 border-t pt-6">
                <a href="/student/advisors" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Book Appointment
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
