@extends('layouts.app', ['wide' => false, 'title' => __('Create temporary route')])
@section('header-title', $headerTitle)

@section('content')
    @include('common.forms.createtemporaryroute'])
@endsection
