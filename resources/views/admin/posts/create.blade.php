@extends('layouts.admin')

@section('title', 'Create Post - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Create New Post</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.posts.index') }}">Posts</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Posts
        </a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.posts.store') }}" id="postForm">
        @csrf

        <div class="row">
            <div class="col-md-8">
                <!-- Main Content -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Post Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Content *</label>
                            <textarea class="form-control" name="content" rows="10" required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Featured Image URL</label>
                            <input type="url" class="form-control" name="featured_image"
                                value="{{ old('featured_image') }}" placeholder="https://example.com/image.jpg">
                            @error('featured_image')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Images (JSON Array)</label>
                            <textarea class="form-control" name="images" rows="3"
                                placeholder='["https://example.com/image1.jpg", "https://example.com/image2.jpg"]'>{{ old('images') ? json_encode(old('images'), JSON_PRETTY_PRINT) : '' }}</textarea>
                            <small class="text-muted">Enter image URLs as a JSON array, or leave empty</small>
                            @error('images')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Adoption Details (shown only for adoption type) -->
                <div class="card mb-4" id="adoption_fields" style="display: {{ old('type') == 'adoption' ? 'block' : 'none' }};">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Adoption Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cat Name *</label>
                                <input type="text" class="form-control" name="adoption[cat_name]" id="adoption_cat_name"
                                    value="{{ old('adoption.cat_name') }}">
                                @error('adoption.cat_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Age</label>
                                <input type="text" class="form-control" name="adoption[age]"
                                    value="{{ old('adoption.age') }}" placeholder="e.g., 2 years old">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender</label>
                                <select class="form-select" name="adoption[gender]">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('adoption.gender') == 'male' ? 'selected' : '' }}>Male
                                    </option>
                                    <option value="female" {{ old('adoption.gender') == 'female' ? 'selected' : '' }}>
                                        Female</option>
                                    <option value="unknown" {{ old('adoption.gender') == 'unknown' ? 'selected' : '' }}>
                                        Unknown</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Breed</label>
                                <input type="text" class="form-control" name="adoption[breed]"
                                    value="{{ old('adoption.breed') }}" placeholder="e.g., Persian">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Health Status</label>
                                <textarea class="form-control" name="adoption[health_status]" rows="3"
                                    placeholder="Vaccinated, spayed/neutered, etc.">{{ old('adoption.health_status') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Adoption Fee (৳)</label>
                                <input type="number" class="form-control" name="adoption[adoption_fee]"
                                    value="{{ old('adoption.adoption_fee') }}" min="0" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Adoption Status</label>
                                <select class="form-select" name="adoption[status]">
                                    <option value="available" {{ old('adoption.status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="pending" {{ old('adoption.status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="adopted" {{ old('adoption.status') == 'adopted' ? 'selected' : '' }}>Adopted</option>
                                </select>
                                @error('adoption.status')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Contact Info (JSON)</label>
                                <textarea class="form-control" name="adoption[contact_info]" rows="3"
                                    placeholder='{"phone": "+8801234567890", "email": "contact@example.com"}'>{{ old('adoption.contact_info') ? json_encode(old('adoption.contact_info'), JSON_PRETTY_PRINT) : '' }}</textarea>
                                <small class="text-muted">Enter contact information as JSON object, or leave empty</small>
                                @error('adoption.contact_info')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Post Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Post Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Post Type *</label>
                            <select class="form-select" name="type" id="post_type" required>
                                <option value="">Select Type</option>
                                <option value="adoption" {{ old('type') == 'adoption' ? 'selected' : '' }}>Adoption
                                </option>
                                <option value="story" {{ old('type') == 'story' ? 'selected' : '' }}>Story</option>
                                <option value="news" {{ old('type') == 'news' ? 'selected' : '' }}>News</option>
                                <option value="job" {{ old('type') == 'job' ? 'selected' : '' }}>Job</option>
                            </select>
                            @error('type')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status *</label>
                            <select class="form-select" name="status" required>
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft
                                </option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published
                                </option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived
                                </option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Post
                            </button>
                            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        (function() {
            // Use vanilla JS for better compatibility
            function toggleAdoptionFields() {
                var postType = document.getElementById('post_type');
                var adoptionFields = document.getElementById('adoption_fields');
                var catNameField = document.getElementById('adoption_cat_name');
                
                if (!postType || !adoptionFields || !catNameField) {
                    return;
                }
                
                if (postType.value === 'adoption') {
                    adoptionFields.style.display = 'block';
                    catNameField.setAttribute('required', 'required');
                } else {
                    adoptionFields.style.display = 'none';
                    catNameField.removeAttribute('required');
                }
            }
            
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    var postType = document.getElementById('post_type');
                    if (postType) {
                        postType.addEventListener('change', toggleAdoptionFields);
                        toggleAdoptionFields(); // Initial call
                    }
                });
            } else {
                // DOM is already ready
                var postType = document.getElementById('post_type');
                if (postType) {
                    postType.addEventListener('change', toggleAdoptionFields);
                    toggleAdoptionFields(); // Initial call
                }
            }
            
            // Also use jQuery if available (for compatibility)
            if (typeof jQuery !== 'undefined') {
                jQuery(document).ready(function($) {
                    console.log('jQuery is ready');
                    $('#post_type').on('change', toggleAdoptionFields);
                    toggleAdoptionFields();
                });
            }
        })();
    </script>
@endpush
