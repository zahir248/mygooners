@extends('layouts.admin')

@section('title', 'Logs - MyGooners Admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">System Logs</h1>
                    <p class="mt-2 text-sm text-gray-600">View and manage Laravel application logs</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.logs.download') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-admin-500">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Logs
                    </a>
                    <button type="button" 
                            onclick="showClearLogsModal()"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Clear Logs
                    </button>
                </div>
            </div>
        </div>



        @if($error)
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @else
            <!-- Filters -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Filters</h3>
                </div>
                <div class="px-6 py-4">
                    <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   placeholder="Search in logs..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-admin-500 focus:border-admin-500">
                        </div>
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Log Level</label>
                            <select name="level" id="level" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-admin-500 focus:border-admin-500">
                                <option value="all" {{ request('level') === 'all' || !request('level') ? 'selected' : '' }}>All Levels</option>
                                <option value="EMERGENCY" {{ request('level') === 'EMERGENCY' ? 'selected' : '' }}>Emergency</option>
                                <option value="ALERT" {{ request('level') === 'ALERT' ? 'selected' : '' }}>Alert</option>
                                <option value="CRITICAL" {{ request('level') === 'CRITICAL' ? 'selected' : '' }}>Critical</option>
                                <option value="ERROR" {{ request('level') === 'ERROR' ? 'selected' : '' }}>Error</option>
                                <option value="WARNING" {{ request('level') === 'WARNING' ? 'selected' : '' }}>Warning</option>
                                <option value="NOTICE" {{ request('level') === 'NOTICE' ? 'selected' : '' }}>Notice</option>
                                <option value="INFO" {{ request('level') === 'INFO' ? 'selected' : '' }}>Info</option>
                                <option value="DEBUG" {{ request('level') === 'DEBUG' ? 'selected' : '' }}>Debug</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-admin-600 hover:bg-admin-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-admin-500">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Filter Logs
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Log Entries</h3>
                        <span class="text-sm text-gray-500">{{ count($logs) }} entries found</span>
                    </div>
                </div>
                
                @if(count($logs) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Context</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($logs as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $log['timestamp'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $log['level'] === 'ERROR' || $log['level'] === 'CRITICAL' || $log['level'] === 'ALERT' || $log['level'] === 'EMERGENCY' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $log['level'] === 'WARNING' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $log['level'] === 'INFO' || $log['level'] === 'NOTICE' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $log['level'] === 'DEBUG' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ $log['level'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $log['context'] }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="max-w-xs truncate" title="{{ $log['message'] }}">
                                                {{ $log['message'] }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button type="button" 
                                                    onclick="showLogDetails('{{ addslashes($log['timestamp']) }}', '{{ addslashes($log['level']) }}', '{{ addslashes($log['context']) }}', '{{ addslashes($log['message']) }}', '{{ addslashes($log['full_message']) }}')"
                                                    class="text-admin-600 hover:text-admin-900">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No logs found</h3>
                        <p class="mt-1 text-sm text-gray-500">No log entries match your current filters.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Log Details Modal -->
<div id="logDetailsModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-admin-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-admin-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Log Entry Details
                        </h3>
                        <div class="mt-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Timestamp</label>
                                    <p id="modal-timestamp" class="mt-1 text-sm text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Level</label>
                                    <p id="modal-level" class="mt-1 text-sm text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Context</label>
                                    <p id="modal-context" class="mt-1 text-sm text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Message</label>
                                    <p id="modal-message" class="mt-1 text-sm text-gray-900"></p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Full Details</label>
                                <pre id="modal-full-message" class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md overflow-x-auto whitespace-pre-wrap"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        onclick="hideLogDetails()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-admin-600 text-base font-medium text-white hover:bg-admin-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-admin-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Clear Logs Modal -->
<div id="clearLogsModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="clear-logs-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="clear-logs-modal-title">
                            Clear All Logs
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to clear all log entries? This action cannot be undone and will permanently remove all log data from the system.
                            </p>
                        </div>
                        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-md p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Warning
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>This will affect:</p>
                                        <ul class="list-disc pl-5 mt-1 space-y-1">
                                            <li>All log entries will be permanently deleted</li>
                                            <li>No backup will be created automatically</li>
                                            <li>This action cannot be reversed</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form action="{{ route('admin.logs.clear') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Yes, Clear All Logs
                    </button>
                </form>
                <button type="button" 
                        onclick="hideClearLogsModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-admin-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showLogDetails(timestamp, level, context, message, fullMessage) {
    document.getElementById('modal-timestamp').textContent = timestamp;
    document.getElementById('modal-level').textContent = level;
    document.getElementById('modal-context').textContent = context;
    document.getElementById('modal-message').textContent = message;
    document.getElementById('modal-full-message').textContent = fullMessage;
    document.getElementById('logDetailsModal').classList.remove('hidden');
}

function hideLogDetails() {
    document.getElementById('logDetailsModal').classList.add('hidden');
}

function showClearLogsModal() {
    document.getElementById('clearLogsModal').classList.remove('hidden');
}

function hideClearLogsModal() {
    document.getElementById('clearLogsModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('logDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideLogDetails();
    }
});

document.getElementById('clearLogsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideClearLogsModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideLogDetails();
        hideClearLogsModal();
    }
});
</script>
@endsection 