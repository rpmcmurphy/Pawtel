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
                    </div>
                </div>

                <!-- Adoption Details (shown only for adoption type) -->
                <div class="card mb-4" id="adoption_fields" style="display: none;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Adoption Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cat Name *</label>
                                <input type="text" class="form-control" name="adoption[cat_name]"
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

@section('scripts')
    <script>
        $(document).ready(function() {
            // Show/hide adoption fields based on post type
            $('#post_type').change(function() {
                if ($(this).val() === 'adoption') {
                    $('#adoption_fields').show();
                } else {
                    $('#adoption_fields').hide();
                }
            });

            // Trigger on page load
            $('#post_type').trigger('change');
        });
    </script>
@endsection
