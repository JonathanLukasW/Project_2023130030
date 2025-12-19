@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>Ganti Password</h3>
</div>
<section class="section">
    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                
                {{-- PASSWORD LAMA --}}
                <div class="form-group mb-3">
                    <label>Password Saat Ini</label>
                    <div class="input-group">
                        <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('current_password', 'icon_current')">
                            <i class="bi bi-eye-slash" id="icon_current"></i>
                        </button>
                        @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- PASSWORD BARU --}}
                <div class="form-group mb-3">
                    <label>Password Baru</label>
                    <div class="input-group">
                        <input type="password" id="new_password" name="new_password" class="form-control @error('new_password') is-invalid @enderror">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('new_password', 'icon_new')">
                            <i class="bi bi-eye-slash" id="icon_new"></i>
                        </button>
                        @error('new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- KONFIRMASI PASSWORD --}}
                <div class="form-group mb-3">
                    <label>Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <input type="password" id="confirm_password" name="new_password_confirmation" class="form-control">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('confirm_password', 'icon_confirm')">
                            <i class="bi bi-eye-slash" id="icon_confirm"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>
    </div>
</section>

{{-- SCRIPT TOGGLE BOOTSTRAP --}}
<script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        }
    }
</script>
@endsection