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
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No posts found.</p>
                        <a href="{{ route('community.posts') }}" class="btn btn-outline-primary">Clear Filters</a>
                    </div>
                @else
                    @foreach ($posts as $post)
                        <article class="card mb-4">
                            @if (!empty($post['featured_image']))
                                <img src="{{ $post['featured_image'] }}" class="card-img-top"
                                    style="max-height:280px;object-fit:cover;" alt="{{ $post['title'] }}">
                            @endif

                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-primary">
                                        {{ ucfirst($post['type'] ?? 'post') }}
                                    </span>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($post['published_at'] ?? $post['created_at'] ?? now())->format('M d, Y') }}
                                    </small>
                                </div>

                                <h4 class="mb-2">
                                    <a href="{{ route('community.post', $post['slug']) }}"
                                        class="text-decoration-none text-dark">
                                        {{ $post['title'] }}
                                    </a>
                                </h4>

                                <p class="text-muted">
                                    {{ Str::limit(strip_tags($post['content'] ?? ''), 160) }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        @if (!empty($post['user']))
                                            <div class="avatar-circle me-2">
                                                {{ strtoupper(substr($post['user']['name'] ?? 'A', 0, 2)) }}
                                            </div>
                                            <small>{{ $post['user']['name'] ?? 'Anonymous' }}</small>
                                        @else
                                            <small class="text-muted">Anonymous</small>
                                        @endif
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

                    {{-- Pagination --}}
                    @if (!empty($pagination))
                        <nav aria-label="Posts pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                @if ($pagination['current_page'] > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}">Previous</a>
                                    </li>
                                @endif

                                @for ($i = 1; $i <= $pagination['last_page']; $i++)
                                    @if ($i == $pagination['current_page'])
                                        <li class="page-item active">
                                            <span class="page-link">{{ $i }}</span>
                                        </li>
                                    @elseif ($i == 1 || $i == $pagination['last_page'] || ($i >= $pagination['current_page'] - 2 && $i <= $pagination['current_page'] + 2))
                                        <li class="page-item">
                                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                                        </li>
                                    @elseif ($i == $pagination['current_page'] - 3 || $i == $pagination['current_page'] + 3)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                @endfor

                                @if ($pagination['current_page'] < $pagination['last_page'])
                                    <li class="page-item">
                                        <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}">Next</a>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection