@extends('layouts.coreui-master')

@section('title', 'Catatan')

@push('css')
<!-- Masonry CSS -->
<link rel="stylesheet" href="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.css">
<style>
  .note-create-card, .note-card {
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
  }
  .note-create-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  }
  .note-card {
    color: #1f2937;
    position: relative;
    word-wrap: break-word;
    white-space: pre-wrap;
    cursor: default;
    overflow: hidden;
    height: auto;
    min-height: 180px;
    margin-bottom: 20px;
    width: 100%;
  }
  .note-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.12);
  }
  .note-card.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
  }
  .note-title {
    font-weight: 700;
    font-size: 1.15rem;
    margin-bottom: 0.5rem;
    color: #1f2937;
  }
  .note-content {
    font-size: 0.95rem;
    line-height: 1.5rem;
    color: #4b5563;
  }
  .note-toolbar {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }
  .color-dot {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: inline-block;
    cursor: pointer;
    border: 2px solid rgba(0,0,0,0.1);
    transition: all 0.2s ease;
  }
  .color-dot:hover {
    transform: scale(1.1);
  }
  .color-dot.selected {
    box-shadow: 0 0 0 3px rgba(0,0,0,0.15);
    transform: scale(1.15);
  }
  .palette {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
  }
  .note-actions .btn {
    padding: 0.3rem 0.6rem;
    font-size: 0.8rem;
    border-radius: 6px;
    transition: all 0.2s ease;
  }
  .note-actions .btn:hover {
    transform: scale(1.05);
  }
  .grid-empty {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 3rem;
    color: #6b7280;
    text-align: center;
    background: #f9fafb;
  }
  
  /* Masonry grid styles */
  .masonry-grid {
    display: block;
  }
  
  .masonry-grid .note-card-wrapper {
    width: 100%;
    margin-bottom: 20px;
  }
  
  /* Different card sizes based on content */
  .note-card-small {
    min-height: 180px;
  }
  
  .note-card-medium {
    min-height: 240px;
  }
  
  .note-card-large {
    min-height: 320px;
  }
  
  /* Responsive adjustments */
  @media (min-width: 576px) {
    .masonry-grid .note-card-wrapper {
      width: calc(50% - 10px);
    }
  }
  
  @media (min-width: 768px) {
    .masonry-grid .note-card-wrapper {
      width: calc(33.333% - 14px);
    }
  }
  
  @media (min-width: 992px) {
    .masonry-grid .note-card-wrapper {
      width: calc(25% - 15px);
    }
  }
  
  @media (min-width: 1200px) {
    .masonry-grid .note-card-wrapper {
      width: calc(20% - 16px);
    }
  }
</style>
@endpush

