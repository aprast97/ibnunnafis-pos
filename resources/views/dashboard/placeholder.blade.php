@extends('layouts.app')

@section('title', $module)
@section('page-header', $module)
@section('page-subtitle', 'Modul ' . $module . ' Kantin Ibnunnafis')

@section('content')
<div class="card border-0 shadow-sm p-5 text-center rounded-3 bg-white">
    <div class="mb-3">
        <iconify-icon icon="lucide:construction" class="text-primary fs-1" style="font-size: 3rem; color: var(--primary);"></iconify-icon>
    </div>
    <h3 class="fw-bold text-slate-800">Modul {{ $module }}</h3>
    <p class="text-muted text-sm mb-4">Halaman ini terhubung secara terstruktur dan siap diimplementasikan pada fase berikutnya.</p>
    <div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <iconify-icon icon="lucide:arrow-left" class="me-1"></iconify-icon> Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection