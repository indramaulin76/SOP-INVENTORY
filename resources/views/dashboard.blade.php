@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="welcome-section card">
        <div class="card-header">
            <h2>🎉 Selamat Datang di Dashboard</h2>
        </div>
        <div class="card-body">
            <p>
                Anda telah berhasil login ke sistem Sae Bakery. Dashboard ini adalah halaman utama Anda 
                untuk mengakses semua fitur dan layanan. Mulai dengan menjelajahi menu di sebelah kiri untuk memulai.
            </p>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .welcome-section {
            margin-bottom: 30px;
        }

        .welcome-section .card-body {
            font-size: 16px;
            line-height: 1.8;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
        }

        .empty-state p {
            margin: 10px 0;
        }

        .text-muted {
            color: var(--text-light);
            font-size: 14px;
        }
    </style>
@endsection
