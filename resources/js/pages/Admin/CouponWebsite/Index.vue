<template>
    <AppLayout title="Website Coupons Management">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Website Coupons Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Header with Add Button -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Website Coupons List</h3>
                        <Link
                            :href="route('admin.coupon-website.create')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            <Plus class="w-4 h-4 mr-2" />
                            Add New Coupon
                        </Link>
                    </div>
                </div>

                <!-- Coupons Table -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Coupon Code
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Discount Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Discount Value
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Expiry Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="coupon in coupons.data" :key="coupon.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ coupon.code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span :class="{
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                            'bg-blue-100 text-blue-800': coupon.discount_type === 'fixed',
                                            'bg-purple-100 text-purple-800': coupon.discount_type === 'percentage'
                                        }">
                                            {{ coupon.discount_type === 'fixed' ? 'Fixed Amount' : 'Percentage' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ coupon.discount_type === 'fixed' ? coupon.discount + ' AED' : coupon.discount + '%' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(coupon.expire_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span :class="{
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                            'bg-green-100 text-green-800': isActive(coupon.expire_at),
                                            'bg-red-100 text-red-800': !isActive(coupon.expire_at)
                                        }">
                                            {{ isActive(coupon.expire_at) ? 'Active' : 'Expired' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <Link
                                                :href="route('admin.coupon-website.edit', coupon.id)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                <Edit class="w-4 h-4" />
                                            </Link>
                                            <button
                                                @click="deleteCoupon(coupon.id)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="coupons.links && coupons.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="coupons.prev_page_url"
                                    :href="coupons.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Previous
                                </Link>
                                <Link
                                    v-if="coupons.next_page_url"
                                    :href="coupons.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing
                                        <span class="font-medium">{{ coupons.from }}</span>
                                        to
                                        <span class="font-medium">{{ coupons.to }}</span>
                                        of
                                        <span class="font-medium">{{ coupons.total }}</span>
                                        results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link
                                            v-for="(link, index) in coupons.links"
                                            :key="index"
                                            :href="link.url"
                                            v-html="link.label"
                                            :class="{
                                                'relative inline-flex items-center px-4 py-2 border text-sm font-medium': true,
                                                'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active && link.url,
                                                'bg-indigo-50 border-indigo-500 text-indigo-600': link.active,
                                                'bg-white border-gray-300 text-gray-300 cursor-not-allowed': !link.url
                                            }"
                                        />
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Plus, Edit, Trash2 } from 'lucide-vue-next'

const props = defineProps({
    coupons: Object,
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const isActive = (expireAt) => {
    return new Date(expireAt) > new Date()
}

const deleteCoupon = (id) => {
    if (confirm('Are you sure you want to delete this coupon?')) {
        router.delete(route('admin.coupon-website.destroy', id))
    }
}
</script>
