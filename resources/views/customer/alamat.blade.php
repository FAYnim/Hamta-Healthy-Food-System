<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hamta — Pilih Alamat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .al-head { display:flex; align-items:center; gap:16px; margin:2px 0 22px; }
        .al-back { display:grid; place-items:center; width:42px; height:42px; border-radius:50%; color:var(--green); text-decoration:none; transition:background .2s; }
        .al-back:hover { background:rgba(11,65,43,.08); }
        .al-head h1 { margin:0; font-size:29px; line-height:1.15; letter-spacing:-.5px; }
        .al-list { display:grid; gap:20px; }
        .al-card { display:flex; gap:14px; border:2px solid #d8e2d2; border-radius:13px; padding:22px 24px; background:#fbf8f0; cursor:pointer; transition:border-color .15s, background .15s; }
        .al-card:hover { border-color:var(--green); }
        .al-card.is-selected { border-color:var(--green); background:#eef5ea; }
        .al-radio { flex:0 0 auto; width:20px; height:20px; margin-top:2px; border:2px solid var(--green); border-radius:50%; display:grid; place-items:center; }
        .al-card.is-selected .al-radio:before { content:''; width:10px; height:10px; border-radius:50%; background:var(--green); }
        .al-body { flex:1 1 auto; min-width:0; }
        .al-top { display:flex; align-items:baseline; justify-content:space-between; gap:16px; }
        .al-name { margin:0; font-size:17px; line-height:1.2; font-weight:700; }
        .al-phone { font-weight:600; font-size:13px; opacity:.7; }
        .al-actions { flex:0 0 auto; display:flex; gap:14px; }
        .al-edit, .al-del { border:0; background:none; padding:0; font-family:inherit; color:var(--green); font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; }
        .al-edit:hover { text-decoration:underline; text-underline-offset:3px; }
        .al-del { color:#c0392b; }
        .al-del:hover { text-decoration:underline; text-underline-offset:3px; }
        .al-address { margin:7px 0 0; font-size:13px; line-height:1.4; max-width:520px; }
        .al-region { margin:10px 0 0; font-size:11.5px; line-height:1.4; text-transform:uppercase; color:#4a5a52; }
        .al-empty { border:2px dashed #d8e2d2; border-radius:13px; background:#fbf8f0; padding:22px 24px; text-align:center; }
        .al-empty p { margin:0; font-size:15px; font-weight:700; }
        .al-empty small { display:block; margin-top:6px; font-size:13px; font-weight:500; opacity:.7; }
        .al-add { display:flex; align-items:center; justify-content:center; gap:10px; width:100%; margin:20px 0 8px; padding:14px 18px; border:2px solid #d8e2d2; border-radius:13px; background:#fff; color:var(--green); font-family:inherit; font-size:15px; font-weight:700; cursor:pointer; transition:border-color .15s, background .15s; box-sizing:border-box; }
        .al-add:hover { border-color:var(--green); background:#eef5ea; }
        .al-modal { position:fixed; inset:0; z-index:60; display:none; align-items:center; justify-content:center; padding:16px; background:rgba(11,65,43,.45); }
        .al-modal.is-open { display:flex; }
        .al-dialog { width:100%; max-width:480px; background:#fbf8f0; border:2px solid var(--green); border-radius:13px; padding:22px 24px; }
        .al-dialog h2 { margin:0; font-size:18px; line-height:1.2; }
        .al-dialog p.al-hint { margin:5px 0 0; font-size:13px; opacity:.7; }
        .al-field { display:grid; gap:4px; margin-top:12px; }
        .al-field label { font-size:12px; font-weight:700; }
        .al-field input, .al-field textarea { width:100%; box-sizing:border-box; font-family:inherit; font-size:14px; padding:10px 12px; border:2px solid #d8e2d2; border-radius:11px; background:#fff; }
        .al-field input:focus, .al-field textarea:focus { outline:none; border-color:var(--green); }
        .al-field textarea { min-height:76px; resize:vertical; }
        .al-error { display:none; margin:10px 0 0; font-size:12.5px; font-weight:700; color:#c0392b; }
        .al-error.is-show { display:block; }
        .al-dialog-actions { display:flex; gap:10px; margin-top:16px; }
        .al-btn { flex:1; padding:12px 14px; border-radius:11px; border:2px solid var(--green); font-family:inherit; font-size:14px; font-weight:700; cursor:pointer; }
        .al-btn-cancel { background:transparent; color:var(--green); }
        .al-btn-save { background:var(--green); color:#fff; }
        @media (max-width:700px) {
            .al-head { gap:10px; margin-bottom:16px; }
            .al-back { width:34px; height:34px; }
            .al-head h1 { font-size:21px; }
            .al-list { gap:14px; }
            .al-card { gap:11px; padding:16px 14px; }
            .al-radio { width:18px; height:18px; }
            .al-card.is-selected .al-radio:before { width:8px; height:8px; }
            .al-top { flex-wrap:wrap; gap:4px 12px; }
            .al-name { font-size:13px; }
            .al-phone { font-size:11px; }
            .al-edit, .al-del { font-size:12px; }
            .al-address { font-size:11px; margin-top:5px; }
            .al-region { font-size:10px; margin-top:8px; }
            .al-add { margin:14px 0 6px; padding:12px 14px; font-size:13px; gap:8px; }
            .al-dialog { padding:16px 14px; }
        }
    </style>
</head>
<body>
<div class="page">
    @include('customer.partials.header')

    <div class="al-head">
        <a class="al-back" href="{{ route('customer.checkout') }}" aria-label="Kembali">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        </a>
        <h1>Pilih Alamat</h1>
    </div>

    <div class="al-list" id="al-list"></div>

    <div class="al-empty" id="al-empty" hidden>
        <p>Belum ada alamat tersimpan</p>
        <small>Tambah alamat pengiriman pertamamu dulu.</small>
    </div>

    <button type="button" class="al-add" id="al-add">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
        Tambah Alamat Baru
    </button>

    <div class="al-modal" id="al-modal" role="dialog" aria-modal="true" aria-labelledby="al-modal-title">
        <div class="al-dialog">
            <h2 id="al-modal-title">Tambah Alamat</h2>
            <p class="al-hint">Nama, telepon, dan alamat wajib diisi.</p>
            <form id="al-form" novalidate>
                <div class="al-field">
                    <label for="al-nama">Nama Penerima</label>
                    <input id="al-nama" name="nama" type="text" autocomplete="name" placeholder="cth. Brendata">
                </div>
                <div class="al-field">
                    <label for="al-telepon">Nomor Telepon</label>
                    <input id="al-telepon" name="telepon" type="tel" inputmode="tel" autocomplete="tel" placeholder="cth. 082331129819">
                </div>
                <div class="al-field">
                    <label for="al-alamat">Alamat Lengkap</label>
                    <textarea id="al-alamat" name="alamat" placeholder="cth. Jalan Tangkis Turi Kecil C No. 22, RT.1/RW.4"></textarea>
                </div>
                <div class="al-field">
                    <label for="al-patokan">Patokan (opsional)</label>
                    <input id="al-patokan" name="patokan" type="text" placeholder="cth. Kos Ibu Maria">
                </div>
                <p class="al-error" id="al-error" role="alert"></p>
                <div class="al-dialog-actions">
                    <button type="button" class="al-btn al-btn-cancel" id="al-cancel">Batal</button>
                    <button type="submit" class="al-btn al-btn-save" id="al-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>
<script>
(function () {
    var LIST_KEY = 'hamta_addresses';
    var ACTIVE_KEY = 'hamta_address';
    var list = document.getElementById('al-list');
    var empty = document.getElementById('al-empty');
    var addBtn = document.getElementById('al-add');
    var modal = document.getElementById('al-modal');
    var modalTitle = document.getElementById('al-modal-title');
    var form = document.getElementById('al-form');
    var errEl = document.getElementById('al-error');
    var editingId = null;

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function loadList() {
        try {
            var v = JSON.parse(localStorage.getItem(LIST_KEY) || '[]');
            return Array.isArray(v) ? v : [];
        } catch (e) { return []; }
    }

    function saveList(items) {
        try { localStorage.setItem(LIST_KEY, JSON.stringify(items)); } catch (e) {}
    }

    function loadActive() {
        try { return JSON.parse(localStorage.getItem(ACTIVE_KEY) || 'null'); } catch (e) { return null; }
    }

    function toast(msg) {
        if (window.hamtaToast) window.hamtaToast(msg);
    }

    function activeId() {
        var a = loadActive();
        return a && a.id ? a.id : null;
    }

    function render() {
        var items = loadList();
        var sel = activeId();
        list.textContent = '';
        empty.hidden = items.length > 0;
        items.forEach(function (item) {
            var card = document.createElement('article');
            card.className = 'al-card' + (item.id === sel ? ' is-selected' : '');
            card.setAttribute('role', 'button');
            card.setAttribute('tabindex', '0');
            card.setAttribute('aria-pressed', item.id === sel ? 'true' : 'false');
            card.dataset.id = item.id;

            var radio = document.createElement('span');
            radio.className = 'al-radio';
            radio.setAttribute('aria-hidden', 'true');

            var body = document.createElement('div');
            body.className = 'al-body';

            var top = document.createElement('div');
            top.className = 'al-top';

            var name = document.createElement('p');
            name.className = 'al-name';
            name.innerHTML = esc(item.nama) + ' <span class="al-phone">(' + esc(item.telepon) + ')</span>';

            var actions = document.createElement('div');
            actions.className = 'al-actions';

            var edit = document.createElement('button');
            edit.type = 'button';
            edit.className = 'al-edit';
            edit.textContent = 'Ubah';
            edit.dataset.act = 'edit';

            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'al-del';
            del.textContent = 'Hapus';
            del.dataset.act = 'del';

            actions.append(edit, del);
            top.append(name, actions);

            var addr = document.createElement('p');
            addr.className = 'al-address';
            addr.textContent = item.alamat;

            body.append(top, addr);
            if (item.patokan) {
                var region = document.createElement('p');
                region.className = 'al-region';
                region.textContent = item.patokan;
                body.appendChild(region);
            }

            card.append(radio, body);

            card.addEventListener('click', function (e) {
                var act = e.target && e.target.dataset ? e.target.dataset.act : null;
                if (act === 'edit') { openModal(item.id); return; }
                if (act === 'del') { removeItem(item.id); return; }
                selectItem(item.id);
            });
            card.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectItem(item.id); }
            });

            list.appendChild(card);
        });
    }

    function selectItem(id) {
        var items = loadList();
        var found = null;
        items.forEach(function (item) { if (item.id === id) found = item; });
        if (!found) return;
        try { localStorage.setItem(ACTIVE_KEY, JSON.stringify(found)); } catch (e) {}
        render();
        toast('Alamat pengiriman dipilih');
    }

    function removeItem(id) {
        if (!window.confirm('Hapus alamat ini?')) return;
        var items = loadList().filter(function (item) { return item.id !== id; });
        saveList(items);
        if (activeId() === id) {
            if (items.length) {
                try { localStorage.setItem(ACTIVE_KEY, JSON.stringify(items[0])); } catch (e) {}
            } else {
                try { localStorage.removeItem(ACTIVE_KEY); } catch (e) {}
            }
        }
        render();
        toast('Alamat dihapus');
    }

    function openModal(id) {
        editingId = id || null;
        errEl.classList.remove('is-show');
        errEl.textContent = '';
        if (editingId) {
            modalTitle.textContent = 'Ubah Alamat';
            var items = loadList();
            var found = null;
            items.forEach(function (item) { if (item.id === editingId) found = item; });
            form.nama.value = found ? found.nama : '';
            form.telepon.value = found ? found.telepon : '';
            form.alamat.value = found ? found.alamat : '';
            form.patokan.value = found && found.patokan ? found.patokan : '';
        } else {
            modalTitle.textContent = 'Tambah Alamat';
            form.reset();
        }
        modal.classList.add('is-open');
        form.nama.focus();
    }

    function closeModal() {
        modal.classList.remove('is-open');
        editingId = null;
    }

    function showError(msg) {
        errEl.textContent = msg;
        errEl.classList.add('is-show');
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var nama = (form.nama.value || '').trim();
        var telepon = (form.telepon.value || '').trim();
        var alamat = (form.alamat.value || '').trim();
        var patokan = (form.patokan.value || '').trim();
        if (!nama || !telepon || !alamat) { showError('Nama, telepon, dan alamat wajib diisi.'); return; }
        if (telepon.replace(/\D/g, '').length < 9) { showError('Nomor telepon minimal 9 digit angka.'); return; }
        var items = loadList();
        if (editingId) {
            items = items.map(function (item) {
                return item.id === editingId
                    ? { id: item.id, nama: nama, telepon: telepon, alamat: alamat, patokan: patokan }
                    : item;
            });
        } else {
            var entry = {
                id: 'addr-' + Date.now().toString(36),
                nama: nama,
                telepon: telepon,
                alamat: alamat,
                patokan: patokan
            };
            items.push(entry);
            editingId = entry.id;
        }
        saveList(items);
        var saved = null;
        items.forEach(function (item) { if (item.id === editingId) saved = item; });
        if (saved) {
            try { localStorage.setItem(ACTIVE_KEY, JSON.stringify(saved)); } catch (e) {}
        }
        closeModal();
        render();
        toast('Alamat tersimpan');
    });

    addBtn.addEventListener('click', function () { openModal(null); });
    document.getElementById('al-cancel').addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });

    render();
})();
</script>
</div>
@include('customer.partials.footer')
</body>
</html>
