<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center">
            <h1 class="text-3xl font-bold text-gray-900">Luxuria UAE</h1>
          </div>
          <div class="flex items-center space-x-4">
            <button
              @click="refreshData"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
            >
              <Icon name="refresh-cw" class="w-4 h-4 mr-2" />
              Refresh
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- API Status Alert -->
    <div v-if="error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
      <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center">
          <Icon name="alert-triangle" class="w-5 h-5 text-yellow-600 mr-2" />
          <span class="text-sm text-yellow-800">{{ error }}</span>
        </div>
      </div>
    </div>

    <div v-if="apiStatus === 'success'" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
      <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
          <Icon name="check-circle" class="w-5 h-5 text-green-600 mr-2" />
          <span class="text-sm text-green-800">Successfully loaded {{ totalVehicles }} vehicles from API</span>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Categories -->
      <div v-for="(vehicles, category) in categories" :key="category" class="mb-12">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-900">{{ category.toUpperCase() }}</h2>
          <span class="text-sm text-gray-500">{{ vehicles.length }} vehicles</span>
        </div>

        <!-- Vehicle Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div
            v-for="vehicle in vehicles"
            :key="vehicle.id"
            class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
          >
            <!-- Vehicle Image -->
            <div class="h-48 bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
              <div v-if="vehicle.image" class="w-full h-full">
                <img :src="vehicle.image" :alt="vehicle.name" class="w-full h-full object-cover" />
              </div>
              <div v-else class="text-center">
                <Icon name="car" class="w-16 h-16 text-blue-600 mx-auto mb-2" />
                <p class="text-sm text-blue-700 font-medium">{{ vehicle.name }} {{ vehicle.model }}</p>
              </div>
            </div>

            <!-- Vehicle Info -->
            <div class="p-4">
              <!-- Vehicle Name -->
              <h3 class="text-lg font-semibold text-gray-900 mb-2">
                {{ vehicle.name }} {{ vehicle.model }}
              </h3>

              <!-- Features Badges -->
              <div class="flex flex-wrap gap-2 mb-4">
                <span class="px-2 py-1 text-xs font-medium bg-gray-900 text-white rounded-full">
                  {{ vehicle.seats }} Seats
                </span>
                <span class="px-2 py-1 text-xs font-medium bg-gray-900 text-white rounded-full">
                  {{ vehicle.doors }} Doors
                </span>
                <span class="px-2 py-1 text-xs font-medium bg-green-600 text-white rounded-full">
                  {{ vehicle.deposit }}
                </span>
              </div>

              <!-- Pricing -->
              <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="text-center">
                  <p class="text-xs text-gray-500">Daily</p>
                  <p class="text-sm font-bold text-gray-900">{{ vehicle.dailyRate }} AED</p>
                </div>
                <div class="text-center">
                  <p class="text-xs text-gray-500">Weekly</p>
                  <p class="text-sm font-bold text-gray-900">{{ vehicle.weeklyRate }} AED</p>
                </div>
                <div class="text-center">
                  <p class="text-xs text-gray-500">Monthly</p>
                  <p class="text-sm font-bold text-gray-900">{{ vehicle.monthlyRate }} AED</p>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-2">
                <button
                  @click="bookVehicle(vehicle)"
                  class="flex-1 bg-gray-600 text-white text-sm font-medium py-2 px-4 rounded-md hover:bg-gray-700 transition-colors"
                >
                  Book Now
                </button>
                <button
                  @click="contactWhatsApp(vehicle)"
                  class="bg-green-500 text-white p-2 rounded-md hover:bg-green-600 transition-colors"
                >
                  <Icon name="message-circle" class="w-4 h-4" />
                </button>
              </div>

              <!-- Vehicle Details -->
              <div class="mt-3 pt-3 border-t border-gray-100">
                <div class="flex justify-between text-xs text-gray-500">
                  <span>{{ vehicle.year }} • {{ vehicle.color }}</span>
                  <span>{{ vehicle.plateNumber }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="Object.keys(categories).length === 0" class="text-center py-12">
        <Icon name="car" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
        <h3 class="text-lg font-medium text-gray-900 mb-2">No vehicles available</h3>
        <p class="text-gray-500">Check back later for available vehicles.</p>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Icon from '@/components/Icon.vue'

const page = usePage()

// Get data from props
const categories = ref(page.props.categories || {})
const apiStatus = ref(page.props.apiStatus || 'loading')
const error = ref(page.props.error || null)
const totalVehicles = ref(page.props.totalVehicles || 0)

const refreshData = () => {
  window.location.reload()
}

const bookVehicle = (vehicle: any) => {
  alert(`Booking ${vehicle.name} ${vehicle.model} for ${vehicle.dailyRate} AED/day`)
}

const contactWhatsApp = (vehicle: any) => {
  const message = `Hi, I'm interested in booking the ${vehicle.name} ${vehicle.model} for ${vehicle.dailyRate} AED/day.`
  const whatsappUrl = `https://wa.me/971501234567?text=${encodeURIComponent(message)}`
  window.open(whatsappUrl, '_blank')
}
</script>
