@extends('layouts.admin')

@section('title', 'Community Posts - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Community Posts</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Posts</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Post
            </a>
            <a href="{{ route('admin.posts.comments.pending') }}" class="btn btn-warning">
                <i class="fas fa-clock"></i> Pending Comments
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Post Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="adoption" {{ request('type') == 'adoption' ? 'selected' : '' }}>Adoption</option>
                        <option value="story" {{ request('type') == 'story' ? 'selected' : '' }}>Story</option>
                        <option value="news" {{ request('type') == 'news' ? 'selected' : '' }}>News</option>
                        <option value="job" {{ request('type') == 'job' ? 'selected' : '' }}>Job</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published
                        </option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search posts..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Posts List -->
    <div class="card">
        <div class="card-body">
            @if (empty($posts))
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No posts found</p>
                    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create First Post
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Author</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($posts as $post)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if (!empty($post['featured_image']))
                                                <img src="{{ $post['featured_image'] }}" class="rounded me-2"
                                                    style="width: 40px; height: 40px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $post['title'] }}</h6>
                                                <small
                                                    class="text-muted">{{ Str::limit(strip_tags($post['content']), 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $post['type'] == 'adoption' ? 'success' : ($post['type'] == 'news' ? 'primary' : 'info') }}">
                                            {{ ucfirst($post['type']) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $post['status'] == 'published' ? 'success' : ($post['status'] == 'draft' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($post['status']) }}
                                        </span>
                                    </td>
                                    <td>{{ $post['user']['name'] ?? 'Admin' }}</td>
                                    <td>
                                        {{ $post['published_at'] ? \Carbon\Carbon::parse($post['published_at'])->format('M d, Y') : '-' }}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.posts.show', $post['id']) }}"
                                                class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.posts.edit', $post['id']) }}"
                                                class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if ($post['status'] == 'draft')
                                                <form action="{{ route('admin.posts.publish', $post['id']) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Publish"
                                                        onclick="return confirm('Publish this post?')">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                </form>
                                            @elseif($post['status'] == 'published')
                                                <form action="{{ route('admin.posts.archive', $post['id']) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-warning" title="Archive"
                                                        onclick="return confirm('Archive this post?')">
                                                        <i class="fas fa-archive"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.posts.destroy', $post['id']) }}" method="POST"
                                                style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete"
                                                    onclick="return confirm('Delete this post? This action cannot be undone.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