@section('breadcrumb')
  @parent
  <li class="active">Catatan</li>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-12 mb-3">
    <div class="card note-create-card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Buat Catatan</h5>
        </div>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Judul</label>
            <input type="text" id="create-title" class="form-control" placeholder="Masukkan judul catatan">
          </div>
          <div class="col-md-6">
            <label class="form-label">Isi Catatan</label>
            <textarea id="create-content" class="form-control" rows="2" placeholder="Tulis catatan sederhana..."></textarea>
          </div>
          <div class="col-md-2">
            <label class="form-label">Warna</label>
            <div id="create-color-palette" class="palette mb-2"></div>
            <button class="btn btn-primary w-100" id="create-note-btn">
              <i class="cil-plus"></i> Create
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-12">
    <div class="masonry-grid" id="notes-grid">
      @forelse($notes as $note)
      <?php
        // Determine card size based on content length
        $contentLength = strlen($note->content);
        $cardSize = 'note-card-small';
        if ($contentLength > 100 && $contentLength <= 200) {
          $cardSize = 'note-card-medium';
        } elseif ($contentLength > 200) {
          $cardSize = 'note-card-large';
        }
      ?>
      <div class="note-card-wrapper" id="note-card-{{ $note->id }}" data-note-id="{{ $note->id }}">
        <div class="card note-card {{ $cardSize }}" style="background-color: {{ $note->color }}">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="note-title" data-note-title="{{ $note->id }}">{{ $note->title }}</div>
              </div>
              <div class="note-actions btn-group">
                <button class="btn btn-light btn-sm btn-edit" data-id="{{ $note->id }}">
                  <i class="cil-pencil"></i>
                </button>
                <button class="btn btn-light btn-sm btn-delete" data-id="{{ $note->id }}">
                  <i class="cil-trash"></i>
                </button>
              </div>
            </div>
            <div class="note-content mt-1" data-note-content="{{ $note->id }}">{{ $note->content }}</div>
            <div class="mt-3 d-flex justify-content-between align-items-center">
              <div class="palette small-palette" data-palette-note="{{ $note->id }}"></div>
              <div class="text-muted small">
                @if(auth()->user()->level == 1)
                  <i class="cil-user"></i> {{ optional($note->user)->name ?? '—' }}
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
      @empty
      <div class="note-card-wrapper">
        <div class="grid-empty">
          Belum ada catatan. Buat catatan pertama kamu di panel atas.
        </div>
      </div>
      @endforelse
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editNoteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Catatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit-id">
        <div class="mb-3">
          <label class="form-label">Judul</label>
          <input type="text" id="edit-title" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Isi Catatan</label>
          <textarea id="edit-content" class="form-control" rows="4"></textarea>
        </div>
        <div class="mb-2">
          <label class="form-label">Warna</label>
          <div id="edit-color-palette" class="palette"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-primary" id="save-edit-btn"><i class="cil-save"></i> Simpan</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- Masonry JS -->
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script>
(function() {
  const COLORS = ['#FDE68A','#BFDBFE','#C7D2FE','#FBCFE8','#FCA5A5','#A7F3D0','#F8D7DA','#E2E3E5','#FFF3CD','#D1FAE5'];
  let selectedCreateColor = COLORS[0];
  let selectedEditColor = COLORS[0];
  let masonryInstance = null;

  // Initialize Masonry after DOM is ready
  $(document).ready(function() {
    initializeMasonry();
  });

  // Initialize Masonry layout
  function initializeMasonry() {
    const $grid = $('#notes-grid');
    
    // Initialize Masonry
    masonryInstance = new Masonry($grid[0], {
      itemSelector: '.note-card-wrapper',
      columnWidth: '.note-card-wrapper',
      gutter: 20,
      horizontalOrder: true,
      percentPosition: true
    });

    // Re-layout after images load
    $grid.imagesLoaded().progress(function() {
      masonryInstance.layout();
    });
  }

  // Get card size based on content length
  function getCardSize(content) {
    const contentLength = content ? content.length : 0;
    if (contentLength > 200) {
      return 'note-card-large';
    } else if (contentLength > 100) {
      return 'note-card-medium';
    }
    return 'note-card-small';
  }

  // CSRF
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  // Helpers
  function paletteDot(color, selected=false) {
    return '<span class="color-dot '+(selected?'selected':'')+'" data-color="'+color+'" style="background:'+color+';"></span>';
  }

  function renderPalette($container, selectedColor) {
    $container.empty();
    COLORS.forEach(c => {
      $container.append($(paletteDot(c, c === selectedColor)));
    });
  }

  function cardTemplate(note) {
    const cardSize = getCardSize(note.content);
    return `
      <div class="note-card-wrapper" id="note-card-${note.id}" data-note-id="${note.id}">
        <div class="card note-card ${cardSize}" style="background-color: ${note.color}">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="note-title" data-note-title="${note.id}">${escapeHtml(note.title)}</div>
              </div>
              <div class="note-actions btn-group">
                <button class="btn btn-light btn-sm btn-edit" data-id="${note.id}"><i class="cil-pencil"></i></button>
                <button class="btn btn-light btn-sm btn-delete" data-id="${note.id}"><i class="cil-trash"></i></button>
              </div>
            </div>
            <div class="note-content mt-1" data-note-content="${note.id}">${escapeHtml(note.content || '')}</div>
            <div class="mt-3 d-flex justify-content-between align-items-center">
              <div class="palette small-palette" data-palette-note="${note.id}"></div>
              <div class="text-muted small"></div>
            </div>
          </div>
        </div>
      </div>`;
  }

  function escapeHtml(str) {
    return (str || '').replace(/[<>&"]/g, function(m) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m];
    });
  }

  // Init palettes
  renderPalette($('#create-color-palette'), selectedCreateColor);
  // Attach click for create palette
  $('#create-color-palette').on('click', '.color-dot', function() {
    selectedCreateColor = $(this).data('color');
    renderPalette($('#create-color-palette'), selectedCreateColor);
  });

  // Render palettes for existing cards
  $('[data-palette-note]').each(function() {
    renderPalette($(this), $(this).closest('.note-card').css('background-color'));
  });

  // Delegate palette click for existing notes (quick color change)
  $('#notes-grid').on('click', '.small-palette .color-dot', function() {
    const color = $(this).data('color');
    const $palette = $(this).closest('[data-palette-note]');
    const id = $palette.data('palette-note');

    $.ajax({
      url: "{{ route('catatan.update', ':id') }}".replace(':id', id),
      type: 'PUT',
      data: { title: $('[data-note-title="'+id+'"]').text().trim(), content: $('[data-note-content="'+id+'"]').text().trim(), color },
      success: function(resp) {
        $('#note-card-'+id+' .note-card').css('background-color', color);
        renderPalette($palette, color);
      }
    });
  });

  // Create note
  $('#create-note-btn').on('click', function() {
    const title = $('#create-title').val().trim();
    const content = $('#create-content').val().trim();

    if (!title) {
      alert('Judul wajib diisi.');
      return;
    }

    $.post("{{ route('catatan.store') }}", { title, content, color: selectedCreateColor })
      .done(function(resp) {
        const note = resp.note;
        const $grid = $('#notes-grid');
        const html = cardTemplate(note);
        
        // Remove empty state if exists
        if ($grid.find('.grid-empty').length) {
          $grid.empty();
        }
        
        // Add new note to grid
        const $newNote = $(html).appendTo($grid);
        
        // Initialize palette for new card
        const $pal = $newNote.find('[data-palette-note="'+note.id+'"]');
        renderPalette($pal, note.color);
        
        // Re-layout masonry
        setTimeout(function() {
          masonryInstance.appended($newNote[0]);
          masonryInstance.layout();
        }, 100);

        // reset form
        $('#create-title').val('');
        $('#create-content').val('');
        selectedCreateColor = COLORS[0];
        renderPalette($('#create-color-palette'), selectedCreateColor);
      })
      .fail(function(xhr) {
        alert('Gagal membuat catatan.');
      });
  });

  // Edit
  $('#notes-grid').on('click', '.btn-edit', function() {
    const id = $(this).data('id');
    const title = $('[data-note-title="'+id+'"]').text().trim();
    const content = $('[data-note-content="'+id+'"]').text().trim();
    const color = $('#note-card-'+id+' .note-card').css('background-color');

    $('#edit-id').val(id);
    $('#edit-title').val(title);
    $('#edit-content').val(content);

    // Convert rgb to hex if needed
    selectedEditColor = rgbToHex(color) || COLORS[0];
    renderPalette($('#edit-color-palette'), selectedEditColor);

    $('#edit-color-palette').off('click').on('click', '.color-dot', function() {
      selectedEditColor = $(this).data('color');
      renderPalette($('#edit-color-palette'), selectedEditColor);
    });

    $('#editNoteModal').modal('show');
  });

  $('#save-edit-btn').on('click', function() {
    const id = $('#edit-id').val();
    const title = $('#edit-title').val().trim();
    const content = $('#edit-content').val().trim();
    const color = selectedEditColor;

    if (!title) {
      alert('Judul wajib diisi.');
      return;
    }

    $.ajax({
      url: "{{ route('catatan.update', ':id') }}".replace(':id', id),
      type: 'PUT',
      data: { title, content, color },
      success: function(resp) {
        $('[data-note-title="'+id+'"]').text(title);
        $('[data-note-content="'+id+'"]').text(content);
        $('#note-card-'+id+' .note-card').css('background-color', color);
        
        // Update card size based on new content
        const $noteCard = $('#note-card-'+id+' .note-card');
        const newCardSize = getCardSize(content);
        $noteCard.removeClass('note-card-small note-card-medium note-card-large').addClass(newCardSize);
        
        const $pal = $('#note-card-'+id+' [data-palette-note="'+id+'"]');
        renderPalette($pal, color);
        
        // Re-layout masonry after content change
        setTimeout(function() {
          masonryInstance.layout();
        }, 100);
        
        $('#editNoteModal').modal('hide');
      },
      error: function() {
        alert('Gagal menyimpan catatan.');
      }
    });
  });

  // Delete
  $('#notes-grid').on('click', '.btn-delete', function() {
    const id = $(this).data('id');
    if (!confirm('Hapus catatan ini?')) return;
    
    const $noteCard = $('#note-card-'+id);
    
    $.ajax({
      url: "{{ route('catatan.destroy', ':id') }}".replace(':id', id),
      type: 'DELETE',
      success: function() {
        // Remove with animation
        $noteCard.fadeOut(300, function() {
          $(this).remove();
          
          // Show empty state if no notes left
          if (!$('#notes-grid').children().length) {
            $('#notes-grid').html('<div class="note-card-wrapper"><div class="grid-empty">Belum ada catatan.</div></div>');
          }
          
          // Re-layout masonry after removal
          setTimeout(function() {
            masonryInstance.layout();
          }, 100);
        });
      },
      error: function() {
        alert('Gagal menghapus catatan.');
      }
    });
  });

  function rgbToHex(rgb) {
    const m = (rgb||'').match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
    return m ? "#"
      + ("0"+parseInt(m[1],10).toString(16)).slice(-2)
      + ("0"+parseInt(m[2],10).toString(16)).slice(-2)
      + ("0"+parseInt(m[3],10).toString(16)).slice(-2) : null;
  }
})();
</script>
@endpush