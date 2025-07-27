<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Coupons', href: '/coupons' },
];

const showModal = ref(false);
const form = ref({
    name: '',
    code: '',
    discountType: 'percent',
    discountValue: '',
    startDate: '',
    expiryDate: '',
    status: 'active',
});

// Coupons array for table
const coupons = ref([
    // Initial placeholder data
    {
        name: 'Welcome Coupon',
        code: 'WELCOME10',
        discount_type: 'percent',
        discount_value: 10,
        start_date: '2024-01-01',
        expiry_date: '2024-12-31',
        status: 'active',
    },
    {
        name: 'Summer Offer',
        code: 'SUMMER25',
        discount_type: 'fixed',
        discount_value: 25,
        start_date: '2024-06-01',
        expiry_date: '2024-08-15',
        status: 'inactive',
    },
]);

async function submitCoupon() {
    try {
        const response = await fetch('/coupons', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name: form.value.name,
                code: form.value.code,
                discount_type: form.value.discountType,
                discount_value: form.value.discountValue,
                start_date: form.value.startDate,
                expiry_date: form.value.expiryDate,
                status: form.value.status,
            }),
        });
        if (!response.ok) {
            const error = await response.json();
            alert(error.message || 'Error adding coupon');
            return;
        }
        const data = await response.json();
        coupons.value.unshift(data.coupon);
        // Reset form and close modal
        form.value = {
            name: '',
            code: '',
            discountType: 'percent',
            discountValue: '',
            startDate: '',
            expiryDate: '',
            status: 'active',
        };
        showModal.value = false;
    } catch (e) {
        alert('Error adding coupon');
    }
}
</script>

