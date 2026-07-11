<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak — {{ $surat->nomor_surat }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.3.5/dist/docx-preview.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body { background: #525659; font-family: system-ui, "Segoe UI", sans-serif; }

        #toolbar {
            position: fixed; top: 0; left: 0; right: 0; height: 52px; z-index: 10;
            background: #1C6B47; color: #fff; display: flex; align-items: center; gap: 12px;
            padding: 0 18px; box-shadow: 0 2px 8px rgba(0, 0, 0, .3);
        }
        #toolbar .title { font-weight: 600; font-size: 14px; }
        #toolbar .spacer { flex: 1; }
        #toolbar button, #toolbar a {
            font-size: 13px; font-weight: 600; border: 0; border-radius: 6px;
            padding: 8px 16px; cursor: pointer; text-decoration: none; background: #fff; color: #1C6B47;
        }
        #toolbar button:disabled { opacity: .6; cursor: default; }
        #toolbar a.ghost { background: transparent; color: #fff; border: 1px solid rgba(255, 255, 255, .55); }

        #status { color: #fff; text-align: center; padding: 90px 20px; font-size: 15px; }

        #viewer { padding: 72px 12px 32px; display: flex; justify-content: center; }
        .docx-wrapper { background: transparent !important; padding: 0 !important; }
        .docx-wrapper > section.docx {
            background: #fff; margin: 0 auto 16px; box-shadow: 0 4px 16px rgba(0, 0, 0, .35);
        }

        @media print {
            body { background: #fff; }
            #toolbar, #status { display: none !important; }
            #viewer { padding: 0; }
            .docx-wrapper > section.docx { box-shadow: none; margin: 0; }
        }
    </style>
</head>

<body>
    <div id="toolbar">
        <span class="title">Cetak Surat &mdash; {{ $surat->nomor_surat }}</span>
        <span class="spacer"></span>
        <button id="btn-print" onclick="window.print()" disabled>Cetak (Ctrl+P)</button>
        <a class="ghost" href="{{ route('surat.download', $surat) }}">Unduh .docx</a>
        <a class="ghost" href="{{ route('surat.show', $surat) }}">Tutup</a>
    </div>

    <div id="status">Memuat &amp; menyiapkan surat untuk dicetak…</div>
    <div id="viewer"></div>

    <script>
        const docxUrl = @json($docxUrl);
        const viewer = document.getElementById('viewer');
        const statusEl = document.getElementById('status');
        const btnPrint = document.getElementById('btn-print');

        fetch(docxUrl, { credentials: 'same-origin' })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.blob();
            })
            .then(function (blob) {
                return docx.renderAsync(blob, viewer, null, {
                    inWrapper: true,
                    ignoreWidth: false,
                    ignoreHeight: false,
                    breakPages: true,
                });
            })
            .then(function () {
                statusEl.style.display = 'none';
                btnPrint.disabled = false;
                setTimeout(function () { window.print(); }, 500);
            })
            .catch(function (err) {
                statusEl.innerHTML = 'Gagal memuat surat: ' + err.message +
                    '<br><a style="color:#fff" href="{{ route('surat.download', $surat) }}">Unduh .docx</a>';
            });
    </script>
</body>

</html>
