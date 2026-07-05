@extends('layouts.dashboard', ['activeId' => 'home'])

@section('title', 'Buat Jalan Baru')
@section('page_title', 'Buat Jalan Baru')

@section('content')
    <livewire:create-project />
@endsection
