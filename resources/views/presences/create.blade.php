@extends('layouts.dashboard')

@section('content')
<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <h3>Form Absensi</h3>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                @if(isset($isCheckIn) && $isCheckIn)
                    Absen Masuk (Check In)
                @else
                    Absen Pulang (Check Out)
                @endif
            </h5>
        </div>
        <div class="card-body">

            {{-- CEK IZIN: Admin vs Karyawan --}}
            @can('presence_view_all')
                
                {{-- === TAMPILAN ADMIN (INPUT MANUAL SIMPLE) === --}}
                <form action="{{ route('presences.store')}}" method="POST">
                    @csrf
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Mode Admin:</strong> Waktu Check-in akan otomatis diset sesuai <strong>Jam Input Sekarang</strong> jika status "Hadir".
                    </div>

                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Pilih Karyawan</label>
                        <select name="employee_id" class="form-select select2">
                            <option value="">-- Cari Karyawan --</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->fullname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="date" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status Kehadiran</label>
                        <select name="status" id="status" class="form-select">
                            <option value="present">Hadir (Present)</option>
                            <option value="absent">Alfa (Absent)</option>
                            <option value="leave">Cuti/Izin (Leave)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                    <a href="{{ route('presences.index') }}" class="btn btn-secondary">Kembali</a>
                </form>

            @else

                {{-- === TAMPILAN KARYAWAN (WEBCAM + GPS) === --}}
                
                @if(isset($todayPresence) && $todayPresence->check_out)
                    <div class="alert alert-success text-center">
                        <h4><i class="bi bi-check-circle-fill"></i> Anda sudah menyelesaikan absensi hari ini.</h4>
                        <p>Sampai jumpa besok!</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">Kembali ke Dashboard</a>
                    </div>
                @else

                    <form action="{{ route('presences.store') }}" method="POST" id="presence-form">
                        @csrf
                        
                        {{-- Hidden Input --}}
                        <input type="hidden" name="type" value="{{ $isCheckIn ? 'in' : 'out' }}">
                        <input type="hidden" name="photo" id="photo">
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">

                        @if($isCheckIn)
                            {{-- TAMPILAN CHECK IN (Ada Kamera) --}}
                            <div class="row">
                                <div class="col-md-6 mb-3 text-center">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <h6>1. Foto Selfie</h6>
                                            <div id="my_camera" class="mx-auto mb-2" style="background:#eee"></div>
                                            <div id="results" style="display:none" class="mb-2"></div>
                                            
                                            <button type="button" class="btn btn-info btn-sm mt-2" onClick="take_snapshot()">
                                                <i class="bi bi-camera"></i> Ambil Foto
                                            </button>
                                            <button type="button" class="btn btn-warning btn-sm mt-2" onClick="reset_camera()" id="btn-reset" style="display:none">
                                                <i class="bi bi-arrow-counterclockwise"></i> Foto Ulang
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <h6>2. Lokasi</h6>
                                            <div id="location-status" class="alert alert-warning py-1 px-2 text-sm">Mencari lokasi...</div>
                                            {{-- Peta --}}
                                            <iframe id="map-frame" width="100%" height="250" style="border:0" allowfullscreen loading="lazy"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid mt-3">
                                <button type="button" class="btn btn-primary btn-lg" id="btn-submit" onClick="submitCheckIn()" disabled>
                                    <i class="bi bi-box-arrow-in-right"></i> CHECK IN SEKARANG
                                </button>
                            </div>

                        @else
                            {{-- TAMPILAN CHECK OUT (Hanya Tombol) --}}
                            <div class="text-center py-5">
                                <h3>Halo, {{ Auth::user()->name }}</h3>
                                <p>Waktu Check-In Anda: <strong>{{ \Carbon\Carbon::parse($todayPresence->check_in)->format('H:i') }}</strong></p>
                                <p class="text-muted mb-4">Apakah Anda ingin mengakhiri jam kerja hari ini?</p>
                                
                                <button type="submit" class="btn btn-danger btn-lg px-5">
                                    <i class="bi bi-box-arrow-left"></i> CHECK OUT SEKARANG
                                </button>
                            </div>
                        @endif
                    </form>

                @endif

                {{-- SCRIPT JAVASCRIPT FIXED URL --}}
                @if($isCheckIn && !(isset($todayPresence) && $todayPresence->check_out))
                <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
                <script>
                    Webcam.set({
                        width: 320,
                        height: 240,
                        image_format: 'jpeg',
                        jpeg_quality: 90
                    });
                    
                    setTimeout(() => { 
                        Webcam.attach('#my_camera'); 
                    }, 500);

                    function take_snapshot() {
                        Webcam.snap(function(data_uri) {
                            document.getElementById('my_camera').style.display = 'none';
                            document.getElementById('results').style.display = 'block';
                            document.getElementById('results').innerHTML = '<img src="'+data_uri+'" class="img-fluid rounded"/>';
                            document.getElementById('photo').value = data_uri;
                            document.getElementById('btn-reset').style.display = 'inline-block';
                            checkReady();
                        });
                    }

                    function reset_camera() {
                        document.getElementById('photo').value = '';
                        document.getElementById('results').style.display = 'none';
                        document.getElementById('my_camera').style.display = 'block';
                        document.getElementById('btn-reset').style.display = 'none';
                        checkReady(); 
                    }

                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                const lat = position.coords.latitude;
                                const long = position.coords.longitude;
                                
                                document.getElementById('latitude').value = lat;
                                document.getElementById('longitude').value = long;
                                
                                const locStatus = document.getElementById('location-status');
                                locStatus.className = 'alert alert-success py-1 px-2 text-sm';
                                locStatus.innerHTML = `Lokasi: ${lat.toFixed(5)}, ${long.toFixed(5)}`;
                                const mapUrl = `https://maps.google.com/maps?q=$${lat},${long}&hl=id&z=16&output=embed`;
                                document.getElementById('map-frame').src = mapUrl;
                                
                                checkReady();
                            },
                            function(error) {
                                const locStatus = document.getElementById('location-status');
                                locStatus.className = 'alert alert-danger py-1 px-2 text-sm';
                                locStatus.innerHTML = "Gagal akses lokasi: " + error.message;
                            },
                            {
                                enableHighAccuracy: true
                            }
                        );
                    } else {
                        document.getElementById('location-status').innerHTML = "Browser ini tidak mendukung Geolocation.";
                    }

                    function checkReady() {
                        const photoVal = document.getElementById('photo').value;
                        const latVal = document.getElementById('latitude').value;

                        if(photoVal && latVal) {
                            document.getElementById('btn-submit').removeAttribute('disabled');
                        } else {
                            document.getElementById('btn-submit').setAttribute('disabled', 'disabled');
                        }
                    }

                    function submitCheckIn() {
                        if(!document.getElementById('photo').value) { alert('Foto wajib diambil!'); return; }
                        if(!document.getElementById('latitude').value) { alert('Lokasi belum ditemukan!'); return; }
                        
                        const btn = document.getElementById('btn-submit');
                        btn.innerHTML = 'Mengirim Absensi...';
                        btn.disabled = true;

                        document.getElementById('presence-form').submit();
                    }
                </script>
                @endif

            @endcan
        </div>
    </div>
</section>
@endsection