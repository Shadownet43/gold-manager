<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>WoW Gold RMT Tracker</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #0f172a; font-family: 'Segoe UI', sans-serif; }
        .card { background-color: #1e293b; border: 1px solid #334155; border-radius: 12px; margin-bottom: 1rem; }
        .card-header { border-bottom: 1px solid #334155; font-weight: bold; color: #fbbf24; }
        .text-gold { color: #fbbf24 !important; }
        .text-info-light { color: #38bdf8 !important; }
        .border-info { border-color: #0ea5e9 !important; }
        .btn-add-stok { width: 30px; height: 30px; border-radius: 50%; font-size: 0.8rem; padding: 0; line-height: 0; }
        
        .progress { height: 25px; background-color: #0f172a; border-radius: 12px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.3); }
        .progress-bar { background: linear-gradient(90deg, #fbbf24, #d97706); color: #000; font-weight: bold; line-height: 25px; }
        
        /* Table Styles */
        .table-dark { --bs-table-bg: #1e293b; border-color: #334155; }
        .table-hover tbody tr:hover { color: #fbbf24; }
        
        /* Form Inputs */
        .form-control, .form-select { background-color: #0f172a; border-color: #334155; color: #fff; }
        .form-control:focus, .form-select:focus { background-color: #0f172a; border-color: #fbbf24; color: #fff; box-shadow: none; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            <i class="fas fa-coins text-gold me-2"></i>WoW Gold RMT <span class="badge bg-gold text-dark" style="font-size:0.7rem; vertical-align:top;">Laravel</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <select id="filterBulan" class="form-select form-select-sm"></select>
            <select id="filterTahun" class="form-select form-select-sm"><option value="{{ date('Y') }}">{{ date('Y') }}</option></select>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                    <li><span class="dropdown-item-text small text-secondary">{{ Auth::user()->email }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item small" href="{{ route('profile.edit') }}"><i class="fas fa-user-edit me-2"></i>Profil</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="mb-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger small"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 d-flex flex-column justify-content-center h-100">
                <h6 class="text-secondary mb-1">Pendapatan (Bln)</h6>
                <h3 class="mb-0 fw-bold text-success" id="statIncome">Rp 0</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 h-100 border-info position-relative overflow-hidden">
                <div class="d-flex justify-content-between align-items-start z-1">
                    <h6 class="text-info-light mb-1">Stok Tersedia</h6>
                    <button class="btn btn-sm btn-info btn-add-stok fw-bold text-white" onclick="openEditStokModal()" title="Edit Stok Terakhir"><i class="fas fa-pen"></i></button>
                </div>
                <h3 class="mb-0 fw-bold text-white z-1" id="statStok">0 g</h3>
                <i class="fas fa-sack-dollar position-absolute text-info" style="opacity:0.1; font-size:4rem; right:-10px; bottom:-10px;"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 d-flex flex-column justify-content-center h-100">
                <h6 class="text-secondary mb-1">Terjual (Bln)</h6>
                <h3 class="mb-0 fw-bold text-gold" id="statSold">0 g</h3>
            </div>
        </div>

        <div class="col-md-3">
            <button class="btn btn-warning w-100 h-100 fw-bold fs-5 shadow" onclick="openModal()">
                <i class="fas fa-cart-arrow-down me-2"></i> Jual Gold
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-chart-line me-2"></i>Tren Penjualan</span>
                    <small class="text-secondary" style="font-weight:normal;">Grafik Harian</small>
                </div>
                <div class="card-body">
                    <canvas id="incomeChart" style="max-height: 250px;"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><span><i class="fas fa-history me-2"></i>Riwayat Transaksi</span></div>
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr class="text-secondary" style="font-size:0.9rem;">
                                <th>Tanggal</th>
                                <th>Gold</th>
                                <th>Rate</th>
                                <th class="text-end">Total (IDR)</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            
            <div class="card shadow-sm border-warning">
                <div class="card-header bg-transparent border-0 pb-0 d-flex justify-content-between">
                    <span><i class="fas fa-bullseye text-danger me-2"></i>Target Stok</span>
                    <i class="fas fa-cog text-secondary" style="cursor:pointer" onclick="$('#targetSettings').slideToggle()"></i>
                </div>
                <div class="card-body pt-2">
                    <div class="d-flex justify-content-between mb-1 fw-bold">
                        <span id="targetName" class="text-white">Loading...</span>
                        <span class="text-gold" id="targetPersen">0 / 0 g</span>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" style="width: 0%">0%</div>
                    </div>
                    
                    <div id="targetSettings" class="mt-3 p-2 bg-dark rounded border border-secondary" style="display:none;">
                        <small class="text-secondary d-block mb-1">Ubah Target:</small>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="targetLabelInput" placeholder="Nama Target">
                            <input type="number" class="form-control" id="targetValueInput" placeholder="Jumlah Gold">
                            <button class="btn btn-warning" onclick="updateTarget()">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-transparent border-0 pb-0">
                    <i class="fas fa-calculator text-info me-2"></i>Kalkulator AH (5%)
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fas fa-coins"></i></span>
                        <input type="number" class="form-control text-gold fw-bold" id="ahPrice" placeholder="Harga Jual Item" oninput="calcAH()">
                    </div>
                    <div class="d-flex justify-content-between bg-dark p-2 rounded border border-secondary">
                        <div class="text-center w-50 border-end border-secondary">
                            <small class="text-secondary" style="font-size:0.7rem;">POTONGAN</small><br>
                            <span class="text-danger fw-bold" id="ahCut">0</span>
                        </div>
                        <div class="text-center w-50">
                            <small class="text-secondary" style="font-size:0.7rem;">BERSIH</small><br>
                            <span class="text-success fw-bold fs-5" id="ahNet">0</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="trxModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-gold">Input Penjualan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formTrx">
                    <div class="mb-3">
                        <label class="text-secondary small">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="date" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="text-secondary small">Rate (Rp)</label>
                            <input type="number" class="form-control" id="rate" name="rate" placeholder="860" min="1" step="1" oninput="calcPreview()" required>
                        </div>
                        <div class="col-6">
                            <label class="text-secondary small">Jumlah Gold</label>
                            <input type="number" class="form-control" id="jumlah_gold" name="gold_amount" placeholder="1000" min="0.01" step="0.01" oninput="calcPreview()" required>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="tax_status" name="tax_status" checked onchange="calcPreview()">
                        <label class="form-check-label text-white small">Potong Fee Admin (1.5%)</label>
                    </div>
                    <div class="alert alert-success d-flex justify-content-between align-items-center mb-0">
                        <small>Estimasi Terima:</small>
                        <strong class="fs-5" id="previewTotal">Rp 0</strong>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-warning w-100 fw-bold" onclick="saveData()">SIMPAN & UPDATE STOK</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="farmingModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content bg-dark border-info">
            <div class="modal-header border-info">
                <h5 class="modal-title text-info">Edit Stok Terakhir</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="text-secondary mb-2 small">Stok terakhir Anda (g)</label>
                <input type="number" class="form-control text-info fw-bold mb-3" id="farmingAmount" placeholder="Ex: 2000" min="0" step="1">
                <button class="btn btn-info w-100 fw-bold text-white" onclick="saveEditStok()">SIMPAN</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // --- SETUP LARAVEL CSRF (PENTING!) ---
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let myChart;
    let currentStock = 0;
    var modalTrx, modalFarm;

    // Formatter
    const fmtRp = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
    const fmtNum = (n) => new Intl.NumberFormat('id-ID').format(n);

    $(document).ready(function() {
        // Isi Dropdown Bulan
        var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        var monthHtml = "", currentM = new Date().getMonth() + 1;
        for (var i = 0; i < months.length; i++) {
            monthHtml += '<option value="' + (i+1) + '"' + (i+1 == currentM ? ' selected' : '') + '>' + months[i] + '</option>';
        }
        $('#filterBulan').html(monthHtml);

        var elTrx = document.getElementById('trxModal');
        var elFarm = document.getElementById('farmingModal');
        if (elTrx && typeof bootstrap !== 'undefined') modalTrx = new bootstrap.Modal(elTrx);
        if (elFarm && typeof bootstrap !== 'undefined') modalFarm = new bootstrap.Modal(elFarm);
        loadData();
        loadTargetSettings();
    });

    $('#filterBulan, #filterTahun').change(loadData);

    // --- 1. CORE: LOAD DATA DARI LARAVEL ---
    function loadData() {
        let params = {
            month: $('#filterBulan').val(),
            year: $('#filterTahun').val()
        };

        // Show loading
        $('#tableBody').html('<tr><td colspan="5" class="text-center py-4"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>');

        $.get('/api/data', params, function(res) {
            try {
                if (!res || !res.stats) {
                    throw new Error('Format respons API tidak valid');
                }
                // Update Stats
                $('#statIncome').text(fmtRp(res.stats.income));
                $('#statSold').text(fmtNum(res.stats.gold_sold) + ' g');
                currentStock = res.stats.stok_gold;
                $('#statStok').text(fmtNum(currentStock) + ' g');
                updateProgressBar();

                // Update Tabel
                let html = '';
                if (!res.data || res.data.length === 0) {
                    html = '<tr><td colspan="5" class="text-center py-4 text-secondary">Belum ada data bulan ini.</td></tr>';
                } else {
                    res.data.forEach(d => {
                        html += '<tr><td>' + d.date + '</td><td class="fw-bold text-gold">' + fmtNum(d.gold_amount) + '</td><td>' + d.rate + '</td><td class="text-end fw-bold text-success">' + fmtRp(d.total_rupiah) + '</td><td class="text-center"><button class="btn btn-sm btn-outline-danger border-0" onclick="deleteData(' + d.id + ')"><i class="fas fa-trash"></i></button></td></tr>';
                    });
                }
                $('#tableBody').html(html);

                // Update Chart
                if (res.chart && res.chart.labels && res.chart.values) {
                    renderChart(res.chart.labels, res.chart.values);
                }

                // Filter tahun dinamis (dari API)
                let years = (res.years && res.years.length) ? res.years : [];
                let cy = new Date().getFullYear();
                if (!years.includes(cy)) years.push(cy);
                years = [...new Set(years)].sort((a,b) => b - a);
                let currentVal = $('#filterTahun').val() || cy;
                let yearOptions = years.map(y => '<option value="' + y + '"' + (y == currentVal ? ' selected' : '') + '>' + y + '</option>').join('');
                $('#filterTahun').html(yearOptions || '<option value="' + cy + '">' + cy + '</option>');
            } catch (err) {
                console.error('loadData error:', err);
                $('#tableBody').html('<tr><td colspan="5" class="text-center py-4 text-danger">Error: ' + (err.message || 'Memuat data gagal') + '</td></tr>');
                var cy = new Date().getFullYear();
                $('#filterTahun').html('<option value="' + cy + '">' + cy + '</option>');
            }
        }).fail(function(xhr) {
            var msg = 'Gagal memuat data.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            } else if (xhr.status === 401) {
                msg = 'Sesi habis. Silakan login lagi.';
                window.location.href = '/login';
                return;
            } else if (xhr.status === 0) {
                msg = 'Tidak dapat terhubung ke server. Periksa koneksi.';
            }
            $('#tableBody').html('<tr><td colspan="5" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>' + msg + '</td></tr>');
            var cy = new Date().getFullYear();
            $('#filterTahun').html('<option value="' + cy + '">' + cy + '</option>');
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: msg, background:'#1e293b', color:'#fff' });
            }
        });
    }

    // --- 2. TRANSAKSI (JUAL) ---
    function openModal() {
        if (!modalTrx && typeof bootstrap !== 'undefined') {
            var el = document.getElementById('trxModal');
            if (el) modalTrx = new bootstrap.Modal(el);
        }
        if (modalTrx) {
            $('#formTrx')[0].reset();
            $('#tanggal').val(new Date().toISOString().split('T')[0]);
            $('#previewTotal').text('Rp 0');
            modalTrx.show();
        }
    }

    function saveData() {
        // Validasi form
        let rate = parseFloat($('#rate').val());
        let goldAmount = parseFloat($('#jumlah_gold').val());
        let date = $('#tanggal').val();

        if (!date) {
            Swal.fire({ icon: 'warning', title: 'Tanggal harus diisi', background:'#1e293b', color:'#fff' });
            return;
        }
        if (!rate || rate < 1) {
            Swal.fire({ icon: 'warning', title: 'Rate harus diisi dan minimal 1', background:'#1e293b', color:'#fff' });
            return;
        }
        if (!goldAmount || goldAmount <= 0) {
            Swal.fire({ icon: 'warning', title: 'Jumlah Gold harus diisi dan minimal 0.01', background:'#1e293b', color:'#fff' });
            return;
        }

        // Cek stok tersedia
        if (goldAmount > currentStock) {
            Swal.fire({ 
                icon: 'error', 
                title: 'Stok tidak mencukupi', 
                text: `Stok tersedia: ${fmtNum(currentStock)} g. Anda ingin jual: ${fmtNum(goldAmount)} g`,
                background:'#1e293b', 
                color:'#fff' 
            });
            return;
        }

        let formData = $('#formTrx').serialize();
        let btnSave = $('#trxModal .btn-warning');
        let originalText = btnSave.html();
        btnSave.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');

        $.post('/api/save', formData, function(res) {
            modalTrx.hide();
            Swal.fire({icon: 'success', title: 'Berhasil', text: 'Transaksi berhasil disimpan', timer: 1500, showConfirmButton:false, background:'#1e293b', color:'#fff'});
            loadData();
        }).fail(function(xhr) {
            let msg = 'Gagal menyimpan data.';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.responseJSON.errors) {
                    let errors = Object.values(xhr.responseJSON.errors).flat().join(', ');
                    msg = 'Validasi gagal: ' + errors;
                }
            } else if (xhr.status === 0) {
                msg = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
            }
            Swal.fire({ icon: 'error', title: 'Error', text: msg, background:'#1e293b', color:'#fff' });
        }).always(function() {
            btnSave.prop('disabled', false).html(originalText);
        });
    }

    function deleteData(id) {
        Swal.fire({
            title: 'Hapus Transaksi?', 
            text: "Stok akan dikembalikan (Refund). Tindakan ini tidak dapat dibatalkan.", 
            icon: 'warning',
            showCancelButton: true, 
            confirmButtonColor: '#d33', 
            cancelButtonColor: '#3085d6', 
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            background:'#1e293b', 
            color:'#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background:'#1e293b', 
                    color:'#fff'
                });

                $.post(`/api/delete/${id}`, function(res) {
                    Swal.fire({icon: 'success', title: 'Berhasil', text: 'Transaksi berhasil dihapus', timer: 1000, showConfirmButton:false, background:'#1e293b', color:'#fff'});
                    loadData();
                }).fail(function(xhr) {
                    let msg = 'Gagal menghapus transaksi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.status === 0) {
                        msg = 'Tidak dapat terhubung ke server.';
                    }
                    Swal.fire({ icon: 'error', title: 'Error', text: msg, background:'#1e293b', color:'#fff' });
                });
            }
        });
    }

    // --- 3. EDIT STOK TERAKHIR ---
    function openEditStokModal() {
        if (!modalFarm && typeof bootstrap !== 'undefined') {
            var el = document.getElementById('farmingModal');
            if (el) modalFarm = new bootstrap.Modal(el);
        }
        if (modalFarm) {
            $('#farmingAmount').val(currentStock);
            modalFarm.show();
        }
    }
    
    function saveEditStok() {
        let value = parseFloat($('#farmingAmount').val());
        if (isNaN(value) || value < 0) {
            Swal.fire({ icon: 'warning', title: 'Masukkan stok terakhir (angka >= 0)', background:'#1e293b', color:'#fff' });
            return;
        }

        // Konfirmasi sebelum simpan
        Swal.fire({
            title: 'Konfirmasi Edit Stok',
            html: `Stok saat ini: <strong class="text-gold">${fmtNum(currentStock)} g</strong><br>Stok baru: <strong class="text-info">${fmtNum(value)} g</strong>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0ea5e9',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            background:'#1e293b', 
            color:'#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                let btnSave = $('#farmingModal .btn-info');
                let originalText = btnSave.html();
                btnSave.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');

                $.post('/api/stock', { value: value }, function(res) {
                    modalFarm.hide();
                    Swal.fire({ icon: 'success', title: 'Stok diperbarui', text: `Stok berhasil diubah menjadi ${fmtNum(value)} g`, timer: 1500, showConfirmButton: false, background:'#1e293b', color:'#fff' });
                    loadData();
                }).fail(function(xhr) {
                    let msg = 'Gagal menyimpan.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.status === 0) {
                        msg = 'Tidak dapat terhubung ke server.';
                    }
                    Swal.fire({ icon: 'error', title: 'Error', text: msg, background:'#1e293b', color:'#fff' });
                }).always(function() {
                    btnSave.prop('disabled', false).html(originalText);
                });
            }
        });
    }

    // --- 4. UTILS & CHART ---
    function calcPreview() {
        let r = parseFloat($('#rate').val()) || 0, g = parseFloat($('#jumlah_gold').val()) || 0;
        let t = r * g; if($('#tax_status').is(':checked')) t *= 0.985;
        $('#previewTotal').text(fmtRp(t));
    }

    function calcAH() {
        let p = parseFloat($('#ahPrice').val()) || 0;
        $('#ahCut').text(fmtNum(p * 0.05)); $('#ahNet').text(fmtNum(p * 0.95));
    }

    function updateTarget() {
        localStorage.setItem('wowTargetName', $('#targetLabelInput').val());
        localStorage.setItem('wowTargetVal', $('#targetValueInput').val());
        loadTargetSettings(); $('#targetSettings').slideUp();
    }

    function loadTargetSettings() {
        let tName = localStorage.getItem('wowTargetName') || "Target Mount";
        let tVal = localStorage.getItem('wowTargetVal') || 5000;
        $('#targetName').text(tName); $('#targetName').data('val', tVal);
        $('#targetLabelInput').val(tName); $('#targetValueInput').val(tVal);
        updateProgressBar();
    }

    function updateProgressBar() {
        let target = parseFloat($('#targetName').data('val')) || 1;
        let pct = (currentStock / target) * 100;
        if(pct > 100) pct = 100;
        $('#progressBar').css('width', pct+'%').text(pct.toFixed(1)+'%');
        $('#targetPersen').text(`${fmtNum(currentStock)} / ${fmtNum(target)} g`);
        
        let bar = $('#progressBar');
        bar.removeClass('bg-warning bg-success');
        if(pct >= 100) bar.addClass('bg-success'); else bar.addClass('bg-warning');
    }

    function renderChart(labels, data) {
        const ctx = document.getElementById('incomeChart').getContext('2d');
        if(myChart) myChart.destroy();
        let gradient = ctx.createLinearGradient(0,0,0,400); 
        gradient.addColorStop(0,'rgba(251,191,36,0.5)'); gradient.addColorStop(1,'rgba(0,0,0,0)');
        
        myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'IDR', 
                    data: data, 
                    borderColor: '#fbbf24', 
                    backgroundColor: gradient, 
                    borderWidth: 2, 
                    fill: true, 
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(30, 41, 59, 0.95)',
                        titleColor: '#fbbf24',
                        bodyColor: '#fff',
                        borderColor: '#334155',
                        borderWidth: 1,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + fmtNum(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        grid: { color: '#334155' }, 
                        ticks: { 
                            color: '#94a3b8',
                            callback: function(value) {
                                return 'Rp ' + fmtNum(value);
                            }
                        } 
                    },
                    x: { 
                        grid: { display: false }, 
                        ticks: { color: '#94a3b8' } 
                    }
                }
            }
        });
    }
</script>

</body>
</html>