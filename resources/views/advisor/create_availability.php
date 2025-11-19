<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">
    <h1 class="text-2xl font-semibold text-gray-900">Set Availability</h1>
    <div class="mt-8 bg-white shadow rounded-lg p-6">
        <form action="/advisor/availability/store" method="POST" class="space-y-6">
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700">Date *</label>
                <input type="date" name="date" id="date" required min="<?php echo date('Y-m-d'); ?>"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time *</label>
                    <input type="time" name="start_time" id="start_time" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time *</label>
                    <input type="time" name="end_time" id="end_time" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>
            <div class="flex items-center justify-end gap-x-4 border-t pt-6">
                <a href="/advisor/availability" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">Cancel</a>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                    Set Availability
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
