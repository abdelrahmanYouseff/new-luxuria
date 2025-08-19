<template>
    <AppLayout title="Edit Coupon">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Coupon
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Back Button -->
                    <div class="mb-6">
                        <Link
                            :href="route('admin.coupon-website.index')"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            <ArrowLeft class="w-4 h-4 mr-2" />
                            Back to List
                        </Link>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Code -->
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                    Coupon Code
                                </label>
                                <input
                                    id="code"
                                    v-model="form.code"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': form.errors.code }"
                                    required
                                />
                                <div v-if="form.errors.code" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.code }}
                                </div>
                            </div>

                            <!-- Discount Type -->
                            <div>
                                <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Discount Type
                                </label>
                                <select
                                    id="discount_type"
                                    v-model="form.discount_type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': form.errors.discount_type }"
                                    required
                                >
                                    <option value="fixed">Fixed Amount (AED)</option>
                                    <option value="percentage">Percentage (%)</option>
                                </select>
                                <div v-if="form.errors.discount_type" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.discount_type }}
                                </div>
                            </div>

                            <!-- Discount Value -->
                            <div>
                                <label for="discount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Discount Value
                                    <span v-if="form.discount_type === 'fixed'">(AED)</span>
                                    <span v-else>(%)</span>
                                </label>
                                <input
                                    id="discount"
                                    v-model="form.discount"
                                    type="number"
                                    :step="form.discount_type === 'percentage' ? '1' : '0.01'"
                                    :min="0"
                                    :max="form.discount_type === 'percentage' ? '100' : null"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': form.errors.discount }"
                                    required
                                />
                                <div v-if="form.errors.discount" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.discount }}
                                </div>
                                <p v-if="form.discount_type === 'percentage'" class="mt-1 text-sm text-gray-500">
                                    Maximum 100%
                                </p>
                            </div>

                            <!-- Expire At -->
                            <div>
                                <label for="expire_at" class="block text-sm font-medium text-gray-700 mb-2">
                                    Expiry Date
                                </label>
                                <input
                                    id="expire_at"
                                    v-model="form.expire_at"
                                    type="datetime-local"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    :class="{ 'border-red-500': form.errors.expire_at }"
                                    required
                                />
                                <div v-if="form.errors.expire_at" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.expire_at }}
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 flex justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                            >
                                <Save class="w-4 h-4 mr-2" />
                                {{ form.processing ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ArrowLeft, Save } from 'lucide-vue-next'

const props = defineProps({
    coupon: Object,
})

const form = useForm({
    code: props.coupon.code,
    discount: props.coupon.discount,
    discount_type: props.coupon.discount_type || 'fixed',
    expire_at: props.coupon.expire_at ? new Date(props.coupon.expire_at).toISOString().slice(0, 16) : '',
})

const submit = () => {
    form.put(route('admin.coupon-website.update', props.coupon.id))
}
</script>
