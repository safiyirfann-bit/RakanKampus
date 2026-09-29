<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Admin Dashboard - RakanKampus</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/3.44.0/tabler-icons.min.css">
<style>
body { margin: 0; color: #1f2937; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }

/* layout: topics on the left, "needs you" column on the right */
.dash-grid { display: grid; grid-template-columns: minmax(0, 1.75fr) minmax(320px, 1fr); gap: 18px; align-items: start; }
.dash-side { display: grid; gap: 18px; }

/* knowledge base topic cards */
.topics { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 12px; }
.tp { position: relative; display: flex; gap: 12px; align-items: flex-start; padding: 13px; border-radius: 16px; background: #f8fbf9; border: 1px solid #e6efe8; transition: transform .15s, box-shadow .15s, border-color .15s; min-width: 0; }
.tp:hover { transform: translateY(-2px); border-color: #cfe3d4; box-shadow: 0 10px 22px rgba(47,79,58,.08); }
.tp-ic { width: 42px; height: 42px; border-radius: 13px; display: grid; place-items: center; font-size: 20px; flex-shrink: 0; }
.tp-body { min-width: 0; flex: 1; text-decoration: none; color: inherit; }
.tp-body b { display: block; font-size: 14.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #1f2937; }
.tp-body small { display: block; font-size: 12px; color: #64748b; margin-top: 2px; }
.tp-tags { display: flex; gap: 5px; margin-top: 8px; flex-wrap: nowrap; overflow: hidden; }
.tp-tags i { font-style: normal; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 7px; background: #ecfdf5; color: #3f7a52; white-space: nowrap; }
.tp-actions { position: absolute; top: 10px; right: 10px; display: flex; gap: 4px; padding: 3px; border-radius: 11px; background: rgba(255,255,255,.92); box-shadow: 0 4px 12px rgba(47,79,58,.12); opacity: 0; transform: translateY(-3px); transition: opacity .15s, transform .15s; }
.tp:hover .tp-actions, .tp:focus-within .tp-actions { opacity: 1; transform: none; }
.tp-actions form { margin: 0; }
.icon-btn { width: 30px; height: 30px; border-radius: 9px; border: 0; background: transparent; display: grid; place-items: center; cursor: pointer; text-decoration: none; }
.icon-btn svg { width: 16px; height: 16px; }
.icon-btn:hover { background: #ecfdf5; }
.icon-btn.view-btn { color: #3f7a52; } .icon-btn.edit-btn { color: #b7791f; } .icon-btn.delete-btn { color: #dc2626; }
.icon-btn.delete-btn:hover { background: #fee2e2; }
.add-tile { display: flex; align-items: center; justify-content: center; gap: 8px; border: 1.5px dashed #b7d3bf; background: transparent; color: #3f7a52; font-weight: 800; font-size: 14px; border-radius: 16px; min-height: 76px; cursor: pointer; font-family: inherit; }
.add-tile:hover { background: #ecfdf5; }

/* needs an answer / feedback rows */
.row { display: flex; gap: 11px; align-items: flex-start; padding: 11px 0; border-bottom: 1px solid #eef2ef; }
.row:last-child { border-bottom: 0; padding-bottom: 0; }
.row .qi { width: 32px; height: 32px; border-radius: 10px; display: grid; place-items: center; font-weight: 900; flex-shrink: 0; background: #fff4dd; color: #b7791f; }
.row .qi.fb { background: #ecfdf5; color: #3f7a52; } .row .qi.ft { background: #eef2ff; color: #4f46e5; } .row .qi.is { background: #fee2e2; color: #dc2626; }
.row p { margin: 0; font-size: 13.5px; font-weight: 600; color: #1f2937; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.row small { display: block; font-size: 11.5px; color: #64748b; margin-top: 3px; }
.row .ans { margin-left: auto; flex-shrink: 0; font-size: 12px; font-weight: 800; padding: 7px 11px; border-radius: 10px; background: #3f7a52; color: #fff; text-decoration: none; }
.row .ans:hover { background: #2f4f3a; }
.row .new { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; margin-left: auto; margin-top: 6px; flex-shrink: 0; }

/* online today */
.bars { display: flex; align-items: flex-end; gap: 6px; height: 96px; margin-top: 4px; }
.bars .bar { flex: 1; border-radius: 6px 6px 2px 2px; background: linear-gradient(180deg, #8fc79d, #3f7a52); min-height: 4px; position: relative; }
.bars .bar.now { background: linear-gradient(180deg, #f5c563, #b7791f); }
.bars .bar:hover::after { content: attr(data-tip); position: absolute; bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%); background: #1f2937; color: #fff; font-size: 11px; font-weight: 700; padding: 4px 7px; border-radius: 7px; white-space: nowrap; }
.hours { display: flex; justify-content: space-between; font-size: 10.5px; color: #94a3b8; margin-top: 5px; }
.bars-empty { font-size: 12.5px; color: #94a3b8; margin-top: 8px; }

.no-results { display: none; text-align: center; color: #64748b; padding: 20px; }

/* modals (add / edit / delete topic) */
.modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.6);
    z-index:999;
    align-items:center;
    justify-content:center;
}

.modal-content{
    width:90%;
    max-width:600px;
    background:#fff;
    border:1px solid #e3ece5;
    border-radius:24px;
    padding:24px;
    box-shadow: 0 20px 44px rgba(20, 40, 100, 0.18);
}

.modal-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:20px;
}

.modal-header h2{
    color:#1f2937;
    margin:0;
}

.close-btn{
    background:none;
    border:none;
    color:#64748b;
    font-size:30px;
    cursor:pointer;
}

.form-group{
    margin-bottom:18px;
}

.form-group label{
    display:block;
    color:#3f7a52;
    margin-bottom:8px;
    font-size:14px;
    font-weight:700;
    letter-spacing:.08em;
    text-transform:uppercase;
}

.form-group input,
.form-group textarea{
    width:100%;
    padding:16px 18px;
    border-radius:16px;
    border:1px solid #e3ece5;
    background:#f8fafc;
    color:#1f2937;
    font-size:16px;
    outline:none;
    box-sizing:border-box;
}

.modal-actions{
    display:flex;
    gap:14px;
    margin-top:24px;
}

.confirm-icon{
    width:64px; height:64px; margin:0 auto 16px;
    border-radius:50%;
    background:rgba(220,38,38,.1);
    display:flex; align-items:center; justify-content:center;
    color:#dc2626;
}
.confirm-icon svg{ width:28px; height:28px; }
.confirm-title{ color:#1f2937; font-size:19px; font-weight:800; margin:0 0 8px; text-align:center; }
.confirm-message{ color:#64748b; font-size:14px; margin:0; text-align:center; line-height:1.5; }
.delete-confirm-btn{
    flex:1; padding:16px; border:none; border-radius:16px;
    font-size:16px; font-weight:700; cursor:pointer;
    background:#ef4444; color:#fff;
}
.delete-confirm-btn:hover{ background:#dc2626; }

.cancel-btn,
.submit-btn{
    flex:1;
    padding:16px;
    border:none;
    border-radius:16px;
    font-size:16px;
    font-weight:700;
    cursor:pointer;
}

.cancel-btn{
    background:#f1f5f9;
    color:#374151;
    border:1px solid #e3ece5;
}

.submit-btn{
    background:#4a7856;
    color:#fff;
}

.no-results{
    color:#64748b;
    text-align:center;
    padding:24px;
    display:none;
}

.status-msg{
    background:#ecfdf5;
    border:1px solid #a7f3d0;
    color:#065f46;
    padding:12px 18px;
    border-radius:12px;
    margin-bottom:20px;
    font-size:14px;
}


.submit-btn { background: #3f7a52 !important; }
</style>
</head>
<body>

@include('partials.admin-nav', ['active' => 'dashboard', 'unansweredCount' => $unansweredCount, 'unreadFeedbackCount' => $unreadFeedbackCount])
@php
  // A friendly icon + colour per topic, picked from words in its title
  $topicLook = function ($t) {
      $t = mb_strtolower($t);
      $map = [
          ['kelab|persatuan|club', '🎭', '#fdf4e7'], ['kantin|surau|makan', '🕌', '#e8f7f5'], ['program|pengajian|diploma|kursus', '🎓', '#fef2f2'],
          ['dewan|kuliah|bilik|lokasi', '🏛️', '#f0f9ff'], ['payment|bayar|yuran|fee', '💳', '#eef2ff'], ['pengurusan|ketua|staff|pensyarah', '👥', '#f5f3ff'],
          ['sejarah|pengenalan|visi|misi', '📜', '#fff7ed'], ['singkatan|akronim', '🔤', '#ecfeff'], ['spmp|sistem|portal|cidos', '💻', '#eef2ff'],
          ['peperiksaan|exam|jadual', '📅', '#fefce8'], ['asrama|kolej|hostel', '🏠', '#f0fdf4'], ['sukan|sport', '⚽', '#f0fdf4'],
      ];
      foreach ($map as [$re, $e, $bg]) if (preg_match('/' . $re . '/u', $t)) return [$e, $bg];
      return ['📘', '#ecfdf5'];
  };
  // "Title — tag1, tag2" descriptions → tags
  $topicTags = function ($desc) {
      $parts = preg_split('/\s+—\s+/u', (string) $desc);
      if (count($parts) < 2) return [];
      return collect(explode(',', end($parts)))->map(fn ($x) => trim($x))->filter()->take(2)->all();
  };
  $hour = (int) now()->format('G');
  $greet = $hour < 12 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat tengah hari' : ($hour < 19 ? 'Selamat petang' : 'Selamat malam'));
  $firstName = explode(' ', trim(auth()->user()->name ?? 'Admin'))[0];
  $feedbackKind = function ($f) {
      if (filled($f->issue_report)) return ['!', 'is', 'Report issue', $f->issue_report];
      if (filled($f->feature_request)) return ['★', 'ft', 'Feature request', $f->feature_request];
      return ['✉', 'fb', 'Feedback', $f->feedback];
  };
  $maxOnline = max(1, max($onlineToday ?: [0]));
@endphp

<div class="adm-wrap">

  <x-admin-hero :title="$greet . ', ' . e($firstName) . ' 👋'" :sub="now()->translatedFormat('l, j M Y') . ' · RakanKampus is answering students right now'"
    :kpis="[
      ['icon' => 'users', 'value' => number_format($studentCount), 'label' => 'Students', 'tag' => $newStudents ? '+' . $newStudents . ' this week' : null, 'spark' => $signupSpark, 'href' => route('admin.analytics')],
      ['icon' => 'chat', 'value' => number_format($questionsAsked), 'label' => 'Questions asked', 'href' => route('admin.analytics')],
      ['icon' => 'book', 'value' => $informations->count() . ' · ' . number_format($entryCount), 'label' => 'Topics · Q&A entries', 'href' => '#topics'],
      ['icon' => 'alert', 'value' => $unansweredCount, 'label' => 'Unanswered', 'tag' => $unansweredCount ? 'needs you' : null, 'tone' => 'red', 'href' => route('admin.unanswered.index')],
      ['icon' => 'check', 'value' => $answeredRate === null ? '—' : $answeredRate . '%', 'label' => 'Answered by bot'],
    ]">
    <x-slot:actions>
      <label class="adm-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
        <input type="text" placeholder="Search topics…" id="cardSearchInput" oninput="filterCards()"></label>
      <button type="button" class="adm-btn-light" onclick="openModal()">＋ Add topic</button>
    </x-slot:actions>
  </x-admin-hero>

  @if(session('status'))
    <div class="adm-flash">{{ session('status') }}</div>
  @endif

  <div class="dash-grid">
    <section class="adm-card" id="topics">
      <h2 class="adm-card-h">📚 Knowledge base topics <span style="font-size:12.5px;color:#64748b;font-weight:600">{{ $informations->count() }} topics</span></h2>
      <div class="topics" id="cardsGrid">
        @foreach($informations as $info)
          @php [$ico, $bg] = $topicLook($info->main_topic); $tags = $topicTags($info->description); @endphp
          <div class="tp">
            <span class="tp-ic" style="background: {{ $bg }}">{{ $ico }}</span>
            <a class="tp-body" href="{{ route('admin.information.show', $info->id) }}">
              <b title="{{ $info->main_topic }}">{{ $info->main_topic }}</b>
              <small>{{ $info->knowledge_entries_count }} {{ \Illuminate\Support\Str::plural('entry', $info->knowledge_entries_count) }} · updated {{ $info->updated_at?->diffForHumans() }}</small>
              @if(count($tags))<span class="tp-tags">@foreach($tags as $tg)<i>{{ $tg }}</i>@endforeach</span>@endif
            </a>
            <div class="tp-actions">
              <a href="{{ route('admin.information.show', $info->id) }}" class="icon-btn view-btn" aria-label="View" title="Open"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg></a>
              <button type="button" class="icon-btn edit-btn" aria-label="Edit" title="Edit"
                onclick="openEditInfoModal({{ $info->id }}, {{ json_encode($info->main_topic) }}, {{ json_encode($info->description) }})"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button>
              <form method="POST" action="{{ route('admin.information.destroy', $info->id) }}">
                @csrf
                @method('DELETE')
                <button type="button" class="icon-btn delete-btn" aria-label="Delete" title="Delete"
                  onclick="openDeleteModal(this.closest('form'), {{ json_encode('Delete '.$info->main_topic.'?') }}, 'This will also delete all its knowledge base entries. This cannot be undone.')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg></button>
              </form>
            </div>
          </div>
        @endforeach
        <button type="button" class="add-tile" onclick="openModal()">＋ Add a new topic</button>
      </div>
      <p class="no-results" id="noResultsMsg">No matching topics found.</p>
    </section>

    <div class="dash-side">
      <section class="adm-card">
        <h2 class="adm-card-h">❓ Needs an answer <a href="{{ route('admin.unanswered.index') }}">See all {{ $unansweredCount }} →</a></h2>
        @forelse($pendingQuestions as $q)
          <div class="row"><span class="qi">?</span>
            <div><p>{{ $q->question }}</p><small>Asked {{ $q->asked_count }}× · {{ $q->updated_at?->diffForHumans() }}</small></div>
            <a class="ans" href="{{ route('admin.unanswered.index') }}#q{{ $q->id }}">Answer</a></div>
        @empty
          <div class="adm-empty"><b>All caught up 🎉</b>The bot has an answer for everything students asked.</div>
        @endforelse
      </section>

      <section class="adm-card">
        <h2 class="adm-card-h">✉️ Latest feedback <a href="{{ route('admin.inbox') }}">Inbox →</a></h2>
        @forelse($latestFeedback as $f)
          @php [$sym, $cls, $kind, $text] = $feedbackKind($f); @endphp
          <div class="row"><span class="qi {{ $cls }}">{{ $sym }}</span>
            <div><p>“{{ \Illuminate\Support\Str::limit($text, 90) }}”</p><small>{{ $kind }} · {{ $f->user_name ?: 'Student' }} · {{ $f->created_at?->diffForHumans() }}</small></div>
            @unless($f->is_read)<span class="new" title="Unread"></span>@endunless</div>
        @empty
          <div class="adm-empty"><b>No feedback yet</b>Student feedback and feature requests show up here.</div>
        @endforelse
      </section>

      <section class="adm-card">
        <h2 class="adm-card-h">📈 Students online today <a href="{{ route('admin.analytics') }}">Analytics →</a></h2>
        <div class="bars">
          @foreach($onlineToday as $h => $n)
            <span class="bar {{ $h === (int) now()->format('G') ? 'now' : '' }}" style="height: {{ max(4, round($n / $maxOnline * 96)) }}px"
                  data-tip="{{ \Illuminate\Support\Carbon::createFromTime($h)->format('g A') }} · {{ $n }} {{ \Illuminate\Support\Str::plural('student', $n) }}"></span>
          @endforeach
        </div>
        <div class="hours"><span>8am</span><span>12pm</span><span>4pm</span><span>8pm</span><span>10pm</span></div>
        @if(array_sum($onlineToday) === 0)<p class="bars-empty">No student activity yet today.</p>@endif
      </section>
    </div>
  </div>

    <!-- Add Information Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Add New Information</h2>
                <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
            </div>

            <form action="{{ route('admin.information.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Main Topic</label>
                    <input type="text" name="main_topic"
                           placeholder="e.g. Course Registration" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="6"
                              placeholder="Write the description here..." required></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="submit-btn">Submit</button>
                </div>

            </form>

        </div>
    </div>

    <!-- Edit Information Modal -->
    <div id="editInfoModal" class="modal">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Edit Information</h2>
                <button type="button" class="close-btn" onclick="closeEditInfoModal()">&times;</button>
            </div>

            <form id="editInfoForm" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Main Topic</label>
                    <input type="text" name="main_topic" id="edit_info_main_topic" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_info_description" rows="6" required></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="cancel-btn" onclick="closeEditInfoModal()">Cancel</button>
                    <button type="submit" class="submit-btn">Save</button>
                </div>

            </form>

        </div>
    </div>


</div>

<!-- Delete Confirm Modal -->
<div id="deleteConfirmModal" class="modal">
    <div class="modal-content" style="max-width:380px; text-align:center;">
        <div class="confirm-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m5 0V4a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
        </div>
        <h2 class="confirm-title" id="deleteConfirmTitle">Delete this item?</h2>
        <p class="confirm-message" id="deleteConfirmMessage">This action cannot be undone.</p>
        <div class="modal-actions">
            <button type="button" class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="delete-confirm-btn" onclick="confirmDeleteAction()">Delete</button>
        </div>
    </div>
</div>

<script>
  let __deleteFormTarget = null;

  function openDeleteModal(formEl, title, message) {
      __deleteFormTarget = formEl;
      document.getElementById('deleteConfirmTitle').textContent = title || 'Delete this item?';
      document.getElementById('deleteConfirmMessage').textContent = message || 'This action cannot be undone.';
      document.getElementById('deleteConfirmModal').style.display = 'flex';
  }

  function closeDeleteModal() {
      document.getElementById('deleteConfirmModal').style.display = 'none';
      __deleteFormTarget = null;
  }

  function confirmDeleteAction() {
      if (__deleteFormTarget) __deleteFormTarget.submit();
      closeDeleteModal();
  }

  window.addEventListener('click', function(e) {
      const m = document.getElementById('deleteConfirmModal');
      if (e.target === m) closeDeleteModal();
  });
</script>

<script>
  function openModal() {
      document.getElementById('addModal').style.display = 'flex';
  }

  function closeModal() {
      document.getElementById('addModal').style.display = 'none';
  }

  function openEditInfoModal(id, mainTopic, description) {
      document.getElementById('edit_info_main_topic').value = mainTopic;
      document.getElementById('edit_info_description').value = description;

      document.getElementById('editInfoForm').action =
          "{{ route('admin.information.update', '__ID__') }}".replace('__ID__', id);

      document.getElementById('editInfoModal').style.display = 'flex';
  }

  function closeEditInfoModal() {
      document.getElementById('editInfoModal').style.display = 'none';
  }

  window.onclick = function(event) {
      const addModal = document.getElementById('addModal');
      const editInfoModal = document.getElementById('editInfoModal');
      if (event.target === addModal) closeModal();
      if (event.target === editInfoModal) closeEditInfoModal();
  }

  function filterCards() {
      const query = document.getElementById('cardSearchInput').value.toLowerCase();
      const cards = document.querySelectorAll('#cardsGrid > .tp');
      const noResultsMsg = document.getElementById('noResultsMsg');

      let visibleCount = 0;

      cards.forEach(card => {
          const matches = card.textContent.toLowerCase().includes(query);
          card.style.display = matches ? 'flex' : 'none';
          if (matches) visibleCount++;
      });

      noResultsMsg.style.display = visibleCount === 0 ? 'block' : 'none';
  }
</script>

</body>
</html>