<template>
    <Head title="Coupons" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="lux-bg-page min-h-[100vh] py-8 px-2 md:px-0 flex flex-col items-center justify-start">
            <div class="w-full max-w-5xl">
                <div class="flex flex-col sm:flex-row items-center justify-between mb-7 gap-4">
                    <h1 class="text-3xl font-bold lux-heading">Coupons</h1>
                    <button class="lux-btn-gold-add flex items-center gap-2 px-5 py-2 rounded-full shadow" @click="showModal = true">
                        <span class="text-lg font-semibold">+ Add New Coupon</span>
                    </button>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-0 overflow-x-auto border border-[#f7e7c1]">
                    <table class="table w-full text-left align-middle">
                        <thead>
                            <tr class="lux-table-header">
                                <th class="px-5 py-4 font-semibold text-[#bfa133] text-lg">Code</th>
                                <th class="px-5 py-4 font-semibold text-[#bfa133] text-lg">Discount</th>
                                <th class="px-5 py-4 font-semibold text-[#bfa133] text-lg">Expiry Date</th>
                                <th class="px-5 py-4 font-semibold text-[#bfa133] text-lg">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="coupon in coupons" :key="coupon.code" class="border-b hover:bg-[#f9f6ef] transition">
                                <td class="px-5 py-4 font-medium">{{ coupon.code }}</td>
                                <td class="px-5 py-4">
                                    <template v-if="coupon.discount_type === 'percent'">
                                        {{ coupon.discount_value }}% Off
                                    </template>
                                    <template v-else>
                                        {{ coupon.discount_value }} AED
                                    </template>
                                </td>
                                <td class="px-5 py-4">{{ coupon.expiry_date }}</td>
                                <td class="px-5 py-4">
                                    <span :class="coupon.status === 'active' ? 'lux-badge-active' : 'lux-badge-expired'">
                                        {{ coupon.status === 'active' ? 'Active' : 'Expired' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal for Add Coupon -->
        <div v-if="showModal" class="lux-modal-overlay">
            <div class="lux-modal-pro">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <span class="lux-modal-icon"><i class="bi bi-ticket-perforated"></i></span>
                        <h2 class="lux-modal-title">Add New Coupon</h2>
                    </div>
                    <button class="lux-modal-close" @click="showModal = false">&times;</button>
                </div>
                <form @submit.prevent="submitCoupon">
                    <div class="mb-4">
                        <label class="lux-label-pro">Coupon Name</label>
                        <input v-model="form.name" type="text" class="lux-input-pro" placeholder="Enter coupon name" />
                    </div>
                    <div class="mb-4">
                        <label class="lux-label-pro">Coupon Code</label>
                        <input v-model="form.code" type="text" class="lux-input-pro" placeholder="Enter coupon code" />
                    </div>
                    <div class="mb-4 flex gap-3">
                        <div class="flex-1">
                            <label class="lux-label-pro">Discount Type</label>
                            <select v-model="form.discountType" class="lux-input-pro">
                                <option value="percent">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (AED)</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="lux-label-pro">Discount Value</label>
                            <input v-model="form.discountValue" type="number" min="0" class="lux-input-pro" placeholder="Value" />
                        </div>
                    </div>
                    <div class="mb-4 flex gap-3">
                        <div class="flex-1">
                            <label class="lux-label-pro">Start Date</label>
                            <input v-model="form.startDate" type="date" class="lux-input-pro" />
                        </div>
                        <div class="flex-1">
                            <label class="lux-label-pro">Expiry Date</label>
                            <input v-model="form.expiryDate" type="date" class="lux-input-pro" />
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="lux-label-pro">Status</label>
                        <select v-model="form.status" class="lux-input-pro">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" class="lux-btn-cancel-pro" @click="showModal = false">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="lux-btn-gold-pro">
                            <i class="bi bi-check-circle me-1"></i> Save Coupon
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Modal -->
    </AppLayout>
</template>

<style scoped>
.lux-bg-page {
    background: #f7f6f3;
}
.lux-heading {
    font-family: 'Playfair Display', serif;
    font-weight: 900;
    letter-spacing: 1px;
    color: #111;
}
.lux-btn-gold-add {
    background: #bfa133;
    color: #fff;
    border: none;
    border-radius: 2rem;
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    font-size: 1.1rem;
    transition: background 0.18s, box-shadow 0.18s;
    box-shadow: 0 2px 12px 0 #bfa13322;
}
.lux-btn-gold-add:hover {
    background: #a88c2c;
    color: #fff;
    box-shadow: 0 4px 18px 0 #bfa13344;
}
.lux-table-header {
    background: #f7e7c1;
    border-top-left-radius: 1.2rem;
    border-top-right-radius: 1.2rem;
}
.lux-badge-active {
    display: inline-block;
    padding: 0.35em 1.1em;
    border-radius: 1.2em;
    background: #e6f7e6;
    color: #1a7f37;
    font-size: 0.98rem;
    font-weight: 700;
    box-shadow: 0 1px 4px 0 #bfa13311;
}
.lux-badge-expired {
    display: inline-block;
    padding: 0.35em 1.1em;
    border-radius: 1.2em;
    background: #fbeaea;
    color: #b91c1c;
    font-size: 0.98rem;
    font-weight: 700;
    box-shadow: 0 1px 4px 0 #bfa13311;
}
/* Modal Styles */
.lux-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(24, 24, 24, 0.25);
    backdrop-filter: blur(2px);
    z-index: 50;
    display: flex;
    align-items: center;
    justify-content: center;
}
.lux-modal-pro {
    background: #fff;
    border-radius: 1.7rem;
    box-shadow: 0 8px 32px 0 rgba(191,161,51,0.13), 0 0 0 3px #bfa13322;
    padding: 2.5rem 2rem 2rem 2rem;
    min-width: 340px;
    max-width: 98vw;
    width: 430px;
    position: relative;
    animation: fadeIn 0.2s;
}
.lux-modal-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    font-weight: 900;
    color: #bfa133;
    letter-spacing: 1px;
}
.lux-modal-icon {
    font-size: 2.1rem;
    color: #bfa133;
    background: #f7e7c1;
    border-radius: 50%;
    padding: 0.3rem 0.6rem;
    margin-right: 0.2rem;
    box-shadow: 0 2px 8px #bfa13322;
}
.lux-modal-close {
    background: none;
    border: none;
    font-size: 2.2rem;
    color: #bfa133;
    cursor: pointer;
    transition: color 0.18s;
}
.lux-modal-close:hover {
    color: #a88c2c;
}
.lux-label-pro {
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    color: #bfa133;
    margin-bottom: 0.2rem;
    display: block;
    font-size: 1.08rem;
}
.lux-input-pro {
    width: 100%;
    padding: 0.6rem 1.1rem;
    border-radius: 1rem;
    border: 1.5px solid #f7e7c1;
    background: #f9f6ef;
    color: #222;
    font-size: 1.08rem;
    font-family: 'Playfair Display', serif;
    margin-top: 0.1rem;
    margin-bottom: 0.1rem;
    transition: border 0.18s, background 0.18s;
}
.lux-input-pro:focus {
    border-color: #bfa133;
    background: #fff;
    outline: none;
}
.lux-btn-cancel-pro {
    background: #eee;
    color: #bfa133;
    border: none;
    border-radius: 2rem;
    font-weight: 700;
    font-size: 1.08rem;
    padding: 0.5rem 1.5rem;
    font-family: 'Playfair Display', serif;
    transition: background 0.18s, color 0.18s;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.lux-btn-cancel-pro:hover {
    background: #bfa13322;
    color: #a88c2c;
}
.lux-btn-gold-pro {
    background: #bfa133;
    color: #fff;
    border: none;
    border-radius: 2rem;
    font-weight: 700;
    font-size: 1.08rem;
    padding: 0.5rem 1.7rem;
    font-family: 'Playfair Display', serif;
    box-shadow: 0 2px 12px 0 #bfa13322;
    transition: background 0.18s, box-shadow 0.18s;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.lux-btn-gold-pro:hover {
    background: #a88c2c;
    color: #fff;
    box-shadow: 0 4px 18px 0 #bfa13344;
}
@media (max-width: 600px) {
    .lux-modal-pro { padding: 1.2rem 0.5rem; min-width: 0; width: 99vw; }
    .lux-modal-title { font-size: 1.1rem; }
}
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.98); }
    to { opacity: 1; transform: scale(1); }
}
</style>
