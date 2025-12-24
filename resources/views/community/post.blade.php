@extends('layouts.app')

@section('title', $post['title'] . ' - Community')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <article class="post-article">
                    <header class="post-header mb-4">
                        <div class="mb-3">
                            <span class="badge bg-primary">{{ ucfirst($post['type'] ?? 'post') }}</span>
                            <small class="text-muted ms-2">
                                Published on {{ \Carbon\Carbon::parse($post['published_at'] ?? $post['created_at'] ?? now())->format('F j, Y') }}
                            </small>
                        </div>

                        <h1 class="post-title">{{ $post['title'] }}</h1>

                        <div class="post-meta d-flex justify-content-between align-items-center">
                            <div class="author-info d-flex align-items-center">
                                @if (!empty($post['user']))
                                    <div class="avatar-circle me-2">
                                        {{ strtoupper(substr($post['user']['name'] ?? 'A', 0, 2)) }}
                                    </div>
                                    <span>{{ $post['user']['name'] ?? 'Anonymous' }}</span>
                                @else
                                    <span class="text-muted">Anonymous</span>
                                @endif
                            </div>

                            <div class="post-stats">
                                <span class="me-3" id="likes-count">
                                    <i class="fas fa-heart"></i> <span>{{ $post['likes_count'] ?? 0 }}</span>
                                </span>
                                <span id="comments-count">
                                    <i class="fas fa-comment"></i> <span>{{ $post['comments_count'] ?? 0 }}</span>
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
                            <button class="btn btn-outline-danger like-btn" 
                                    data-post-id="{{ $post['id'] }}"
                                    data-is-liked="{{ $post['is_liked'] ?? false ? 'true' : 'false' }}">
                                <i class="fas fa-heart me-1"></i>
                                <span class="like-text">{{ ($post['is_liked'] ?? false) ? 'Unlike' : 'Like' }}</span>
                            </button>
                        </div>
                    @endauth
                </article>

                <!-- Comments Section -->
                <section class="comments-section mt-5">
                    <h4>Comments (<span id="comments-count-text">{{ $post['comments_count'] ?? count($comments) }}</span>)</h4>

                    @auth
                        <div class="comment-form mb-4">
                            <form id="comment-form" method="POST" action="{{ route('community.post.comment', $post['id']) }}">
                                @csrf
                                <div class="mb-3">
                                    <textarea class="form-control" name="comment" id="comment-text" rows="3" placeholder="Write your comment..." required></textarea>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-comment me-1"></i>
                                    Post Comment
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <a href="{{ route('auth.login') }}">Login</a> to post a comment.
                        </div>
                    @endauth

                    <div class="comments-list" id="comments-list">
                        @if (!empty($comments) && count($comments) > 0)
                            @foreach ($comments as $comment)
                                <div class="comment mb-3 p-3 bg-light rounded">
                                    <div class="comment-header d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            @if (!empty($comment['user']))
                                                <div class="avatar-circle me-2"
                                                    style="width: 30px; height: 30px; font-size: 0.75rem;">
                                                    {{ strtoupper(substr($comment['user']['name'] ?? 'A', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $comment['user']['name'] ?? 'Anonymous' }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($comment['created_at'] ?? now())->format('M j, Y \a\t g:i A') }}
                                                    </small>
                                                </div>
                                            @else
                                                <div>
                                                    <strong>Anonymous</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($comment['created_at'] ?? now())->format('M j, Y \a\t g:i A') }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="comment-content">
                                        {{ $comment['comment'] ?? $comment['content'] ?? '' }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No comments yet. Be the first to comment!</p>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Like/Unlike functionality
            const likeBtn = document.querySelector('.like-btn');
            if (likeBtn) {
                likeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const btn = this;
                    const postId = btn.dataset.postId;
                    const isLiked = btn.dataset.isLiked === 'true';
                    
                    // Disable button during request
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Loading...';

                    fetch(`/community/post/${postId}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            is_liked: isLiked
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update like count
                            const likesCountEl = document.querySelector('#likes-count span');
                            if (likesCountEl && data.data.likes_count !== undefined) {
                                likesCountEl.textContent = data.data.likes_count;
                            }
                            
                            // Update button state
                            const newIsLiked = data.data.is_liked;
                            btn.dataset.isLiked = newIsLiked;
                            btn.querySelector('.like-text').textContent = newIsLiked ? 'Unlike' : 'Like';
                            
                            // Update button style
                            if (newIsLiked) {
                                btn.classList.remove('btn-outline-danger');
                                btn.classList.add('btn-danger');
                            } else {
                                btn.classList.remove('btn-danger');
                                btn.classList.add('btn-outline-danger');
                            }
                        } else {
                            alert(data.message || 'Failed to update like status');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-heart me-1"></i><span class="like-text">' + 
                            (btn.dataset.isLiked === 'true' ? 'Unlike' : 'Like') + '</span>';
                    });
                });
            }

            // Comment form submission
            const commentForm = document.getElementById('comment-form');
            if (commentForm) {
                commentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const form = this;
                    const formData = new FormData(form);
                    const commentText = formData.get('comment');
                    const postId = '{{ $post["id"] }}';
                    
                    // Disable form during submission
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Posting...';

                    fetch(`/community/post/${postId}/comment`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear form
                            form.querySelector('#comment-text').value = '';
                            
                            // Update comments count
                            const commentsCountEl = document.querySelector('#comments-count-text');
                            if (commentsCountEl && data.data.comments_count !== undefined) {
                                commentsCountEl.textContent = data.data.comments_count;
                            }
                            
                            // Add new comment to list
                            if (data.data.comment) {
                                const commentsList = document.getElementById('comments-list');
                                const commentDiv = document.createElement('div');
                                commentDiv.className = 'comment mb-3 p-3 bg-light rounded';
                                commentDiv.innerHTML = `
                                    <div class="comment-header d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                                            </div>
                                            <div>
                                                <strong>{{ auth()->user()->name ?? 'You' }}</strong>
                                                <br>
                                                <small class="text-muted">Just now</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="comment-content">
                                        ${commentText}
                                    </div>
                                `;
                                
                                // Remove "No comments" message if exists
                                const noCommentsMsg = commentsList.querySelector('p.text-muted');
                                if (noCommentsMsg) {
                                    noCommentsMsg.remove();
                                }
                                
                                commentsList.insertBefore(commentDiv, commentsList.firstChild);
                            }
                            
                            // Show success message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                ${data.message || 'Comment added successfully!'}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            form.parentElement.insertBefore(alertDiv, form);
                            
                            // Auto-dismiss after 3 seconds
                            setTimeout(() => {
                                alertDiv.remove();
                            }, 3000);
                        } else {
                            alert(data.message || 'Failed to add comment');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    });
                });
            }
        });
    </script>
@endpush
