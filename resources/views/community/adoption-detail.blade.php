@extends('layouts.app')

@section('title', ($adoption['adoption_details']['cat_name'] ?? 'Adoption') . ' - Adoption')

@section('content')
    @php
        $adoptionDetail = $adoption['adoption_details'] ?? null;
        $postId = $adoption['id'] ?? null;
    @endphp
    
    @if (!$adoptionDetail)
        <div class="container py-5">
            <div class="alert alert-danger">
                Adoption listing not found.
            </div>
        </div>
    @else
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-8">
                    <div class="adoption-detail-card">
                        <!-- Image Gallery -->
                        @if (!empty($adoption['featured_image']))
                            <div class="adoption-gallery mb-4">
                                <img src="{{ $adoption['featured_image'] }}" class="img-fluid rounded-3"
                                    alt="{{ $adoptionDetail['cat_name'] }}" style="height: 400px; object-fit: cover; width: 100%;">
                            </div>
                        @elseif (!empty($adoption['images']) && count($adoption['images']) > 0)
                            <div class="adoption-gallery mb-4">
                                <div id="adoptionCarousel" class="carousel slide">
                                    <div class="carousel-inner">
                                        @foreach ($adoption['images'] as $index => $image)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ is_string($image) ? $image : ($image['url'] ?? $image) }}" 
                                                    class="d-block w-100 rounded-3"
                                                    alt="{{ $adoptionDetail['cat_name'] }}" 
                                                    style="height: 400px; object-fit: cover;">
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
                                    <h1 class="adoption-title">{{ $adoptionDetail['cat_name'] }}</h1>
                                    <div class="adoption-badges mt-2">
                                        @if ($adoptionDetail['age'])
                                            <span class="badge bg-primary">{{ ucfirst($adoptionDetail['age']) }}</span>
                                        @endif
                                        @if ($adoptionDetail['gender'])
                                            <span class="badge bg-info">{{ ucfirst($adoptionDetail['gender']) }}</span>
                                        @endif
                                        @if ($adoptionDetail['status'] === 'available')
                                            <span class="badge bg-success">Available</span>
                                        @elseif ($adoptionDetail['status'] === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">Adopted</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-end">
                                    <small class="text-muted">
                                        Posted {{ \Carbon\Carbon::parse($adoption['published_at'] ?? $adoption['created_at'] ?? now())->format('F j, Y') }}
                                    </small>
                                </div>
                            </div>

                            <!-- Quick Facts -->
                            <div class="quick-facts mb-4">
                                <div class="row g-3">
                                    @if ($adoptionDetail['breed'])
                                        <div class="col-md-6">
                                            <div class="fact-item">
                                                <strong>Breed:</strong> {{ $adoptionDetail['breed'] }}
                                            </div>
                                        </div>
                                    @endif

                                    @if ($adoptionDetail['age'])
                                        <div class="col-md-6">
                                            <div class="fact-item">
                                                <strong>Age:</strong> {{ $adoptionDetail['age'] }}
                                            </div>
                                        </div>
                                    @endif

                                    @if ($adoptionDetail['adoption_fee'])
                                        <div class="col-md-6">
                                            <div class="fact-item">
                                                <strong>Adoption Fee:</strong> ৳{{ number_format($adoptionDetail['adoption_fee'], 2) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="adoption-description mb-4">
                                <h4>About {{ $adoptionDetail['cat_name'] }}</h4>
                                <div>{!! $adoption['content'] ?? 'No description available.' !!}</div>
                            </div>

                            <!-- Health Status -->
                            @if ($adoptionDetail['health_status'])
                                <div class="medical-info mb-4">
                                    <h4>Health Information</h4>
                                    <p>{{ $adoptionDetail['health_status'] }}</p>
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
                                <h5 class="mb-0">Interested in {{ $adoptionDetail['cat_name'] }}?</h5>
                            </div>
                            <div class="card-body">
                                @if ($adoptionDetail['status'] !== 'available')
                                    <div class="alert alert-warning">
                                        This cat is {{ $adoptionDetail['status'] }}.
                                    </div>
                                @else
                                    @auth
                                        @if (!empty($adoptionDetail['contact_info']))
                                            <div class="mb-3">
                                                <h6>Contact Information:</h6>
                                                @if (is_array($adoptionDetail['contact_info']))
                                                    @if (!empty($adoptionDetail['contact_info']['phone']))
                                                        <p class="mb-1">
                                                            <i class="fas fa-phone me-2"></i>
                                                            {{ $adoptionDetail['contact_info']['phone'] }}
                                                        </p>
                                                    @endif
                                                    @if (!empty($adoptionDetail['contact_info']['email']))
                                                        <p class="mb-1">
                                                            <i class="fas fa-envelope me-2"></i>
                                                            <a href="mailto:{{ $adoptionDetail['contact_info']['email'] }}">
                                                                {{ $adoptionDetail['contact_info']['email'] }}
                                                            </a>
                                                        </p>
                                                    @endif
                                                @else
                                                    <p>{{ $adoptionDetail['contact_info'] }}</p>
                                                @endif
                                            </div>
                                        @endif

                                        <form method="POST" action="{{ route('community.adoption.interest', $postId) }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="message" class="form-label">Message to Owner (Optional)</label>
                                                <textarea class="form-control" id="message" name="message" rows="3"
                                                    placeholder="Tell the owner why you'd be a great match for {{ $adoptionDetail['cat_name'] }}..."></textarea>
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
                                            Please login to express interest in adopting {{ $adoptionDetail['cat_name'] }}.
                                        </p>
                                        <a href="{{ route('auth.login') }}" class="btn btn-primary w-100">
                                            Login to Contact
                                        </a>
                                    @endauth
                                @endif
                            </div>
                        </div>

                        <!-- Owner Info (if available) -->
                        @if (!empty($adoption['user']))
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Posted By</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            {{ strtoupper(substr($adoption['user']['name'] ?? 'A', 0, 2)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $adoption['user']['name'] ?? 'Anonymous' }}</h6>
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
