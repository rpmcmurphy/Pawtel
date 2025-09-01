@extends('layouts.app')

@section('title', $post['title'] . ' - Community')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <article class="post-article">
                    <header class="post-header mb-4">
                        <div class="mb-3">
                            <span class="badge bg-primary">{{ $post['category']['name'] ?? 'General' }}</span>
                            <small class="text-muted ms-2">
                                Published on {{ date('F j, Y', strtotime($post['created_at'])) }}
                            </small>
                        </div>

                        <h1 class="post-title">{{ $post['title'] }}</h1>

                        <div class="post-meta d-flex justify-content-between align-items-center">
                            <div class="author-info d-flex align-items-center">
                                <div class="avatar-circle me-2">
                                    {{ strtoupper(substr($post['author']['name'] ?? 'A', 0, 2)) }}
                                </div>
                                <span>{{ $post['author']['name'] ?? 'Anonymous' }}</span>
                            </div>

                            <div class="post-stats">
                                <span class="me-3">
                                    <i class="fas fa-heart"></i> {{ $post['likes_count'] ?? 0 }}
                                </span>
                                <span>
                                    <i class="fas fa-comment"></i> {{ $post['comments_count'] ?? 0 }}
                                </span>
                            </div>
                        </div>
                    </header>

                    @if ($post['featured_image'])
                        <div class="post-image mb-4">
                            <img src="{{ $post['featured_image'] }}" alt="{{ $post['title'] }}" class="img-fluid rounded">
                        </div>
                    @endif

                    <div class="post-content">
                        {!! $post['content'] !!}
                    </div>

                    @auth
                        <div class="post-actions mt-4 pt-4 border-top">
                            <button class="btn btn-outline-danger like-btn" data-post-id="{{ $post['id'] }}">
                                <i class="fas fa-heart me-1"></i>
                                {{ $post['user_has_liked'] ? 'Unlike' : 'Like' }}
                            </button>
                        </div>
                    @endauth
                </article>

                <!-- Comments Section -->
                <section class="comments-section mt-5">
                    <h4>Comments ({{ count($comments) }})</h4>

                    @auth
                        <div class="comment-form mb-4">
                            <form method="POST" action="{{ route('community.post.comment', $post['id']) }}">
                                @csrf
                                <div class="mb-3">
                                    <textarea class="form-control" name="comment" rows="3" placeholder="Write your comment..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-comment me-1"></i>
                                    Post Comment
                                </button>
                            </form>
                        </div>
                    @endauth

                    <div class="comments-list">
                        @foreach ($comments as $comment)
                            <div class="comment mb-3 p-3 bg-light rounded">
                                <div class="comment-header d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2"
                                            style="width: 30px; height: 30px; font-size: 0.75rem;">
                                            {{ strtoupper(substr($comment['author']['name'] ?? 'A', 0, 2)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $comment['author']['name'] ?? 'Anonymous' }}</strong>
                                            <br>
                                            <small
                                                class="text-muted">{{ date('M j, Y \a\t g:i A', strtotime($comment['created_at'])) }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-content">
                                    {{ $comment['content'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        document.addEventListener('click', function(e) {
            if (e.target.matches('.like-btn') || e.target.closest('.like-btn')) {
                e.preventDefault();
                const btn = e.target.matches('.like-btn') ? e.target : e.target.closest('.like-btn');
                const postId = btn.dataset.postId;

                fetch(`/community/post/${postId}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload(); // Simple reload to update like status
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });
    </script>
@endpush
