@extends('layouts.dashboard')
@section('content')

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap');

        :root {
            --wood: #c8a96e;
            --wood-dark: #a0783a;
            --wood-light: #e2c98a;
            --wall: #f7f5f1;
            --ink: #2c2825;
            --gold: #000000;
            --danger: #000000;
        }

        * {
            box-sizing: border-box;
        }

        /* ── Page background: warm off-white wall ── */
        .shelf-page {
            background: var(--wall);
            min-height: 100vh;
            padding: 36px 32px 60px;
            font-family: 'DM Sans', sans-serif;
            position: relative;
        }

        /* Subtle wall texture */
        .shelf-page::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(201, 168, 76, 0.04) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(160, 120, 58, 0.04) 0%, transparent 40%);
            pointer-events: none;
            z-index: 0;
        }

        .shelf-page>* {
            position: relative;
            z-index: 1;
        }

        /* ── Header ── */
        .shelf-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 44px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .shelf-title-block .eyebrow {
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 4px;
            opacity: 0.8;
        }

        .shelf-title-block h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(22px, 3vw, 36px);
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
            margin: 0;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: var(--ink);
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.18s, transform 0.15s, box-shadow 0.18s;
            box-shadow: 0 2px 8px rgba(44, 40, 37, 0.18);
            letter-spacing: 0.3px;
        }

        .btn-add:hover {
            background: #1a1410;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(44, 40, 37, 0.22);
            color: #fff;
            text-decoration: none;
        }

        .btn-add svg {
            width: 15px;
            height: 15px;
        }

        /* ── Shelf row ── */
        .shelf-row {
            position: relative;
            margin-bottom: 56px;
        }

        /* The wooden plank */
        .shelf-plank {
            position: relative;
            padding: 0 16px 28px;
        }



        /* Shelf bracket left & right */
        .shelf-bracket {
            position: absolute;
            bottom: 0;
            width: 14px;
            height: 50px;
            background: linear-gradient(to right, #b0b0b0, #d8d8d8, #b0b0b0);
            border-radius: 2px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            z-index: 3;
        }

        .shelf-bracket::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 6px;
            background: #a0a0a0;
            border-radius: 0 0 3px 3px;
        }

        .shelf-bracket.left {
            left: 30px;
        }

        .shelf-bracket.right {
            right: 30px;
        }

        /* ── Books grid ── */
        .books-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 20px;
            padding-bottom: 10px;
            align-items: flex-end;
            position: relative;
            z-index: 1;
        }

        /* ── Single book card ── */
        .book-card {
            position: relative;
            cursor: pointer;
            animation: riseUp 0.5s ease both;
            min-width: 0;
        }

        @keyframes riseUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .book-card:nth-child(1) {
            animation-delay: 0.05s;
        }

        .book-card:nth-child(2) {
            animation-delay: 0.10s;
        }

        .book-card:nth-child(3) {
            animation-delay: 0.15s;
        }

        .book-card:nth-child(4) {
            animation-delay: 0.20s;
        }

        .book-card:nth-child(5) {
            animation-delay: 0.25s;
        }

        .book-card:nth-child(6) {
            animation-delay: 0.30s;
        }

        .book-card:hover .book-cover {
            transform: translateY(-10px) ;
            box-shadow:
                -4px 0 0 0 rgba(0, 0, 0, 0.15),
                0 20px 40px rgba(0, 0, 0, 0.22),
                0 8px 16px rgba(0, 0, 0, 0.14);
        }

        .book-card:hover .book-actions {
            opacity: 1;
            transform: translateY(0);
        }

        /* Book cover wrapper */
        .book-cover-wrap {
            position: relative;
            perspective: 800px;
        }

        /* The cover itself */
        .book-cover {
            width: 100%;
            aspect-ratio: 3/4;
            background: #e8e0d4;
            border-radius: 2px 4px 4px 2px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow:
                -3px 0 0 0 rgba(0, 0, 0, 0.12),
                0 10px 24px rgba(0, 0, 0, 0.15),
                0 4px 8px rgba(0, 0, 0, 0.10);
            position: relative;
        }

        /* Spine edge on left of cover */
        .book-cover::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 6px;
            background: linear-gradient(to right,
                    rgba(0, 0, 0, 0.20),
                    rgba(0, 0, 0, 0.06),
                    transparent);
            z-index: 2;
        }

        /* Page edges on right */
        .book-cover::after {
            content: '';
            position: absolute;
            top: 2px;
            right: -4px;
            bottom: 2px;
            width: 5px;
            background: repeating-linear-gradient(to bottom,
                    #f0ece4 0px, #f0ece4 1.5px,
                    #e0dbd2 1.5px, #e0dbd2 2px);
            border-radius: 0 1px 1px 0;
            z-index: 0;
        }

        .book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* PDF thumbnail via iframe */
        .book-cover iframe {
            width: 100%;
            height: 100%;
            border: none;
            pointer-events: none;
            display: block;
            transform: scale(1);
            transform-origin: top left;
        }

        /* Placeholder when no cover */
        .book-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #e8e0d4, #d4ccc0);
            padding: 16px;
            text-align: center;
        }

        .book-placeholder svg {
            width: 32px;
            height: 32px;
            opacity: 0.3;
        }

        .book-placeholder span {
            font-family: 'Playfair Display', serif;
            font-size: 13px;
            font-weight: 600;
            color: #6e6458;
            line-height: 1.3;
            word-break: break-word;
        }

        /* Title label below cover */
        .book-label-title {
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            font-weight: 500;
            color: #6e6458;
            text-align: center;
            margin-top: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 4px;
        }

        /* ── Hover action buttons — below cover, centered ── */
        .book-actions {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
            opacity: 0;
            transform: translateY(6px);
            transition: opacity 0.22s ease, transform 0.22s ease;
            z-index: 10;
            position: relative;
        }

        .book-card:hover .book-actions {
            opacity: 1;
            transform: translateY(0);
        }

        /* No overlay needed anymore */
        .cover-overlay {
            display: none;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.15s;
            z-index: 11;
            position: relative;
        }

        .action-btn:hover {
            transform: scale(1.1);
            text-decoration: none;
        }

        .btn-view {
            background: #fff;
            color: var(--ink);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-edit {
            background: var(--gold);
            color: #fff;
            box-shadow: 0 2px 8px rgba(201, 168, 76, 0.35);
        }

        .btn-del {
            background: var(--danger);
            color: #fff;
            border: none;
            box-shadow: 0 2px 8px rgba(217, 79, 79, 0.35);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
        }

        .action-btn svg,
        .btn-del svg {
            width: 15px;
            height: 15px;
            pointer-events: none;
        }

        /* ── Empty state ── */
        .empty-shelf {
            text-align: center;
            padding: 60px 20px;
            color: #b0a898;
        }

        .empty-shelf svg {
            width: 56px;
            height: 56px;
            opacity: 0.25;
            margin-bottom: 16px;
        }

        .empty-shelf p {
            font-size: 15px;
        }

        /* ── Responsive ── */
        @media(max-width: 1024px) {
            .books-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media(max-width: 768px) {
            .shelf-page {
                padding: 24px 16px 40px;
            }

            .books-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .shelf-bracket {
                display: none;
            }
        }

        @media(max-width: 480px) {
            .books-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <div class="shelf-page">

        <!-- Header -->
        <div class="shelf-header">
            <div class="shelf-title-block">
                <h1>All Flipbooks</h1>
            </div>
            <a href="{{ route('admin.flipbooks.create') }}" class="btn-add">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Flipbook
            </a>
        </div>

        @if ($flipbooks->isEmpty())
            <div class="empty-shelf">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <p>No flipbooks yet. Add your first one!</p>
            </div>
        @else
            {{-- Split into rows of 3 for shelf effect --}}
            @php $chunks = $flipbooks->chunk(6); @endphp

            @foreach ($chunks as $row)
                <div class="shelf-row">

                    <div class="shelf-plank">
                        <div class="books-grid">
                            @foreach ($row as $flipbook)
                                <div class="book-card">
                                    <a href="{{ route('admin.flipbooks.show', $flipbook->id) }}"
                                        style="text-decoration:none;">
                                        <div class="book-cover-wrap">
                                            <div class="book-cover">
                                                {{-- PDF preview via iframe --}}
                                                <iframe
                                                    src="{{ asset('storage/' . $flipbook->file_path) }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH"
                                                    loading="lazy" title="{{ $flipbook->title }}">
                                                </iframe>
                                            </div><!-- /.book-cover -->
                                        </div><!-- /.book-cover-wrap -->
                                    </a>

                                    <!-- Title -->
                                    <div class="book-label-title">{{ $flipbook->title }}</div>

                                    <!-- Action buttons — appear below on hover, centered -->
                                    <div class="book-actions">
                                        <!-- View -->
                                        <a href="{{ route('admin.flipbooks.show', $flipbook->id) }}"
                                            class="action-btn btn-view" title="View">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('admin.flipbooks.edit', $flipbook->id) }}"
                                            class="action-btn btn-edit" title="Edit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </a>

                                        <!-- Delete -->
                                        <form action="{{ route('admin.flipbooks.destroy', $flipbook->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this flipbook?')" style="margin:0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-del" title="Delete">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                                    <path d="M10 11v6M14 11v6"></path>
                                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div><!-- /.book-actions -->
                                </div><!-- /.book-card -->
                            @endforeach
                        </div><!-- /.books-grid -->
                    </div><!-- /.shelf-plank -->
                </div><!-- /.shelf-row -->
            @endforeach
        @endif

    </div><!-- /.shelf-page -->

@endsection
