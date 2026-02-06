@extends('layouts.app')

@section('title', 'Tambah Penerimaan Barang')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('penerimaan.index') }}"
                class="inline-flex items-center text-purple-600 hover:text-purple-700 font-semibold transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar Penerimaan
            </a>
        </div>

        <form method="POST" action="{{ route('penerimaan.store') }}" id="formPenerimaan">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form Section -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Header Card -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                            <h2 class="text-2xl font-bold text-white flex items-center">
                                <i class="fas fa-box-open mr-3"></i>
                                Form Penerimaan Barang
                            </h2>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Supplier -->
                            <div>
                                <label for="id_supplier" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Supplier <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-truck text-gray-400"></i>
                                    </div>
                                    <select name="id_supplier" id="id_supplier"
                                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('id_supplier') border-red-500 @enderror"
                                        required>
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id_supplier }}"
                                                {{ old('id_supplier') == $supplier->id_supplier ? 'selected' : '' }}>
                                                {{ $supplier->nama_supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('id_supplier')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Penerimaan -->
                            <div>
                                <label for="tanggal_penerimaan" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tanggal Penerimaan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar text-gray-400"></i>
                                    </div>
                                    <input type="datetime-local" name="tanggal_penerimaan" id="tanggal_penerimaan"
                                        value="{{ old('tanggal_penerimaan', now()->format('Y-m-d\TH:i')) }}"
                                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('tanggal_penerimaan') border-red-500 @enderror"
                                        required>
                                </div>
                                @error('tanggal_penerimaan')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Items Card -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div
                            class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4 flex justify-between items-center">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-list mr-3"></i>
                                Daftar Produk
                            </h3>
                            <button type="button" onclick="addItem()"
                                class="bg-white text-green-600 px-4 py-2 rounded-lg font-semibold hover:bg-green-50 transition">
                                <i class="fas fa-plus mr-2"></i>Tambah Item
                            </button>
                        </div>

                        <div class="p-6">
                            <div id="items-container" class="space-y-4">
                                <!-- Items will be added here dynamically -->
                            </div>

                            <div id="empty-state" class="text-center py-12">
                                <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                                <p class="text-gray-500 text-lg mb-4">Belum ada produk ditambahkan</p>
                                <button type="button" onclick="addItem()"
                                    class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transition">
                                    <i class="fas fa-plus mr-2"></i>Tambah Produk Pertama
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Penerimaan
                        </button>
                        <a href="{{ route('penerimaan.index') }}"
                            class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all duration-200 text-center">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>
                    </div>
                </div>

                <!-- Sidebar Summary -->
                <div class="lg:col-span-1">
                    <div class="sticky top-6 space-y-6">
                        <!-- Summary Card -->
                        <div
                            class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl shadow-lg p-6 border border-blue-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-calculator text-blue-600 mr-2"></i>
                                Ringkasan
                            </h3>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                                    <span class="text-gray-600">Total Item:</span>
                                    <span id="total-items" class="text-xl font-bold text-gray-800">0</span>
                                </div>

                                <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                                    <span class="text-gray-600">Total Qty:</span>
                                    <span id="total-qty" class="text-xl font-bold text-gray-800">0</span>
                                </div>

                                <div class="border-t-2 border-blue-200 pt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-700 font-semibold">Total Harga:</span>
                                        <span id="total-harga" class="text-2xl font-bold text-blue-600">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Card -->
                        <div
                            class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl shadow-lg p-6 border border-yellow-100">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center text-white mr-3">
                                    <i class="fas fa-info-circle text-xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Informasi</h3>
                            </div>

                            <ul class="space-y-3 text-sm text-gray-700">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2 flex-shrink-0"></i>
                                    <span>Pilih <strong>supplier</strong> terlebih dahulu</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2 flex-shrink-0"></i>
                                    <span>Tambahkan <strong>minimal 1 produk</strong></span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2 flex-shrink-0"></i>
                                    <span>Harga akan <strong>terisi otomatis</strong> saat pilih produk</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-2 flex-shrink-0"></i>
                                    <span>Stock produk akan <strong>bertambah otomatis</strong></span>
                                </li>
                            </ul>
                        </div>

                        <!-- Tips Card -->
                        <div
                            class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl shadow-lg p-6 border border-purple-100">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center text-white mr-3">
                                    <i class="fas fa-lightbulb text-xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Tips</h3>
                            </div>

                            <ul class="space-y-3 text-sm text-gray-700">
                                <li class="flex items-start">
                                    <i class="fas fa-star text-yellow-500 mt-1 mr-2 flex-shrink-0"></i>
                                    <span>Periksa <strong>harga produk</strong> sebelum menyimpan</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-star text-yellow-500 mt-1 mr-2 flex-shrink-0"></i>
                                    <span>Pastikan <strong>qty</strong> sudah benar</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-star text-yellow-500 mt-1 mr-2 flex-shrink-0"></i>
                                    <span>Data tidak bisa <strong>diubah</strong> setelah disimpan</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            let itemIndex = 0;
            const products = @json($products);

            function addItem() {
                const container = document.getElementById('items-container');
                const emptyState = document.getElementById('empty-state');

                const itemHtml = `
        <div class="item-row bg-gray-50 border-2 border-gray-200 rounded-xl p-4" id="item-${itemIndex}">
            <div class="grid grid-cols-12 gap-4">
                <!-- Produk -->
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Produk</label>
                    <select name="detail[${itemIndex}][id_produk]"
                            class="produk-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            onchange="updatePrice(${itemIndex})"
                            required>
                        <option value="">-- Pilih Produk --</option>
                        ${products.map(p => `<option value="${p.id_produk}" data-price="${p.harga_produk}">${p.nama_produk} (Stock: ${p.stock_produk})</option>`).join('')}
                    </select>
                </div>

                <!-- Harga -->
                <div class="col-span-12 md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Harga</label>
                    <input type="number"
                           name="detail[${itemIndex}][harga_produk]"
                           class="harga-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="0"
                           min="0"
                           step="0.01"
                           onchange="calculateSubtotal(${itemIndex})"
                           required>
                </div>

                <!-- Qty -->
                <div class="col-span-12 md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Qty</label>
                    <input type="number"
                           name="detail[${itemIndex}][qty_produk]"
                           class="qty-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="0"
                           min="1"
                           value="1"
                           onchange="calculateSubtotal(${itemIndex})"
                           required>
                </div>

                <!-- Subtotal -->
                <div class="col-span-10 md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Subtotal</label>
                    <input type="text"
                           class="subtotal-display w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-right"
                           value="Rp 0"
                           readonly>
                    <input type="hidden" name="detail[${itemIndex}][subtotal_harga]" class="subtotal-value" value="0">
                </div>

                <!-- Remove Button -->
                <div class="col-span-2 md:col-span-1 flex items-end">
                    <button type="button"
                            onclick="removeItem(${itemIndex})"
                            class="w-full px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

                container.insertAdjacentHTML('beforeend', itemHtml);
                emptyState.style.display = 'none';
                itemIndex++;
                updateSummary();
            }

            function removeItem(index) {
                const item = document.getElementById(`item-${index}`);
                item.remove();

                const container = document.getElementById('items-container');
                const emptyState = document.getElementById('empty-state');

                if (container.children.length === 0) {
                    emptyState.style.display = 'block';
                }

                updateSummary();
            }

            function updatePrice(index) {
                const item = document.getElementById(`item-${index}`);
                const select = item.querySelector('.produk-select');
                const hargaInput = item.querySelector('.harga-input');

                const selectedOption = select.options[select.selectedIndex];
                const price = selectedOption.getAttribute('data-price');

                if (price) {
                    hargaInput.value = price;
                    calculateSubtotal(index);
                }
            }

            function calculateSubtotal(index) {
                const item = document.getElementById(`item-${index}`);
                const harga = parseFloat(item.querySelector('.harga-input').value) || 0;
                const qty = parseInt(item.querySelector('.qty-input').value) || 0;
                const subtotal = harga * qty;

                item.querySelector('.subtotal-value').value = subtotal;
                item.querySelector('.subtotal-display').value = 'Rp ' + subtotal.toLocaleString('id-ID');

                updateSummary();
            }

            function updateSummary() {
                const items = document.querySelectorAll('.item-row');
                let totalItems = items.length;
                let totalQty = 0;
                let totalHarga = 0;

                items.forEach(item => {
                    const qty = parseInt(item.querySelector('.qty-input').value) || 0;
                    const subtotal = parseFloat(item.querySelector('.subtotal-value').value) || 0;

                    totalQty += qty;
                    totalHarga += subtotal;
                });

                document.getElementById('total-items').textContent = totalItems;
                document.getElementById('total-qty').textContent = totalQty;
                document.getElementById('total-harga').textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
            }

            // Form validation
            document.getElementById('formPenerimaan').addEventListener('submit', function(e) {
                const items = document.querySelectorAll('.item-row');

                if (items.length === 0) {
                    e.preventDefault();
                    alert('Tambahkan minimal 1 produk!');
                    return false;
                }

                // Validate all items have values
                let valid = true;
                items.forEach(item => {
                    const produk = item.querySelector('.produk-select').value;
                    const harga = item.querySelector('.harga-input').value;
                    const qty = item.querySelector('.qty-input').value;

                    if (!produk || !harga || !qty || harga <= 0 || qty <= 0) {
                        valid = false;
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    alert('Pastikan semua produk terisi dengan lengkap dan nilai > 0!');
                    return false;
                }
            });

            // Add first item on load
            document.addEventListener('DOMContentLoaded', function() {
                addItem();
            });
        </script>
    @endpush
@endsection
