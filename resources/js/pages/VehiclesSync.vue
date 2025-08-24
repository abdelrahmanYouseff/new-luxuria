<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Sync Vehicles from API</h1>
        <p class="text-gray-600">Synchronize vehicles from external API to local database</p>
      </div>

      <!-- Sync Status Card -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold text-gray-900">Sync Status</h2>
          <div class="flex space-x-2">
            <button
              @click="checkApiConnection"
              :disabled="loading"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
            >
              {{ loading ? 'Checking...' : 'Check API Connection' }}
            </button>
            <button
              @click="syncVehicles"
              :disabled="syncing"
              class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
            >
              {{ syncing ? 'Syncing...' : 'Sync Vehicles' }}
            </button>
          </div>
        </div>

        <!-- API Connection Status -->
        <div v-if="apiStatus" class="mb-4">
          <div class="flex items-center space-x-2">
            <div
              :class="[
                'w-3 h-3 rounded-full',
                apiStatus.success ? 'bg-green-500' : 'bg-red-500'
              ]"
            ></div>
            <span class="font-medium">
              {{ apiStatus.success ? 'API Connected' : 'API Connection Failed' }}
            </span>
          </div>
          <p v-if="apiStatus.message" class="text-sm text-gray-600 mt-1">
            {{ apiStatus.message }}
          </p>
        </div>

        <!-- Sync Results -->
        <div v-if="syncResults" class="space-y-3">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 p-4 rounded-lg">
              <div class="text-2xl font-bold text-green-600">
                {{ syncResults.synced_count || 0 }}
              </div>
              <div class="text-sm text-green-700">New Vehicles</div>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg">
              <div class="text-2xl font-bold text-blue-600">
                {{ syncResults.updated_count || 0 }}
              </div>
              <div class="text-sm text-blue-700">Updated Vehicles</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-2xl font-bold text-gray-600">
                {{ syncResults.total_api_vehicles || 0 }}
              </div>
              <div class="text-sm text-gray-700">Total in API</div>
            </div>
          </div>

          <!-- Errors -->
          <div v-if="syncResults.errors && syncResults.errors.length > 0" class="mt-4">
            <h3 class="font-medium text-red-700 mb-2">Errors ({{ syncResults.errors.length }})</h3>
            <div class="bg-red-50 p-3 rounded-lg max-h-40 overflow-y-auto">
              <div v-for="error in syncResults.errors" :key="error.api_id" class="text-sm text-red-600">
                <strong>{{ error.api_id }}:</strong> {{ error.error }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Database Status -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Database Status</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-gray-600">
              {{ databaseStats.total || 0 }}
            </div>
            <div class="text-sm text-gray-700">Total Vehicles</div>
          </div>
          <div class="bg-green-50 p-4 rounded-lg">
            <div class="text-2xl font-bold text-green-600">
              {{ databaseStats.available || 0 }}
            </div>
            <div class="text-sm text-green-700">Available Vehicles</div>
          </div>
        </div>
      </div>

      <!-- Recent Vehicles -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Vehicles</h2>
        <div v-if="recentVehicles.length > 0" class="space-y-3">
          <div
            v-for="vehicle in recentVehicles"
            :key="vehicle.id"
            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
          >
            <div>
              <div class="font-medium">{{ vehicle.make }} {{ vehicle.model }}</div>
              <div class="text-sm text-gray-600">
                {{ vehicle.plate_number }} • {{ vehicle.year }} • AED {{ vehicle.daily_rate }}/day
              </div>
            </div>
            <div class="text-right">
              <span
                :class="[
                  'px-2 py-1 text-xs rounded-full',
                  vehicle.status === 'Available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]"
              >
                {{ vehicle.status }}
              </span>
            </div>
          </div>
        </div>
        <div v-else class="text-center text-gray-500 py-8">
          No vehicles found in database
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const loading = ref(false)
const syncing = ref(false)
const apiStatus = ref(null)
const syncResults = ref(null)
const databaseStats = ref({})
const recentVehicles = ref([])

const checkApiConnection = async () => {
  loading.value = true
  try {
    const response = await fetch('/api/v1/vehicles/test/connection')
    const data = await response.json()
    apiStatus.value = data
  } catch (error) {
    apiStatus.value = {
      success: false,
      message: 'Failed to connect to API'
    }
  } finally {
    loading.value = false
  }
}

const syncVehicles = async () => {
  syncing.value = true
  try {
    const response = await fetch('/vehicles/sync', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    const data = await response.json()
    syncResults.value = data
    if (data.success) {
      // Refresh database stats
      loadDatabaseStats()
      loadRecentVehicles()
    }
  } catch (error) {
    syncResults.value = {
      success: false,
      error: 'Failed to sync vehicles'
    }
  } finally {
    syncing.value = false
  }
}

const loadDatabaseStats = async () => {
  try {
    const response = await fetch('/api/vehicles')
    const data = await response.json()
    if (data.success) {
      databaseStats.value = {
        total: data.pagination?.total || 0,
        available: data.data?.filter(v => v.status === 'Available').length || 0
      }
    }
  } catch (error) {
    console.error('Failed to load database stats:', error)
  }
}

const loadRecentVehicles = async () => {
  try {
    const response = await fetch('/api/vehicles?per_page=5')
    const data = await response.json()
    if (data.success) {
      recentVehicles.value = data.data || []
    }
  } catch (error) {
    console.error('Failed to load recent vehicles:', error)
  }
}

onMounted(() => {
  loadDatabaseStats()
  loadRecentVehicles()
})
</script>
