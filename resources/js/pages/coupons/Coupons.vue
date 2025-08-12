<template>
  <AppLayout>
    <div class="p-6">
      <div class="mb-6">
        <Heading title="Coupons Management" description="Manage your promotional coupons and discounts" />
      </div>

      <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6">
          <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">Coupons</h2>
            <p class="text-gray-600 mt-1">Manage your promotional coupons and discounts</p>
          </div>

          <!-- Coupons List -->
          <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4 border">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-medium text-gray-900">SUMMER2024</h3>
                  <p class="text-sm text-gray-600">20% off on all summer items</p>
                  <p class="text-xs text-gray-500 mt-1">Valid until: 31 Aug 2024</p>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                    Active
                  </span>
                  <Button variant="outline" size="sm">
                    <Icon name="edit" class="w-4 h-4" />
                  </Button>
                </div>
              </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 border">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-medium text-gray-900">WELCOME10</h3>
                  <p class="text-sm text-gray-600">10% off for new customers</p>
                  <p class="text-xs text-gray-500 mt-1">Valid until: 31 Dec 2024</p>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                    Active
                  </span>
                  <Button variant="outline" size="sm">
                    <Icon name="edit" class="w-4 h-4" />
                  </Button>
                </div>
              </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 border">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="font-medium text-gray-900">FLASH50</h3>
                  <p class="text-sm text-gray-600">50% off flash sale</p>
                  <p class="text-xs text-gray-500 mt-1">Expired: 15 Jun 2024</p>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                    Expired
                  </span>
                  <Button variant="outline" size="sm">
                    <Icon name="edit" class="w-4 h-4" />
                  </Button>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State (when no coupons) -->
          <div v-if="false" class="text-center py-12">
            <Icon name="ticket" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
            <h3 class="text-lg font-medium text-gray-900 mb-2">No coupons yet</h3>
            <p class="text-gray-600 mb-6">No coupons available at the moment.</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import Button from '@/components/ui/button/Button.vue'
import Icon from '@/components/Icon.vue'
</script>
