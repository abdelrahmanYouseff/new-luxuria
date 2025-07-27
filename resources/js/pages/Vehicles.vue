<template>
  <AppLayout>
    <div class="p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <Heading title="Vehicles" />
        <div class="flex items-center space-x-3">
          <Button
            @click="syncFromApi"
            variant="default"
            size="sm"
            :disabled="syncing"
          >
            <Icon name="refresh-cw" :class="`w-4 h-4 mr-2 ${syncing ? 'animate-spin' : ''}`" />
            {{ syncing ? 'Syncing...' : 'Sync from API' }}
          </Button>
          <Button
            @click="openVehiclesApi"
            variant="outline"
            size="sm"
          >
            <Icon name="database" class="w-4 h-4 mr-2" />
            Vehicles API
          </Button>
          <Button
            @click="refreshPage"
            variant="outline"
            size="sm"
          >
            <Icon name="refresh-cw" class="w-4 h-4 mr-2" />
            Refresh
          </Button>
          <Button
            @click="showHiddenVehicles = !showHiddenVehicles"
            :variant="showHiddenVehicles ? 'default' : 'outline'"
            size="sm"
          >
            <Icon :name="showHiddenVehicles ? 'eye-off' : 'eye'" class="w-4 h-4 mr-2" />
            {{ showHiddenVehicles ? 'Hide Hidden' : 'Show Hidden' }} ({{ hiddenCount }})
          </Button>
        </div>
      </div>

      <!-- API Status Alert -->
      <div v-if="error" class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-center">
          <Icon name="alert-triangle" class="w-5 h-5 text-yellow-600 mr-2" />
          <span class="text-sm text-yellow-800">{{ error }}</span>
        </div>
      </div>

      <div v-if="apiStatus === 'success'" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center">
          <Icon name="check-circle" class="w-5 h-5 text-green-600 mr-2" />
          <span class="text-sm text-green-800">
            Successfully loaded {{ totalCount }} vehicles from {{ dataSource === 'database' ? 'database' : 'API' }}
          </span>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="bg-white rounded-lg shadow-sm border">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Vehicles List</h3>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plate Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ownership</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>

                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Daily Rate</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="vehicle in filteredVehicles" :key="vehicle.id" :class="[
                'hover:bg-gray-50',
                !vehicle.is_visible ? 'bg-red-25 border-l-4 border-red-300' : ''
              ]">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3 overflow-hidden">
                      <img
                        v-if="vehicle.image"
                        :src="vehicle.image_url"
                        :alt="vehicle.name"
                        class="w-full h-full object-cover"
                      />
                      <Icon v-else name="car" class="w-6 h-6 text-blue-600" />
                    </div>
                    <div>
                      <div class="text-sm font-medium text-gray-900 flex items-center">
                        {{ vehicle.name }}
                        <Icon
                          :name="vehicle.is_visible ? 'eye' : 'eye-off'"
                          :class="`w-4 h-4 ml-2 ${vehicle.is_visible ? 'text-green-600' : 'text-red-600'}`"
                          :title="vehicle.is_visible ? 'Visible on website' : 'Hidden from website'"
                        />
                        <span v-if="!vehicle.is_visible" class="ml-1 text-xs text-red-500">(Hidden)</span>
                      </div>
                      <div class="text-sm text-gray-500">{{ vehicle.model }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                    {{ vehicle.plateNumber }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="space-y-1">
                    <span :class="[
                      'px-2 py-1 text-xs font-medium rounded-full',
                      vehicle.status === 'Available' ? 'bg-green-100 text-green-800' :
                      vehicle.status === 'Rented' ? 'bg-red-100 text-red-800' :
                      vehicle.status === 'Maintenance' ? 'bg-yellow-100 text-yellow-800' :
                      'bg-gray-100 text-gray-800'
                    ]">
                      {{ vehicle.status }}
                    </span>
                    <div v-if="!vehicle.is_visible" class="text-xs text-red-600 font-medium bg-red-50 px-2 py-1 rounded border border-red-200">
                      Hidden from website
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'px-2 py-1 text-xs font-medium rounded-full',
                    vehicle.category === 'Economy' ? 'bg-blue-100 text-blue-800' :
                    vehicle.category === 'Luxury' ? 'bg-purple-100 text-purple-800' :
                    vehicle.category === 'SUV' ? 'bg-green-100 text-green-800' :
                    'bg-gray-100 text-gray-800'
                  ]">
                    {{ vehicle.category }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'px-2 py-1 text-xs font-medium rounded-full',
                    vehicle.ownership === 'Owned' ? 'bg-green-100 text-green-800' :
                    'bg-orange-100 text-orange-800'
                  ]">
                    {{ vehicle.ownership }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ vehicle.year || 'N/A' }}
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span class="font-medium">AED {{ vehicle.dailyRate }}</span>
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
                      <DropdownMenuItem @click="viewVehicle(vehicle.id)">
                        <Icon name="eye" class="w-4 h-4 mr-2" />
                        View Details
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="editVehicle(vehicle.id)">
                        <Icon name="edit" class="w-4 h-4 mr-2" />
                        Edit Vehicle
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="manageImage(vehicle.id)">
                        <Icon name="image" class="w-4 h-4 mr-2" />
                        Manage Image
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="toggleVisibility(vehicle)" :class="vehicle.is_visible ? 'text-orange-600' : 'text-green-600'">
                        <Icon :name="vehicle.is_visible ? 'eye-off' : 'eye'" class="w-4 h-4 mr-2" />
                        {{ vehicle.is_visible ? 'Hide from Website' : 'Show on Website' }}
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="deleteVehicle(vehicle.id)" class="text-red-600">
                        <Icon name="trash-2" class="w-4 h-4 mr-2" />
                        Delete Vehicle
                      </DropdownMenuItem>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing <span class="font-medium">{{ filteredVehicles.length }}</span> of <span class="font-medium">{{ vehicles.length }}</span> vehicles
              <span v-if="hiddenCount > 0" class="text-gray-500 ml-2">({{ visibleCount }} visible, {{ hiddenCount }} hidden)</span>
            </div>
            <div class="flex items-center space-x-2">
              <Button size="sm" variant="outline" disabled>Previous</Button>
              <Button size="sm" variant="outline" disabled>Next</Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import Button from '@/components/ui/button/Button.vue'
