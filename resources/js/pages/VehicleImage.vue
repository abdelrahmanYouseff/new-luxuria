<template>
  <AppLayout>
    <div class="p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <div>
          <Heading title="Vehicle Image Management" />
          <p class="text-gray-600 mt-2">
            Managing image for {{ vehicle.make }} {{ vehicle.model }} ({{ vehicle.plate_number }})
          </p>
        </div>
        <Button @click="goBack" variant="outline" size="sm">
          <Icon name="arrow-left" class="w-4 h-4 mr-2" />
          Back to Vehicles
        </Button>
      </div>

      <!-- Main Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Current Image Section -->
        <Card>
          <CardHeader>
            <CardTitle>Current Image</CardTitle>
            <CardDescription>
              Current image for this vehicle
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div class="text-center">
              <div v-if="vehicle.has_image" class="mb-4">
                <img
                  :src="vehicle.current_image"
                  :alt="`${vehicle.make} ${vehicle.model}`"
                  class="w-full max-w-md mx-auto rounded-lg shadow-lg"
                />
                <p class="text-sm text-gray-500 mt-2">{{ vehicle.image_path }}</p>
              </div>
              <div v-else class="mb-4">
                <div class="w-full max-w-md mx-auto h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                  <div class="text-center">
                    <Icon name="image" class="w-16 h-16 text-gray-400 mx-auto mb-2" />
                    <p class="text-gray-500">No image uploaded</p>
                  </div>
                </div>
              </div>

              <div class="flex gap-2 justify-center">
                <Button
                  v-if="vehicle.has_image"
                  @click="removeImage"
                  variant="destructive"
                  size="sm"
                  :disabled="removing"
                >
                  <Icon name="trash-2" class="w-4 h-4 mr-2" />
                  {{ removing ? 'Removing...' : 'Remove Image' }}
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Upload Section -->
        <Card>
          <CardHeader>
            <CardTitle>Upload New Image</CardTitle>
            <CardDescription>
              Upload a new image for this vehicle
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="uploadImage" class="space-y-4">
              <div>
                <Label for="image">Select Image</Label>
                <Input
                  id="image"
                  type="file"
                  accept="image/*"
                  @change="handleFileSelect"
                  :disabled="uploading"
                  required
                />
                <p class="text-sm text-gray-500 mt-1">
                  Supported formats: JPEG, PNG, JPG, GIF, WEBP (max 2MB)
                </p>
              </div>

              <!-- Image Preview -->
              <div v-if="selectedFile" class="text-center">
                <img
                  :src="previewUrl"
                  alt="Preview"
                  class="w-full max-w-md mx-auto rounded-lg shadow-md"
                />
                <p class="text-sm text-gray-500 mt-2">{{ selectedFile.name }}</p>
              </div>

              <Button
                type="submit"
                :disabled="!selectedFile || uploading"
                class="w-full"
              >
                <Icon name="upload" class="w-4 h-4 mr-2" />
                {{ uploading ? 'Uploading...' : 'Upload Image' }}
              </Button>
            </form>
          </CardContent>
        </Card>
      </div>

      <!-- Status Messages -->
      <div v-if="message" class="mt-6">
        <div :class="[
          'p-4 rounded-lg border',
          messageType === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'
        ]">
          <div class="flex items-center">
            <Icon
              :name="messageType === 'success' ? 'check-circle' : 'alert-circle'"
              class="w-5 h-5 mr-2"
            />
            {{ message }}
          </div>
        </div>
      </div>

      <!-- Vehicle Info -->
      <Card class="mt-6">
        <CardHeader>
          <CardTitle>Vehicle Information</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
              <Label class="text-sm font-medium text-gray-500">Make</Label>
              <p class="text-lg font-semibold">{{ vehicle.make }}</p>
            </div>
            <div>
              <Label class="text-sm font-medium text-gray-500">Model</Label>
              <p class="text-lg font-semibold">{{ vehicle.model }}</p>
            </div>
            <div>
              <Label class="text-sm font-medium text-gray-500">Plate Number</Label>
              <p class="text-lg font-semibold">{{ vehicle.plate_number }}</p>
            </div>
            <div>
              <Label class="text-sm font-medium text-gray-500">Image Status</Label>
              <p class="text-lg font-semibold">
                <span :class="vehicle.has_image ? 'text-green-600' : 'text-red-600'">
                  {{ vehicle.has_image ? 'Has Image' : 'No Image' }}
                </span>
              </p>
            </div>
          </div>
        </CardContent>
      </Card>
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
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const page = usePage()

// Define vehicle interface
interface Vehicle {
  id: number
  make: string
  model: string
  plate_number: string
  current_image: string
  has_image: boolean
  image_path: string | null
}

// Vehicle data
const vehicle = ref<Vehicle>(page.props.vehicle as Vehicle)

// State
const selectedFile = ref<File | null>(null)
const previewUrl = ref<string>('')
const uploading = ref(false)
const removing = ref(false)
const message = ref('')
const messageType = ref<'success' | 'error'>('success')

// Handle file selection
const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]

  if (file) {
    selectedFile.value = file
    previewUrl.value = URL.createObjectURL(file)
  }
}

// Upload image
const uploadImage = async () => {
  if (!selectedFile.value) return

  uploading.value = true
  message.value = ''

  try {
    const formData = new FormData()
    formData.append('image', selectedFile.value)

    const response = await fetch(`/vehicles/${vehicle.value.id}/image`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: formData
    })

    const result = await response.json()

    if (result.success) {
      message.value = result.message
      messageType.value = 'success'

      // Update vehicle data
      vehicle.value.current_image = result.image_url
      vehicle.value.has_image = true
      vehicle.value.image_path = result.image_path

      // Clear form
      selectedFile.value = null
      previewUrl.value = ''

      // Clear message after 3 seconds
      setTimeout(() => {
        message.value = ''
      }, 3000)
    } else {
      message.value = result.message
      messageType.value = 'error'
    }
  } catch (error) {
    message.value = 'Failed to upload image. Please try again.'
    messageType.value = 'error'
  } finally {
    uploading.value = false
  }
}

// Remove image
const removeImage = async () => {
  if (!confirm('Are you sure you want to remove this image?')) return

  removing.value = true
  message.value = ''

  try {
    const response = await fetch(`/vehicles/${vehicle.value.id}/image`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Content-Type': 'application/json',
      }
    })

    const result = await response.json()

    if (result.success) {
      message.value = result.message
      messageType.value = 'success'

      // Update vehicle data
      vehicle.value.current_image = ''
      vehicle.value.has_image = false
      vehicle.value.image_path = ''

      // Clear message after 3 seconds
      setTimeout(() => {
        message.value = ''
      }, 3000)
    } else {
      message.value = result.message
      messageType.value = 'error'
    }
  } catch (error) {
    message.value = 'Failed to remove image. Please try again.'
    messageType.value = 'error'
  } finally {
    removing.value = false
  }
}

// Go back to vehicles page
const goBack = () => {
  router.visit('/vehicles')
}

// Cleanup preview URL on unmount
onMounted(() => {
  return () => {
    if (previewUrl.value) {
      URL.revokeObjectURL(previewUrl.value)
    }
  }
})
</script>
