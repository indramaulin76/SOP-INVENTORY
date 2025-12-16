@extends('layouts.app')

@section('title', 'Barang Jadi')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Form Input Barang Jadi</h2>
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

        <form action="{{ route('input-barang.store-jadi') }}" method="POST" id="mainForm">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="tanggal" class="required">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" class="input" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="nomor_faktur" class="required">Nomor Faktur</label>
                    <input type="text" id="nomor_faktur" name="nomor_faktur" class="input" value="{{ old('nomor_faktur') }}" placeholder="INV-001" required>
                    @error('nomor_faktur') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" class="input" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nama_supplier">Nama Supplier</label>
                    <input type="text" id="nama_supplier" name="nama_supplier" class="input" value="{{ old('nama_supplier') }}" placeholder="PT. Supplier Bahan Baku">
                    @error('nama_supplier') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label for="kode_supplier">Kode Supplier</label>
                    <input type="text" id="kode_supplier" name="kode_supplier" class="input" value="{{ old('kode_supplier') }}" placeholder="SUP-001">
                    @error('kode_supplier') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                </div>
            </div>

            <h3 class="mb-20 mt-30">Daftar Barang</h3>

            <div class="table-responsive">
                <table class="table table-gold table-striped" id="itemTable">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Kode Barang</th>
                            <th>Quantity</th>
                            <th>Satuan</th>
                            <th>Harga Beli</th>
                            <th>Jumlah</th>
                            <th style="width: 50px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemBody">
                        <tr class="item-row">
                            <td><input type="text" name="barang_nama[]" class="input barang-nama-input" placeholder="Nama Barang" required onchange="loadBarangByName(this)"></td>
                            <td><input type="text" name="barang_kode[]" class="input barang-kode-input" placeholder="Kode akan terisi otomatis" readonly style="background-color: #f5f5f5;" required></td>
                            <td><input type="number" name="barang_qty[]" class="input qty-input" value="0" min="0" step="0.01" required></td>
                            <td>
                                <select name="barang_satuan[]" class="input-select">
                                    <option value="Gram">Gram (g)</option>
                                    <option value="Kilogram">Kilogram (kg)</option>
                                    <option value="Milliliter">Milliliter (ml)</option>
                                    <option value="Pcs">Pcs (piece)</option>
                                    <option value="Pack">Pack</option>
                                </select>
                            </td>
                            <td><input type="number" name="barang_harga[]" class="input harga-input" value="0" min="0" step="0.01" required></td>
                            <td><input type="number" name="barang_jumlah[]" class="input jumlah-input" value="0" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)">🗑️</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn btn-primary mt-20" id="btnAddRow" onclick="addRow()">+ Tambah Barang</button>

            <div class="total-section" style="margin-top: 20px; padding: 15px; background: var(--cream); border-radius: 5px; text-align: right; font-size: 18px; font-weight: 600;">
                Total: Rp <span id="totalAmount">0</span>
            </div>

            <div class="form-actions" style="margin-top: 20px;">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Barang Jadi 💾</button>
            </div>
        </form>
    </div>
</div>
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
            <td><input type="number" name="barang_qty[]" class="input qty-input" value="0" min="0" step="0.01" required></td>
            <td>
                <select name="barang_satuan[]" class="input-select">
                    <option value="Gram">Gram (g)</option>
                    <option value="Kilogram">Kilogram (kg)</option>
                    <option value="Milliliter">Milliliter (ml)</option>
                    <option value="Pcs">Pcs (piece)</option>
                    <option value="Pack">Pack</option>
                </select>
            </td>
            <td><input type="number" name="barang_harga[]" class="input harga-input" value="0" min="0" step="0.01" required></td>
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
    // Hanya untuk barang dengan kategori "Barang Jadi"
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

            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                throw new Error('Network response was not ok');
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                window.location.href = '/login';
                return;
            }

            const data = await response.json();
            
            if (data.success && data.barang && Array.isArray(data.barang)) {
                // Check multiple results
                 if (data.barang.length > 1) {
                    let message = "Ditemukan beberapa barang dengan nama mirip:\n";
                    data.barang.forEach((item, index) => {
                        message += `${index + 1}. ${item.nama_barang} (Kode: ${item.kode_barang})\n`;
                    });
                    message += "\nMasukkan NOMOR barang yang ingin dipilih:";

                    let choice = prompt(message);
                    if (choice && !isNaN(choice) && choice > 0 && choice <= data.barang.length) {
                        var selectedItem = data.barang[choice - 1];
                    } else {
                        return; // Cancelled
                    }
                } else {
                    var selectedItem = data.barang[0];
                }

                // Validasi kategori: hanya "Barang Jadi" yang diperbolehkan
                const kategori = (selectedItem.kategori || selectedItem.deskripsi || '').toLowerCase().trim();
                const allowedKategori = ['barang jadi', 'barang_jadi', 'barangjadi'];
                
                // Flexible match
                let isMatch = false;
                for (let k of allowedKategori) {
                    if (kategori.includes(k) || k.includes(kategori)) {
                        isMatch = true;
                        break;
                    }
                }

                if (!isMatch && kategori !== '') {
                    alert('Barang "' + selectedItem.nama_barang + '" bukan kategori Barang Jadi. Hanya barang dengan kategori "Barang Jadi" yang dapat digunakan untuk input barang jadi.');
                    namaInput.value = '';
                    if (kodeInput) kodeInput.value = '';
                    if (hargaInput) hargaInput.value = '0';
                    if (qtyInput) calculateRow(qtyInput);
                    return;
                }
                
                // Update fields
                namaInput.value = selectedItem.nama_barang; // Ensure exact name
                if (kodeInput) {
                    kodeInput.value = selectedItem.kode_barang || '';
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
                const dbSatuan = selectedItem.satuan || '';
                if (satuanSelect) {
                    satuanSelect.value = satuanMapping[dbSatuan] || 'Kilogram';
                }
                
                // Fill harga beli (dari database)
                if (hargaInput && selectedItem.harga_beli) {
                    hargaInput.value = selectedItem.harga_beli;
                    if (qtyInput) calculateRow(qtyInput);
                }
            } else if (data.success && data.barang) {
                 // Fallback for single object response
                let selectedItem = data.barang;

                if (kodeInput) kodeInput.value = selectedItem.kode_barang || '';
                // ... same logic as above ...
                 // Validasi kategori
                 const kategori = (selectedItem.kategori || selectedItem.deskripsi || '').toLowerCase().trim();
                 // ... check logic ...
                 if (!['barang jadi', 'barang_jadi'].includes(kategori) && kategori !== '') {
                    // alert...
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
