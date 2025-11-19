<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Find Advisors</h1>
            <p class="mt-2 text-sm text-gray-700">Search for advisors by name or department</p>
        </div>
    </div>

    <!-- Search -->
    <div class="mt-6 bg-white shadow rounded-lg p-4">
        <form method="GET" action="/student/advisors" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search by name</label>
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                       placeholder="Enter advisor name..."
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="department" class="block text-sm font-medium text-gray-700">Filter by department</label>
                <input type="text" name="department" id="department" value="<?php echo htmlspecialchars($_GET['department'] ?? ''); ?>"
                       placeholder="Enter department..."
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="sm:col-span-2 flex justify-end">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- Advisors Grid -->
    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php if (empty($advisors)): ?>
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No advisors found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria.</p>
            </div>
        <?php else: ?>
            <?php foreach ($advisors as $advisor): ?>
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-6 py-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-xl font-medium text-indigo-600">
                                        <?php echo strtoupper(substr($advisor['name'], 0, 1)); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($advisor['name']); ?></h3>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($advisor['department']); ?></p>
                            </div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <?php echo htmlspecialchars($advisor['email']); ?>
                            </div>
                            <?php if (!empty($advisor['office_location'])): ?>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <?php echo htmlspecialchars($advisor['office_location']); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($advisor['phone_number'])): ?>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <?php echo htmlspecialchars($advisor['phone_number']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mt-6">
                            <a href="/student/appointments/create?advisor_id=<?php echo $advisor['id']; ?>"
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Book Appointment
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
