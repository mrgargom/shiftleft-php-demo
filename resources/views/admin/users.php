<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">User Management</h1>
            <p class="mt-2 text-sm text-gray-700">Manage all users in the system</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none space-x-3">
            <a href="/admin/users/create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create User
            </a>
        </div>
    </div>

    <!-- CSV Import Section -->
    <div class="mt-6 bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Import Users (CSV)</h3>
        <form action="/admin/users/import" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="csv_file" class="block text-sm font-medium text-gray-700">Upload CSV File</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv" required
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-2 text-sm text-gray-500">
                    CSV Format: role,name,email,password,id_field,field1,field2,...
                    <a href="#" onclick="showCSVFormat(); return false;" class="text-indigo-600 hover:text-indigo-500">View format details</a>
                </p>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Import Users
            </button>
        </form>
    </div>

    <!-- Filter -->
    <div class="mt-6 bg-white shadow rounded-lg p-4">
        <form method="GET" action="/admin/users" class="flex items-end gap-4">
            <div class="flex-1">
                <label for="role-filter" class="block text-sm font-medium text-gray-700">Filter by Role</label>
                <select id="role-filter" name="role" onchange="this.form.submit()"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Roles</option>
                    <option value="student" <?php echo (isset($_GET['role']) && $_GET['role'] === 'student') ? 'selected' : ''; ?>>Students</option>
                    <option value="advisor" <?php echo (isset($_GET['role']) && $_GET['role'] === 'advisor') ? 'selected' : ''; ?>>Advisors</option>
                    <option value="administrator" <?php echo (isset($_GET['role']) && $_GET['role'] === 'administrator') ? 'selected' : ''; ?>>Administrators</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No users found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $roleColors = [
                                    'student' => 'bg-blue-100 text-blue-800',
                                    'advisor' => 'bg-green-100 text-green-800',
                                    'administrator' => 'bg-purple-100 text-purple-800'
                                ];
                                $colorClass = $roleColors[$user['role']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $colorClass; ?>">
                                    <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form method="POST" action="/admin/users/delete" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function showCSVFormat() {
    alert(`CSV Format Examples:

For Students:
role,name,email,password,student_id,major,year_level,gpa,phone
student,John Doe,john@example.com,password,STU001,Computer Science,Junior,3.75,555-1234

For Advisors:
role,name,email,password,advisor_id,department,office_location,phone_number
advisor,Dr. Smith,smith@example.com,password,ADV001,Engineering,Building A Room 101,555-5678

For Administrators:
role,name,email,password,admin_id
administrator,Admin User,admin@example.com,password,ADM001

Notes:
- First row should be the header
- Email must be unique
- IDs can be left empty for auto-generation
- Password will be hashed automatically`);
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
