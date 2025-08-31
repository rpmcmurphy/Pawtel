@extends('layouts.app')

@section('title', $adoption['cat_name'] . ' - Adoption')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="adoption-detail-card">
                    <!-- Image Gallery -->
                    @if ($adoption['images'] && count($adoption['images']) > 0)
                        <div class="adoption-gallery mb-4">
                            <div id="adoptionCarousel" class="carousel slide">
                                <div class="carousel-inner">
                                    @foreach ($adoption['images'] as $index => $image)
                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                            <img src="{{ $image['url'] }}" class="d-block w-100 rounded-3"
                                                alt="{{ $adoption['cat_name'] }}" style="height: 400px; object-fit: cover;">
                                        </div>
                                    @endforeach
                                </div>
                                @if (count($adoption['images']) > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#adoptionCarousel"
                                        data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#adoptionCarousel"
                                        data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                    <div class="carousel-indicators">
                                        @foreach ($adoption['images'] as $index => $image)
                                            <button type="button" data-bs-target="#adoptionCarousel"
                                                data-bs-slide-to="{{ $index }}"
                                                class="{{ $index === 0 ? 'active' : '' }}"></button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Cat Details -->
                    <div class="adoption-info">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h1 class="adoption-title">{{ $adoption['cat_name'] }}</h1>
                                <div class="adoption-badges mt-2">
                                    <span class="badge bg-primary">{{ ucfirst($adoption['age_category']) }}</span>
                                    <span class="badge bg-info">{{ ucfirst($adoption['gender']) }}</span>
                                    @if ($adoption['spayed_neutered'])
                                        <span class="badge bg-success">Spayed/Neutered</span>
                                    @endif
                                    @if ($adoption['vaccinated'])
                                        <span class="badge bg-success">Vaccinated</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-end">
                                <p class="mb-0">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $adoption['location'] ?? 'Location not specified' }}
                                </p>
                                <small class="text-muted">
                                    Posted {{ date('F j, Y', strtotime($adoption['created_at'])) }}
                                </small>
                            </div>
                        </div>

                        <!-- Quick Facts -->
                        <div class="quick-facts mb-4">
                            <div class="row g-3">
                                @if ($adoption['breed'])
                                    <div class="col-md-6">
                                        <div class="fact-item">
                                            <strong>Breed:</strong> {{ $adoption['breed'] }}
                                        </div>
                                    </div>
                                @endif

                                @if ($adoption['age_months'])
                                    <div class="col-md-6">
                                        <div class="fact-item">
                                            <strong>Age:</strong> {{ $adoption['age_months'] }} months
                                        </div>
                                    </div>
                                @endif

                                @if ($adoption['weight'])
                                    <div class="col-md-6">
                                        <div class="fact-item">
                                            <strong>Weight:</strong> {{ $adoption['weight'] }}kg
                                        </div>
                                    </div>
                                @endif

                                @if ($adoption['color'])
                                    <div class="col-md-6">
                                        <div class="fact-item">
                                            <strong>Color:</strong> {{ $adoption['color'] }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="adoption-description mb-4">
                            <h4>About {{ $adoption['cat_name'] }}</h4>
                            <p>{{ $adoption['description'] }}</p>
                        </div>

                        <!-- Medical Info -->
                        @if ($adoption['medical_notes'])
                            <div class="medical-info mb-4">
                                <h4>Medical Information</h4>
                                <p>{{ $adoption['medical_notes'] }}</p>
                            </div>
                        @endif

                        <!-- Special Needs -->
                        @if ($adoption['special_needs'])
                            <div class="special-needs mb-4">
                                <h4>Special Care Requirements</h4>
                                <p>{{ $adoption['special_needs'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="adoption-sidebar">
                    <!-- Contact Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Interested in {{ $adoption['cat_name'] }}?</h5>
                        </div>
                        <div class="card-body">
                            @auth
                                <form method="POST" action="{{ route('community.adoption.interest', $adoption['id']) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Message to Owner (Optional)</label>
                                        <textarea class="form-control" id="message" name="message" rows="3"
                                            placeholder="Tell the owner why you'd be a great match for {{ $adoption['cat_name'] }}..."></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-heart me-2"></i>
                                        Express Interest
                                    </button>
                                </form>

                                <div class="mt-3 text-center">
                                    <small class="text-muted">
                                        The owner will be notified of your interest and contact details.
                                    </small>
                                </div>
                            @else
                                <p class="text-center mb-3">
                                    Please login to express interest in adopting {{ $adoption['cat_name'] }}.
                                </p>
                                <a href="{{ route('auth.login') }}" class="btn btn-primary w-100">
                                    Login to Contact
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Owner Info (if available) -->
                    @if ($adoption['owner'])
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Owner Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        {{ strtoupper(substr($adoption['owner']['name'], 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $adoption['owner']['name'] }}</h6>
                                        <small class="text-muted">
                                            Member since {{ date('Y', strtotime($adoption['owner']['created_at'])) }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Adoption Tips -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Adoption Tips</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Ask about the cat's personality and habits
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Prepare your home before adoption
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Schedule a vet visit soon after adoption
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Be patient during the adjustment period
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
