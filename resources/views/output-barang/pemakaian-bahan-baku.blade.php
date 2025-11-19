@extends('layouts.app')

@section('title', 'Pemakaian Bahan Baku')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Form Input Pemakaian Bahan Baku</h2>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('output-barang.store-pemakaian-bahan') }}" method="POST" id="mainForm">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="tanggal" class="required">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="input" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="nomor_bukti" class="required">Nomor Bukti</label>
                    <input type="text" id="nomor_bukti" name="nomor_bukti" class="input" value="{{ old('nomor_bukti') }}" placeholder="BUKTI-001" required>
                    @error('nomor_bukti') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" class="input" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="position: relative;">
                    <label for="nama_customer" class="required">Nama Departemen/Produksi</label>
                    <input type="text" id="nama_customer" name="nama_customer" class="input autocomplete-input" value="{{ old('nama_customer') }}" placeholder="Cari customer..." autocomplete="off" required>
                    <div id="customer-autocomplete" class="autocomplete-dropdown"></div>
                    @error('nama_customer') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="kode_customer" class="required">Kode Referensi</label>
                    <input type="text" id="kode_customer" name="kode_customer" class="input" value="{{ old('kode_customer') }}" placeholder="Kode akan terisi otomatis" readonly style="background-color: #f5f5f5;" required>
                    @error('kode_customer') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
            </div>

            <h3 class="mb-20 mt-30">Daftar Bahan Baku</h3>

            <div class="table-responsive">
                <table class="table table-gold table-striped" id="itemTable">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Kode Barang</th>
                            <th>Quantity</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th style="width: 50px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemBody">
                        <tr class="item-row">
                            <td><input type="text" name="barang_nama[]" class="input barang-nama-input" placeholder="Nama Barang" required onchange="loadBarangByName(this)"></td>
                            <td><input type="text" name="barang_kode[]" class="input barang-kode-input" placeholder="Kode akan terisi otomatis" readonly style="background-color: #f5f5f5;" required></td>
                            <td><input type="number" name="barang_qty[]" class="input qty-input" value="1" min="0" step="0.01" required></td>
                            <td>
                                <select name="barang_satuan[]" class="input-select">
                                    <option value="Gram">Gram (g)</option>
                                    <option value="Kilogram">Kilogram (kg)</option>
                                    <option value="Milliliter">Milliliter (ml)</option>
                                    <option value="Pcs">Pcs (piece)</option>
                                    <option value="Pack">Pack</option>
                                </select>
                            </td>
                            <td><input type="number" name="barang_harga[]" class="input harga-input" value="0" min="0" step="0.01" required readonly style="background-color: #f5f5f5;"></td>
                            <td><input type="number" name="barang_jumlah[]" class="input jumlah-input" value="0" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)">🗑️</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn btn-primary mt-20" id="btnAddRow" onclick="addRow()">+ Tambah Bahan Baku</button>

            <div class="total-section" style="margin-top: 20px; padding: 15px; background: var(--cream); border-radius: 5px; text-align: right; font-size: 18px; font-weight: 600;">
                Total: Rp <span id="totalAmount">0</span>
            </div>

            <div class="form-actions" style="margin-top: 20px;">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Pemakaian 💾</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
    .autocomplete-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-top: 2px;
    }
    
    .autocomplete-dropdown.show {
        display: block;
    }
    
    .autocomplete-item {
        padding: 10px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
    }
    
    .autocomplete-item:hover,
    .autocomplete-item.selected {
        background-color: #f8f9fa;
    }
    
    .autocomplete-item:last-child {
        border-bottom: none;
    }
    
    .autocomplete-item strong {
        color: #333;
        font-weight: 600;
    }
    
    .autocomplete-item small {
        color: #666;
        display: block;
        margin-top: 2px;
    }
    
    .form-group {
        position: relative;
    }
</style>
@endsection

