<div id="filePreviewModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-70 overflow-y-auto h-full w-full" style="z-index:9999">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="filePreviewTitle" class="text-lg font-medium text-gray-900">Pratinjau Dokumen</h3>
            <button type="button" onclick="hideFileModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <div id="filePreviewToolbar" class="hidden items-center gap-2 mb-2 flex-wrap">
            <div id="filePreviewPager" class="hidden items-center gap-1">
                <button type="button" onclick="filePdfPrev()" title="Halaman sebelumnya" class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 text-sm">&lsaquo;</button>
                <span class="text-xs text-gray-600 px-1">Hal. <span id="filePdfPage">1</span> / <span id="filePdfPages">1</span></span>
                <button type="button" onclick="filePdfNext()" title="Halaman berikutnya" class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 text-sm">&rsaquo;</button>
            </div>
            <button type="button" onclick="fileZoomOut()" title="Perkecil" class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 text-sm">&minus;</button>
            <span id="filePreviewZoomLevel" class="text-xs text-gray-600 w-12 text-center">100%</span>
            <button type="button" onclick="fileZoomIn()" title="Perbesar" class="px-3 py-1 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 text-sm">+</button>
            <button type="button" onclick="fileZoomReset()" class="px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-xs">Reset</button>
            <span class="text-xs text-gray-400 ml-auto">Ctrl + scroll untuk zoom, seret untuk geser</span>
        </div>

        <div id="filePreviewBody" class="max-h-[70vh] overflow-auto bg-gray-50 rounded flex items-center justify-center p-2"></div>

        <div class="flex justify-end mt-4">
            <button type="button" onclick="hideFileModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Tutup</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
if (window.pdfjsLib) {
    window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
}

var fileZoomScale = 1;
var filePreviewMode = null;
var filePdfDoc = null;
var filePdfPageNum = 1;
var filePdfRendering = false;

function fileSetToolbar(show, withPager) {
    var bar = document.getElementById('filePreviewToolbar');
    var pager = document.getElementById('filePreviewPager');
    bar.classList.toggle('hidden', !show);
    bar.classList.toggle('flex', !!show);
    pager.classList.toggle('hidden', !withPager);
    pager.classList.toggle('flex', !!withPager);
}

function fileUpdateZoomLabel() {
    document.getElementById('filePreviewZoomLevel').textContent = Math.round(fileZoomScale * 100) + '%';
}

function fileApplyZoom() {
    fileUpdateZoomLabel();
    if (filePreviewMode === 'image') {
        var img = document.getElementById('filePreviewImage');
        if (!img) return;
        img.style.transformOrigin = 'center center';
        img.style.transform = 'scale(' + fileZoomScale + ')';
    } else if (filePreviewMode === 'pdf') {
        filePdfRender();
    }
}

function fileZoomIn() {
    fileZoomScale = Math.min(5, fileZoomScale + 0.25);
    fileApplyZoom();
}

function fileZoomOut() {
    fileZoomScale = Math.max(0.25, fileZoomScale - 0.25);
    fileApplyZoom();
}

function fileZoomReset() {
    fileZoomScale = 1;
    fileApplyZoom();
    var body = document.getElementById('filePreviewBody');
    body.scrollTop = 0;
    body.scrollLeft = 0;
}

function filePdfRender() {
    if (!filePdfDoc || filePdfRendering) return;
    filePdfRendering = true;

    filePdfDoc.getPage(filePdfPageNum).then(function (page) {
        var body = document.getElementById('filePreviewBody');
        var base = page.getViewport({ scale: 1 });
        var fit = Math.max(0.2, (body.clientWidth - 24) / base.width);
        var viewport = page.getViewport({ scale: fit * fileZoomScale });

        var canvas = document.getElementById('filePdfCanvas');
        if (!canvas) {
            filePdfRendering = false;
            return;
        }
        var ctx = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        canvas.style.width = viewport.width + 'px';
        canvas.style.height = viewport.height + 'px';

        return page.render({ canvasContext: ctx, viewport: viewport }).promise;
    }).then(function () {
        filePdfRendering = false;
        document.getElementById('filePdfPage').textContent = filePdfPageNum;
    }).catch(function () {
        filePdfRendering = false;
    });
}

function filePdfPrev() {
    if (!filePdfDoc || filePdfPageNum <= 1) return;
    filePdfPageNum--;
    filePdfRender();
}

function filePdfNext() {
    if (!filePdfDoc || filePdfPageNum >= filePdfDoc.numPages) return;
    filePdfPageNum++;
    filePdfRender();
}

