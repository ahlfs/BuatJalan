@extends('layouts.dashboard')

@section('title', $project->title)
@section('page_title', $project->title)

@section('content')
    <livewire:project-detail :project="$project" />
@endsection
