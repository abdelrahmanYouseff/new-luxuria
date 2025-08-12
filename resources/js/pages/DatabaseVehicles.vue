<template>
  <AppLayout>
    <div class="p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <Heading title="Database Vehicles" />
        <div class="flex items-center space-x-3">
          <Button
            @click="syncFromApi"
            variant="outline"
            size="sm"
            :disabled="syncing"
          >
            <Icon name="refresh-cw" :class="`w-4 h-4 mr-2 ${syncing ? 'animate-spin' : ''}`" />
            {{ syncing ? 'Syncing...' : 'Sync from API' }}
          </Button>
          <Button
            @click="createVehicle"
            size="sm"
          >
            <Icon name="plus" class="w-4 h-4 mr-2" />
            Add Vehicle
          </Button>
        </div>
      </div>

      <!-- Sync Status -->
      <div v-if="syncResult" class="mb-6 p-4 rounded-lg" :class="syncResult.success ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
        <div class="flex items-center">
          <Icon :name="syncResult.success ? 'check-circle' : 'alert-triangle'" class="w-5 h-5 mr-2" :class="syncResult.success ? 'text-green-600' : 'text-red-600'" />
          <span class="text-sm" :class="syncResult.success ? 'text-green-800' : 'text-red-800'">
            {{ syncResult.success ?
              `Sync completed! ${syncResult.synced_count} new vehicles added, ${syncResult.updated_count} vehicles updated.` :
              `Sync failed: ${syncResult.error}`
            }}
          </span>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow-sm border mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Filters</h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <Label for="category">Category</Label>
              <select id="category" v-model="filters.category" class="w-full mt-1 border border-gray-300 rounded-md px-3 py-2">
                <option value="">All Categories</option>
                <option value="economy">Economy</option>
                <option value="mid-range">Mid-Range</option>
                <option value="luxury">Luxury</option>
                <option value="suv">SUV</option>
                <option value="sports">Sports</option>
              </select>
            </div>
            <div>
              <Label for="status">Status</Label>
              <select id="status" v-model="filters.status" class="w-full mt-1 border border-gray-300 rounded-md px-3 py-2">
                <option value="">All Status</option>
                <option value="available">Available</option>
                <option value="rented">Rented</option>
                <option value="maintenance">Maintenance</option>
                <option value="out_of_service">Out of Service</option>
                <option value="reserved">Reserved</option>
              </select>
            </div>
            <div>
              <Label for="make">Make</Label>
              <Input id="make" v-model="filters.make" placeholder="Search by make..." />
            </div>
            <div class="flex items-end">
              <Button @click="clearFilters" variant="outline" size="sm" class="w-full">
                <Icon name="x" class="w-4 h-4 mr-2" />
                Clear Filters
              </Button>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
              <Icon name="car" class="w-6 h-6 text-blue-600" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600">Total Vehicles</p>
              <p class="text-2xl font-bold text-gray-900">{{ totalCount }}</p>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
              <Icon name="check-circle" class="w-6 h-6 text-green-600" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600">Available</p>
              <p class="text-2xl font-bold text-gray-900">{{ availableCount }}</p>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
              <Icon name="car" class="w-6 h-6 text-red-600" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600">Rented</p>
              <p class="text-2xl font-bold text-gray-900">{{ rentedCount }}</p>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
              <Icon name="wrench" class="w-6 h-6 text-yellow-600" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-600">Maintenance</p>
              <p class="text-2xl font-bold text-gray-900">{{ maintenanceCount }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Vehicles Table -->
      <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Vehicles</h3>
            <div class="flex items-center space-x-2">
              <Button
                @click="exportData"
                variant="outline"
                size="sm"
              >
                <Icon name="download" class="w-4 h-4 mr-2" />
                Export CSV
              </Button>
            </div>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plate Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Daily Rate</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="vehicle in filteredVehicles" :key="vehicle.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                      <img v-if="vehicle.image_url" :src="vehicle.image_url" :alt="vehicle.display_name" class="w-12 h-12 object-cover rounded-lg" />
                      <Icon v-else name="car" class="w-6 h-6 text-blue-600" />
                    </div>
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ vehicle.display_name }}</div>
                      <div class="text-sm text-gray-500">{{ vehicle.year }} • {{ vehicle.color }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                    {{ vehicle.plate_number || 'N/A' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="['px-2 py-1 text-xs font-medium rounded-full', vehicle.status_badge_class]">
                    {{ vehicle.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="['px-2 py-1 text-xs font-medium rounded-full', vehicle.category_badge_class]">
                    {{ vehicle.category }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span class="font-medium">AED {{ vehicle.daily_rate }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                      <Button size="sm" variant="outline" class="h-8 w-8 p-0">
                        <Icon name="more-horizontal" class="w-4 h-4" />
                        <span class="sr-only">Open menu</span>
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                      <DropdownMenuItem @click="viewVehicle(vehicle)">
                        <Icon name="eye" class="w-4 h-4 mr-2" />
                        View Details
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="editVehicle(vehicle)">
                        <Icon name="edit" class="w-4 h-4 mr-2" />
                        Edit
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="deleteVehicle(vehicle)">
                        <Icon name="trash" class="w-4 h-4 mr-2" />
                        Delete
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="filteredVehicles.length === 0" class="text-center py-12">
          <Icon name="car" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
          <h3 class="text-lg font-medium text-gray-900 mb-2">No vehicles found</h3>
          <p class="text-gray-500 mb-4">Try adjusting your filters or sync from API to get started.</p>
          <Button @click="syncFromApi" variant="outline">
            <Icon name="refresh-cw" class="w-4 h-4 mr-2" />
            Sync from API
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import Button from '@/components/ui/button/Button.vue'
import Icon from '@/components/Icon.vue'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

const page = usePage()

// Define vehicle type
interface Vehicle {
  id: number
  api_id?: string
  plate_number?: string
  status: string
  ownership_status: string
  make?: string
  model?: string
  year?: string
  color?: string
  category: string
  daily_rate: number
  weekly_rate?: number
  monthly_rate?: number
  transmission: string
  seats: number
  doors: number
  odometer: number
  description?: string
  image?: string
  image_url?: string
  display_name?: string
  status_badge_class?: string
  category_badge_class?: string
  ownership_badge_class?: string
  created_at: string
  updated_at: string
}

// Define sync result type
interface SyncResult {
  success: boolean
  synced_count?: number
  updated_count?: number
  error?: string
}

// Props
const vehicles = ref<Vehicle[]>(page.props.vehicles as Vehicle[] || [])
const syncResult = ref<SyncResult | null>(page.props.syncResult as SyncResult || null)
const totalCount = ref(page.props.totalCount || 0)

// State
const syncing = ref(false)
const filters = ref({
  category: '',
  status: '',
  make: ''
})

// Computed
const filteredVehicles = computed(() => {
  return vehicles.value.filter(vehicle => {
    if (filters.value.category && vehicle.category !== filters.value.category) return false
    if (filters.value.status && vehicle.status !== filters.value.status) return false
    if (filters.value.make && !vehicle.make?.toLowerCase().includes(filters.value.make.toLowerCase())) return false
    return true
  })
})

const availableCount = computed(() => vehicles.value.filter(v => v.status === 'available').length)
const rentedCount = computed(() => vehicles.value.filter(v => v.status === 'rented').length)
const maintenanceCount = computed(() => vehicles.value.filter(v => v.status === 'maintenance').length)

// Methods
const syncFromApi = async () => {
  syncing.value = true
  try {
    await router.post('/database/vehicles/sync')
  } catch (error) {
    console.error('Sync failed:', error)
  } finally {
    syncing.value = false
  }
}

const createVehicle = () => {
  router.get('/database/vehicles/create')
}

const viewVehicle = (vehicle: Vehicle) => {
  router.get(`/database/vehicles/${vehicle.id}`)
}

const editVehicle = (vehicle: Vehicle) => {
  router.get(`/database/vehicles/${vehicle.id}/edit`)
}

const deleteVehicle = async (vehicle: Vehicle) => {
  if (confirm(`Are you sure you want to delete ${vehicle.display_name}?`)) {
    await router.delete(`/database/vehicles/${vehicle.id}`)
  }
}

const clearFilters = () => {
  filters.value = {
    category: '',
    status: '',
    make: ''
  }
}

const exportData = () => {
  const csvContent = [
    ['ID', 'Make', 'Model', 'Year', 'Color', 'Category', 'Status', 'Plate Number', 'Daily Rate', 'Transmission', 'Seats', 'Doors'],
    ...filteredVehicles.value.map(v => [
      v.id,
      v.make || '',
      v.model || '',
      v.year || '',
      v.color || '',
      v.category,
      v.status,
      v.plate_number || '',
      v.daily_rate,
      v.transmission,
      v.seats,
      v.doors
    ])
  ].map(row => row.join(',')).join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = 'vehicles.csv'
  link.click()
  URL.revokeObjectURL(url)
}

onMounted(() => {
  // Clear sync result after 5 seconds
  if (syncResult.value) {
    setTimeout(() => {
      syncResult.value = null
    }, 5000)
  }
})
</script>
