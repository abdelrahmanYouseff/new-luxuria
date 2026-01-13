<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mobile app reservations API test page for Luxuria UAE luxury car rental service." />
    <title>اختبار API الحجوزات للتطبيق الهاتفي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">اختبار API الحجوزات للتطبيق الهاتفي</h1>

        <!-- Authentication Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">المصادقة</h2>
            <div class="flex gap-4 mb-4">
                <input type="email" id="email" placeholder="البريد الإلكتروني" value="test06@gmail.com" class="flex-1 px-4 py-2 border rounded-lg">
                <input type="password" id="password" placeholder="كلمة المرور" value="password" class="flex-1 px-4 py-2 border rounded-lg">
                <button onclick="login()" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">تسجيل الدخول</button>
            </div>
            <div class="text-sm text-gray-600 mb-2">
                <p><strong>حسابات تجريبية:</strong></p>
                <p>• test06@gmail.com / password</p>
                <p>• test11@gmail.com / password</p>
                <p>• abdelrahmanyouseff@gmail.com / password (Admin)</p>
            </div>
            <div id="authStatus" class="text-sm text-gray-600"></div>
        </div>

        <!-- Create Reservation Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">إنشاء حجز جديد</h2>
            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                <h3 class="font-semibold mb-2">السيارات المتاحة للاختبار (API IDs):</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                    <div>• Hyundai Venue 2022 (99 AED/يوم)</div>
                    <div>• Nissan Versa 2021 (79 AED/يوم)</div>
                    <div>• Hyundai Santa Fe 2023 (199 AED/يوم)</div>
                    <div>• Mitsubishi Outlander 2024 (200 AED/يوم)</div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <select id="vehicleApiId" class="px-4 py-2 border rounded-lg">
                    <option value="">اختر المركبة</option>
                    <option value="01982195-f34e-7249-b37e-3e26839759ed">Hyundai Venue 2022 - 99 AED/يوم</option>
                    <option value="01982195-f338-70e0-a8b5-380fbebb4eba">Nissan Versa 2021 - 79 AED/يوم</option>
                    <option value="01982195-f33d-7041-b2d7-98826b308e1e">Hyundai Santa Fe 2023 - 199 AED/يوم</option>
                    <option value="01982195-f335-731d-b256-9341744685c6">Mitsubishi Outlander 2024 - 200 AED/يوم</option>
                </select>
                <select id="emirate" class="px-4 py-2 border rounded-lg">
                    <option value="">اختر الإمارة</option>
                    <option value="Dubai" selected>دبي</option>
                    <option value="Abu Dhabi">أبو ظبي</option>
                    <option value="Sharjah">الشارقة</option>
                    <option value="Ajman">عجمان</option>
                    <option value="Umm Al Quwain">أم القيوين</option>
                    <option value="Ras Al Khaimah">رأس الخيمة</option>
                    <option value="Fujairah">الفجيرة</option>
                </select>
                <input type="date" id="startDate" class="px-4 py-2 border rounded-lg">
                <input type="date" id="endDate" class="px-4 py-2 border rounded-lg">
                <input type="text" id="pickupLocation" placeholder="مكان الاستلام (اختياري)" value="مطار دبي الدولي" class="px-4 py-2 border rounded-lg">
                <input type="text" id="dropoffLocation" placeholder="مكان التسليم (اختياري)" value="مطار دبي الدولي" class="px-4 py-2 border rounded-lg">
            </div>
            <textarea id="notes" placeholder="ملاحظات (اختياري)" class="w-full px-4 py-2 border rounded-lg mb-4" rows="3"></textarea>
            <button onclick="createReservation()" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">إنشاء حجز</button>
        </div>

        <!-- Get Reservations Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">عرض الحجوزات</h2>
            <button onclick="getUserReservations()" class="bg-purple-500 text-white px-6 py-2 rounded-lg hover:bg-purple-600 mb-4">جلب حجوزاتي</button>
            <div id="reservationsList" class="space-y-4"></div>
        </div>

        <!-- Stripe Checkout Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">إنشاء رابط الدفع Stripe</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <input type="number" id="checkoutReservationId" placeholder="رقم الحجز للدفع" class="px-4 py-2 border rounded-lg">
                <input type="url" id="successUrl" placeholder="رابط النجاح (اختياري)" value="https://wpp.rentluxuria.com/booking/payment/success" class="px-4 py-2 border rounded-lg">
                <input type="url" id="cancelUrlCheckout" placeholder="رابط الإلغاء (اختياري)" value="https://wpp.rentluxuria.com/booking/payment/cancel" class="px-4 py-2 border rounded-lg">
                <button onclick="createCheckout()" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">إنشاء رابط الدفع</button>
            </div>
            <div class="border-t pt-4">
                <h3 class="font-semibold mb-2">الدفع السريع (حجز + دفع معاً)</h3>
                <button onclick="createQuickCheckout()" class="bg-purple-500 text-white px-6 py-2 rounded-lg hover:bg-purple-600">حجز ودفع فوري</button>
            </div>
        </div>

        <!-- Cancel Reservation Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">إلغاء حجز</h2>
            <div class="flex gap-4">
                <input type="number" id="cancelReservationId" placeholder="رقم الحجز للإلغاء" class="flex-1 px-4 py-2 border rounded-lg">
                <button onclick="cancelReservation()" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600">إلغاء الحجز</button>
            </div>
        </div>

        <!-- Response Section -->
        <div class="bg-gray-900 text-green-400 rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-white">استجابة API</h2>
            <pre id="response" class="whitespace-pre-wrap text-sm overflow-auto max-h-96"></pre>
        </div>
    </div>

    <script>
        let authToken = null;

        // Set up axios defaults
        axios.defaults.baseURL = window.location.origin;
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateResponse(data) {
            document.getElementById('response').textContent = JSON.stringify(data, null, 2);
        }

        function updateAuthStatus(message, isSuccess = true) {
            const authStatus = document.getElementById('authStatus');
            authStatus.textContent = message;
            authStatus.className = `text-sm ${isSuccess ? 'text-green-600' : 'text-red-600'}`;
        }

        async function login() {
            try {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;

                if (!email || !password) {
                    updateAuthStatus('يرجى إدخال البريد الإلكتروني وكلمة المرور', false);
                    return;
                }

                const response = await axios.post('/api/mobile/login', {
                    email: email,
                    password: password
                });

                if (response.data.success) {
                    authToken = response.data.data.token;
                    axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
                    updateAuthStatus(`تم تسجيل الدخول بنجاح - مرحباً ${response.data.data.user.name}`);
                    updateResponse(response.data);
                } else {
                    updateAuthStatus('فشل تسجيل الدخول', false);
                    updateResponse(response.data);
                }
            } catch (error) {
                updateAuthStatus('خطأ في تسجيل الدخول', false);
                updateResponse(error.response?.data || error.message);
            }
        }

        async function createReservation() {
            try {
                if (!authToken) {
                    updateResponse({error: 'يجب تسجيل الدخول أولاً'});
                    return;
                }

                const vehicleId = document.getElementById('vehicleId').value;
                const emirate = document.getElementById('emirate').value;
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                const pickupLocation = document.getElementById('pickupLocation').value;
                const dropoffLocation = document.getElementById('dropoffLocation').value;
                const notes = document.getElementById('notes').value;

                if (!vehicleId || !emirate || !startDate || !endDate) {
                    updateResponse({error: 'يرجى ملء جميع الحقول المطلوبة'});
                    return;
                }

                const userEmail = document.getElementById('email').value;

                const reservationData = {
                    vehicle_id: parseInt(vehicleId),
                    emirate: emirate,
                    start_date: startDate,
                    end_date: endDate,
                    user_email: userEmail,
                    notes: notes || null
                };

                if (pickupLocation) reservationData.pickup_location = pickupLocation;
                if (dropoffLocation) reservationData.dropoff_location = dropoffLocation;

                const response = await axios.post('/api/mobile/reservations', reservationData);
                updateResponse(response.data);
            } catch (error) {
                updateResponse(error.response?.data || error.message);
            }
        }

        async function getUserReservations() {
            try {
                if (!authToken) {
                    updateResponse({error: 'يجب تسجيل الدخول أولاً'});
                    return;
                }

                const response = await axios.get('/api/mobile/reservations');
                updateResponse(response.data);

                // Display reservations in a nice format
                if (response.data.success && response.data.data.reservations) {
                    displayReservations(response.data.data.reservations);
                }
            } catch (error) {
                updateResponse(error.response?.data || error.message);
            }
        }

        function displayReservations(reservations) {
            const container = document.getElementById('reservationsList');
            container.innerHTML = '';

            if (reservations.length === 0) {
                container.innerHTML = '<p class="text-gray-500">لا توجد حجوزات</p>';
                return;
            }

            reservations.forEach(reservation => {
                const div = document.createElement('div');
                div.className = 'border rounded-lg p-4 bg-gray-50';
                div.innerHTML = `
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-semibold text-lg">حجز رقم ${reservation.id}</h3>
                        <span class="px-2 py-1 rounded text-xs font-medium ${getStatusClass(reservation.status)}">${getStatusText(reservation.status)}</span>
                    </div>
                    <p><strong>المركبة:</strong> ${reservation.vehicle.make} ${reservation.vehicle.model} (${reservation.vehicle.year})</p>
                    <p><strong>رقم اللوحة:</strong> ${reservation.vehicle.plate_number}</p>
                    <p><strong>التواريخ:</strong> من ${reservation.dates.start_date} إلى ${reservation.dates.end_date} (${reservation.dates.total_days} أيام)</p>
                    <p><strong>الإمارة:</strong> ${reservation.location.emirate}</p>
                    <p><strong>المبلغ الإجمالي:</strong> ${reservation.pricing.total_amount} درهم</p>
                    ${reservation.external_reservation_id ? `<p><strong>رقم الحجز الخارجي:</strong> ${reservation.external_reservation_id}</p>` : ''}
                    ${reservation.notes ? `<p><strong>ملاحظات:</strong> ${reservation.notes}</p>` : ''}
                `;
                container.appendChild(div);
            });
        }

        function getStatusClass(status) {
            switch(status) {
                case 'pending': return 'bg-yellow-100 text-yellow-800';
                case 'confirmed': return 'bg-green-100 text-green-800';
                case 'cancelled': return 'bg-red-100 text-red-800';
                case 'completed': return 'bg-blue-100 text-blue-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        function getStatusText(status) {
            switch(status) {
                case 'pending': return 'في الانتظار';
                case 'confirmed': return 'مؤكد';
                case 'cancelled': return 'ملغي';
                case 'completed': return 'مكتمل';
                default: return status;
            }
        }

        async function cancelReservation() {
            try {
                if (!authToken) {
                    updateResponse({error: 'يجب تسجيل الدخول أولاً'});
                    return;
                }

                const reservationId = document.getElementById('cancelReservationId').value;
                if (!reservationId) {
                    updateResponse({error: 'يرجى إدخال رقم الحجز'});
                    return;
                }

                const response = await axios.patch(`/api/mobile/reservations/${reservationId}/cancel`);
                updateResponse(response.data);
            } catch (error) {
                updateResponse(error.response?.data || error.message);
            }
        }

        async function createCheckout() {
            try {
                if (!authToken) {
                    updateResponse({error: 'يجب تسجيل الدخول أولاً'});
                    return;
                }

                const reservationId = document.getElementById('checkoutReservationId').value;
                const successUrl = document.getElementById('successUrl').value;
                const cancelUrl = document.getElementById('cancelUrlCheckout').value;

                if (!reservationId) {
                    updateResponse({error: 'يرجى إدخال رقم الحجز'});
                    return;
                }

                const checkoutData = {
                    reservation_id: parseInt(reservationId)
                };

                if (successUrl) checkoutData.success_url = successUrl;
                if (cancelUrl) checkoutData.cancel_url = cancelUrl;

                const response = await axios.post('/api/mobile/reservations/checkout', checkoutData);
                updateResponse(response.data);

                // If successful, open checkout URL
                if (response.data.success && response.data.data.checkout_url) {
                    setTimeout(() => {
                        if (confirm('هل تريد فتح رابط الدفع الآن؟')) {
                            window.open(response.data.data.checkout_url, '_blank');
                        }
                    }, 1000);
                }
            } catch (error) {
                updateResponse(error.response?.data || error.message);
            }
        }

        async function createQuickCheckout() {
            try {
                if (!authToken) {
                    updateResponse({error: 'يجب تسجيل الدخول أولاً'});
                    return;
                }

                const vehicleId = document.getElementById('vehicleId').value;
                const emirate = document.getElementById('emirate').value;
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                const pickupLocation = document.getElementById('pickupLocation').value;
                const dropoffLocation = document.getElementById('dropoffLocation').value;
                const notes = document.getElementById('notes').value;
                const userEmail = document.getElementById('email').value;
                const successUrl = document.getElementById('successUrl').value;
                const cancelUrl = document.getElementById('cancelUrlCheckout').value;

                if (!vehicleId || !emirate || !startDate || !endDate) {
                    updateResponse({error: 'يرجى ملء جميع الحقول المطلوبة للحجز'});
                    return;
                }

                const quickCheckoutData = {
                    vehicle_id: parseInt(vehicleId),
                    emirate: emirate,
                    start_date: startDate,
                    end_date: endDate,
                    user_email: userEmail,
                    notes: notes || null
                };

                if (pickupLocation) quickCheckoutData.pickup_location = pickupLocation;
                if (dropoffLocation) quickCheckoutData.dropoff_location = dropoffLocation;
                if (successUrl) quickCheckoutData.success_url = successUrl;
                if (cancelUrl) quickCheckoutData.cancel_url = cancelUrl;

                const response = await axios.post('/api/mobile/reservations/quick-checkout', quickCheckoutData);
                updateResponse(response.data);

                // If successful, open checkout URL
                if (response.data.success && response.data.data.checkout && response.data.data.checkout.checkout_url) {
                    setTimeout(() => {
                        if (confirm('تم إنشاء الحجز! هل تريد المتابعة للدفع الآن؟')) {
                            window.open(response.data.data.checkout.checkout_url, '_blank');
                        }
                    }, 1000);
                }
            } catch (error) {
                updateResponse(error.response?.data || error.message);
            }
        }

        // Set default dates (today and tomorrow)
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);

            document.getElementById('startDate').value = today.toISOString().split('T')[0];
            document.getElementById('endDate').value = tomorrow.toISOString().split('T')[0];
        });
    </script>
</body>
</html>
