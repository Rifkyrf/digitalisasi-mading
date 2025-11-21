<!-- resources/views/profile/_content_list.blade.php -->
@if($contents->isEmpty())
    <p class="text-muted">Belum ada konten.</p>
@else
    <div class="row g-2">
        @foreach($contents as $item)
            <div class="col-md-3 col-sm-4 col-6">
                <a href="{{ route('work.show', $item->id) }}" class="text-decoration-none text-dark">
                    <div class="card border-0 shadow-sm" style="border-radius: 8px;">
                        <img src="{{ asset('storage/' . ($item->thumbnail_path ?? 'placeholders/thumb.jpg')) }}"
                             class="card-img-top"
                             style="height: 120px; object-fit: cover; border-radius: 8px 8px 0 0;">
                        <div class="card-body p-2">
                            <h6 class="card-title text-truncate" style="font-size: 0.9rem;">{{ $item->title }}</h6>
                            <small class="text-muted d-block">Oleh: {{ $item->user->name }}</small>
                            <small class="text-secondary">{{ strtoupper($item->file_type) }}</small>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endif