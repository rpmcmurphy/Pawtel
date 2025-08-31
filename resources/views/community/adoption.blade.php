@extends('layouts.app')

@section('title', 'Cat Adoption - Pawtel')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h1 class="display-5 text-gradient mb-4">Cat Adoption</h1>
                <p class="lead">Find your perfect feline companion and give them a loving home</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('community.adoption') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="age" class="form-label">Age</label>
                                <select class="form-select" name="age" id="age">
                                    <option value="">All Ages</option>
                                    <option value="kitten" {{ request('age') == 'kitten' ? 'selected' : '' }}>Kitten (0-6
                                        months)</option>
                                    <option value="young" {{ request('age') == 'young' ? 'selected' : '' }}>Young (6-24
                                        months)</option>
                                    <option value="adult" {{ request('age') == 'adult' ? 'selected' : '' }}>Adult (2-7
                                        years)</option>
                                    <option value="senior" {{ request('age') == 'senior' ? 'selected' : '' }}>Senior (7+
                                        years)</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" name="gender" id="gender">
                                    <option value="">All</option>
                                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="breed" class="form-label">Breed</label>
                                <input type="text" class="form-control" id="breed" name="breed"
                                    value="{{ request('breed') }}" placeholder="e.g., Persian, Siamese">
                            </div>

                            <div class="col-md-2">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location"
                                    value="{{ request('location') }}" placeholder="City">
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route('community.adoption') }}" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if (!empty($adoptions['data']))
            <div class="row">
                @foreach ($adoptions['data'] as $adoption)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card adoption-card h-100">
                            @if ($adoption['images'] && count($adoption['images']) > 0)
                                <div id="carousel{{ $adoption['id'] }}" class="carousel slide">
                                    <div class="carousel-inner">
                                        @foreach ($adoption['images'] as $index => $image)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ $image['url'] }}" class="d-block w-100 adoption-image"
                                                    alt="{{ $adoption['cat_name'] }}"
                                                    style="height: 250px; object-fit: cover;">
                                            </div>
                                        @endforeach
                                    </div>
                                    @if (count($adoption['images']) > 1)
                                        <button class="carousel-control-prev" type="button"
                                            data-bs-target="#carousel{{ $adoption['id'] }}" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button"
                                            data-bs-target="#carousel{{ $adoption['id'] }}" data-bs-slide="next">
                                            <span class="carousel-control-next-icon"></span>
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="adoption-placeholder">
                                    <i class="fas fa-cat fa-3x text-muted"></i>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <div class="adoption-header mb-3">
                                    <h5 class="card-title">{{ $adoption['cat_name'] }}</h5>
                                    <div class="adoption-badges">
                                        <span class="badge bg-primary">{{ ucfirst($adoption['age_category']) }}</span>
                                        <span class="badge bg-info">{{ ucfirst($adoption['gender']) }}</span>
                                        @if ($adoption['spayed_neutered'])
                                            <span class="badge bg-success">Spayed/Neutered</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="adoption-details mb-3">
                                    @if ($adoption['breed'])
                                        <p class="mb-1"><strong>Breed:</strong> {{ $adoption['breed'] }}</p>
                                    @endif
                                    @if ($adoption['age_months'])
                                        <p class="mb-1"><strong>Age:</strong> {{ $adoption['age_months'] }} months</p>
                                    @endif
                                    <p class="mb-1">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $adoption['location'] ?? 'Location not specified' }}
                                    </p>
                                </div>

                                <p class="card-text text-muted">
                                    {{ Str::limit($adoption['description'], 100) }}
                                </p>

                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            Posted {{ date('M d, Y', strtotime($adoption['created_at'])) }}
                                        </small>
                                        <a href="{{ route('community.adoption.show', $adoption['slug']) }}"
                                            class="btn btn-primary btn-sm">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                <h4>No Cats Available for Adoption</h4>
                <p class="text-muted">Check back later for new adoption listings.</p>
            </div>
        @endif
    </div>
@endsection