import Icon from '@/components/Icon.vue'
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
  name: string
  model: string
  plateNumber: string
  status: string
  category: string
  ownership: string
  year: string
  color: string
  transmission: string
  odometer: number
  dailyRate: number
  image: string | null
  image_url?: string
  is_visible?: boolean
}

// Get vehicles from API or props
const vehicles = ref<Vehicle[]>(page.props.vehicles as Vehicle[] || [])
const apiStatus = ref(page.props.apiStatus || 'loading')
const error = ref(page.props.error || null)
const totalCount = ref(page.props.totalCount || 0)
const dataSource = ref(page.props.dataSource || 'database')

// Computed properties
const filteredVehicles = computed(() => {
  if (showHiddenVehicles.value) {
    return vehicles.value
  }
  return vehicles.value.filter(vehicle => vehicle.is_visible !== false)
})

const visibleCount = computed(() => vehicles.value.filter(v => v.is_visible !== false).length)
const hiddenCount = computed(() => vehicles.value.filter(v => v.is_visible === false).length)

// State
const syncing = ref(false)
const showHiddenVehicles = ref(true) // Default to show all vehicles including hidden ones

const refreshPage = () => {
  window.location.reload()
}



const editVehicle = (id: number) => {
  alert(`Edit vehicle ${id}`)
}

const viewVehicle = (id: number) => {
  alert(`View vehicle ${id}`)
}

const deleteVehicle = (id: number) => {
  if (confirm('Are you sure you want to delete this vehicle?')) {
    alert(`Delete vehicle ${id}`)
  }
}

const manageImage = (id: number) => {
  // Open image management in new tab
  window.open(`/vehicles/${id}/image-blade`, '_blank')
}

const syncFromApi = async () => {
  syncing.value = true
  try {
    await router.post('/vehicles/sync')
    // Refresh the page to show updated data
    window.location.reload()
  } catch (error) {
    console.error('Sync failed:', error)
    alert('Failed to sync vehicles from API')
  } finally {
    syncing.value = false
  }
}

const openVehiclesApi = () => {
  window.location.href = '/vehicles-api'
}

const toggleVisibility = async (vehicle: Vehicle) => {
  try {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

    if (!csrfToken) {
      console.error('CSRF token not found')
      alert('CSRF token not found. Please refresh the page and try again.')
      return
    }

    console.log('Toggling visibility for vehicle:', vehicle.id)
    console.log('CSRF Token:', csrfToken)

    const response = await fetch(`/vehicles/${vehicle.id}/toggle-visibility`, {
      method: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    })

    console.log('Response status:', response.status)
    console.log('Response headers:', response.headers)

    if (response.ok) {
      const data = await response.json()
      console.log('Response data:', data)

      // Update the vehicle's visibility status locally
      vehicle.is_visible = data.is_visible

      // Show success message
      alert(data.message)

      // Refresh the page to show updated data
      window.location.reload()
    } else {
      const errorText = await response.text()
      console.error('Response error:', errorText)
      throw new Error(`HTTP ${response.status}: ${errorText}`)
    }
  } catch (error) {
    console.error('Error toggling visibility:', error)
    const errorMessage = error instanceof Error ? error.message : 'Unknown error'
    alert(`Failed to toggle vehicle visibility: ${errorMessage}`)
  }
}
</script>
