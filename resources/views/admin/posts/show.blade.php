@extends('layouts.admin')

@section('title', 'View Post - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">{{ $post['title'] }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.posts.index') }}">Posts</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.posts.edit', $post['id']) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Post
            </a>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Posts
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Post Content -->
            <div class="card mb-4">
                <div class="card-body">
                    @if (!empty($post['featured_image']))
                        <img src="{{ $post['featured_image'] }}" class="img-fluid rounded mb-3" alt="Featured Image">
                    @endif

                    <div class="post-content">
                        {!! nl2br(e($post['content'])) !!}
                    </div>

                    @if (!empty($post['images']))
                        <div class="mt-4">
                            <h6>Additional Images:</h6>
                            <div class="row">
                                @foreach ($post['images'] as $image)
                                    <div class="col-md-6 mb-2">
                                        <img src="{{ $image }}" class="img-fluid rounded" alt="Post Image">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Adoption Details -->
            @if ($post['type'] === 'adoption' && !empty($post['adoption_detail']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-heart text-success"></i> Adoption Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Cat Name:</strong> {{ $post['adoption_detail']['cat_name'] }}<br>
                                <strong>Age:</strong> {{ $post['adoption_detail']['age'] ?? 'Not specified' }}<br>
                                <strong>Gender:</strong> {{ ucfirst($post['adoption_detail']['gender'] ?? 'Unknown') }}
                            </div>
                            <div class="col-md-6">
                                <strong>Breed:</strong> {{ $post['adoption_detail']['breed'] ?? 'Mixed' }}<br>
                                <strong>Adoption Fee:</strong>
                                {{ $post['adoption_detail']['adoption_fee'] ? '৳' . number_format($post['adoption_detail']['adoption_fee'], 2) : 'Free' }}<br>
                                <strong>Status:</strong>
                                <span
                                    class="badge bg-{{ $post['adoption_detail']['status'] === 'available' ? 'success' : 'warning' }}">
                                    {{ ucfirst($post['adoption_detail']['status']) }}
                                </span>
                            </div>
                        </div>

                        @if (!empty($post['adoption_detail']['health_status']))
                            <div class="mt-3">
                                <strong>Health Status:</strong><br>
                                {{ $post['adoption_detail']['health_status'] }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Comments -->
            @if (!empty($post['comments']))
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-comments"></i> Comments ({{ count($post['comments']) }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach ($post['comments'] as $comment)
                            <div class="d-flex mb-3 {{ $comment['status'] !== 'approved' ? 'bg-light p-3 rounded' : '' }}">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $comment['user']['name'] ?? 'Anonymous' }}</strong>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($comment['created_at'])->format('M d, Y h:i A') }}
                                            </small>
                                            <span
                                                class="badge bg-{{ $comment['status'] === 'approved' ? 'success' : ($comment['status'] === 'pending' ? 'warning' : 'danger') }} ms-2">
                                                {{ ucfirst($comment['status']) }}
                                            </span>
                                        </div>
                                        @if ($comment['status'] === 'pending')
                                            <div>
                                                <form action="{{ route('admin.posts.comments.approve', $comment['id']) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.posts.comments.reject', $comment['id']) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Reject">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="mb-0 mt-2">{{ $comment['comment'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <!-- Post Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Post Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Type:</strong><br>
                        <span
                            class="badge bg-{{ $post['type'] === 'adoption' ? 'success' : ($post['type'] === 'news' ? 'primary' : 'info') }} fs-6">
                            {{ ucfirst($post['type']) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        <span
                            class="badge bg-{{ $post['status'] === 'published' ? 'success' : ($post['status'] === 'draft' ? 'warning' : 'secondary') }} fs-6">
                            {{ ucfirst($post['status']) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Author:</strong><br>
                        {{ $post['user']['name'] ?? 'Admin' }}
                    </div>

                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ \Carbon\Carbon::parse($post['created_at'])->format('M d, Y h:i A') }}
                    </div>

                    @if ($post['published_at'])
                        <div class="mb-3">
                            <strong>Published:</strong><br>
                            {{ \Carbon\Carbon::parse($post['published_at'])->format('M d, Y h:i A') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if ($post['status'] === 'draft')
                            <form action="{{ route('admin.posts.publish', $post['id']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100"
                                    onclick="return confirm('Publish this post?')">
                                    <i class="fas fa-paper-plane"></i> Publish Post
                                </button>
                            </form>
                        @elseif($post['status'] === 'published')
                            <form action="{{ route('admin.posts.archive', $post['id']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100"
                                    onclick="return confirm('Archive this post?')">
                                    <i class="fas fa-archive"></i> Archive Post
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('admin.posts.edit', $post['id']) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Post
                        </a>

                        <form action="{{ route('admin.posts.destroy', $post['id']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100"
                                onclick="return confirm('Delete this post? This action cannot be undone.')">
                                <i class="fas fa-trash"></i> Delete Post
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
