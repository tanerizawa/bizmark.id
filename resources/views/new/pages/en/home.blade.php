@extends('new.layouts.app')

@section('title', 'Bizmark.ID — Indonesia Business Licensing Platform')
@section('description', 'Legal-tech platform for business licensing in Indonesia. Check your business license needs with AI, for free.')

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