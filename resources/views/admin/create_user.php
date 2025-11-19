<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Create User</h1>
            <p class="mt-2 text-sm text-gray-700">Add a new student, advisor, or administrator to the system</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="/admin/users" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Back to Users
            </a>
        </div>
    </div>

    <div class="mt-8 max-w-3xl">
        <form action="/admin/users/store" method="POST" class="space-y-6 bg-white shadow rounded-lg p-6">
            <!-- Role Selection -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">User Role *</label>
                <select id="role" name="role" required onchange="toggleRoleFields(this.value)"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Select a role...</option>
                    <option value="student">Student</option>
                    <option value="advisor">Advisor</option>
                    <option value="administrator">Administrator</option>
                </select>
                <p class="mt-2 text-sm text-gray-500">Select the role for this user</p>
            </div>

            <!-- Basic Information -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                    <input type="text" name="name" id="name" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" id="email" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input type="password" name="password" id="password" required minlength="6"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Minimum 6 characters</p>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="tel" name="phone" id="phone"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            <!-- Student-specific fields -->
            <div id="student-fields" class="hidden space-y-6">
                <h3 class="text-lg font-medium text-gray-900 border-t pt-6">Student Information</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
                        <input type="text" name="student_id" id="student_id"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate</p>
                    </div>

                    <div>
                        <label for="major" class="block text-sm font-medium text-gray-700">Major</label>
                        <input type="text" name="major" id="major"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="year_level" class="block text-sm font-medium text-gray-700">Year Level</label>
                        <select name="year_level" id="year_level"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">Select year...</option>
                            <option value="Freshman">Freshman</option>
                            <option value="Sophomore">Sophomore</option>
                            <option value="Junior">Junior</option>
                            <option value="Senior">Senior</option>
                            <option value="Graduate">Graduate</option>
                        </select>
                    </div>

                    <div>
                        <label for="gpa" class="block text-sm font-medium text-gray-700">GPA</label>
                        <input type="number" name="gpa" id="gpa" step="0.01" min="0" max="4" value="0.00"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Advisor-specific fields -->
            <div id="advisor-fields" class="hidden space-y-6">
                <h3 class="text-lg font-medium text-gray-900 border-t pt-6">Advisor Information</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="advisor_id" class="block text-sm font-medium text-gray-700">Advisor ID</label>
                        <input type="text" name="advisor_id" id="advisor_id"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate</p>
                    </div>

                    <div>
                        <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                        <input type="text" name="department" id="department"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="e.g., Computer Science">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="office_location" class="block text-sm font-medium text-gray-700">Office Location</label>
                        <input type="text" name="office_location" id="office_location"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="e.g., Building A, Room 201">
                    </div>
                </div>
            </div>

            <!-- Administrator-specific fields -->
            <div id="admin-fields" class="hidden space-y-6">
                <h3 class="text-lg font-medium text-gray-900 border-t pt-6">Administrator Information</h3>
                <div>
                    <label for="admin_id" class="block text-sm font-medium text-gray-700">Admin ID</label>
                    <input type="text" name="admin_id" id="admin_id"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate</p>
                </div>
            </div>

            <!-- Validation Rules Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <h4 class="text-sm font-medium text-blue-900 mb-2">Validation Rules:</h4>
                <ul class="text-xs text-blue-700 space-y-1 list-disc list-inside">
                    <li>Email must be unique in the system</li>
                    <li>Password must be at least 6 characters</li>
                    <li>All fields marked with * are required</li>
                    <li>Student ID, Advisor ID, and Admin ID are auto-generated if not provided</li>
                    <li>GPA must be between 0.00 and 4.00</li>
                </ul>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-x-4 border-t pt-6">
                <a href="/admin/users" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRoleFields(role) {
    // Hide all role-specific fields
    document.getElementById('student-fields').classList.add('hidden');
    document.getElementById('advisor-fields').classList.add('hidden');
    document.getElementById('admin-fields').classList.add('hidden');
    
    // Show selected role fields
    if (role === 'student') {
        document.getElementById('student-fields').classList.remove('hidden');
    } else if (role === 'advisor') {
        document.getElementById('advisor-fields').classList.remove('hidden');
    } else if (role === 'administrator') {
        document.getElementById('admin-fields').classList.remove('hidden');
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
