@extends('layouts.dashboard')
@section('title', 'Kelola Jurusan')
@section('content')

<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <span>Kelola Jurusan</span>
</div>

<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px;">
  <div>
    <h1 class="page-title" style="margin-bottom:2px;">Daftar Jurusan</h1>
    <p style="font-size:13px;color:var(--tx2);">Kelola jurusan per sekolah dan per jenjang, beserta kuota per jalur.</p>
  </div>
</div>

@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

@include('admin.partials.majors-index')

@endsection
