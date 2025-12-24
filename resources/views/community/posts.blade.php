@extends('layouts.app')

@section('title', 'Community Posts')

@section('content')
    <div class="container py-5">
        <div class="row">

            <!-- Filters -->
            <div class="col-lg-3 mb-4">
                <form method="GET" class="card p-3">
                    <h6 class="mb-3">Filter Posts</h6>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="category" class="form-select">
                            <option value="">All</option>
                            @foreach (['adoption', 'story', 'news', 'job'] as $type)
                                <option value="{{ $type }}" {{ ($filters['category'] ?? '') === $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}"
                            placeholder="Title or content">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort</label>
                        <select name="sort" class="form-select">
                            <option value="latest" {{ ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' }}>Latest
                            </option>
                            <option value="popular" {{ ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' }}>Most liked
                            </option>
                            <option value="commented" {{ ($filters['sort'] ?? '') === 'commented' ? 'selected' : '' }}>Most
                                commented</option>
                        </select>
                    </div>

                    <button class="btn btn-primary w-100">Apply</button>
                </form>
            </div>

            <!-- Posts -->
            <div class="col-lg-9">
                @if (empty($posts) || count($posts) === 0)
                    <div class="text-center py-5">
                        <p class="text-muted">No posts found.</p>
                    </div>
                @else
                    @foreach ($posts as $post)
                        <article class="card mb-4">
                            @if (!empty($post['featured_image']))
                                <img src="{{ $post['featured_image'] }}" class="card-img-top"
                                    style="max-height:280px;object-fit:cover;">
                            @endif

                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-primary">
                                        {{ ucfirst($post['type']) }}
                                    </span>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($post['published_at'] ?? $post['created_at'])->format('M d, Y') }}
                                    </small>
                                </div>

                                <h4 class="mb-2">
                                    <a href="{{ route('community.post.show', $post['slug']) }}"
                                        class="text-decoration-none text-dark">
                                        {{ $post['title'] }}
                                    </a>
                                </h4>

                                <p class="text-muted">
                                    {{ Str::limit(strip_tags($post['content']), 160) }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2">
                                            {{ strtoupper(substr($post['author']['name'] ?? 'A', 0, 2)) }}
                                        </div>
                                        <small>{{ $post['author']['name'] ?? 'Anonymous' }}</small>
                                    </div>

                                    <div class="text-muted small">
                                        <span class="me-3">
                                            <i class="fas fa-heart"></i> {{ $post['likes_count'] ?? 0 }}
                                        </span>
                                        <span>
                                            <i class="fas fa-comment"></i> {{ $post['comments_count'] ?? 0 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach

                    {{-- Pagination (if service returns paginator later) --}}
                    @if (method_exists($posts, 'links'))
                        <div class="mt-4">
                            {{ $posts->appends($filters)->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection