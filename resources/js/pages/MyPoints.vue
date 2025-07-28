<template>
  <AppLayout>
    <div class="p-6">
      <div class="mb-6">
        <Heading title="My Points" description="View your loyalty points and rewards" />
        <div class="mt-4 flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
              <div class="w-3 h-3 bg-green-500 rounded-full"></div>
              <span class="text-sm text-gray-600">Connected to PointSys</span>
            </div>
            <div class="flex items-center space-x-2">
              <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
              <span class="text-sm text-gray-600">Real-time data</span>
            </div>
          </div>
          <Button
            @click="refreshData"
            variant="outline"
            size="sm"
            :disabled="loading || loadingRewards"
          >
            <Icon name="refresh-cw" class="w-4 h-4 mr-2" />
            {{ loading || loadingRewards ? 'Refreshing...' : 'Refresh' }}
          </Button>
        </div>
      </div>

      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Points Balance Card -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Points Balance</h3>
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
              <Icon name="star" class="w-5 h-5 text-blue-600" />
            </div>
          </div>

          <div v-if="loading" class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
            <p class="text-gray-600 mt-2">Loading points...</p>
          </div>

          <div v-else-if="error" class="text-center py-8">
            <Icon name="alert-circle" class="w-8 h-8 text-red-500 mx-auto mb-2" />
            <p class="text-red-600">{{ error }}</p>
            <Button @click="loadPoints" variant="outline" class="mt-2">
              Try Again
            </Button>
          </div>

          <div v-else class="text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ pointsBalance }}</div>
            <p class="text-gray-600">Available Points</p>

            <!-- Customer Tier Display -->
            <div v-if="customerTier" class="mt-3">
              <span :class="`px-3 py-1 text-sm font-medium rounded-full ${getTierInfo(customerTier).color}`">
                <Icon :name="getTierInfo(customerTier).icon" class="w-4 h-4 inline mr-1" />
                {{ getTierInfo(customerTier).name }}
              </span>
            </div>

            <!-- Customer Stats -->
            <div class="mt-4 text-sm text-gray-500 space-y-1">
              <p>Customer ID: {{ customerId || 'Not registered' }}</p>
              <p v-if="totalEarned > 0">Total Earned: {{ totalEarned }} pts</p>
              <p v-if="totalRedeemed > 0">Total Redeemed: {{ totalRedeemed }} pts</p>
            </div>

            <div v-if="pointsBalance === 0" class="mt-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
              <p class="text-sm text-yellow-800">Start earning points by making reservations!</p>
            </div>
          </div>
        </div>

        <!-- Points History Card -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
              <Icon name="activity" class="w-5 h-5 text-green-600" />
            </div>
          </div>

          <div v-if="loading" class="space-y-3">
            <div v-for="i in 3" :key="i" class="animate-pulse">
              <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
              <div class="h-3 bg-gray-200 rounded w-1/2"></div>
            </div>
          </div>

          <div v-else-if="pointsHistory.length === 0" class="text-center py-8">
            <Icon name="inbox" class="w-8 h-8 text-gray-400 mx-auto mb-2" />
            <p class="text-gray-600">No recent activity</p>
          </div>

          <div v-else class="space-y-3">
            <div v-for="activity in pointsHistory.slice(0, 5)" :key="activity.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div>
                <p class="font-medium text-gray-900">{{ activity.description }}</p>
                <p class="text-sm text-gray-500">{{ activity.date }}</p>
              </div>
              <span :class="[
                'px-2 py-1 text-xs font-medium rounded-full',
                activity.type === 'earned' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
              ]">
                {{ activity.type === 'earned' ? '+' : '-' }}{{ activity.points }}
              </span>
            </div>
          </div>
        </div>

        <!-- Rewards Card -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">Available Rewards</h3>
              <p v-if="rewards.length > 0" class="text-sm text-gray-600 mt-1">{{ rewards.length }} rewards available</p>
            </div>
            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
              <Icon name="gift" class="w-5 h-5 text-purple-600" />
            </div>
          </div>

          <div v-if="loadingRewards" class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div>
            <p class="text-gray-600 mt-2">Loading rewards from PointSys...</p>
          </div>

          <div v-else-if="rewards.length === 0" class="text-center py-8">
            <Icon name="gift" class="w-8 h-8 text-gray-400 mx-auto mb-2" />
            <p class="text-gray-600">No rewards available at the moment</p>
            <p class="text-sm text-gray-500 mt-1">Check back later for new rewards!</p>
            <Button @click="loadRewards" variant="outline" size="sm" class="mt-3">
              <Icon name="refresh-cw" class="w-4 h-4 mr-2" />
              Refresh Rewards
            </Button>
          </div>

                    <div v-else class="space-y-3">
            <div v-for="reward in rewards.slice(0, 5)" :key="reward.id" class="p-3 bg-purple-50 rounded-lg border border-purple-200">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="font-medium text-gray-900">{{ reward.title }}</p>
                  <p class="text-sm text-gray-600 mt-1">{{ reward.description }}</p>
                  <div class="mt-2 flex items-center space-x-2">
                    <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">
                      {{ reward.points_required }} pts
                    </span>
                    <span v-if="reward.status === 'active'" class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                      Active
                    </span>
                  </div>
                </div>
                <div class="ml-4 text-right">
                  <Button
                    v-if="pointsBalance >= reward.points_required && reward.status === 'active'"
                    size="sm"
                    variant="outline"
                    @click="redeemReward(reward.id)"
                    :disabled="redeeming"
                    class="whitespace-nowrap"
                  >
                    {{ redeeming ? 'Redeeming...' : 'Redeem' }}
                  </Button>
                  <p v-else-if="pointsBalance < reward.points_required" class="text-xs text-gray-500 mt-1">
                    Need {{ reward.points_required - pointsBalance }} more pts
                  </p>
                  <p v-else-if="pointsBalance === 0" class="text-xs text-gray-500 mt-1">
                    Start earning points!
                  </p>
                  <p v-else-if="reward.status !== 'active'" class="text-xs text-gray-500 mt-1">
                    Not available
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Customer Tier Info -->
      <div v-if="customerTier" class="mt-6 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-lg p-6 border border-amber-200">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-lg font-semibold text-amber-900">Your Membership Level</h3>
            <p class="text-sm text-amber-700">Current tier and benefits</p>
          </div>
          <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
            <Icon :name="getTierInfo(customerTier).icon" class="w-6 h-6 text-amber-600" />
          </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
          <div class="text-center p-4 bg-white rounded-lg border border-amber-200">
            <div class="text-2xl font-bold text-amber-600 mb-1">{{ getTierInfo(customerTier).name }}</div>
            <p class="text-sm text-amber-700">Current Tier</p>
          </div>
          <div class="text-center p-4 bg-white rounded-lg border border-amber-200">
            <div class="text-2xl font-bold text-green-600 mb-1">{{ totalEarned }}</div>
            <p class="text-sm text-green-700">Total Points Earned</p>
          </div>
          <div class="text-center p-4 bg-white rounded-lg border border-amber-200">
            <div class="text-2xl font-bold text-purple-600 mb-1">{{ totalRedeemed }}</div>
            <p class="text-sm text-purple-700">Total Points Redeemed</p>
          </div>
        </div>

        <div class="mt-4 p-4 bg-white rounded-lg border border-amber-200">
          <h4 class="font-medium text-amber-900 mb-2">Tier Benefits:</h4>
          <div class="grid gap-2 md:grid-cols-2">
            <div class="flex items-center space-x-2">
              <Icon name="check" class="w-4 h-4 text-green-600" />
              <span class="text-sm text-amber-800">Access to all rewards</span>
            </div>
            <div class="flex items-center space-x-2">
              <Icon name="check" class="w-4 h-4 text-green-600" />
              <span class="text-sm text-amber-800">Priority customer support</span>
            </div>
            <div class="flex items-center space-x-2">
              <Icon name="check" class="w-4 h-4 text-green-600" />
              <span class="text-sm text-amber-800">Special promotions</span>
            </div>
            <div class="flex items-center space-x-2">
              <Icon name="check" class="w-4 h-4 text-green-600" />
              <span class="text-sm text-amber-800">Exclusive offers</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Points Info -->
      <div class="mt-8 bg-blue-50 rounded-lg p-6 border border-blue-200">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">How to Earn Points</h3>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
              <span class="text-white text-sm font-bold">1</span>
            </div>
            <div>
              <h4 class="font-medium text-blue-900">Make Reservations</h4>
              <p class="text-sm text-blue-700">Earn points for every car reservation</p>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
              <span class="text-white text-sm font-bold">2</span>
            </div>
            <div>
              <h4 class="font-medium text-blue-900">Complete Rentals</h4>
              <p class="text-sm text-blue-700">Get bonus points when you return cars on time</p>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
              <span class="text-white text-sm font-bold">3</span>
            </div>
            <div>
              <h4 class="font-medium text-blue-900">Refer Friends</h4>
              <p class="text-sm text-blue-700">Earn points for successful referrals</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Heading from '@/components/Heading.vue'
import Button from '@/components/ui/button/Button.vue'
import Icon from '@/components/Icon.vue'

const page = usePage()

// Helper function to get tier display info
const getTierInfo = (tier: string) => {
  const tierInfo = {
    bronze: { name: 'برونزي', color: 'bg-amber-100 text-amber-800', icon: 'medal' },
    silver: { name: 'فضي', color: 'bg-gray-100 text-gray-800', icon: 'medal' },
    gold: { name: 'ذهبي', color: 'bg-yellow-100 text-yellow-800', icon: 'crown' },
    platinum: { name: 'بلاتيني', color: 'bg-blue-100 text-blue-800', icon: 'star' },
    diamond: { name: 'ماسي', color: 'bg-purple-100 text-purple-800', icon: 'gem' }
  }
  return tierInfo[tier as keyof typeof tierInfo] || tierInfo.bronze
}
const user = page.props.auth?.user

// State
const loading = ref(false)
const loadingRewards = ref(false)
const redeeming = ref(false)
const error = ref('')
const pointsBalance = ref(0)
const customerId = ref('')
const customerTier = ref('')
const customerName = ref('')
const totalEarned = ref(0)
const totalRedeemed = ref(0)
const pointsHistory = ref<any[]>([])
const rewards = ref<any[]>([])

// Load user points from PointSys API
const loadPoints = async () => {
  // Remove the early return - let the API handle registration
  // if (!user?.pointsys_customer_id) {
  //   error.value = 'You are not registered in the loyalty system. Please contact support.'
  //   loading.value = false
  //   return
  // }

  loading.value = true
  error.value = ''

  try {
    console.log('Loading points for user:', user.pointsys_customer_id)

    const response = await fetch(`/user-points`, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      credentials: 'same-origin'
    })

    console.log('Response status:', response.status)
    console.log('Response headers:', response.headers)

    const data = await response.json()
    console.log('Response data:', data)

    if (response.ok && data.success) {
      pointsBalance.value = data.points || 0
      customerId.value = user.pointsys_customer_id
      customerTier.value = data.tier || 'bronze'
      customerName.value = data.name || user.name
      totalEarned.value = data.total_earned || 0
      totalRedeemed.value = data.total_redeemed || 0
      error.value = '' // Clear any previous errors

      // Show success message if provided
      if (data.message) {
        console.log('Points loaded successfully:', data.message)
      }
    } else {
      error.value = data.error || 'Failed to fetch points balance'
      console.error('API Error:', data)
    }
  } catch (err) {
    console.error('Error loading points:', err)
    error.value = 'Network error. Please check your connection and try again.'
  } finally {
    loading.value = false
  }
}

// Load rewards from PointSys API
const loadRewards = async () => {
  loadingRewards.value = true

  try {
    const response = await fetch('/api/pointsys/rewards', {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      credentials: 'same-origin'
    })
    const data = await response.json()

    if (response.ok && data.status === 'success') {
      rewards.value = data.data || []
    } else {
      rewards.value = [] // Ensure rewards is empty array on error
    }
  } catch (err) {
    console.error('Failed to load rewards:', err)
    // Don't show error for rewards as it's not critical, but log it
    rewards.value = [] // Ensure rewards is empty array on error
  } finally {
    loadingRewards.value = false
  }
}

// Redeem reward
const redeemReward = async (rewardId: string) => {
  if (!user?.pointsys_customer_id) {
    alert('You are not registered in the loyalty system. Please contact support.')
    return
  }

  redeeming.value = true

  try {
    const response = await fetch('/api/pointsys/rewards/redeem', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        reward_id: rewardId,
        customer_id: user.pointsys_customer_id!
      })
    })

    const data = await response.json()

    if (response.ok && data.status === 'success') {
      // Reload points after successful redemption
      await loadPoints()
      alert('Reward redeemed successfully!')
    } else {
      alert(data.message || 'Failed to redeem reward')
    }
  } catch (err) {
    console.error('Error redeeming reward:', err)
    alert('Network error. Please check your connection and try again.')
  } finally {
    redeeming.value = false
  }
}

// Mock points history (replace with real API call)
const loadPointsHistory = () => {
  pointsHistory.value = [
    {
      id: 1,
      description: 'Car reservation - BMW X5',
      points: 100,
      type: 'earned',
      date: '2024-01-15'
    },
    {
      id: 2,
      description: 'Reward redemption - Free wash',
      points: 50,
      type: 'spent',
      date: '2024-01-10'
    },
    {
      id: 3,
      description: 'Car reservation - Mercedes C-Class',
      points: 80,
      type: 'earned',
      date: '2024-01-05'
    }
  ]
}

// Refresh all data
const refreshData = async () => {
  await Promise.all([
    loadPoints(),
    loadRewards()
  ])
}

onMounted(() => {
  // Add a small delay to ensure the page is fully loaded
  setTimeout(() => {
    loadPoints()
    loadRewards()
    loadPointsHistory()
  }, 100)
})
</script>
