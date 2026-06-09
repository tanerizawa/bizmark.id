@extends('new.layouts.app')

@section('title', 'Bizmark.ID — Perizinan Usaha Indonesia')
@section('description', 'Platform legal-tech perizinan usaha di Indonesia. Cek kebutuhan izin usaha Anda dengan AI, gratis.')

@section('content')
    @include('new.sections.hero')
    @include('new.sections.problem-solution')
    @include('new.sections.tools')
    @include('new.sections.services')
    @include('new.sections.business-types')
    @include('new.sections.process')
    @include('new.sections.testimonials')
    @include('new.sections.blog')
    @include('new.sections.newsletter')
    @include('new.sections.faq')
    @include('new.sections.cta')
@endsection
