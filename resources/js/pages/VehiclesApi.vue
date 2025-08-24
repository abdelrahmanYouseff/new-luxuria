<template>
  <AppLayout>
    <div class="p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <Heading title="Vehicles API" />
        <Button
          @click="refreshData"
          variant="outline"
          size="sm"
        >
          <Icon name="refresh-cw" class="w-4 h-4 mr-2" />
          Refresh API
        </Button>
      </div>

      <!-- API Status Alert -->
      <div v-if="error" class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-center">
          <Icon name="alert-triangle" class="w-5 h-5 text-yellow-600 mr-2" />
          <span class="text-sm text-yellow-800">{{ error }}</span>
        </div>
      </div>

      <div v-if="apiStatus === 'success' && syncResult" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center">
          <Icon name="check-circle" class="w-5 h-5 text-green-600 mr-2" />
          <span class="text-sm text-green-800">
            Sync completed! {{ syncResult.synced_count }} new vehicles added, {{ syncResult.updated_count }} vehicles updated.
            Total vehicles in database: {{ totalCount }}
          </span>
        </div>
      </div>

      <!-- API Information Card -->
      <div class="bg-white rounded-lg shadow-sm border mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">API Information</h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg">
              <div class="flex items-center">
                <Icon name="globe" class="w-5 h-5 text-blue-600 mr-2" />
                <div>
                  <p class="text-sm font-medium text-blue-900">API Endpoint</p>
                  <p class="text-sm text-blue-700">https://rlapp.rentluxuria.com/api/vehicles</p>
                </div>
              </div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
              <div class="flex items-center">
                <Icon name="key" class="w-5 h-5 text-green-600 mr-2" />
                <div>
                  <p class="text-sm font-medium text-green-900">API Key</p>
                  <p class="text-sm text-green-700">{{ apiKey || 'Loading...' }}</p>
                </div>
              </div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
              <div class="flex items-center">
                <Icon name="database" class="w-5 h-5 text-purple-600 mr-2" />
                <div>
                  <p class="text-sm font-medium text-purple-900">Total Vehicles</p>
                  <p class="text-sm text-purple-700">{{ totalCount }} vehicles</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Area -->
      <div class="bg-white rounded-lg shadow-sm border">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Raw API Data</h3>
            <div class="flex items-center space-x-2">
              <Button
                @click="exportData"
                variant="outline"
                size="sm"
              >
                <Icon name="download" class="w-4 h-4 mr-2" />
                Export JSON
              </Button>
            </div>
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Daily Rate</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="vehicle in vehicles" :key="vehicle.id" class="hover:bg-gray-50">
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
                  <span :class="[
                    'px-2 py-1 text-xs font-medium rounded-full',
                    vehicle.status === 'Available' ? 'bg-green-100 text-green-800' :
                    vehicle.status === 'Rented' ? 'bg-red-100 text-red-800' :
                    vehicle.status === 'Maintenance' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-gray-100 text-gray-800'
                  ]">
                    {{ vehicle.status }}
                  </span>
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
                     vehicle.ownership_status === 'owned' ? 'bg-green-100 text-green-800' :
                     'bg-orange-100 text-orange-800'
                   ]">
                     {{ vehicle.ownership_status }}
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
                      <DropdownMenuItem @click="viewRawData(vehicle)">
                        <Icon name="eye" class="w-4 h-4 mr-2" />
                        View Raw Data
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="copyVehicleId(vehicle.id)">
                        <Icon name="copy" class="w-4 h-4 mr-2" />
                        Copy ID
                      </DropdownMenuItem>
                      <DropdownMenuItem @click="testVehicleEndpoint(vehicle.id)">
                        <Icon name="external-link" class="w-4 h-4 mr-2" />
                        Test Endpoint
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
              Showing <span class="font-medium">1</span> to <span class="font-medium">{{ vehicles.length }}</span> of <span class="font-medium">{{ vehicles.length }}</span> results
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
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
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

// Get vehicles from API or props
const vehicles = ref<Vehicle[]>(page.props.vehicles as Vehicle[] || [])
const apiStatus = ref(page.props.apiStatus || 'loading')
const error = ref(page.props.error || null)
const totalCount = ref(page.props.totalCount || 0)
const syncResult = ref<SyncResult | null>(page.props.syncResult as SyncResult || null)
const apiKey = ref(page.props.apiKey || 'Loading...')

const refreshData = () => {
  window.location.reload()
}

const exportData = () => {
  const dataStr = JSON.stringify(vehicles.value, null, 2)
  const dataBlob = new Blob([dataStr], { type: 'application/json' })
  const url = URL.createObjectURL(dataBlob)
  const link = document.createElement('a')
  link.href = url
  link.download = 'vehicles-api-data.json'
  link.click()
  URL.revokeObjectURL(url)
}

const viewRawData = (vehicle: Vehicle) => {
  alert(`Raw data for ${vehicle.display_name || vehicle.make}:\n${JSON.stringify(vehicle, null, 2)}`)
}

const copyVehicleId = (id: number) => {
  navigator.clipboard.writeText(id.toString())
  alert(`Vehicle ID ${id} copied to clipboard!`)
}

const testVehicleEndpoint = (id: number) => {
  const url = `https://rlapp.rentluxuria.com/api/vehicles/${id}`
  window.open(url, '_blank')
}
</script>
