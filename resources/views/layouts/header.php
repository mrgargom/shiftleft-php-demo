<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Academic Advisor System'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php if (auth()->check()): ?>
    <!-- Navigation Bar -->
    <nav class="bg-indigo-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-white text-xl font-bold">
                        Academic Advisor System
                    </a>
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
                        <?php if (auth()->role() === 'student'): ?>
                            <a href="/student/dashboard" class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="/student/advisors" class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">Find Advisors</a>
                            <a href="/student/appointments" class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">My Appointments</a>
                        <?php elseif (auth()->role() === 'advisor'): ?>
                            <a href="/advisor/dashboard" class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="/advisor/appointments" class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">Appointments</a>
                            <a href="/advisor/availability" class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">Availability</a>
                        <?php elseif (auth()->role() === 'administrator'): ?>
                            <a href="/admin/dashboard" class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="/admin/users" class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">Users</a>
                            <a href="/admin/appointments" class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">Appointments</a>
                            <a href="/admin/reports" class="text-white hover:bg-indigo-500 px-3 py-2 rounded-md text-sm font-medium">Reports</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-white text-sm">
                        <?php echo htmlspecialchars(auth()->user()['name']); ?>
                        <span class="text-indigo-200">(<?php echo ucfirst(auth()->role()); ?>)</span>
                    </span>
                    <a href="/logout" class="bg-indigo-700 hover:bg-indigo-800 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['warning'])): ?>
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                    <?php echo htmlspecialchars($_SESSION['warning']); unset($_SESSION['warning']); ?>
                </div>
            <?php endif; ?>
