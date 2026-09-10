@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="custom-pagination">
        <div class="pagination-container">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="pagination-btn disabled" aria-disabled="true" aria-label="السابق">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn" rel="prev" aria-label="السابق">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            <div class="pagination-numbers">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="pagination-dots">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="pagination-number active" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pagination-number" aria-label="الذهاب إلى الصفحة {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn" rel="next" aria-label="التالي">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @else
                <span class="pagination-btn disabled" aria-disabled="true" aria-label="التالي">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @endif
        </div>

        {{-- Results Info --}}
        <div class="pagination-info">
            <p>
                عرض 
                <span class="font-semibold">{{ $paginator->firstItem() }}</span>
                إلى
                <span class="font-semibold">{{ $paginator->lastItem() }}</span>
                من
                <span class="font-semibold">{{ $paginator->total() }}</span>
                نتيجة
            </p>
        </div>
    </nav>

    <style>
        .custom-pagination {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            margin-top: 24px;
            padding: 20px 0;
        }

        .pagination-container {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            padding: 8px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .pagination-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: linear-gradient(135deg, #8BC34A 0%, #689F38 100%); /* Apple Green to Olive Green */
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .pagination-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(104, 159, 56, 0.4); /* Green Shadow */
        }

        .pagination-btn.disabled {
            background: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .pagination-btn.disabled:hover {
            transform: none;
            box-shadow: none;
        }

        .pagination-numbers {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .pagination-number {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            border-radius: 8px;
            background: #f3f4f6;
            color: #374151;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .pagination-number:hover {
            background: #e5e7eb;
            color: #1f2937;
            transform: translateY(-1px);
        }

        .pagination-number.active {
            background: linear-gradient(135deg, #8BC34A 0%, #689F38 100%); /* Apple Green to Olive Green */
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(104, 159, 56, 0.3); /* Green Shadow */
            font-weight: 600;
        }

        .pagination-dots {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            color: #9ca3af;
            font-weight: 600;
            font-size: 16px;
        }

        .pagination-info {
            text-align: center;
        }

        .pagination-info p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        .pagination-info .font-semibold {
            color: #374151;
            font-weight: 600;
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .pagination-container {
                background: #1f2937;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            }

            .pagination-number {
                background: #374151;
                color: #e5e7eb;
            }

            .pagination-number:hover {
                background: #4b5563;
                color: #f3f4f6;
            }

            .pagination-info p {
                color: #9ca3af;
            }

            .pagination-info .font-semibold {
                color: #e5e7eb;
            }
        }

        /* Responsive */
        @media (max-width: 640px) {
            .pagination-container {
                padding: 6px;
                gap: 4px;
            }

            .pagination-btn,
            .pagination-number {
                min-width: 36px;
                height: 36px;
                font-size: 13px;
            }

            .pagination-info p {
                font-size: 13px;
            }
        }
    </style>
@endif
