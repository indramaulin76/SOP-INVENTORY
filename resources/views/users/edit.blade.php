@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>✏️ Edit User</h2>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="required">Nama</label>
                        <input type="text" id="name" name="name" class="input" value="{{ old('name', $user->name) }}" placeholder="Nama lengkap" required>
                        @error('name') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-group">
                        <label for="email" class="required">Email</label>
                        <input type="email" id="email" name="email" class="input" value="{{ old('email', $user->email) }}" placeholder="email@example.com" required>
                        @error('email') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" id="password" name="password" class="input" placeholder="Minimal 6 karakter">
                        @error('password') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="input" placeholder="Ulangi password">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="role" class="required">Role</label>
                        <select id="role" name="role" class="input-select" required>
                            @if(auth()->user()->role === 'superadmin')
                                <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Karyawan</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Pimpinan</option>
                            @else
                                <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Karyawan</option>
                            @endif
                        </select>
                        @error('role') <small style="color: #dc3545;">{{ $message }}</small> @enderror
                        @if(auth()->user()->role === 'admin')
                            <small style="color: #666; display: block; margin-top: 5px;">Admin hanya dapat mengubah user dengan role Karyawan</small>
                        @endif
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">← Kembali</a>
                    <button type="submit" class="btn btn-primary">Update User 💾</button>
                </div>
            </form>
        </div>
    </div>
@endsection

