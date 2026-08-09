<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-TBPWPDL3E8"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-TBPWPDL3E8');
        </script>

        <title>画像切り抜きツール | ブラウザだけで完結 - 無料オンラインクロップ</title>
        @php
            $seoDescription = 'アップロード不要、ブラウザ内だけで完結する画像切り抜き(クロップ)ツール。16:9・1:1・9:16のプリセットや自由な範囲指定に対応し、JPG/PNGでダウンロードできます。';
            $seoUrl = url('/imagecrop');
        @endphp
        <meta name="description" content="{{ $seoDescription }}">
        <meta name="robots" content="index, follow">
        <link rel="canonical" href="{{ $seoUrl }}">
        <link rel="icon" href="{{ asset('imagecrop-favicon.ico') }}" sizes="any">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('imagecrop-favicon-32x32.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('imagecrop-apple-touch-icon.png') }}">
        <meta name="theme-color" content="#2563eb">

        <meta property="og:type" content="website">
        <meta property="og:locale" content="ja_JP">
        <meta property="og:site_name" content="画像切り抜きツール">
        <meta property="og:title" content="画像切り抜きツール | ブラウザだけで完結 - 無料オンラインクロップ">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ $seoUrl }}">

        <style>
            :root {
                color-scheme: light dark;
                --bg: #ffffff;
                --fg: #1f2328;
                --muted: #6b7280;
                --border: #d0d7de;
                --panel: #f6f8fa;
                --accent: #2563eb;
                --accent-fg: #ffffff;
                --danger-fg: #1f2328;
                --selection-tint: rgba(37, 99, 235, 0.12);
                --selection-border: #2563eb;
            }
            @media (prefers-color-scheme: dark) {
                :root {
                    --bg: #0d1117;
                    --fg: #e6edf3;
                    --muted: #9198a1;
                    --border: #30363d;
                    --panel: #161b22;
                    --selection-tint: rgba(96, 165, 250, 0.18);
                    --selection-border: #60a5fa;
                }
            }
            * { box-sizing: border-box; }
            body {
                margin: 0;
                background: var(--bg);
                color: var(--fg);
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Hiragino Sans", sans-serif;
                min-height: 100vh;
            }
            main {
                max-width: 960px;
                margin: 0 auto;
                padding: 24px 16px 64px;
            }
            h1 {
                font-size: 20px;
                margin: 0 0 20px;
            }

            /* --- dropzone --- */
            #dropzone {
                border: 2px dashed var(--border);
                border-radius: 12px;
                padding: 64px 24px;
                text-align: center;
                cursor: pointer;
                color: var(--muted);
                transition: border-color .15s, background .15s;
            }
            #dropzone.dragover {
                border-color: var(--accent);
                background: var(--selection-tint);
            }
            #dropzone p { margin: 8px 0; }
            #fileInput { display: none; }

            /* --- editor --- */
            #editor { display: none; }
            .toolbar {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
                margin-bottom: 16px;
            }
            .toolbar .label {
                font-weight: 600;
                margin-right: auto;
            }
            .ratio-buttons {
                display: flex;
                gap: 6px;
            }
            button {
                font: inherit;
                cursor: pointer;
            }
            .btn {
                border: 1px solid var(--border);
                background: var(--bg);
                color: var(--fg);
                border-radius: 8px;
                padding: 8px 14px;
            }
            .btn.active {
                background: var(--accent);
                border-color: var(--accent);
                color: var(--accent-fg);
            }
            .btn:hover:not(.active) { border-color: var(--accent); }
            .btn.primary {
                background: var(--accent);
                border-color: var(--accent);
                color: var(--accent-fg);
                font-weight: 600;
            }
            .btn.secondary { background: var(--panel); }

            .stage {
                position: relative;
                display: inline-block;
                max-width: 100%;
                user-select: none;
                touch-action: none;
                line-height: 0;
            }
            .stage img {
                max-width: 100%;
                display: block;
                border-radius: 4px;
            }
            #selectionBox {
                position: absolute;
                border: 1.5px dashed var(--selection-border);
                background: var(--selection-tint);
                display: none;
            }
            #selectionBox.moving { cursor: move; }
            .handle {
                position: absolute;
                width: 14px;
                height: 14px;
                background: var(--bg);
                border: 2px solid var(--selection-border);
                border-radius: 50%;
                transform: translate(-50%, -50%);
            }
            .handle.nw, .handle.se { cursor: nwse-resize; }
            .handle.ne, .handle.sw { cursor: nesw-resize; }
            .handle.n, .handle.s { cursor: ns-resize; }
            .handle.e, .handle.w { cursor: ew-resize; }
            .handle.n  { top: 0;   left: 50%; }
            .handle.s  { top: 100%;left: 50%; }
            .handle.e  { top: 50%; left: 100%;}
            .handle.w  { top: 50%; left: 0;   }
            .handle.nw { top: 0;   left: 0;   }
            .handle.ne { top: 0;   left: 100%;}
            .handle.se { top: 100%;left: 100%;}
            .handle.sw { top: 100%;left: 0;   }
            #selectionBox.ratio-locked .handle.n,
            #selectionBox.ratio-locked .handle.s,
            #selectionBox.ratio-locked .handle.e,
            #selectionBox.ratio-locked .handle.w {
                display: none;
            }

            #selectionInfo {
                margin: 14px 0;
                color: var(--muted);
                font-size: 14px;
            }

            .hint {
                margin: -10px 0 20px;
                color: var(--muted);
                font-size: 13px;
            }
            .link-btn {
                background: none;
                border: none;
                padding: 0;
                font: inherit;
                font-size: 13px;
                color: var(--accent);
                text-decoration: underline;
                cursor: pointer;
            }

            .controls {
                display: flex;
                gap: 24px;
                flex-wrap: wrap;
                margin-bottom: 20px;
            }
            .field label {
                display: block;
                font-size: 13px;
                color: var(--muted);
                margin-bottom: 4px;
            }
            .field input[type="number"] {
                font: inherit;
                font-size: 18px;
                width: 120px;
                padding: 8px 10px;
                border: 1px solid var(--border);
                border-radius: 8px;
                background: var(--bg);
                color: var(--fg);
            }
            .format-options {
                display: flex;
                gap: 16px;
                align-items: center;
                height: 38px;
            }
            .format-options label {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 15px;
                cursor: pointer;
            }
            .actions {
                display: flex;
                gap: 10px;
            }
            .actions .btn { padding: 12px 24px; }
        </style>
    </head>
    <body>
        <main>
            <h1>画像切り抜きツール</h1>

            <div id="dropzone">
                <p>ここに画像をドラッグ&ドロップ、またはクリックして選択</p>
                <p style="font-size:13px">画像はサーバーに送信されず、ブラウザ内だけで処理されます</p>
                <input id="fileInput" type="file" accept="image/*">
            </div>

            <div id="editor">
                <div class="toolbar">
                    <span class="label">切り抜く範囲を選択</span>
                    <div class="ratio-buttons">
                        <button class="btn" type="button" data-ratio="16/9">16:9</button>
                        <button class="btn" type="button" data-ratio="1">1:1</button>
                        <button class="btn" type="button" data-ratio="9/16">9:16</button>
                        <button class="btn active" type="button" data-ratio="free">自由</button>
                    </div>
                    <button class="btn" type="button" id="clearSelectionBtn">選択解除</button>
                </div>

                <div class="stage" id="stage">
                    <img id="sourceImage" alt="編集中の画像">
                    <div id="selectionBox">
                        <div class="handle n"></div>
                        <div class="handle ne"></div>
                        <div class="handle e"></div>
                        <div class="handle se"></div>
                        <div class="handle s"></div>
                        <div class="handle sw"></div>
                        <div class="handle w"></div>
                        <div class="handle nw"></div>
                    </div>
                </div>

                <p id="selectionInfo">画像上をドラッグして切り抜く範囲を選択してください</p>

                <div class="controls">
                    <div class="field">
                        <label for="widthInput">幅 (px)</label>
                        <input id="widthInput" type="number" min="1">
                    </div>
                    <div class="field">
                        <label for="heightInput">高さ (px)</label>
                        <input id="heightInput" type="number" min="1">
                    </div>
                    <div class="field">
                        <label>出力形式</label>
                        <div class="format-options">
                            <label><input type="radio" name="format" value="jpg" checked> JPG</label>
                            <label><input type="radio" name="format" value="png"> PNG</label>
                        </div>
                    </div>
                </div>

                <p class="hint">
                    幅・高さを小さくすると、切り抜いた画像を縮小してダウンロードできます(縦横比は選択範囲に合わせて自動調整されます)。
                    <button type="button" id="resetOutputSizeBtn" class="link-btn">選択範囲のサイズに戻す</button>
                </p>

                <div class="actions">
                    <button class="btn primary" type="button" id="downloadBtn">ダウンロード</button>
                    <button class="btn secondary" type="button" id="resetBtn">別の画像を選択</button>
                </div>
            </div>
        </main>

        <script>
        (function () {
            "use strict";

            var dropzone = document.getElementById("dropzone");
            var fileInput = document.getElementById("fileInput");
            var editor = document.getElementById("editor");
            var stage = document.getElementById("stage");
            var img = document.getElementById("sourceImage");
            var box = document.getElementById("selectionBox");
            var selectionInfo = document.getElementById("selectionInfo");
            var widthInput = document.getElementById("widthInput");
            var heightInput = document.getElementById("heightInput");
            var downloadBtn = document.getElementById("downloadBtn");
            var resetBtn = document.getElementById("resetBtn");
            var clearSelectionBtn = document.getElementById("clearSelectionBtn");
            var resetOutputSizeBtn = document.getElementById("resetOutputSizeBtn");
            var ratioButtons = Array.prototype.slice.call(document.querySelectorAll("[data-ratio]"));

            var MIN_SIZE = 10; // natural px
            var MAX_OUTPUT = 8000; // sanity cap on the downloaded image's width/height, px
            var objectUrl = null;
            var naturalWidth = 0, naturalHeight = 0;
            var ratio = null; // width/height, or null for free
            var sel = null; // { x, y, w, h } in natural px, or null

            var drag = null; // active pointer interaction state

            function scale() {
                return img.clientWidth / naturalWidth;
            }

            function clamp(v, min, max) {
                return Math.max(min, Math.min(max, v));
            }

            // --- file loading ---

            function loadFile(file) {
                if (!file || file.type.indexOf("image/") !== 0) return;
                if (objectUrl) URL.revokeObjectURL(objectUrl);
                objectUrl = URL.createObjectURL(file);
                img.onload = function () {
                    naturalWidth = img.naturalWidth;
                    naturalHeight = img.naturalHeight;
                    dropzone.style.display = "none";
                    editor.style.display = "block";
                    sel = null;
                    selectionUpdated();
                };
                img.src = objectUrl;
            }

            dropzone.addEventListener("click", function () { fileInput.click(); });
            fileInput.addEventListener("change", function () {
                if (fileInput.files && fileInput.files[0]) loadFile(fileInput.files[0]);
            });
            ["dragenter", "dragover"].forEach(function (evt) {
                dropzone.addEventListener(evt, function (e) {
                    e.preventDefault();
                    dropzone.classList.add("dragover");
                });
            });
            ["dragleave", "drop"].forEach(function (evt) {
                dropzone.addEventListener(evt, function (e) {
                    e.preventDefault();
                    dropzone.classList.remove("dragover");
                });
            });
            dropzone.addEventListener("drop", function (e) {
                var file = e.dataTransfer.files && e.dataTransfer.files[0];
                if (file) loadFile(file);
            });
            document.addEventListener("paste", function (e) {
                var items = e.clipboardData && e.clipboardData.items;
                if (!items) return;
                for (var i = 0; i < items.length; i++) {
                    if (items[i].type.indexOf("image/") === 0) {
                        loadFile(items[i].getAsFile());
                        break;
                    }
                }
            });

            resetBtn.addEventListener("click", function () {
                editor.style.display = "none";
                dropzone.style.display = "block";
                fileInput.value = "";
                sel = null;
                box.style.display = "none";
            });

            // --- selection <-> DOM ---

            // Renders the crop rectangle itself (box position/size + info text).
            // Deliberately does not touch the width/height output-size inputs —
            // those represent the *downloaded* image size, which may be smaller
            // (or larger) than the crop rectangle, and must survive things like
            // a window resize that only repositions the box on screen.
            function renderSelection() {
                if (!sel) {
                    box.style.display = "none";
                    selectionInfo.textContent = "画像上をドラッグして切り抜く範囲を選択してください";
                    return;
                }
                var s = scale();
                box.style.display = "block";
                box.style.left = (sel.x * s) + "px";
                box.style.top = (sel.y * s) + "px";
                box.style.width = (sel.w * s) + "px";
                box.style.height = (sel.h * s) + "px";
                selectionInfo.textContent =
                    "選択範囲: " + Math.round(sel.w) + " × " + Math.round(sel.h) +
                    " px (左から " + Math.round(sel.x) + ", 上から " + Math.round(sel.y) + ")";
            }

            // Resets the output-size fields to match the crop rectangle 1:1 —
            // the sensible default whenever the crop shape itself changes.
            function syncOutputSize() {
                if (!sel) {
                    widthInput.value = "";
                    heightInput.value = "";
                    return;
                }
                widthInput.value = Math.round(sel.w);
                heightInput.value = Math.round(sel.h);
            }

            // Call this (instead of renderSelection() directly) whenever `sel`
            // changes shape/position, so the output-size fields stay in sync.
            function selectionUpdated() {
                renderSelection();
                syncOutputSize();
            }

            function defaultSelection() {
                var w, h;
                if (ratio) {
                    w = naturalWidth * 0.6;
                    h = w / ratio;
                    if (h > naturalHeight * 0.9) {
                        h = naturalHeight * 0.9;
                        w = h * ratio;
                    }
                } else {
                    w = naturalWidth * 0.6;
                    h = naturalHeight * 0.6;
                }
                sel = {
                    x: (naturalWidth - w) / 2,
                    y: (naturalHeight - h) / 2,
                    w: w,
                    h: h,
                };
            }

            // --- aspect ratio buttons ---

            ratioButtons.forEach(function (btn) {
                btn.addEventListener("click", function () {
                    ratioButtons.forEach(function (b) { b.classList.remove("active"); });
                    btn.classList.add("active");
                    var val = btn.getAttribute("data-ratio");
                    ratio = val === "free" ? null : evalRatio(val);
                    box.classList.toggle("ratio-locked", !!ratio);
                    if (!naturalWidth) return;
                    if (!sel) {
                        defaultSelection();
                    } else if (ratio) {
                        // re-fit current selection to the new ratio, anchored at its center
                        var cx = sel.x + sel.w / 2, cy = sel.y + sel.h / 2;
                        var w = sel.w, h = w / ratio;
                        if (h > naturalHeight) { h = naturalHeight; w = h * ratio; }
                        if (w > naturalWidth) { w = naturalWidth; h = w / ratio; }
                        sel = {
                            x: clamp(cx - w / 2, 0, naturalWidth - w),
                            y: clamp(cy - h / 2, 0, naturalHeight - h),
                            w: w,
                            h: h,
                        };
                    }
                    selectionUpdated();
                });
            });

            function evalRatio(val) {
                var parts = val.split("/");
                return parts.length === 2 ? parseFloat(parts[0]) / parseFloat(parts[1]) : parseFloat(val);
            }

            clearSelectionBtn.addEventListener("click", function () {
                sel = null;
                selectionUpdated();
            });

            // --- pointer interactions on the stage ---

            function naturalPoint(clientX, clientY) {
                var rect = img.getBoundingClientRect();
                var s = scale();
                return {
                    x: clamp((clientX - rect.left) / s, 0, naturalWidth),
                    y: clamp((clientY - rect.top) / s, 0, naturalHeight),
                };
            }

            stage.addEventListener("pointerdown", function (e) {
                if (!naturalWidth) return;
                var p = naturalPoint(e.clientX, e.clientY);
                var handle = e.target.closest ? e.target.closest(".handle") : null;

                if (handle) {
                    drag = { mode: "resize", dir: handleDir(handle), start: p, orig: Object.assign({}, sel) };
                } else if (sel && p.x >= sel.x && p.x <= sel.x + sel.w && p.y >= sel.y && p.y <= sel.y + sel.h) {
                    drag = { mode: "move", start: p, orig: Object.assign({}, sel) };
                    box.classList.add("moving");
                } else {
                    drag = { mode: "create", start: p };
                    sel = { x: p.x, y: p.y, w: 0, h: 0 };
                }
                stage.setPointerCapture(e.pointerId);
                e.preventDefault();
            });

            stage.addEventListener("pointermove", function (e) {
                if (!drag) return;
                // Fast drags can occasionally drop the pointerup event itself (seen
                // intermittently across browsers), which used to leave the selection
                // "stuck" following the cursor with no button held. e.buttons === 0
                // means nothing is pressed anymore, so treat this move as a release.
                if (e.buttons === 0) {
                    endDrag();
                    return;
                }
                var p = naturalPoint(e.clientX, e.clientY);

                if (drag.mode === "move") {
                    var dx = p.x - drag.start.x, dy = p.y - drag.start.y;
                    sel = {
                        x: clamp(drag.orig.x + dx, 0, naturalWidth - drag.orig.w),
                        y: clamp(drag.orig.y + dy, 0, naturalHeight - drag.orig.h),
                        w: drag.orig.w,
                        h: drag.orig.h,
                    };
                } else if (drag.mode === "create") {
                    sel = createFromDrag(drag.start, p);
                } else if (drag.mode === "resize") {
                    sel = resizeFromDrag(drag.dir, drag.orig, p);
                }
                selectionUpdated();
            });

            function endDrag(e) {
                if (!drag) return;
                drag = null;
                box.classList.remove("moving");
                if (sel && (sel.w < MIN_SIZE || sel.h < MIN_SIZE)) {
                    sel = null;
                    selectionUpdated();
                }
            }
            stage.addEventListener("pointerup", endDrag);
            stage.addEventListener("pointercancel", endDrag);
            // Belt-and-suspenders: pointer capture is supposed to guarantee pointerup
            // reaches `stage` no matter where the button is released, but this is
            // inconsistent in some browsers (notably Safari), which left `drag` stuck
            // "on" so the selection kept following the mouse after release. A window-level
            // fallback catches those cases too; endDrag() is a no-op if already ended.
            window.addEventListener("pointerup", endDrag);
            window.addEventListener("pointercancel", endDrag);

            function handleDir(el) {
                var cls = el.className;
                return ["n", "ne", "e", "se", "s", "sw", "w", "nw"].filter(function (d) {
                    return (" " + cls + " ").indexOf(" " + d + " ") !== -1;
                })[0];
            }

            function createFromDrag(start, p) {
                if (ratio) {
                    var w = Math.abs(p.x - start.x);
                    var h = w / ratio;
                    var x = p.x >= start.x ? start.x : start.x - w;
                    var y = p.y >= start.y ? start.y : start.y - h;
                    return clampRect(x, y, w, h);
                }
                var x2 = Math.min(start.x, p.x), y2 = Math.min(start.y, p.y);
                return clampRect(x2, y2, Math.abs(p.x - start.x), Math.abs(p.y - start.y));
            }

            function clampRect(x, y, w, h) {
                x = clamp(x, 0, naturalWidth);
                y = clamp(y, 0, naturalHeight);
                w = clamp(w, 0, naturalWidth - x);
                h = clamp(h, 0, naturalHeight - y);
                return { x: x, y: y, w: w, h: h };
            }

            function resizeFromDrag(dir, orig, p) {
                var horiz = dir.indexOf("e") !== -1 ? "e" : (dir.indexOf("w") !== -1 ? "w" : null);
                var vert = dir.indexOf("s") !== -1 ? "s" : (dir.indexOf("n") !== -1 ? "n" : null);

                if (!ratio) {
                    var x = orig.x, y = orig.y, w = orig.w, h = orig.h;
                    if (horiz === "e") w = clamp(p.x - orig.x, MIN_SIZE, naturalWidth - orig.x);
                    if (horiz === "w") { var right = orig.x + orig.w; x = clamp(p.x, 0, right - MIN_SIZE); w = right - x; }
                    if (vert === "s") h = clamp(p.y - orig.y, MIN_SIZE, naturalHeight - orig.y);
                    if (vert === "n") { var bottom = orig.y + orig.h; y = clamp(p.y, 0, bottom - MIN_SIZE); h = bottom - y; }
                    return { x: x, y: y, w: w, h: h };
                }

                // ratio-locked: only corner handles are shown, anchor is the opposite corner
                var anchorX = horiz === "e" ? orig.x : orig.x + orig.w;
                var anchorY = vert === "s" ? orig.y : orig.y + orig.h;

                var maxW = horiz === "e" ? (naturalWidth - anchorX) : anchorX;
                var maxHFromV = vert === "s" ? (naturalHeight - anchorY) : anchorY;
                var maxWFromV = maxHFromV * ratio;
                maxW = Math.min(maxW, maxWFromV);

                var rawW = horiz === "e" ? (p.x - anchorX) : (anchorX - p.x);
                var w = clamp(rawW, MIN_SIZE, Math.max(MIN_SIZE, maxW));
                var h = w / ratio;

                var x = horiz === "e" ? anchorX : anchorX - w;
                var y = vert === "s" ? anchorY : anchorY - h;

                return { x: x, y: y, w: w, h: h };
            }

            // --- width / height inputs: these control the *downloaded* image size,
            // decoupled from the crop rectangle (which can be larger, for shrinking
            // on export). The aspect ratio always follows the crop rectangle's own
            // shape, so the output is never distorted.

            function onSizeInputChange(changed) {
                if (!naturalWidth) return;
                if (!sel) {
                    // width/height typed before any crop selection exists: default
                    // to selecting the whole image, then apply the typed size to it.
                    sel = { x: 0, y: 0, w: naturalWidth, h: naturalHeight };
                    ratio = null;
                    ratioButtons.forEach(function (b) {
                        b.classList.toggle("active", b.getAttribute("data-ratio") === "free");
                    });
                    box.classList.remove("ratio-locked");
                    renderSelection();
                }
                var cropRatio = sel.w / sel.h;
                var w = parseFloat(widthInput.value);
                var h = parseFloat(heightInput.value);

                if (changed === "width") {
                    w = clamp(w || sel.w, 1, MAX_OUTPUT);
                    h = w / cropRatio;
                } else {
                    h = clamp(h || sel.h, 1, MAX_OUTPUT);
                    w = h * cropRatio;
                }
                widthInput.value = Math.round(w);
                heightInput.value = Math.round(h);
            }
            widthInput.addEventListener("change", function () { onSizeInputChange("width"); });
            heightInput.addEventListener("change", function () { onSizeInputChange("height"); });

            resetOutputSizeBtn.addEventListener("click", syncOutputSize);

            // --- reposition selection box on window resize (rendered image size changes) ---
            // Uses renderSelection() directly (not selectionUpdated()) so a layout
            // reflow never clobbers an output size the user deliberately shrank.
            window.addEventListener("resize", function () { if (sel) renderSelection(); });

            // --- download ---

            downloadBtn.addEventListener("click", function () {
                if (!sel || sel.w < 1 || sel.h < 1) {
                    alert("先に切り抜く範囲を選択してください");
                    return;
                }
                var format = document.querySelector('input[name="format"]:checked').value;
                var outW = Math.max(1, Math.round(parseFloat(widthInput.value) || sel.w));
                var outH = Math.max(1, Math.round(parseFloat(heightInput.value) || sel.h));

                var canvas = document.createElement("canvas");
                canvas.width = outW;
                canvas.height = outH;
                var ctx = canvas.getContext("2d");
                if (format === "jpg") {
                    ctx.fillStyle = "#fff";
                    ctx.fillRect(0, 0, outW, outH);
                }
                ctx.drawImage(img, sel.x, sel.y, sel.w, sel.h, 0, 0, outW, outH);

                var mime = format === "jpg" ? "image/jpeg" : "image/png";
                canvas.toBlob(function (blob) {
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement("a");
                    a.href = url;
                    a.download = "cropped." + format;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(url);
                }, mime, format === "jpg" ? 0.92 : undefined);
            });
        })();
        </script>
    </body>
</html>
