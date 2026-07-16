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

<<<<<<< Updated upstream
<<<<<<< Updated upstream
=======
        /* Spasi/line-height dikoreksi lewat JS (samakanSpasiWord) agar identik
           dengan tampilan Word — jangan timpa dengan CSS !important di sini. */

>>>>>>> Stashed changes
=======
        /* Spasi/line-height dikoreksi lewat JS (samakanSpasiWord) agar identik
           dengan tampilan Word — jangan timpa dengan CSS !important di sini. */

>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
<<<<<<< Updated upstream
=======
=======
>>>>>>> Stashed changes
        /**
         * Samakan lebar kolom tabel dengan Microsoft Word.
         *
         * Tabel dengan w:tblW="auto" (autofit) di Word memakai lebar sel (w:tcW),
         * bukan w:tblGrid. docx-preview justru menulis <colgroup> dari tblGrid —
         * bila grid template tidak sinkron dengan tcW (mis. tiga kolom sama lebar),
         * kolom ":" jadi sangat lebar dan nilainya terdorong jauh ke kanan.
         * Hapus <colgroup> pada tabel non-fixed agar lebar sel (sudah ditulis
         * docx-preview di tiap <td>) yang menentukan, persis perilaku Word.
         */
        function samakanLebarKolomWord() {
            viewer.querySelectorAll('.docx-wrapper table').forEach(function (tbl) {
                if (tbl.style.tableLayout === 'fixed') return;
                tbl.querySelectorAll(':scope > colgroup').forEach(function (cg) { cg.remove(); });
            });
        }

        /**
         * Samakan spasi hasil docx-preview dengan Microsoft Word.
         *
         * Word merender "single spacing" (w:line=240, lineRule=auto) setinggi
         * metrik alami font — untuk Arial ±1,15 em — sedangkan docx-preview
         * menghasilkan line-height 1.0 (nilai w:line / 240). Paragraf kosong
         * di Word juga tetap setinggi satu baris penuh (1,15 em), sementara
         * docx-preview hanya memberi min-height 1 em.
         *
         * Koreksi: kalikan semua line-height & min-height tanpa satuan/pt-class
         * dengan faktor 1,15. Line-height dengan satuan pt/px pada inline style
         * (lineRule "exact"/"atLeast") dibiarkan karena sudah akurat.
         */
        function samakanSpasiWord() {
            const FAKTOR = 1.15;
            viewer.querySelectorAll('.docx-wrapper p').forEach(function (p) {
                const inline = p.style.lineHeight || '';
                if (/pt|px|%|em/.test(inline)) return; // lineRule exact/atLeast → sudah sesuai Word

                const cs = window.getComputedStyle(p);
                const fontSize = parseFloat(cs.fontSize);
                const lineHeight = parseFloat(cs.lineHeight); // NaN bila "normal"

                if (fontSize && !isNaN(lineHeight)) {
                    const rasio = lineHeight / fontSize;
                    p.style.lineHeight = (Math.round(rasio * FAKTOR * 1000) / 1000).toString();
                }

                const minHeight = parseFloat(cs.minHeight);
                if (!isNaN(minHeight) && minHeight > 0) {
                    p.style.minHeight = (Math.round(minHeight * FAKTOR * 100) / 100) + 'px';
                }
            });
        }

<<<<<<< Updated upstream
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
<<<<<<< Updated upstream
=======
                samakanLebarKolomWord();
                samakanSpasiWord();
>>>>>>> Stashed changes
=======
                samakanLebarKolomWord();
                samakanSpasiWord();
>>>>>>> Stashed changes
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
