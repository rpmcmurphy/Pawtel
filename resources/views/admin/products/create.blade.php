@extends('layouts.admin')

@section('title', 'Add Product - Admin')
@section('page-title', 'Add Product')

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row
