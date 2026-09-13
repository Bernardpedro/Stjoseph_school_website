<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-6 py-8">
      <div v-if="pageError" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">{{ pageError }}</div>
      <div v-if="pageSuccess" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700">{{ pageSuccess }}</div>
      <div v-if="loading" class="mb-6 text-gray-500">Loading users…</div>

      <!-- Users -->
      <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Manage Users</h2>
            <p v-if="!canManageUsers" class="text-gray-500 mt-1">
              Only administrators can add users and assign roles.
            </p>
          </div>
          <div class="flex flex-wrap gap-3">
            <div class="relative">
              <Search class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search users..."
                class="pl-10 pr-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors"
              />
            </div>
            <select v-model="filterRole" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-blue-500">
              <option value="">All Roles</option>
              <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
            </select>
            <button
              v-if="canManageUsers"
              @click="openModal('addUser')"
              class="flex items-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
            >
              <UserPlus class="w-4 h-4" />
              <span>Add User</span>
            </button>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200">
                <th class="text-left py-3 px-4 text-gray-500 font-semibold">User</th>
                <th class="text-left py-3 px-4 text-gray-500 font-semibold">Email</th>
                <th class="text-left py-3 px-4 text-gray-500 font-semibold">Role</th>
                <th class="text-left py-3 px-4 text-gray-500 font-semibold">Status</th>
                <th class="text-left py-3 px-4 text-gray-500 font-semibold">Last Login</th>
                <th class="text-left py-3 px-4 text-gray-500 font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!loading && !filteredUsers.length">
                <td colspan="6" class="py-8 px-4 text-center text-gray-500">No users found.</td>
              </tr>
              <tr v-for="user in filteredUsers" :key="user.id" class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <td class="py-4 px-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold">
                      {{ (user.name || '?').charAt(0) }}
                    </div>
                    <span class="text-gray-900 font-medium">{{ user.name }}</span>
                  </div>
                </td>
                <td class="py-4 px-4 text-gray-500">{{ user.email }}</td>
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
                    user.status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                  ]">
                    {{ user.status }}
                  </span>
                </td>
                <td class="py-4 px-4 text-gray-500">{{ user.lastLogin }}</td>
                <td class="py-4 px-4">
                  <div class="flex space-x-2">
                    <button
                      @click="openModal('viewUser', user)"
                      class="p-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors"
                      title="View Details"
                    >
                      <Eye class="w-4 h-4" />
                    </button>
                    <button
                      v-if="canManageUsers"
                      @click="openModal('editUser', user)"
                      class="p-2 bg-green-50 hover:bg-green-100 text-green-600 rounded-lg transition-colors"
                      title="Edit User"
                    >
                      <Edit class="w-4 h-4" />
                    </button>
                    <button
                      v-if="canManageUsers"
                      @click="openModal('resetPassword', user)"
                      class="p-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-600 rounded-lg transition-colors"
                      title="Reset Password"
                    >
                      <KeyRound class="w-4 h-4" />
                    </button>
                    <button
                      v-if="canManageUsers && !isOwnerUser(user)"
                      @click="toggleUserStatus(user)"
                      class="p-2 bg-orange-50 hover:bg-orange-100 text-orange-600 rounded-lg transition-colors"
                      :title="user.status === 'Active' ? 'Deactivate' : 'Activate'"
                    >
                      <Ban class="w-4 h-4" />
                    </button>
                    <button
                      v-if="canManageUsers && !isOwnerUser(user)"
                      @click="deleteUser(user)"
                      class="p-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors"
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
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white rounded-2xl p-6 max-w-2xl w-full border border-gray-200 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-2xl font-bold text-gray-900">{{ modalTitle }}</h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-700 transition-colors">
            <X class="w-6 h-6" />
          </button>
        </div>

        <!-- Add/Edit User Form -->
        <div v-if="['addUser', 'editUser'].includes(modalType)" class="space-y-4">
          <p v-if="modalError" class="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{{ modalError }}</p>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-600 text-sm font-medium mb-2">Full Name</label>
              <input
                v-model="formData.name"
                type="text"
                placeholder="Enter full name"
                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors"
              />
            </div>
            <div>
              <label class="block text-gray-600 text-sm font-medium mb-2">Email</label>
              <input
                v-model="formData.email"
                type="email"
                placeholder="Enter email"
                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors"
              />
            </div>
          </div>
          <div>
            <label class="block text-gray-600 text-sm font-medium mb-2">Role</label>
            <select
              v-model="formData.role"
              class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-blue-500 disabled:opacity-60"
              :disabled="selectedItem && isOwnerUser(selectedItem)"
            >
              <option value="">Select Role</option>
              <option v-if="selectedItem && isOwnerUser(selectedItem)" value="Owner">Owner</option>
              <option v-for="role in assignableRoles" :key="role.id" :value="role.name">{{ role.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-gray-600 text-sm font-medium mb-2">Phone Number</label>
            <input
              v-model="formData.phone"
              type="tel"
              placeholder="Enter phone number"
              class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors"
            />
          </div>
          <div v-if="modalType === 'addUser'">
            <label class="block text-gray-600 text-sm font-medium mb-2">Initial Password</label>
            <input
              v-model="formData.password"
              type="password"
              placeholder="Enter initial password"
              class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors"
            />
          </div>
          <div class="flex items-center space-x-2">
            <input
              v-model="formData.sendWelcomeEmail"
              type="checkbox"
              id="sendWelcome"
              class="w-4 h-4 rounded border-gray-300 bg-white text-blue-600 focus:ring-blue-500"
            />
            <label for="sendWelcome" class="text-gray-600 text-sm">Send welcome email with login credentials</label>
          </div>
        </div>

        <!-- View User Details -->
        <div v-if="modalType === 'viewUser'" class="space-y-6">
          <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-2xl">
              {{ selectedItem?.name?.charAt(0) }}
            </div>
            <div>
              <h4 class="text-xl font-bold text-gray-900">{{ selectedItem?.name }}</h4>
              <p class="text-gray-500">{{ selectedItem?.email }}</p>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg">
              <p class="text-gray-400 text-sm mb-1">Role</p>
              <p class="text-gray-900 font-medium">{{ selectedItem?.role }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
              <p class="text-gray-400 text-sm mb-1">Status</p>
              <p class="text-gray-900 font-medium">{{ selectedItem?.status }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
              <p class="text-gray-400 text-sm mb-1">Last Login</p>
              <p class="text-gray-900 font-medium">{{ selectedItem?.lastLogin }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
              <p class="text-gray-400 text-sm mb-1">Account Created</p>
              <p class="text-gray-900 font-medium">{{ selectedItem?.created || 'Jan 15, 2025' }}</p>
            </div>
          </div>
        </div>

        <!-- Reset Password Form -->
        <div v-if="modalType === 'resetPassword'" class="space-y-4">
          <p v-if="modalError" class="p-3 rounded-lg bg-red-50 text-red-700 text-sm">{{ modalError }}</p>
          <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-blue-700 text-sm">Resetting password for: <span class="font-medium text-gray-900">{{ selectedItem?.name }}</span></p>
          </div>
          <div>
            <label class="block text-gray-600 text-sm font-medium mb-2">New Password</label>
            <input
              v-model="formData.newPassword"
              type="password"
              placeholder="Enter new password"
              class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors"
            />
          </div>
          <div>
            <label class="block text-gray-600 text-sm font-medium mb-2">Confirm Password</label>
            <input
              v-model="formData.confirmPassword"
              type="password"
              placeholder="Confirm new password"
              class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors"
            />
          </div>
          <div class="flex items-center space-x-2">
            <input
              v-model="formData.forcePasswordChange"
              type="checkbox"
              id="forceChange"
              class="w-4 h-4 rounded border-gray-300 bg-white text-blue-600 focus:ring-blue-500"
            />
            <label for="forceChange" class="text-gray-600 text-sm">Force password change on next login</label>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
          <button
            @click="closeModal"
            class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors"
          >
            Cancel
          </button>
          <button
            v-if="modalType !== 'viewUser'"
            @click="handleModalAction"
            :disabled="saving"
            class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition-colors disabled:opacity-50"
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
  Eye, Edit, Trash2, KeyRound, Ban, UserPlus, Search, X
} from 'lucide-vue-next';

const { apiFetch } = useApi()
const userStore = useUserStore()
const { confirmDialog } = useConfirmDialog()
const canManageUsers = computed(() => userStore.isAdmin)

const isOwnerUser = (user) => {
  const role = String(user?.role || '').toLowerCase()
  const email = String(user?.email || '').toLowerCase()
  return role === 'super admin' || role === 'owner' || email === 'tssnzuki@gmail.com'
}

const showModal = ref(false);
const modalType = ref('');
const selectedItem = ref(null);
const searchQuery = ref('');
const filterRole = ref('');
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
});

const users = ref([]);

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

const modalTitle = computed(() => {
  switch (modalType.value) {
    case 'addUser': return 'Add New User';
    case 'editUser': return 'Edit User';
    case 'viewUser': return 'User Details';
    case 'resetPassword': return 'Reset Password';
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
    };
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

  if (!canManageUsers.value) {
    modalError.value = 'Only administrators can add users and assign roles.'
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
  const confirmed = await confirmDialog(`Delete ${user.name}? This cannot be undone.`, {
    title: 'Delete user',
    confirmText: 'Delete',
    danger: true,
  })
  if (!confirmed) return
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

const getRoleColor = (role) => {
  const colors = {
    'Owner': 'bg-amber-100 text-amber-700',
    'Admin': 'bg-red-100 text-red-700',
    'User': 'bg-blue-100 text-blue-700',
  };
  return colors[role] || 'bg-gray-100 text-gray-600';
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
  background: rgba(226, 232, 240, 0.6);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: rgba(148, 163, 184, 0.6);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(100, 116, 139, 0.7);
}
</style>
