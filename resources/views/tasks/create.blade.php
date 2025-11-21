@extends('layouts.app')

@section('header', 'Create New Task')

@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-secondary"><i class="bi bi-plus-circle me-2"></i>Submit URLs for Indexing</h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('tasks.store') }}" method="POST" id="createTaskForm">
                        @csrf

                        <!-- Task Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold text-secondary">Task Title <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   placeholder="e.g., My New Blog Posts Batch 1"
                                   value="{{ old('title') }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Search Engine Selection -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold text-secondary">Search Engine</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check card-radio w-100">
                                        <input class="form-check-input d-none" type="radio" name="search_engine" id="google" value="google" {{ old('search_engine', 'google') == 'google' ? 'checked' : '' }}>
                                        <label class="form-check-label card p-3 text-center cursor-pointer border-2" for="google">
                                            <i class="bi bi-google fs-4 mb-1 d-block text-primary"></i>
                                            <span class="fw-bold">Google</span>
                                        </label>
                                    </div>
                                    <div class="form-check card-radio w-100">
                                        <input class="form-check-input d-none" type="radio" name="search_engine" id="yandex" value="yandex" {{ old('search_engine') == 'yandex' ? 'checked' : '' }}>
                                        <label class="form-check-label card p-3 text-center cursor-pointer border-2" for="yandex">
                                            <span class="text-danger fw-bold fs-4 mb-1 d-block">Y</span>
                                            <span class="fw-bold">Yandex</span>
                                        </label>
                                    </div>
                                </div>
                                @error('search_engine')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Task Type Selection -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold text-secondary">Task Type</label>
                                <select class="form-select form-select-lg @error('task_type') is-invalid @enderror" name="task_type" id="task_type">
                                    <option value="indexer" {{ old('task_type') == 'indexer' ? 'selected' : '' }}>Indexer (Submit for Indexing)</option>
                                    <option value="checker" {{ old('task_type') == 'checker' ? 'selected' : '' }}>Checker (Check Index Status)</option>
                                </select>
                                <div class="form-text text-muted mt-1" id="typeDescription">
                                    <i class="bi bi-info-circle"></i> <span id="typeText">Submit links to search engines to get them indexed faster.</span>
                                </div>
                                @error('task_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- URLs Input -->
                        <div class="mb-4">
                            <label for="urls" class="form-label fw-bold text-secondary">URLs List</label>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Enter one URL per line (Max 10,000)</small>
                                <span class="badge bg-light text-dark border" id="lineCount">0 URLs</span>
                            </div>
                            <textarea class="form-control font-monospace @error('urls') is-invalid @enderror" 
                                      id="urls" 
                                      name="urls" 
                                      rows="10" 
                                      placeholder="https://example.com/page1&#10;https://example.com/page2&#10;https://example.com/page3"
                                      required>{{ old('urls') }}</textarea>
                            @error('urls')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- VIP Option -->
                        <div class="mb-4 p-3 bg-warning bg-opacity-10 border border-warning rounded" id="vipSection">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="vip" name="vip" value="1" {{ old('vip') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="vip">
                                    <i class="bi bi-star-fill text-warning me-1"></i> Use VIP Queue
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1 ms-1">
                                Priority processing. Maximum 100 links per task. 
                                <span class="text-danger fw-bold" id="vipWarning" style="display:none;">(Warning: You have entered more than 100 links!)</span>
                            </small>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg border me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                                <i class="bi bi-send me-2"></i> Create Task
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info / Tips -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card bg-light border-0 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>Tips for better results</h6>
                    <ul class="list-unstyled small text-secondary mb-0">
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Ensure all URLs are live (200 OK).</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Don't submit 404 or redirected pages.</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Check your balance before large batches.</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Use Checker tasks to verify results after 72h.</li>
                    </ul>
                </div>
            </div>
            
            <div class="alert alert-info border-0 small">
                <i class="bi bi-info-circle-fill me-2"></i>
                <strong>Note:</strong> Indexing is not instantaneous. It may take up to 72 hours for search engines to process your request.
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    /* Custom Radio Button Styling */
    .card-radio .form-check-input:checked + .card {
        border-color: #0d6efd;
        background-color: #f0f7ff;
        color: #0d6efd;
    }
    .card-radio .card:hover {
        background-color: #f8f9fa;
    }
    .cursor-pointer { cursor: pointer; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlsInput = document.getElementById('urls');
    const lineCountBadge = document.getElementById('lineCount');
    const taskTypeSelect = document.getElementById('task_type');
    const typeText = document.getElementById('typeText');
    const vipCheckbox = document.getElementById('vip');
    const vipWarning = document.getElementById('vipWarning');

    // 1. Dynamic Line Counter
    function updateCount() {
        const text = urlsInput.value;
        // Split by newline and filter out empty lines
        const lines = text.split(/\r\n|\r|\n/).filter(line => line.trim() !== '').length;
        lineCountBadge.textContent = lines + ' URL' + (lines !== 1 ? 's' : '');
        
        // Check VIP limit
        if(vipCheckbox.checked && lines > 100) {
            vipWarning.style.display = 'inline';
            document.getElementById('submitBtn').classList.add('disabled');
        } else {
            vipWarning.style.display = 'none';
            document.getElementById('submitBtn').classList.remove('disabled');
        }
    }

    urlsInput.addEventListener('input', updateCount);
    vipCheckbox.addEventListener('change', updateCount); // Re-check when VIP is toggled

    // 2. Dynamic Description for Task Type
    taskTypeSelect.addEventListener('change', function() {
        if(this.value === 'indexer') {
            typeText.textContent = 'Submit links to search engines to get them indexed faster.';
        } else {
            typeText.textContent = 'Check if your submitted links are already indexed by the search engine.';
        }
    });
});
</script>
@endpush

@endsection