@section('scripts')
<script>
    // Make functions global
    window.addRow = function() {
        const tbody = document.getElementById('itemBody');
        if (!tbody) {
            alert('Error: Tabel tidak ditemukan');
            return;
        }
        
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td><input type="text" name="barang_nama[]" class="input barang-nama-input" placeholder="Nama Barang" required onchange="loadBarangByName(this)"></td>
            <td><input type="text" name="barang_kode[]" class="input barang-kode-input" placeholder="Kode akan terisi otomatis" readonly style="background-color: #f5f5f5;" required></td>
            <td><input type="number" name="barang_qty[]" class="input qty-input" value="1" min="0" step="0.01" required></td>
            <td>
                <select name="barang_satuan[]" class="input-select">
                    <option value="Gram">Gram (g)</option>
                    <option value="Kilogram">Kilogram (kg)</option>
                    <option value="Milliliter">Milliliter (ml)</option>
                    <option value="Pcs">Pcs (piece)</option>
                    <option value="Pack">Pack</option>
                </select>
            </td>
            <td><input type="number" name="barang_harga[]" class="input harga-input" value="0" min="0" step="0.01" required readonly style="background-color: #f5f5f5;"></td>
            <td><input type="number" name="barang_jumlah[]" class="input jumlah-input" value="0" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)">🗑️</button></td>
        `;
        tbody.appendChild(newRow);
    };

    window.deleteRow = function(button) {
        const row = button.closest('tr');
        if (row && confirm('Apakah Anda yakin ingin menghapus baris ini?')) {
            row.remove();
            updateTotal();
        }
    };

    function calculateRow(input) {
        const row = input.closest('tr');
        if (!row) return;
        
        const qtyInput = row.querySelector('.qty-input');
        const hargaInput = row.querySelector('.harga-input');
        const jumlahInput = row.querySelector('.jumlah-input');

        if (!qtyInput || !hargaInput || !jumlahInput) return;

        const qty = parseFloat(qtyInput.value) || 0;
        const harga = parseFloat(hargaInput.value) || 0;
        const jumlah = qty * harga;

        jumlahInput.value = jumlah.toFixed(2);
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.jumlah-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        
        const formattedTotal = new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(total);
        
        const totalElement = document.getElementById('totalAmount');
        if (totalElement) {
            totalElement.textContent = formattedTotal;
        }
    }

    // Function to load barang data by name and auto-fill kode
    // Hanya untuk barang dengan kategori "Bahan Baku"
    async function loadBarangByName(input) {
        const row = input.closest('tr');
        const namaInput = row.querySelector('.barang-nama-input');
        const kodeInput = row.querySelector('.barang-kode-input');
        const satuanSelect = row.querySelector('select[name="barang_satuan[]"]');
        const hargaInput = row.querySelector('.harga-input');
        const qtyInput = row.querySelector('.qty-input');
        
        const namaBarang = namaInput.value.trim();
        
        if (!namaBarang) {
            if (kodeInput) kodeInput.value = '';
            if (hargaInput) hargaInput.value = '0';
            if (qtyInput) calculateRow(qtyInput);
            return;
        }
        
        try {
            const response = await fetch(`/api/get-barang-by-name?nama=${encodeURIComponent(namaBarang)}`);
            const data = await response.json();
            
            if (data.success && data.barang) {
                // Validasi kategori: hanya "Bahan Baku" yang diperbolehkan
                const kategori = (data.barang.kategori || data.barang.deskripsi || '').toLowerCase().trim();
                const allowedKategori = ['bahan baku', 'bahan_baku', 'bahanbaku', 'bahan roti', 'bahan kopi'];
                
                if (!allowedKategori.includes(kategori)) {
                    alert('Barang "' + namaBarang + '" bukan kategori Bahan Baku. Hanya barang dengan kategori "Bahan Baku" yang dapat digunakan untuk pemakaian bahan baku.');
                    namaInput.value = '';
                    if (kodeInput) kodeInput.value = '';
                    if (hargaInput) hargaInput.value = '0';
                    if (qtyInput) calculateRow(qtyInput);
                    return;
                }
                
                if (kodeInput) {
                    kodeInput.value = data.barang.kode_barang || '';
                }
                
                // Map satuan
                const satuanMapping = {
                    'kg': 'Kilogram',
                    'g': 'Gram',
                    'ml': 'Milliliter',
                    'pcs': 'Pcs',
                    'piece': 'Pcs',
                    'Pack': 'Pack',
                    'Gram': 'Gram',
                    'Kilogram': 'Kilogram',
                    'Milliliter': 'Milliliter',
                    'Pcs': 'Pcs'
                };
                const dbSatuan = data.barang.satuan || '';
                if (satuanSelect) {
                    satuanSelect.value = satuanMapping[dbSatuan] || 'Kilogram';
                }
                
                // Fill harga beli (readonly, dari database)
                if (hargaInput && data.barang.harga_beli) {
                    hargaInput.value = data.barang.harga_beli;
                    if (qtyInput) calculateRow(qtyInput);
                }
            } else {
                alert('Nama barang "' + namaBarang + '" tidak ditemukan di master data!');
                namaInput.value = '';
                if (kodeInput) kodeInput.value = '';
                if (hargaInput) hargaInput.value = '0';
                if (qtyInput) calculateRow(qtyInput);
            }
        } catch (error) {
            console.error('Error loading barang data:', error);
            alert('Terjadi kesalahan saat memuat data barang. Pastikan nama barang benar.');
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize customer autocomplete
        initCustomerAutocomplete();
        
        // Use event delegation for all rows (existing and future)
        const itemBody = document.getElementById('itemBody');
        if (itemBody) {
            // Handle input event (fires while typing) - PRIMARY METHOD
            itemBody.addEventListener('input', function(e) {
                if (e.target.classList.contains('qty-input') || e.target.classList.contains('harga-input')) {
                    calculateRow(e.target);
                }
            });
            
            // Handle change event as backup
            itemBody.addEventListener('change', function(e) {
                if (e.target.classList.contains('qty-input') || e.target.classList.contains('harga-input')) {
                    calculateRow(e.target);
                }
            });
            
            // Handle keyup as additional backup
            itemBody.addEventListener('keyup', function(e) {
                if (e.target.classList.contains('qty-input') || e.target.classList.contains('harga-input')) {
                    calculateRow(e.target);
                }
            });
        }
        
        // Ensure addRow button works (both onclick and event listener)
        const btnAddRow = document.getElementById('btnAddRow');
        if (btnAddRow) {
            btnAddRow.addEventListener('click', function(e) {
                e.preventDefault();
                addRow();
            });
        }
        
        // Initial calculation
        updateTotal();
    });

    // ============================================
    // AUTOCOMPLETE LOGIC FOR CUSTOMER
    // ============================================
    
    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Autocomplete for Customer
    function initCustomerAutocomplete() {
        const input = document.getElementById('nama_customer');
        const dropdown = document.getElementById('customer-autocomplete');
        const kodeInput = document.getElementById('kode_customer');
        let selectedIndex = -1;
        let currentResults = [];
        
        if (!input || !dropdown) return;
        
        const searchCustomer = debounce(async (term) => {
            if (term.length < 2) {
                dropdown.classList.remove('show');
                return;
            }
            
            try {
                const response = await fetch(`/api/customer/search?term=${encodeURIComponent(term)}`);
                const results = await response.json();
                currentResults = results;
                selectedIndex = -1;
                
                if (results.length === 0) {
                    dropdown.innerHTML = '<div class="autocomplete-item">Tidak ada customer ditemukan</div>';
                    dropdown.classList.add('show');
                    return;
                }
                
                dropdown.innerHTML = results.map((customer, index) => `
                    <div class="autocomplete-item" data-index="${index}">
                        <strong>${customer.nama_customer}</strong>
                        <small>Kode: ${customer.kode_customer}${customer.telepon ? ' | Telp: ' + customer.telepon : ''}</small>
                    </div>
                `).join('');
                
                dropdown.classList.add('show');
                
                // Add click handlers
                dropdown.querySelectorAll('.autocomplete-item').forEach((item, index) => {
                    item.addEventListener('click', () => {
                        selectCustomer(results[index]);
                    });
                });
            } catch (error) {
                console.error('Error searching customer:', error);
            }
        }, 300);
        
        function selectCustomer(customer) {
            input.value = customer.nama_customer;
            kodeInput.value = customer.kode_customer;
            dropdown.classList.remove('show');
            currentResults = [];
        }
        
        input.addEventListener('input', (e) => {
            searchCustomer(e.target.value);
        });
        
        input.addEventListener('keydown', (e) => {
            const items = dropdown.querySelectorAll('.autocomplete-item');
            if (!dropdown.classList.contains('show') || items.length === 0) return;
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                items.forEach((item, i) => {
                    item.classList.toggle('selected', i === selectedIndex);
                });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, -1);
                items.forEach((item, i) => {
                    item.classList.toggle('selected', i === selectedIndex);
                });
            } else if (e.key === 'Enter' && selectedIndex >= 0) {
                e.preventDefault();
                if (currentResults[selectedIndex]) {
                    selectCustomer(currentResults[selectedIndex]);
                }
            } else if (e.key === 'Escape') {
                dropdown.classList.remove('show');
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    }

    // Also handle form submission to ensure calculations are correct
    document.addEventListener('DOMContentLoaded', function() {
        const mainForm = document.getElementById('mainForm');
        if (mainForm) {
            mainForm.addEventListener('submit', function(e) {
                // Recalculate all rows before submit
                document.querySelectorAll('.item-row').forEach(row => {
                    const qtyInput = row.querySelector('.qty-input');
                    if (qtyInput) {
                        calculateRow(qtyInput);
                    }
                });
            });
        }
    });
</script>
@endsection
