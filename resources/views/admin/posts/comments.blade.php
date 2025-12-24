@extends('layouts.admin')

@section('title', 'Pending Comments - Admin')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Pending Comments</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.posts.index') }}">Posts</a></li>
                    <li class="breadcrumb-item active">Pending Comments</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Posts
        </a>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @if (empty($comments))
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>No Pending Comments</h5>
                    <p class="text-muted">All comments have been reviewed!</p>
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Posts
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Post</th>
                                <th>Commenter</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($comments['data'] as $comment)
                                @if (isset($comment['post']) && isset($comment['user']))
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="{{ route('admin.posts.show', $comment['post']['id']) }}"
                                                        class="text-decoration-none">
                                                        {{ Str::limit($comment['post']['title'], 50) }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    <span
                                                       class="badge bg-{{ ($comment['post']['type'] ?? '') === 'adoption' ? 'success' : 'info' }}">
                                                        {{ ucfirst($comment['post']['type'] ?? 'news') }}
                                                    </span>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $comment['user']['name'] ?? 'Anonymous' }}</strong><br>
                                                <small class="text-muted">{{ $comment['user']['email'] ?? '' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="max-width: 300px;">
                                                <p class="mb-0">{{ Str::limit($comment['comment'] ?? '', 150) }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ isset($comment['created_at']) ? \Carbon\Carbon::parse($comment['created_at'])->format('M d, Y') : '' }}<br>
                                                {{ isset($comment['created_at']) ? \Carbon\Carbon::parse($comment['created_at'])->format('h:i A') : '' }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <form action="{{ route('admin.posts.comments.approve', $comment['id']) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success" title="Approve Comment"
                                                        onclick="return confirm('Approve this comment?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.posts.comments.reject', $comment['id']) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger" title="Reject Comment"
                                                        onclick="return confirm('Reject this comment?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                                    data-bs-target="#commentModal{{ $comment['id'] }}"
                                                    title="View Full Comment">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                                <!-- Comment Modal -->
                                @if (isset($comment['post']) && isset($comment['user']))
                                <div class="modal fade" id="commentModal{{ $comment['id'] }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Comment Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <strong>Post:</strong> {{ $comment['post']['title'] }}
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Commenter:</strong>
                                                    {{ $comment['user']['name'] ?? 'Anonymous' }}
                                                    @if (!empty($comment['user']['email']))
                                                        <br><small
                                                            class="text-muted">{{ $comment['user']['email'] }}</small>
                                                    @endif
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Comment Date:</strong>
                                                    {{ \Carbon\Carbon::parse($comment['created_at'])->format('M d, Y h:i A') }}
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Comment:</strong>
                                                    <div class="border rounded p-3 mt-2" style="background-color: #f8f9fa;">
                                                        {{ $comment['comment'] }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('admin.posts.comments.approve', $comment['id']) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success"
                                                        onclick="return confirm('Approve this comment?')">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.posts.comments.reject', $comment['id']) }}"
                                                    method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirm('Reject this comment?')">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
