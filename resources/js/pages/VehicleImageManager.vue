<template>
  <AppLayout>
    <div class="p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <Heading title="Vehicle Image Manager" />
        <Button
          @click="goBack"
          variant="outline"
          size="sm"
        >
          <Icon name="arrow-left" class="w-4 h-4 mr-2" />
          Back to Vehicles
        </Button>
      </div>

      <!-- Vehicle Info -->
      <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="flex items-center">
          <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mr-4 overflow-hidden">
            <img
              v-if="vehicle.image"
              :src="vehicle.image"
              :alt="vehicle.name"
              class="w-full h-full object-cover"
            />
            <Icon v-else name="car" class="w-8 h-8 text-blue-600" />
          </div>
          <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ vehicle.name }}</h2>
            <p class="text-gray-600">{{ vehicle.model }} • {{ vehicle.plateNumber }}</p>
            <p class="text-sm text-gray-500">{{ vehicle.category }} • {{ vehicle.status }}</p>
          </div>
        </div>
      </div>

      <!-- Image Upload Section -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Vehicle Image</h3>

        <!-- Current Image -->
        <div v-if="vehicle.image" class="mb-6">
          <h4 class="text-sm font-medium text-gray-700 mb-2">Current Image:</h4>
          <div class="relative inline-block">
            <img
              :src="vehicle.image"
              :alt="vehicle.name"
              class="w-48 h-32 object-cover rounded-lg border"
            />
            <Button
              @click="removeImage"
              size="sm"
              variant="outline"
              class="absolute top-2 right-2 bg-white"
            >
              <Icon name="x" class="w-3 h-3" />
            </Button>
          </div>
        </div>

        <!-- Upload Form -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Select Image File
            </label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
              <input
                type="file"
                ref="fileInput"
                @change="handleFileSelect"
                accept="image/*"
                class="hidden"
              />
              <div v-if="!selectedFile" @click="fileInput?.click()" class="cursor-pointer">
                <Icon name="upload" class="w-8 h-8 text-gray-400 mx-auto mb-2" />
                <p class="text-sm text-gray-600">Click to select an image</p>
                <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF up to 5MB</p>
              </div>
              <div v-else class="text-left">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <Icon name="image" class="w-5 h-5 text-blue-600 mr-2" />
                    <span class="text-sm font-medium text-gray-900">{{ selectedFile.name }}</span>
                  </div>
                  <Button @click="clearSelection" size="sm" variant="outline">
                    <Icon name="x" class="w-3 h-3" />
                  </Button>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ formatFileSize(selectedFile.size) }}</p>
              </div>
            </div>
          </div>

          <!-- Preview -->
          <div v-if="imagePreview" class="mt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Preview:</h4>
            <img
              :src="imagePreview"
              alt="Preview"
              class="w-48 h-32 object-cover rounded-lg border"
            />
          </div>

          <!-- Upload Button -->
          <div class="flex items-center space-x-3">
            <Button
              @click="uploadImage"
              :disabled="!selectedFile || uploading"
              size="sm"
            >
              <Icon v-if="uploading" name="loader-2" class="w-4 h-4 mr-2 animate-spin" />
              <Icon v-else name="upload" class="w-4 h-4 mr-2" />
              {{ uploading ? 'Uploading...' : 'Upload Image' }}
            </Button>
            <Button
              @click="clearSelection"
              variant="outline"
              size="sm"
              :disabled="!selectedFile"
            >
              Cancel
            </Button>
          </div>
        </div>

        <!-- Image Guidelines -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
          <h4 class="text-sm font-medium text-blue-900 mb-2">Image Guidelines:</h4>
          <ul class="text-sm text-blue-800 space-y-1">
            <li>• Recommended size: 800x600 pixels or larger</li>
            <li>• Supported formats: JPG, PNG, GIF</li>
            <li>• Maximum file size: 5MB</li>
            <li>• Image should clearly show the vehicle</li>
            <li>• Good lighting and clear background preferred</li>
          </ul>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import Button from '@/components/ui/button/Button.vue'
import Icon from '@/components/Icon.vue'

const page = usePage()

// Mock vehicle data (in real app, this would come from API)
const vehicle = ref({
  id: 1,
  name: 'Toyota Camry',
  model: '2023',
  plateNumber: 'ABC-123',
  status: 'Available',
  category: 'Economy',
  image: null as string | null
})

const selectedFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)
const uploading = ref(false)

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]

  if (file) {
    // Validate file type
    if (!file.type.startsWith('image/')) {
      alert('Please select an image file')
      return
    }

    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
      alert('File size must be less than 5MB')
      return
    }

    selectedFile.value = file

    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}

const fileInput = ref<HTMLInputElement | null>(null)

const clearSelection = () => {
  selectedFile.value = null
  imagePreview.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const uploadImage = async () => {
  if (!selectedFile.value) return

  uploading.value = true

  try {
    // Simulate upload delay
    await new Promise(resolve => setTimeout(resolve, 2000))

    // In real app, this would be an API call
    // const formData = new FormData()
    // formData.append('image', selectedFile.value)
    // formData.append('vehicle_id', vehicle.value.id.toString())

    // Simulate successful upload
    vehicle.value.image = imagePreview.value
    alert('Image uploaded successfully!')
    clearSelection()

  } catch (error) {
    alert('Failed to upload image. Please try again.')
  } finally {
    uploading.value = false
  }
}

const removeImage = () => {
  if (confirm('Are you sure you want to remove the current image?')) {
    vehicle.value.image = null
    alert('Image removed successfully!')
  }
}

const formatFileSize = (bytes: number) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const goBack = () => {
  router.visit('/vehicles')
}
</script>
