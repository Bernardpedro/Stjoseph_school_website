<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
    <!-- Header -->
    <div class="bg-slate-900/80 backdrop-blur-lg border-b border-white/10">
      <div class="container mx-auto px-6 py-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-white mb-2">User Management & Roles</h1>
            <p class="text-blue-200">Manage users, roles, permissions, and track activities</p>
          </div>
          <NuxtLink to="/dashboard/admin" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg transition-colors">
            Back to Dashboard
          </NuxtLink>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-6 py-8">
      <div v-if="pageError" class="mb-6 p-4 bg-red-500/15 border border-red-500/30 rounded-xl text-red-200">{{ pageError }}</div>
      <div v-if="pageSuccess" class="mb-6 p-4 bg-green-500/15 border border-green-500/30 rounded-xl text-green-200">{{ pageSuccess }}</div>
      <div v-if="loading" class="mb-6 text-blue-200">Loading users…</div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl">
          <div class="flex items-center justify-between mb-4">
            <Users class="w-8 h-8" />
            <span class="text-3xl font-bold">{{ users.length }}</span>
          </div>
          <p class="text-blue-100">Total Users</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl">
          <div class="flex items-center justify-between mb-4">
            <Shield class="w-8 h-8" />
            <span class="text-3xl font-bold">{{ roles.length }}</span>
          </div>
          <p class="text-purple-100">Active Roles</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl">
          <div class="flex items-center justify-between mb-4">
            <UserCheck class="w-8 h-8" />
            <span class="text-3xl font-bold">{{ activeUsers }}</span>
          </div>
          <p class="text-green-100">Active Now</p>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl">
          <div class="flex items-center justify-between mb-4">
            <Activity class="w-8 h-8" />
            <span class="text-3xl font-bold">{{ recentActivities.length }}</span>
          </div>
          <p class="text-orange-100">Recent Activities</p>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex flex-wrap gap-3 mb-8">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'flex items-center space-x-2 px-6 py-3 rounded-lg transition-all duration-300',
            activeTab === tab.id
              ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg'
              : 'bg-slate-800/50 text-blue-200 hover:bg-slate-700/50'
          ]"
        >
          <component :is="tab.icon" class="w-5 h-5" />
          <span class="font-medium">{{ tab.label }}</span>
        </button>
      </div>

      <!-- Users Tab -->
      <div v-if="activeTab === 'users'" class="bg-slate-800/50 backdrop-blur-lg rounded-2xl p-6 border border-white/10">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
          <div>
            <h2 class="text-2xl font-bold text-white">Manage Users</h2>
            <p v-if="!canManageUsers" class="text-blue-200 mt-1">
              Only the school owner can add users and assign roles.
            </p>
          </div>
          <div class="flex flex-wrap gap-3">
            <div class="relative">
              <Search class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-300" />
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search users..."
                class="pl-10 pr-4 py-2 bg-slate-700 border border-white/10 rounded-lg text-white placeholder-blue-200/50 focus:outline-none focus:border-blue-500 transition-colors"
              />
            </div>
            <select v-model="filterRole" class="px-4 py-2 bg-slate-700 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500">
              <option value="">All Roles</option>
              <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
            </select>
            <button
              v-if="canManageUsers"
              @click="openModal('addUser')"
              class="flex items-center space-x-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
            >
              <UserPlus class="w-4 h-4" />
              <span>Add User</span>
            </button>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-white/10">
                <th class="text-left py-3 px-4 text-blue-200 font-semibold">User</th>
                <th class="text-left py-3 px-4 text-blue-200 font-semibold">Email</th>
                <th class="text-left py-3 px-4 text-blue-200 font-semibold">Role</th>
                <th class="text-left py-3 px-4 text-blue-200 font-semibold">Status</th>
                <th class="text-left py-3 px-4 text-blue-200 font-semibold">Last Login</th>
                <th class="text-left py-3 px-4 text-blue-200 font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!loading && !filteredUsers.length">
                <td colspan="6" class="py-8 px-4 text-center text-blue-200">No users found.</td>
              </tr>
              <tr v-for="user in filteredUsers" :key="user.id" class="border-b border-white/5 hover:bg-slate-700/30 transition-colors">
                <td class="py-4 px-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold">
                      {{ (user.name || '?').charAt(0) }}
                    </div>
                    <span class="text-white font-medium">{{ user.name }}</span>
                  </div>
                </td>
                <td class="py-4 px-4 text-blue-200">{{ user.email }}</td>
                <td class="py-4 px-4">
                  <span :class="[
                    'px-3 py-1 rounded-full text-sm font-medium',
                    getRoleColor(user.role)
                  ]">
                    {{ user.role }}
                  </span>
                </td>
                <td class="py-4 px-4">
                  <span :class="[
                    'px-3 py-1 rounded-full text-sm font-medium',
                    user.status === 'Active' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'
                  ]">
                    {{ user.status }}
                  </span>
                </td>
                <td class="py-4 px-4 text-blue-200">{{ user.lastLogin }}</td>
                <td class="py-4 px-4">
                  <div class="flex space-x-2">
                    <button 
                      @click="openModal('viewUser', user)"
                      class="p-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg transition-colors"
                      title="View Details"
                    >
                      <Eye class="w-4 h-4" />
                    </button>
                    <button 
                      v-if="canManageUsers"
                      @click="openModal('editUser', user)"
                      class="p-2 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg transition-colors"
                      title="Edit User"
                    >
                      <Edit class="w-4 h-4" />
                    </button>
                    <button 
                      v-if="canManageUsers"
                      @click="openModal('resetPassword', user)"
                      class="p-2 bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-400 rounded-lg transition-colors"
                      title="Reset Password"
                    >
                      <KeyRound class="w-4 h-4" />
                    </button>
                    <button 
                      v-if="canManageUsers && !isOwnerUser(user)"
                      @click="toggleUserStatus(user)"
                      class="p-2 bg-orange-500/20 hover:bg-orange-500/30 text-orange-400 rounded-lg transition-colors"
                      :title="user.status === 'Active' ? 'Deactivate' : 'Activate'"
                    >
                      <Ban class="w-4 h-4" />
                    </button>
                    <button 
                      v-if="canManageUsers && !isOwnerUser(user)"
                      @click="deleteUser(user)"
                      class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-colors"
                      title="Delete User"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Roles & Permissions Tab -->
      <div v-if="activeTab === 'roles'" class="space-y-6">
        <div class="bg-slate-800/50 backdrop-blur-lg rounded-2xl p-6 border border-white/10">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white">Manage Roles</h2>
            <p class="text-blue-300 text-sm">System roles are Admin and User.</p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="role in roles" :key="role.id" class="bg-slate-700/50 rounded-xl p-6 border border-white/10 hover:border-white/20 transition-all">
              <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                  <Shield class="w-6 h-6 text-purple-400" />
                </div>
                <div class="flex space-x-2">
                  <button 
                    @click="openModal('editRole', role)"
                    class="p-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg transition-colors"
                  >
                    <Settings class="w-4 h-4" />
                  </button>
                  <button 
                    v-if="!role.isDefault"
                    @click="deleteRole(role)"
                    class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-colors"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </div>
              <h3 class="text-xl font-bold text-white mb-2">{{ role.name }}</h3>
              <p class="text-blue-200 text-sm mb-4">{{ role.description }}</p>
              <div class="flex items-center justify-between text-sm">
                <span class="text-blue-300">{{ role.userCount }} users</span>
                <button 
                  @click="openModal('managePermissions', role)"
                  class="text-blue-400 hover:text-blue-300 font-medium"
                >
                  Manage Permissions →
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Permissions Overview -->
        <div class="bg-slate-800/50 backdrop-blur-lg rounded-2xl p-6 border border-white/10">
          <h2 class="text-2xl font-bold text-white mb-6">Permissions Overview</h2>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-white/10">
                  <th class="text-left py-3 px-4 text-blue-200 font-semibold">Module</th>
                  <th v-for="role in roles.slice(0, 5)" :key="role.id" class="text-center py-3 px-4 text-blue-200 font-semibold">
                    {{ role.name }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="module in permissionModules" :key="module.id" class="border-b border-white/5 hover:bg-slate-700/30 transition-colors">
                  <td class="py-4 px-4 text-white font-medium">{{ module.name }}</td>
                  <td v-for="role in roles.slice(0, 5)" :key="role.id" class="py-4 px-4 text-center">
                    <span v-if="hasPermission(role.id, module.id)" class="inline-block">
                      <Check class="w-5 h-5 text-green-400 mx-auto" />
                    </span>
                    <span v-else class="inline-block">
                      <X class="w-5 h-5 text-red-400 mx-auto" />
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Activity Tracking Tab -->
      <div v-if="activeTab === 'activity'" class="space-y-6">
        <div class="bg-slate-800/50 backdrop-blur-lg rounded-2xl p-6 border border-white/10">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-white">Activity Log</h2>
            <div class="flex flex-wrap gap-3">
              <select v-model="activityFilter" class="px-4 py-2 bg-slate-700 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500">
                <option value="">All Activities</option>
                <option value="login">Login Events</option>
                <option value="create">Created</option>
                <option value="update">Updated</option>
                <option value="delete">Deleted</option>
              </select>
              <button
                @click="exportActivityLog"
                class="flex items-center space-x-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
              >
                <Download class="w-4 h-4" />
                <span>Export Log</span>
              </button>
            </div>
          </div>
          <div v-if="!filteredActivities.length" class="p-6 text-blue-200 text-center bg-slate-700/30 rounded-lg">
            Activity logging is not enabled yet. User create, update, and delete actions are saved immediately to the database.
          </div>
          <div v-else class="space-y-3">
            <div v-for="activity in filteredActivities" :key="activity.id" class="flex items-start space-x-4 p-4 bg-slate-700/30 rounded-lg hover:bg-slate-700/50 transition-colors">
              <div :class="[
                'w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0',
                getActivityColor(activity.type)
              ]">
                <component :is="getActivityIcon(activity.type)" class="w-5 h-5" />
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <p class="text-white font-medium">{{ activity.user }}</p>
                    <p class="text-blue-200 text-sm mt-1">{{ activity.description }}</p>
                  </div>
                  <span class="text-blue-300 text-sm whitespace-nowrap">{{ activity.timestamp }}</span>
                </div>
                <div class="flex items-center gap-2 mt-2">
                  <span :class="[
                    'px-2 py-1 rounded text-xs font-medium',
                    activity.type === 'login' ? 'bg-blue-500/20 text-blue-400' :
                    activity.type === 'create' ? 'bg-green-500/20 text-green-400' :
                    activity.type === 'update' ? 'bg-yellow-500/20 text-yellow-400' :
                    'bg-red-500/20 text-red-400'
                  ]">
                    {{ activity.type.toUpperCase() }}
                  </span>
                  <span class="text-blue-300 text-xs">{{ activity.ipAddress }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Login History -->
        <div class="bg-slate-800/50 backdrop-blur-lg rounded-2xl p-6 border border-white/10">
          <h2 class="text-2xl font-bold text-white mb-6">Recent Login History</h2>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-white/10">
                  <th class="text-left py-3 px-4 text-blue-200 font-semibold">User</th>
                  <th class="text-left py-3 px-4 text-blue-200 font-semibold">Login Time</th>
                  <th class="text-left py-3 px-4 text-blue-200 font-semibold">IP Address</th>
                  <th class="text-left py-3 px-4 text-blue-200 font-semibold">Device</th>
                  <th class="text-left py-3 px-4 text-blue-200 font-semibold">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!loginHistory.length">
                  <td colspan="5" class="py-6 px-4 text-center text-blue-200">No login history stored yet. Last login time is shown on each user.</td>
                </tr>
                <tr v-for="login in loginHistory" :key="login.id" class="border-b border-white/5 hover:bg-slate-700/30 transition-colors">
                  <td class="py-4 px-4 text-white font-medium">{{ login.user }}</td>
                  <td class="py-4 px-4 text-blue-200">{{ login.timestamp }}</td>
                  <td class="py-4 px-4 text-blue-200">{{ login.ipAddress }}</td>
                  <td class="py-4 px-4 text-blue-200">{{ login.device }}</td>
                  <td class="py-4 px-4">
                    <span :class="[
                      'px-3 py-1 rounded-full text-sm font-medium',
                      login.status === 'Success' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'
                    ]">
                      {{ login.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Account Setup Tab -->
      <div v-if="activeTab === 'setup'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Bulk User Creation -->
          <div class="bg-slate-800/50 backdrop-blur-lg rounded-2xl p-6 border border-white/10">
            <div class="flex items-center space-x-3 mb-6">
              <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                <Users class="w-6 h-6 text-blue-400" />
              </div>
              <h3 class="text-xl font-bold text-white">Bulk User Creation</h3>
            </div>
            <p class="text-blue-200 mb-6">Upload a CSV file to create multiple user accounts at once.</p>
            <div class="space-y-4">
              <div class="border-2 border-dashed border-white/20 rounded-lg p-8 text-center hover:border-blue-500/50 transition-colors cursor-pointer">
                <Upload class="w-12 h-12 text-blue-400 mx-auto mb-3" />
                <p class="text-white font-medium mb-1">Click to upload CSV file</p>
                <p class="text-blue-300 text-sm">or drag and drop</p>
              </div>
              <button class="w-full px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors font-medium">
                Upload & Create Accounts
              </button>
              <button class="w-full px-4 py-3 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
                Download Template
              </button>
            </div>
          </div>

          <!-- Password Reset Management -->
          <div class="bg-slate-800/50 backdrop-blur-lg rounded-2xl p-6 border border-white/10">
            <div class="flex items-center space-x-3 mb-6">
              <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                <KeyRound class="w-6 h-6 text-purple-400" />
              </div>
              <h3 class="text-xl font-bold text-white">Password Management</h3>
            </div>
            <p class="text-blue-200 mb-6">Reset passwords and manage security settings.</p>
            <div class="space-y-3">
              <button class="w-full flex items-center justify-between p-4 bg-slate-700/50 hover:bg-slate-700 rounded-lg transition-colors">
                <div class="flex items-center space-x-3">
                  <KeyRound class="w-5 h-5 text-blue-400" />
                  <span class="text-white">Reset All Default Passwords</span>
                </div>
                <ChevronRight class="w-5 h-5 text-blue-400" />
              </button>
              <button class="w-full flex items-center justify-between p-4 bg-slate-700/50 hover:bg-slate-700 rounded-lg transition-colors">
                <div class="flex items-center space-x-3">
                  <Shield class="w-5 h-5 text-green-400" />
                  <span class="text-white">Force Password Change</span>
                </div>
                <ChevronRight class="w-5 h-5 text-green-400" />
              </button>
              <button class="w-full flex items-center justify-between p-4 bg-slate-700/50 hover:bg-slate-700 rounded-lg transition-colors">
                <div class="flex items-center space-x-3">
                  <Lock class="w-5 h-5 text-yellow-400" />
                  <span class="text-white">Password Policy Settings</span>
                </div>
                <ChevronRight class="w-5 h-5 text-yellow-400" />
              </button>
            </div>
          </div>
        </div>

        <!-- Quick Account Setup -->
        <div class="bg-slate-800/50 backdrop-blur-lg rounded-2xl p-6 border border-white/10">
          <h3 class="text-xl font-bold text-white mb-6">Quick Account Setup</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button
              v-for="quickRole in quickRoles"
              :key="quickRole.id"
              @click="openModal('quickSetup', quickRole)"
              class="p-6 bg-slate-700/50 hover:bg-slate-700 rounded-xl transition-all border border-white/10 hover:border-white/20 text-left"
            >
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mb-4">
                <component :is="quickRole.icon" class="w-6 h-6 text-white" />
              </div>
              <h4 class="text-white font-bold mb-2">Create {{ quickRole.name }}</h4>
              <p class="text-blue-200 text-sm">{{ quickRole.description }}</p>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-slate-800 rounded-2xl p-6 max-w-2xl w-full border border-white/10 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-2xl font-bold text-white">{{ modalTitle }}</h3>
          <button @click="closeModal" class="text-blue-200 hover:text-white transition-colors">
            <X class="w-6 h-6" />
          </button>
        </div>

        <!-- Add/Edit User Form -->
        <div v-if="['addUser', 'editUser'].includes(modalType)" class="space-y-4">
          <p v-if="modalError" class="p-3 rounded-lg bg-red-500/15 text-red-200 text-sm">{{ modalError }}</p>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-blue-200 text-sm font-medium mb-2">Full Name</label>
              <input
                v-model="formData.name"
                type="text"
                placeholder="Enter full name"
                class="w-full px-4 py-3 bg-slate-700 border border-white/10 rounded-lg text-white placeholder-blue-200/50 focus:outline-none focus:border-blue-500 transition-colors"
              />
            </div>
            <div>
              <label class="block text-blue-200 text-sm font-medium mb-2">Email</label>
              <input
                v-model="formData.email"
                type="email"
                placeholder="Enter email"
                class="w-full px-4 py-3 bg-slate-700 border border-white/10 rounded-lg text-white placeholder-blue-200/50 focus:outline-none focus:border-blue-500 transition-colors"
              />
            </div>
          </div>
          <div>
            <label class="block text-blue-200 text-sm font-medium mb-2">Role</label>
            <select
              v-model="formData.role"
              class="w-full px-4 py-3 bg-slate-700 border border-white/10 rounded-lg text-white focus:outline-none focus:border-blue-500 disabled:opacity-60"
              :disabled="selectedItem && isOwnerUser(selectedItem)"
            >
              <option value="">Select Role</option>
              <option v-if="selectedItem && isOwnerUser(selectedItem)" value="Owner">Owner</option>
              <option v-for="role in assignableRoles" :key="role.id" :value="role.name">{{ role.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-blue-200 text-sm font-medium mb-2">Phone Number</label>
            <input
              v-model="formData.phone"
              type="tel"
              placeholder="Enter phone number"
              class="w-full px-4 py-3 bg-slate-700 border border-white/10 rounded-lg text-white placeholder-blue-200/50 focus:outline-none focus:border-blue-500 transition-colors"
            />
          </div>
          <div v-if="modalType === 'addUser'">
            <label class="block text-blue-200 text-sm font-medium mb-2">Initial Password</label>
            <input
              v-model="formData.password"
              type="password"
              placeholder="Enter initial password"
              class="w-full px-4 py-3 bg-slate-700 border border-white/10 rounded-lg text-white placeholder-blue-200/50 focus:outline-none focus:border-blue-500 transition-colors"
            />
          </div>
          <div class="flex items-center space-x-2">
            <input
              v-model="formData.sendWelcomeEmail"
              type="checkbox"
              id="sendWelcome"
              class="w-4 h-4 rounded border-white/10 bg-slate-700 text-blue-500 focus:ring-blue-500"
            />
            <label for="sendWelcome" class="text-blue-200 text-sm">Send welcome email with login credentials</label>
          </div>
        </div>

        <!-- View User Details -->
        <div v-if="modalType === 'viewUser'" class="space-y-6">
          <div class="flex items-center space-x-4 p-4 bg-slate-700/30 rounded-lg">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-2xl">
              {{ selectedItem?.name?.charAt(0) }}
            </div>
            <div>
              <h4 class="text-xl font-bold text-white">{{ selectedItem?.name }}</h4>
              <p class="text-blue-200">{{ selectedItem?.email }}</p>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-slate-700/30 rounded-lg">
              <p class="text-blue-300 text-sm mb-1">Role</p>
              <p class="text-white font-medium">{{ selectedItem?.role }}</p>
            </div>
            <div class="p-4 bg-slate-700/30 rounded-lg">
              <p class="text-blue-300 text-sm mb-1">Status</p>
              <p class="text-white font-medium">{{ selectedItem?.status }}</p>
            </div>
            <div class="p-4 bg-slate-700/30 rounded-lg">
              <p class="text-blue-300 text-sm mb-1">Last Login</p>
              <p class="text-white font-medium">{{ selectedItem?.lastLogin }}</p>
            </div>
            <div class="p-4 bg-slate-700/30 rounded-lg">
              <p class="text-blue-300 text-sm mb-1">Account Created</p>
              <p class="text-white font-medium">{{ selectedItem?.created || 'Jan 15, 2025' }}</p>
            </div>
          </div>
        </div>

        <!-- Reset Password Form -->
        <div v-if="modalType === 'resetPassword'" class="space-y-4">
          <p v-if="modalError" class="p-3 rounded-lg bg-red-500/15 text-red-200 text-sm">{{ modalError }}</p>
          <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg">
            <p class="text-blue-300 text-sm">Resetting password for: <span class="font-medium text-white">{{ selectedItem?.name }}</span></p>
          </div>
          <div>
            <label class="block text-blue-200 text-sm font-medium mb-2">New Password</label>
            <input
              v-model="formData.newPassword"
              type="password"
              placeholder="Enter new password"
              class="w-full px-4 py-3 bg-slate-700 border border-white/10 rounded-lg text-white placeholder-blue-200/50 focus:outline-none focus:border-blue-500 transition-colors"
            />
          </div>
          <div>
            <label class="block text-blue-200 text-sm font-medium mb-2">Confirm Password</label>
            <input
              v-model="formData.confirmPassword"
              type="password"
              placeholder="Confirm new password"
              class="w-full px-4 py-3 bg-slate-700 border border-white/10 rounded-lg text-white placeholder-blue-200/50 focus:outline-none focus:border-blue-500 transition-colors"
            />
          </div>
          <div class="flex items-center space-x-2">
            <input
              v-model="formData.forcePasswordChange"
              type="checkbox"
              id="forceChange"
              class="w-4 h-4 rounded border-white/10 bg-slate-700 text-blue-500 focus:ring-blue-500"
            />
            <label for="forceChange" class="text-blue-200 text-sm">Force password change on next login</label>
          </div>
        </div>

        <!-- Manage Permissions Form -->
        <div v-if="modalType === 'managePermissions'" class="space-y-4">
          <div class="p-4 bg-purple-500/10 border border-purple-500/20 rounded-lg mb-4">
            <p class="text-purple-300 text-sm">Managing permissions for: <span class="font-medium text-white">{{ selectedItem?.name }}</span></p>
          </div>
          <div class="space-y-3">
            <div v-for="module in permissionModules" :key="module.id" class="p-4 bg-slate-700/30 rounded-lg">
              <div class="flex items-center justify-between mb-3">
                <h4 class="text-white font-medium">{{ module.name }}</h4>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" :checked="hasPermission(selectedItem?.id, module.id)" class="sr-only peer">
                  <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                </label>
              </div>
              <div class="grid grid-cols-4 gap-2">
                <label v-for="action in ['View', 'Create', 'Edit', 'Delete']" :key="action" class="flex items-center space-x-2">
                  <input type="checkbox" class="w-4 h-4 rounded border-white/10 bg-slate-700 text-blue-500 focus:ring-blue-500">
                  <span class="text-blue-200 text-sm">{{ action }}</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Add/Edit Role Form -->
        <div v-if="['addRole', 'editRole'].includes(modalType)" class="space-y-4">
          <div>
            <label class="block text-blue-200 text-sm font-medium mb-2">Role Name</label>
            <input
              v-model="formData.roleName"
              type="text"
              placeholder="Enter role name"
              class="w-full px-4 py-3 bg-slate-700 border border-white/10 rounded-lg text-white placeholder-blue-200/50 focus:outline-none focus:border-blue-500 transition-colors"
            />
          </div>
          <div>
            <label class="block text-blue-200 text-sm font-medium mb-2">Description</label>
            <textarea
              v-model="formData.roleDescription"
              rows="3"
              placeholder="Enter role description"
              class="w-full px-4 py-3 bg-slate-700 border border-white/10 rounded-lg text-white placeholder-blue-200/50 focus:outline-none focus:border-blue-500 transition-colors resize-none"
            ></textarea>
          </div>
          <div>
            <label class="block text-blue-200 text-sm font-medium mb-2">Select Permissions</label>
            <div class="max-h-64 overflow-y-auto space-y-2 p-4 bg-slate-700/30 rounded-lg">
              <label v-for="module in permissionModules" :key="module.id" class="flex items-center space-x-3 p-2 hover:bg-slate-700/50 rounded cursor-pointer">
                <input type="checkbox" class="w-4 h-4 rounded border-white/10 bg-slate-700 text-blue-500 focus:ring-blue-500">
                <span class="text-white">{{ module.name }}</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-white/10">
          <button
            @click="closeModal"
            class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors"
          >
            Cancel
          </button>
          <button 
            v-if="modalType !== 'viewUser'"
            @click="handleModalAction"
            :disabled="saving"
            class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors disabled:opacity-50"
          >
            {{ saving ? 'Saving…' : (modalType === 'resetPassword' ? 'Reset Password' : 'Save Changes') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { 
  Users, Shield, UserCheck, Activity, Search, UserPlus, Eye, Edit, Trash2, 
  KeyRound, Ban, Settings, Plus, Check, X, Download, Upload, ChevronRight, 
  Lock, LogIn, ShieldCheck
} from 'lucide-vue-next';

const { apiFetch } = useApi()
const userStore = useUserStore()
const canManageUsers = computed(() => userStore.isSuperAdmin)

const isOwnerUser = (user) => {
  const role = String(user?.role || '').toLowerCase()
  const email = String(user?.email || '').toLowerCase()
  return role === 'super admin' || role === 'owner' || email === 'tssnzuki@gmail.com'
}

const activeTab = ref('users');
const showModal = ref(false);
const modalType = ref('');
const selectedItem = ref(null);
const searchQuery = ref('');
const filterRole = ref('');
const activityFilter = ref('');
const loading = ref(false);
const saving = ref(false);
const pageError = ref('');
const pageSuccess = ref('');
const modalError = ref('');

const formData = ref({
  name: '',
  email: '',
  role: 'User',
  phone: '',
  password: '',
  sendWelcomeEmail: false,
  newPassword: '',
  confirmPassword: '',
  forcePasswordChange: false,
  roleName: '',
  roleDescription: ''
});

const users = ref([]);

const permissionModules = ref([
  { id: 1, name: 'Admissions' },
  { id: 2, name: 'Requirements' },
  { id: 3, name: 'Projects' },
  { id: 4, name: 'Events' },
  { id: 5, name: 'Announcements' },
  { id: 6, name: 'User Management' },
  { id: 7, name: 'Notifications' },
  { id: 8, name: 'Site Content' },
]);

const rolePermissions = ref({
  1: [1, 2, 3, 4, 5, 6, 7, 8],
  2: [],
});

const recentActivities = ref([]);
const loginHistory = ref([]);

const quickRoles = ref([
  { id: 1, name: 'Admin', description: 'Create an administrator with full dashboard access', icon: ShieldCheck },
  { id: 2, name: 'User', description: 'Create a standard account without dashboard access', icon: Users },
]);

const tabs = [
  { id: 'users', label: 'Users', icon: Users },
  { id: 'roles', label: 'Roles & Permissions', icon: Shield },
  { id: 'activity', label: 'Activity Tracking', icon: Activity },
  { id: 'setup', label: 'Account Setup', icon: ShieldCheck },
];

const formatDate = (value) => {
  if (!value) return ''
  try {
    return new Date(value).toLocaleString()
  } catch {
    return value
  }
}

const mapUser = (u) => {
  const name = u.name || [u.firstName, u.lastName].filter(Boolean).join(' ').trim() || u.email || 'User'
  const raw = String(u.role || 'user').toLowerCase()
  const role = raw === 'super_admin' ? 'Owner' : raw === 'admin' ? 'Admin' : 'User'
  const status = (u.status || 'active').toLowerCase() === 'inactive' ? 'Inactive' : 'Active'
  return {
    id: u.id,
    firstName: u.firstName,
    lastName: u.lastName,
    name,
    email: u.email,
    phone: u.phone || '',
    role,
    status,
    lastLogin: u.last_login ? formatDate(u.last_login) : 'Never',
    created: formatDate(u.created_at),
  }
}

const roles = computed(() => [
  {
    id: 0,
    name: 'Owner',
    description: 'Full privilege: add users, assign roles, and manage the school site',
    userCount: users.value.filter((u) => u.role === 'Owner').length,
    isDefault: true,
  },
  {
    id: 1,
    name: 'Admin',
    description: 'Dashboard access: admissions, events, and content (cannot add users or assign roles)',
    userCount: users.value.filter((u) => u.role === 'Admin').length,
    isDefault: true,
  },
  {
    id: 2,
    name: 'User',
    description: 'Public site account without admin dashboard access',
    userCount: users.value.filter((u) => u.role === 'User').length,
    isDefault: true,
  },
]);

const assignableRoles = computed(() => roles.value.filter((role) => role.name !== 'Owner'))

const activeUsers = computed(() => users.value.filter(u => u.status === 'Active').length);

const filteredUsers = computed(() => {
  let filtered = users.value;
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    filtered = filtered.filter(u =>
      u.name.toLowerCase().includes(q) ||
      u.email.toLowerCase().includes(q)
    );
  }
  if (filterRole.value) {
    filtered = filtered.filter(u => u.role === filterRole.value);
  }
  return filtered;
});

const filteredActivities = computed(() => {
  if (!activityFilter.value) return recentActivities.value;
  return recentActivities.value.filter(a => a.type === activityFilter.value);
});

const modalTitle = computed(() => {
  switch (modalType.value) {
    case 'addUser': return 'Add New User';
    case 'editUser': return 'Edit User';
    case 'viewUser': return 'User Details';
    case 'resetPassword': return 'Reset Password';
    case 'managePermissions': return 'Manage Permissions';
    case 'addRole': return 'Add New Role';
    case 'editRole': return 'Edit Role';
    case 'quickSetup': return `Quick Setup - ${selectedItem.value?.name}`;
    default: return '';
  }
});

const apiError = (e, fallback) => e?.data?.message || e?.message || fallback

const loadUsers = async () => {
  loading.value = true
  pageError.value = ''
  try {
    const res = await apiFetch('/api/admin/users')
    users.value = (res?.data || []).map(mapUser)
  } catch (e) {
    pageError.value = apiError(e, 'Failed to load users')
  } finally {
    loading.value = false
  }
}

const openModal = (type, item = null) => {
  modalType.value = type;
  selectedItem.value = item;
  showModal.value = true;
  modalError.value = '';

  if (type === 'editUser' && item) {
    formData.value = {
      name: item.name,
      email: item.email,
      role: item.role,
      phone: item.phone || '',
      password: '',
      sendWelcomeEmail: false,
      newPassword: '',
      confirmPassword: '',
      forcePasswordChange: false,
      roleName: '',
      roleDescription: ''
    };
  } else if (type === 'quickSetup' && item) {
    resetFormData();
    formData.value.role = item.name;
    modalType.value = 'addUser';
  } else if (type === 'editRole' && item) {
    formData.value.roleName = item.name;
    formData.value.roleDescription = item.description;
  } else {
    resetFormData();
  }
};

const closeModal = () => {
  showModal.value = false;
  modalType.value = '';
  selectedItem.value = null;
  modalError.value = '';
  resetFormData();
};

const resetFormData = () => {
  formData.value = {
    name: '',
    email: '',
    role: 'User',
    phone: '',
    password: '',
    sendWelcomeEmail: false,
    newPassword: '',
    confirmPassword: '',
    forcePasswordChange: false,
    roleName: '',
    roleDescription: ''
  };
};

const roleValue = (label) => {
  const value = String(label || '').toLowerCase()
  if (value === 'admin') return 'admin'
  return 'user'
}

const handleModalAction = async () => {
  modalError.value = ''
  pageError.value = ''
  pageSuccess.value = ''

  if (['addRole', 'editRole', 'managePermissions'].includes(modalType.value)) {
    modalError.value = 'Custom roles are not stored. Use Admin or User.'
    return
  }

  if (!canManageUsers.value) {
    modalError.value = 'Only the school owner can add users and assign roles.'
    return
  }

  saving.value = true
  try {
    if (modalType.value === 'addUser') {
      if (!formData.value.name?.trim() || !formData.value.email?.trim() || !formData.value.password) {
        modalError.value = 'Name, email and password are required'
        return
      }
      await apiFetch('/api/admin/users', {
        method: 'POST',
        body: {
          name: formData.value.name.trim(),
          email: formData.value.email.trim(),
          phone: formData.value.phone || '',
          password: formData.value.password,
          role: roleValue(formData.value.role),
        },
      })
      pageSuccess.value = 'User created'
    } else if (modalType.value === 'editUser' && selectedItem.value) {
      const body = {
        name: formData.value.name.trim(),
        email: formData.value.email.trim(),
        phone: formData.value.phone || '',
      }
      if (!isOwnerUser(selectedItem.value)) {
        body.role = roleValue(formData.value.role)
      }
      await apiFetch(`/api/admin/users/update?id=${selectedItem.value.id}`, {
        method: 'POST',
        body,
      })
      pageSuccess.value = 'User updated'
    } else if (modalType.value === 'resetPassword' && selectedItem.value) {
      if (!formData.value.newPassword || formData.value.newPassword.length < 8) {
        modalError.value = 'Password must be at least 8 characters'
        return
      }
      if (formData.value.newPassword !== formData.value.confirmPassword) {
        modalError.value = 'Passwords do not match'
        return
      }
      await apiFetch(`/api/admin/users/update?id=${selectedItem.value.id}`, {
        method: 'POST',
        body: { password: formData.value.newPassword },
      })
      pageSuccess.value = 'Password reset'
    }

    closeModal()
    await loadUsers()
  } catch (e) {
    modalError.value = apiError(e, 'Request failed')
  } finally {
    saving.value = false
  }
};

const toggleUserStatus = async (user) => {
  if (!canManageUsers.value || isOwnerUser(user)) return
  pageError.value = ''
  pageSuccess.value = ''
  const next = user.status === 'Active' ? 'inactive' : 'active'
  try {
    await apiFetch(`/api/admin/users/update?id=${user.id}`, {
      method: 'POST',
      body: { status: next },
    })
    pageSuccess.value = `${user.name} is now ${next}`
    await loadUsers()
  } catch (e) {
    pageError.value = apiError(e, 'Failed to update status')
  }
};

const deleteUser = async (user) => {
  if (!canManageUsers.value || isOwnerUser(user)) return
  if (!confirm(`Delete ${user.name}? This cannot be undone.`)) return
  pageError.value = ''
  pageSuccess.value = ''
  try {
    await apiFetch(`/api/admin/users/${user.id}`, { method: 'DELETE' })
    pageSuccess.value = 'User deleted'
    await loadUsers()
  } catch (e) {
    pageError.value = apiError(e, 'Failed to delete user')
  }
};

const deleteRole = () => {
  pageError.value = 'Admin and User are system roles and cannot be deleted.'
};

const hasPermission = (roleId, moduleId) => {
  return rolePermissions.value[roleId]?.includes(moduleId) || false;
};

const getRoleColor = (role) => {
  const colors = {
    'Owner': 'bg-amber-500/20 text-amber-300',
    'Admin': 'bg-red-500/20 text-red-400',
    'User': 'bg-blue-500/20 text-blue-400',
  };
  return colors[role] || 'bg-gray-500/20 text-gray-400';
};

const getActivityColor = (type) => {
  const colors = {
    'login': 'bg-blue-500/20 text-blue-400',
    'create': 'bg-green-500/20 text-green-400',
    'update': 'bg-yellow-500/20 text-yellow-400',
    'delete': 'bg-red-500/20 text-red-400',
  };
  return colors[type] || 'bg-gray-500/20 text-gray-400';
};

const getActivityIcon = (type) => {
  const icons = {
    'login': LogIn,
    'create': Plus,
    'update': Edit,
    'delete': Trash2,
  };
  return icons[type] || Activity;
};

const exportActivityLog = () => {
  pageError.value = 'Activity export is not available yet.'
};

onMounted(() => {
  userStore.hydrate()
  loadUsers()
})
</script>

<style scoped>
/* Smooth scrollbar styling */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(15, 23, 42, 0.5);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: rgba(59, 130, 246, 0.5);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(59, 130, 246, 0.7);
}
</style>