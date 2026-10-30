<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->title }}</title>
    @php
            $typeLabels = [
                'single_choice'   => ['en' => 'Single choice',   'badge' => 'type-single'],
                'multiple_choice' => ['en' => 'Multiple choice', 'badge' => 'type-multiple'],
                'true_false'      => ['en' => 'True/False',      'badge' => 'type-truefalse'],
                'short_answer'    => ['en' => 'Short answer',    'badge' => 'type-short'],
                'essay'           => ['en' => 'Essay',           'badge' => 'type-essay'],
            ];
        @endphp
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #1e293b;
            padding: 40px;
            line-height: 1.6;
        }

        .header {
            border-bottom: 2px solid #16a34a;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .header-meta {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .header-title {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .info-row {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            font-size: 11px;
            color: #475569;
        }

        .info-row span { font-weight: bold; }
        .info-row strong { color: #0f172a; }

        .instructions {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #16a34a;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 24px;
            font-size: 11px;
            color: #475569;
        }

        .instructions strong {
            color: #0f172a;
            display: block;
            margin-bottom: 2px;
            font-size: 12px;
        }

        .question-block {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .question-head {
            display: flex;
            gap: 10px;
            margin-bottom: 8px;
        }

       .question-number {
        background: #0f172a;
        color: #fff;
        font-size: 10px;
        font-weight: 900;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        text-align: center;
        line-height: 22px;
        flex-shrink: 0;
                        }

        .question-content {
            font-weight: 700;
            color: #0f172a;
            flex: 1;
        }

        .question-meta {
            font-size: 10px;
            color: #94a3b8;
            margin-left: 32px;
            margin-bottom: 8px;
        }

            .options {
            margin-left: 32px;
            width: calc(100% - 32px);
        }

        .options-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .option-cell {
            width: 50%;
            padding: 3px 12px 3px 0;
            vertical-align: top;
        }

        .option-label {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 1.5px solid #cbd5e1;
            border-radius: 50%;
            text-align: center;
            line-height: 16px;
            font-size: 9px;
            font-weight: 900;
            color: #64748b;
            vertical-align: middle;
        }

        .option-label.square {
            border-radius: 3px;
        }

        .option-text {
            font-size: 12px;
            color: #334155;
            vertical-align: middle;
            padding-left: 4px;
        }

        .answer-blank {
            margin-left: 32px;
            border-bottom: 1.5px solid #cbd5e1;
            height: 32px;
            margin-top: 6px;
        }

        .divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 16px 0;
        }

        .footer {
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #94a3b8;
        }

        .type-badge {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1px 6px;
            border-radius: 99px;
            margin-left: 4px;
        }

        .type-single   { background: #dcfce7; color: #15803d; }
        .type-multiple { background: #dbeafe; color: #1d4ed8; }
        .type-truefalse{ background: #fef9c3; color: #a16207; }
        .type-short    { background: #f1f5f9; color: #475569; }
        .type-essay    { background: #fce7f3; color: #be185d; }

        
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <p class="header-meta">
            {{ $exam->subject ?: 'Exam' }}
            @if($exam->topic) — {{ $exam->topic }}@endif
        </p>
        <h1 class="header-title">{{ $exam->title }}</h1>
        <div class="info-row">
            <div><span>Duration:</span> <strong>{{ $exam->duration_minutes }} minutes</strong></div>
            <div><span>Questions:</span> <strong>{{ $exam->total_questions }}</strong></div>
            <div><span>Total points:</span> <strong>{{ $exam->total_points }}</strong></div>
            <div><span>Exam date:</span> <strong>{{ $exam->starts_at?->format('M j, Y') ?? '____________' }}</strong></div>
        </div>
    </div>

    {{-- Candidate information --}}
    <div class="instructions">
        <strong>Candidate information</strong>
        Name: __________________________________ &nbsp;&nbsp;&nbsp;
        Student ID: ______________ &nbsp;&nbsp;&nbsp;
        Class: ______________
    </div>

    {{-- Questions --}}
    @foreach($exam->questions as $index => $question)
        @php
            $type    = $question->type;
            $meta    = $typeLabels[$type] ?? ['en' => $type, 'badge' => 'type-short'];
            $options = $question->options ?? [];
            $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
        @endphp

        <div class="question-block">
            <div class="question-head">
                <div class="question-number">{{ $index + 1 }}</div>
                <div class="question-content">
                    {{ $question->content }}
                </div>
            </div>

            <div class="question-meta">
                {{ number_format($question->points, 2) }} points
                @if($question->difficulty)
                    &middot;
                    @php $diffMap = ['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard']; @endphp
                    {{ $diffMap[$question->difficulty] ?? $question->difficulty }}
                @endif
            </div>

            {{-- Single / Multiple choice --}}
            @if(in_array($type, ['single_choice', 'multiple_choice']) && !empty($options))
    @php $optionChunks = array_chunk($options, 2); @endphp
    <div class="options">
        <table class="options-table">
            @foreach($optionChunks as $chunkIndex => $row)
            <tr>
                @foreach($row as $i => $option)
                    @php $globalIndex = ($chunkIndex * 2) + $i; @endphp
                    <td class="option-cell">
                        <span class="option-label {{ $type === 'multiple_choice' ? 'square' : '' }}">{{ $letters[$globalIndex] ?? ($globalIndex + 1) }}</span>
                        <span class="option-text">{{ is_array($option) ? ($option['text'] ?? '') : $option }}</span>
                    </td>
                @endforeach
                @if(count($row) === 1)
                    <td class="option-cell"></td>
                @endif
            </tr>
            @endforeach
        </table>
    </div>

            {{-- True / False --}}
            @elseif($type === 'true_false')
                <div class="options">
                    <div class="option">
                        <div class="option-label">A</div>
                        <div class="option-text">True</div>
                    </div>
                    <div class="option">
                        <div class="option-label">B</div>
                        <div class="option-text">False</div>
                    </div>
                </div>

            {{-- Short answer / Essay --}}
            @else
                <div class="answer-blank"></div>
                @if($type === 'essay')
                    <div class="answer-blank" style="margin-top:6px"></div>
                    <div class="answer-blank" style="margin-top:6px"></div>
                @endif
            @endif
        </div>

        @if(!$loop->last)
            <hr class="divider">
        @endif
    @endforeach

    {{-- Footer --}}
    <div class="footer">
        <span>{{ $exam->title }}</span>
        <span>Printed {{ now()->format('g:i A · M j, Y') }}</span>
    </div>

</body>
</html>