function showFileModal(url, title) {
    var body = document.getElementById('filePreviewBody');
    document.getElementById('filePreviewTitle').textContent = title || 'Pratinjau Dokumen';
    var clean = url.toLowerCase().split('?')[0];
    var nameClean = String(title || '').toLowerCase().split('?')[0];
    var isPdf = clean.endsWith('.pdf') || nameClean.endsWith('.pdf');
    var isImage = /\.(jpg|jpeg|png|gif|webp|bmp|svg)$/.test(clean) || /\.(jpg|jpeg|png|gif|webp|bmp|svg)$/.test(nameClean);
    fileZoomScale = 1;
    filePdfDoc = null;
    filePdfPageNum = 1;
    fileSetToolbar(false, false);
    document.getElementById('filePreviewModal').classList.remove('hidden');
    if (isPdf) {
        filePreviewMode = 'pdf';
        if (!window.pdfjsLib) {
            body.innerHTML = '<div class="p-8 text-center text-sm text-gray-600">Gagal memuat penampil PDF.</div>';
            return;
        }
        body.innerHTML = '<div class="p-8 text-center text-sm text-gray-500">Memuat dokumen...</div>';
        window.pdfjsLib.getDocument(url).promise.then(function (pdf) {
            filePdfDoc = pdf;
            document.getElementById('filePdfPages').textContent = pdf.numPages;
            fileSetToolbar(true, pdf.numPages > 1);
            fileUpdateZoomLabel();
            body.innerHTML = '<canvas id="filePdfCanvas" class="shadow rounded bg-white" style="cursor:grab"></canvas>';
            filePdfRender();
        }).catch(function () {
            body.innerHTML = '<div class="p-8 text-center text-sm text-red-600">Dokumen PDF gagal dibuka. <a href="' + url + '" target="_blank" class="underline">Buka di tab baru</a></div>';
        });
    } else if (isImage) {
        filePreviewMode = 'image';
        body.innerHTML = '<img id="filePreviewImage" src="' + url + '" alt="Pratinjau" class="max-w-full h-auto rounded" style="transition:transform .15s ease-out;cursor:grab" onerror="this.outerHTML=\'<div class=p-8 text-center text-sm text-red-600>Gagal memuat gambar. <a href=' + url + ' target=_blank class=underline>Buka di tab baru</a></div>\'">';
        fileSetToolbar(true, false);
        fileApplyZoom();
    } else {
        filePreviewMode = null;
        body.innerHTML = '<div class="p-8 text-center text-sm text-gray-600">Format file tidak dapat ditampilkan. <a href="' + url + '" target="_blank" class="underline">Buka / unduh file</a></div>';
    }
}

function hideFileModal() {
    document.getElementById('filePreviewModal').classList.add('hidden');
    document.getElementById('filePreviewBody').innerHTML = '';
    fileSetToolbar(false, false);
    fileZoomScale = 1;
    filePreviewMode = null;
    filePdfDoc = null;
    filePdfPageNum = 1;
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { hideFileModal(); return; }
    if (document.getElementById('filePreviewModal').classList.contains('hidden')) return;
    if (!filePreviewMode) return;
    if (e.key === '+' || e.key === '=') { e.preventDefault(); fileZoomIn(); }
    if (e.key === '-' || e.key === '_') { e.preventDefault(); fileZoomOut(); }
    if (e.key === '0') { e.preventDefault(); fileZoomReset(); }
    if (filePreviewMode === 'pdf') {
        if (e.key === 'ArrowLeft' || e.key === 'PageUp') { e.preventDefault(); filePdfPrev(); }
        if (e.key === 'ArrowRight' || e.key === 'PageDown') { e.preventDefault(); filePdfNext(); }
    }
});

document.getElementById('filePreviewModal').addEventListener('click', function (e) {
    if (e.target === this) hideFileModal();
});

(function () {
    var body = document.getElementById('filePreviewBody');

    body.addEventListener('wheel', function (e) {
        if (!filePreviewMode || !e.ctrlKey) return;
        e.preventDefault();
        if (e.deltaY < 0) fileZoomIn(); else fileZoomOut();
    }, { passive: false });

    var dragging = false, startX = 0, startY = 0, startLeft = 0, startTop = 0;

    body.addEventListener('mousedown', function (e) {
        var target = document.getElementById('filePreviewImage') || document.getElementById('filePdfCanvas');
        if (!target) return;
        dragging = true;
        startX = e.clientX;
        startY = e.clientY;
        startLeft = body.scrollLeft;
        startTop = body.scrollTop;
        target.style.cursor = 'grabbing';
        e.preventDefault();
    });

    document.addEventListener('mousemove', function (e) {
        if (!dragging) return;
        body.scrollLeft = startLeft - (e.clientX - startX);
        body.scrollTop = startTop - (e.clientY - startY);
    });

    document.addEventListener('mouseup', function () {
        if (!dragging) return;
        dragging = false;
        var target = document.getElementById('filePreviewImage') || document.getElementById('filePdfCanvas');
        if (target) target.style.cursor = 'grab';
    });
})();
</script>
