@extends('layouts.app')

@section('title', 'Input Saldo Awal')

@section('styles')
    <style>
        .saldo-awal-box {
            background: var(--cream);
            border: 2px dashed var(--gold);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }

        .saldo-awal-label {
            font-size: 16px;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .saldo-awal-amount {
            font-size: 32px;
            font-weight: 700;
            color: var(--gold);
        }

        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
        }

        .saldo-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            border-radius: 8px;
            overflow: hidden;
        }

        .saldo-table thead {
            background: var(--gold-dark);
            color: var(--white);
        }

        .saldo-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 2px solid var(--gold);
        }

        .saldo-table td {
            padding: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .saldo-table tbody tr:hover {
            background-color: var(--cream);
        }

        .saldo-table input,
        .saldo-table select {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 13px;
        }

        .saldo-table input:focus,
        .saldo-table select:focus {
            outline: none;
            border-color: var(--gold);
        }

        .btn-remove-row {
            background: #DC3545;
            color: var(--white);
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .btn-remove-row:hover {
            background: #C82333;
        }

        .btn-add-row {
            background: var(--gold);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .btn-add-row:hover {
            background: var(--gold-dark);
        }

        .btn-submit {
            background: var(--gold-dark);
            color: var(--white);
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 20px auto 0;
        }

        .btn-submit:hover {
            background: var(--gold);
        }
    </style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>💰 Input Saldo Awal</h2>
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

        <!-- Saldo Awal Display Box -->
        <div class="saldo-awal-box">
            <div class="saldo-awal-label">Saldo Awal</div>
            <div class="saldo-awal-amount" id="totalSaldo">Rp {{ number_format($totalSaldo ?? 0, 0, ',', '.') }}</div>
        </div>

        <form action="{{ route('input-data.store-saldo') }}" method="POST" id="saldoForm">
            @csrf

            <button type="button" class="btn-add-row" onclick="addRow()">+ Tambah Barang</button>

            <div class="table-container">
                <table class="saldo-table">
                    <thead>
                        <tr>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Kategori Barang</th>
                            <th>Jumlah Unit</th>
                            <th>Jenis Satuan</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Jumlah (Rp)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemBody">
                        @forelse($barangs ?? [] as $barang)
                            <tr class="item-row">
                                <td>
                                    <input type="text" name="barang_kode[]" class="input" value="{{ $barang->kode_barang }}" readonly style="background-color: #f5f5f5;">
                                </td>
                                <td>
                                    <input type="text" name="barang_nama[]" class="input" value="{{ $barang->nama_barang }}" readonly style="background-color: #f5f5f5;">
                                </td>
                                <td>
                                    <select name="barang_kategori[]" class="input-select">
                                        <option value="bahan_baku" {{ ($barang->deskripsi ?? '') == 'bahan_baku' || ($barang->deskripsi ?? '') == 'Bahan Roti' || ($barang->deskripsi ?? '') == 'Bahan Kopi' ? 'selected' : '' }}>Bahan Baku</option>
                                        <option value="barang_jadi" {{ ($barang->deskripsi ?? '') == 'barang_jadi' ? 'selected' : '' }}>Barang Jadi</option>
                                        <option value="barang_proses" {{ ($barang->deskripsi ?? '') == 'barang_proses' ? 'selected' : '' }}>Barang Dalam Proses</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="barang_qty[]" class="input" value="{{ $barang->stok }}" min="0" step="0.01" onchange="calculateRow(this)">
                                </td>
                                <td>
                                    <select name="barang_satuan[]" class="input-select">
                                        <option value="Gram" {{ $barang->satuan == 'Gram' || $barang->satuan == 'g' ? 'selected' : '' }}>Gram (g)</option>
                                        <option value="Kilogram" {{ $barang->satuan == 'Kilogram' || $barang->satuan == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                        <option value="Milliliter" {{ $barang->satuan == 'Milliliter' || $barang->satuan == 'ml' ? 'selected' : '' }}>Milliliter (ml)</option>
                                        <option value="Pcs" {{ $barang->satuan == 'Pcs' || $barang->satuan == 'pcs' || $barang->satuan == 'piece' ? 'selected' : '' }}>Pcs (piece)</option>
                                        <option value="Pack" {{ $barang->satuan == 'Pack' ? 'selected' : '' }}>Pack</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="barang_harga[]" class="input" value="{{ $barang->harga_beli }}" min="0" step="0.01" onchange="calculateRow(this)">
                                </td>
                                <td>
                                    <input type="number" name="barang_harga_jual[]" class="input" value="{{ $barang->harga_jual ?? 0 }}" min="0" step="0.01">
                                </td>
                                <td>
                                    <input type="number" name="barang_jumlah[]" class="input" value="{{ $barang->stok * $barang->harga_beli }}" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn-remove-row" onclick="removeRow(this)">🗑️</button>
                                </td>
                            </tr>
                        @empty
                            <tr class="item-row">
                                <td>
                                    <input type="text" name="barang_kode[]" class="input barang-kode-input" placeholder="KODE-001" readonly style="background-color: #f5f5f5;">
                                </td>
                                <td>
                                    <input type="text" name="barang_nama[]" class="input barang-nama-input" placeholder="Nama Barang" onchange="loadBarangByName(this)">
                                </td>
                                <td>
                                    <select name="barang_kategori[]" class="input-select barang-kategori-select">
                                        <option value="">Pilih Kategori</option>
                                        <option value="bahan_baku">Bahan Baku</option>
                                        <option value="barang_jadi">Barang Jadi</option>
                                        <option value="barang_proses">Barang Dalam Proses</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="barang_qty[]" class="input" placeholder="0" value="0" min="0" step="0.01" onchange="calculateRow(this)">
                                </td>
                                <td>
                                    <select name="barang_satuan[]" class="input-select barang-satuan-select">
                                        <option value="">Pilih Satuan</option>
                                        <option value="Gram">Gram (g)</option>
                                        <option value="Kilogram">Kilogram (kg)</option>
                                        <option value="Milliliter">Milliliter (ml)</option>
                                        <option value="Pcs">Pcs (piece)</option>
                                        <option value="Pack">Pack</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="barang_harga[]" class="input" placeholder="0" value="0" min="0" step="0.01" onchange="calculateRow(this)">
                                </td>
                                <td>
                                    <input type="number" name="barang_harga_jual[]" class="input barang-harga-jual-input" placeholder="0" value="0" min="0" step="0.01">
                                </td>
                                <td>
                                    <input type="number" name="barang_jumlah[]" class="input" placeholder="0" value="0" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn-remove-row" onclick="removeRow(this)">🗑️</button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn-submit">
                📁 Submit Data
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let rowCount = 1;

    function addRow() {
        rowCount++;
        const tbody = document.getElementById('itemBody');
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td>
                <input type="text" name="barang_kode[]" class="input barang-kode-input" placeholder="KODE-001" readonly style="background-color: #f5f5f5;">
            </td>
            <td>
                <input type="text" name="barang_nama[]" class="input barang-nama-input" placeholder="Nama Barang" onchange="loadBarangByName(this)">
            </td>
            <td>
                <select name="barang_kategori[]" class="input-select barang-kategori-select">
                    <option value="">Pilih Kategori</option>
                    <option value="bahan_baku">Bahan Baku</option>
                    <option value="barang_jadi">Barang Jadi</option>
                    <option value="barang_proses">Barang Dalam Proses</option>
                </select>
            </td>
            <td>
                <input type="number" name="barang_qty[]" class="input" placeholder="0" value="0" min="0" step="0.01" onchange="calculateRow(this)">
            </td>
            <td>
                <select name="barang_satuan[]" class="input-select barang-satuan-select">
                    <option value="">Pilih Satuan</option>
                    <option value="Gram">Gram (g)</option>
                    <option value="Kilogram">Kilogram (kg)</option>
                    <option value="Milliliter">Milliliter (ml)</option>
                    <option value="Pcs">Pcs (piece)</option>
                    <option value="Pack">Pack</option>
                </select>
            </td>
            <td>
                <input type="number" name="barang_harga[]" class="input" placeholder="0" value="0" min="0" step="0.01" onchange="calculateRow(this)">
            </td>
            <td>
                <input type="number" name="barang_harga_jual[]" class="input barang-harga-jual-input" placeholder="0" value="0" min="0" step="0.01">
            </td>
            <td>
                <input type="number" name="barang_jumlah[]" class="input" placeholder="0" value="0" readonly>
            </td>
            <td>
                <button type="button" class="btn-remove-row" onclick="removeRow(this)">🗑️</button>
            </td>
        `;
        tbody.appendChild(newRow);
    }

    function removeRow(button) {
        const row = button.closest('tr');
        const kodeBarang = row.querySelector('input[name="barang_kode[]"]').value;
        const isReadonly = row.querySelector('input[name="barang_kode[]"]').hasAttribute('readonly');
        
        // Konfirmasi sebelum menghapus
        let confirmMessage = 'Apakah Anda yakin ingin menghapus baris ini?';
        if (isReadonly && kodeBarang) {
            confirmMessage = `Apakah Anda yakin ingin menghapus ${kodeBarang} dari form?\n\nCatatan: Data akan tetap ada di database sampai Anda submit form dengan menghapus baris ini.`;
        }
        
        if (confirm(confirmMessage)) {
            row.remove();
            updateTotal();
        }
    }

    function calculateRow(input) {
        const row = input.closest('tr');
        const qty = parseFloat(row.querySelector('input[name="barang_qty[]"]').value) || 0;
        const harga = parseFloat(row.querySelector('input[name="barang_harga[]"]').value) || 0;
        const jumlah = qty * harga;
        
        row.querySelector('input[name="barang_jumlah[]"]').value = jumlah.toFixed(2);
        updateTotal();
    }

    function updateTotal() {
        const rows = document.querySelectorAll('.item-row');
        let total = 0;
        
        rows.forEach(row => {
            const jumlah = parseFloat(row.querySelector('input[name="barang_jumlah[]"]').value) || 0;
            total += jumlah;
        });
        
        document.getElementById('totalSaldo').textContent = 'Rp ' + formatNumber(total);
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(num);
    }

    // Function to load barang data by name and auto-fill fields
    async function loadBarangByName(input) {
        const row = input.closest('tr');
        const namaInput = row.querySelector('.barang-nama-input');
        const kodeInput = row.querySelector('.barang-kode-input');
        const kategoriSelect = row.querySelector('.barang-kategori-select');
        const satuanSelect = row.querySelector('.barang-satuan-select');
        const hargaJualInput = row.querySelector('.barang-harga-jual-input');
        
        const namaBarang = namaInput.value.trim();
        
        if (!namaBarang) {
            // Reset fields if nama is empty
            kodeInput.value = '';
            kategoriSelect.value = '';
            satuanSelect.value = '';
            if (hargaJualInput) hargaJualInput.value = '0';
            return;
        }
        
        try {
            const response = await fetch(`/api/get-barang-by-name?nama=${encodeURIComponent(namaBarang)}`);

            if (!response.ok) {
                // If 401 Unauthorized (session expired), redirect to login
                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                throw new Error('Network response was not ok');
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // If response is not JSON (e.g. HTML login page), redirect to login
                window.location.href = '/login';
                return;
            }

            const data = await response.json();
            
            if (data.success && data.barang && Array.isArray(data.barang)) {
                // If multiple items found, let user choose by exact code match or taking the first one
                // Since this is a simple text input, we'll implement a basic logic:
                // If exact match found, use it. If not, use first.
                // ideally we would show a dropdown, but that requires UI changes.
                // For now, we take the first item, but we should handle the list properly in a real autocomplete.

                // CHECK if there are multiple items
                if (data.barang.length > 1) {
                    // Create a prompt for the user to select the code
                    let message = "Ditemukan beberapa barang dengan nama mirip:\n";
                    data.barang.forEach((item, index) => {
                        message += `${index + 1}. ${item.nama_barang} (Kode: ${item.kode_barang})\n`;
                    });
                    message += "\nMasukkan NOMOR barang yang ingin dipilih:";

                    let choice = prompt(message);
                    if (choice && !isNaN(choice) && choice > 0 && choice <= data.barang.length) {
                        var selectedItem = data.barang[choice - 1];
                    } else {
                        // User cancelled or invalid
                        return;
                    }
                } else {
                    var selectedItem = data.barang[0];
                }

                // Fill in the data
                namaInput.value = selectedItem.nama_barang; // Update to exact name
                kodeInput.value = selectedItem.kode_barang || '';
                
                // Map kategori (deskripsi) to select value
                const kategoriMapping = {
                    'bahan_baku': 'bahan_baku',
                    'barang_jadi': 'barang_jadi',
                    'barang_proses': 'barang_proses',
                    'Bahan Roti': 'bahan_baku',
                    'Bahan Kopi': 'bahan_baku',
                };
                const dbKategori = selectedItem.kategori || selectedItem.deskripsi || '';
                kategoriSelect.value = kategoriMapping[dbKategori] || '';
                
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
                satuanSelect.value = satuanMapping[dbSatuan] || '';
                
                // Fill harga jual
                if (hargaJualInput) {
                    hargaJualInput.value = selectedItem.harga_jual || 0;
                }
            } else if (data.success && data.barang) {
                // Backward compatibility if single object returned (though API now returns array)
                let item = data.barang;
                kodeInput.value = item.kode_barang || '';
                 // ... mapping logic same as above ...
            } else {
                alert('Nama barang "' + namaBarang + '" tidak ditemukan di master data!');
                namaInput.value = '';
                kodeInput.value = '';
                kategoriSelect.value = '';
                satuanSelect.value = '';
                if (hargaJualInput) hargaJualInput.value = '0';
            }
        } catch (error) {
            console.error('Error loading barang data:', error);
            alert('Terjadi kesalahan saat memuat data barang. Pastikan nama barang benar.');
        }
    }

    // Initialize total on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Calculate initial total from existing rows
        const rows = document.querySelectorAll('.item-row');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('input[name="barang_qty[]"]').value) || 0;
            const harga = parseFloat(row.querySelector('input[name="barang_harga[]"]').value) || 0;
            const jumlah = qty * harga;
            row.querySelector('input[name="barang_jumlah[]"]').value = jumlah.toFixed(2);
        });
        updateTotal();
    });
</script>
@endsection
