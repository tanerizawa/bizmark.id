@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">
    @include('admin.dashboard.kpi-cards')
    @include('admin.dashboard.critical-focus')
    @include('admin.dashboard.financial-intelligence')
    @include('admin.dashboard.operational-monitoring')
</div>
@endsection
