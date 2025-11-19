@extends('layouts.app')
@section('title', 'Manajemen User')

@section('content')
    <div class="card">
        <div class="card-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>👥 Manajemen User</h2>
                <a href="{{ route('users.create') }}" class="btn btn-primary">➕ Tambah User</a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role === 'superadmin')
                                        <span style="background: #DC3545; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Pimpinan</span>
                                    @elseif($user->role === 'admin')
                                        <span style="background: #FFC107; color: #333; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Admin</span>
                                    @else
                                        <span style="background: #28A745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Karyawan</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-secondary btn-sm">✏️ Edit</a>
                                        @if(auth()->user()->id !== $user->id)
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">🗑️ Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #999; padding: 40px;">Tidak ada data user tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .table-container {
            background: var(--white);
            border-radius: 8px;
            overflow: auto;
            margin-top: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: var(--white);
        }

        .table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            border-bottom: 2px solid var(--gold-dark);
        }

        .table td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
            font-size: 13px;
        }

        .table tbody tr:hover {
            background-color: var(--cream);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
    </style>
@endsection

