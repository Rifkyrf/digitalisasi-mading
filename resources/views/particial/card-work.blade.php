{{--
<div class="col-md-4 col-12 mb-4">
    <div class="card h-100">
        <img src="{{ $work->thumbnail_path ? asset('storage/' . $work->thumbnail_path) : $work->icon }}"
             class="card-img-top"
             style="height: 180px; object-fit: cover;"
             alt="{{ $work->title }}">

        <div class="card-body">
            <h5 class="card-title" title="{{ $work->title }}">
                {{ Str::limit($work->title, 40) }}
            </h5>
            <p class="card-text">
                {{ Str::limit($work->description, 60) }}
            </p>
            <small class="d-block text-muted mb-1">
                Oleh: <strong>{{ $work->user->name }}</strong>
            </small>
            <small class="text-secondary">
                {{ strtoupper($work->file_type) }}
            </small>
            <a href="{{ route('work.show', $work->id) }}" class="btn btn-outline-primary btn-sm mt-2">
                <i class="fas fa-eye me-1"></i> Lihat
            </a>
        </div>
    </div>
</div> --}}