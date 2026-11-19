@extends('layouts.admin')
@section('title', 'Edit Announcement')

@section('content')
<div class="section-header">
    <div>
        <h2>Edit Announcement</h2>
        <p>Update announcement content for students.</p>
    </div>
    <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $announcement->title) }}" required placeholder="Announcement title...">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message / Body</label>
                        <textarea name="body" rows="6" class="form-control @error('body') is-invalid @enderror"
                                  required placeholder="Write the announcement content here...">{{ old('body', $announcement->body) }}</textarea>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Audience</label>
                            <select name="audience" class="form-select" required>
                                <option value="all" {{ old('audience', $announcement->audience) === 'all' ? 'selected' : '' }}>All Students</option>
                                <option value="jhs" {{ old('audience', $announcement->audience) === 'jhs' ? 'selected' : '' }}>JHS Only</option>
                                <option value="shs" {{ old('audience', $announcement->audience) === 'shs' ? 'selected' : '' }}>SHS Only</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select" required>
                                <option value="normal"    {{ old('priority', $announcement->priority) === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="important" {{ old('priority', $announcement->priority) === 'important' ? 'selected' : '' }}>Important</option>
                                <option value="urgent"    {{ old('priority', $announcement->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Expires On <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="date" name="expires_at" class="form-control"
                                   value="{{ old('expires_at', $announcement->expires_at?->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="mb-4 p-3 rounded-3" style="background:#f8faff;border:1px solid #e2e8f0;">
                        <div class="form-check">
                            <input type="hidden" name="publish" value="0">
                            <input class="form-check-input" type="checkbox" name="publish" value="1" id="publish"
                                   {{ old('publish', $announcement->is_published) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="publish">
                                <i class="bi bi-megaphone me-1 text-primary"></i> Publish immediately (visible to students)
                            </label>
                        </div>
                        <div class="text-muted mt-1" style="font-size:.8rem;">
                            Leave unchecked to save as draft.
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn flex-grow-1 text-white fw-semibold"
                                style="background:linear-gradient(135deg,#1a3c6e,#2356a0);border-radius:10px;padding:.7rem;">
                            <i class="bi bi-save me-1"></i> Update Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

