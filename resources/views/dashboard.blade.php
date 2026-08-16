@extends('layouts.dashboard')
@section('title', 'Dashboard')

@section('content')
<div class="breadcrumb">
  <a href="{{ route('admin.dashboard') }}">Dashboard</a>
  <span class="sep">/</span>
  <span>Overview</span>
</div>
<h1 class="page-title">Welcome back, {{ Auth::user()->name }}</h1>

<div class="deal-meta">
  <span class="created">Today <span>{{ now()->format('d M, Y') }}</span></span>
</div>

<div class="summary-cards">
  <div class="summary-card">
    <div class="label"><i class="fa-regular fa-clock"></i> Pending Tasks</div>
    <div class="value">0</div>
  </div>
  <div class="summary-card">
    <div class="label"><i class="fa-solid fa-check"></i> Completed</div>
    <div class="value">0</div>
  </div>
  <div class="summary-card">
    <div class="label"><i class="fa-solid fa-bell"></i> Notifications</div>
    <div class="value">0</div>
  </div>
</div>

<div class="empty-state">No recent activity</div>
@endsection
