@page {
    size: A4 portrait;
    margin: 40px 36px 60px 36px;
    @bottom-left {
        content: "SILKA Keuangan";
        font-family: 'Segoe UI', 'DejaVu Sans', sans-serif;
        font-size: 8px;
        color: #94a3b8;
    }
    @bottom-right {
        content: "Hal " counter(page) " dari " counter(pages);
        font-family: 'Segoe UI', 'DejaVu Sans', sans-serif;
        font-size: 8px;
        color: #94a3b8;
    }
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Segoe UI', 'DejaVu Sans', sans-serif;
    font-size: 10.5px;
    color: #0f172a;
    line-height: 1.45;
}

/* ---------- Header band ---------- */
.brand-band {
    background: #0f172a;
    color: #ffffff;
    padding: 14px 18px;
    border-radius: 10px 10px 0 0;
}
.brand-band table { width: 100%; }
.brand-word {
    font-size: 21px;
    font-weight: 700;
    letter-spacing: 1px;
}
.brand-sub {
    font-size: 8.5px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #94a3b8;
}
.brand-band h1 {
    font-size: 15px;
    font-weight: 600;
    text-align: right;
    line-height: 1.2;
}
.band-sub {
    font-size: 8.5px;
    color: #94a3b8;
    text-align: right;
    margin-top: 3px;
}
.accent-bar {
    height: 4px;
    background: #10b981;
    border-radius: 0 0 10px 10px;
    margin-bottom: 14px;
}

/* ---------- Meta / chips ---------- */
.meta {
    font-size: 8.5px;
    color: #64748b;
    margin-bottom: 10px;
}
.chips { margin-bottom: 12px; }
.chip {
    display: inline-block;
    background: #eef2ff;
    color: #3730a3;
    border: 1px solid #c7d2fe;
    border-radius: 20px;
    padding: 2px 9px;
    font-size: 8.5px;
    font-weight: 600;
    margin-right: 4px;
    margin-bottom: 3px;
}

/* ---------- Summary cards ---------- */
table.summary {
    width: 100%;
    border-collapse: separate;
    border-spacing: 6px 0;
    margin: 0 -6px 16px -6px;
}
table.summary td { width: 25%; vertical-align: top; }
.sum-card {
    border: 1px solid #e2e8f0;
    border-top: 3px solid #94a3b8;
    border-radius: 8px;
    padding: 10px 12px;
    background: #ffffff;
}
.sum-card .lbl {
    font-size: 8px;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #64748b;
    font-weight: 600;
}
.sum-card .val {
    font-size: 14.5px;
    font-weight: 700;
    margin-top: 3px;
    white-space: nowrap;
}
.sum-card .note { font-size: 8px; color: #94a3b8; margin-top: 2px; }
.sum-card.emerald { border-top-color: #10b981; }
.sum-card .val.emerald { color: #047857; }
.sum-card.rose { border-top-color: #f43f5e; }
.sum-card .val.rose { color: #be123c; }
.sum-card.indigo { border-top-color: #4f46e5; }
.sum-card .val.indigo { color: #4338ca; }
.sum-card.navy { border-top-color: #0f172a; }
.sum-card .val.navy { color: #0f172a; }

/* ---------- Section title ---------- */
.sec-title {
    font-size: 11px;
    font-weight: 700;
    color: #0f172a;
    margin: 2px 0 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.sec-title .sec-line {
    display: inline-block;
    width: 3px;
    height: 10px;
    background: #10b981;
    border-radius: 2px;
    margin-right: 6px;
    vertical-align: middle;
}

/* ---------- Data table ---------- */
table.data {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 16px;
}
table.data th {
    background: #0f172a;
    color: #ffffff;
    font-size: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    padding: 7px 8px;
    text-align: left;
}
table.data th.r { text-align: right; }
table.data th.c { text-align: center; }
table.data td {
    padding: 6px 8px;
    border-bottom: 1px solid #eef2f7;
    vertical-align: middle;
}
table.data tr.alt td { background: #f8fafc; }
table.data td.r { text-align: right; white-space: nowrap; }
table.data td.c { text-align: center; }
table.data tr.total td {
    background: #f1f5f9;
    font-weight: 700;
    border-top: 2px solid #0f172a;
}

/* ---------- Badges ---------- */
.badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 8.5px;
    font-weight: 600;
}
.badge.masuk { color: #047857; background: #d1fae5; }
.badge.keluar { color: #be123c; background: #ffe4e6; }
.badge.ok { color: #047857; background: #d1fae5; }
.badge.no { color: #b45309; background: #fef3c7; }

/* ---------- Progress bar ---------- */
.progress {
    width: 120px;
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    display: inline-block;
    vertical-align: middle;
}
.progress > div {
    height: 8px;
    background: #10b981;
    border-radius: 4px;
}
.progress.warn > div { background: #f59e0b; }

/* ---------- Signature ---------- */
table.sig {
    width: 100%;
    margin-top: 28px;
}
table.sig td {
    width: 50%;
    text-align: center;
    font-size: 9.5px;
    color: #334155;
}
.sig-label { font-size: 9px; color: #64748b; }
.sig-line {
    margin-top: 42px;
    border-top: 1px solid #0f172a;
    padding-top: 4px;
    font-weight: 600;
    color: #0f172a;
}

/* ---------- Misc ---------- */
.empty-box {
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    padding: 22px 14px;
    text-align: center;
    color: #64748b;
    font-size: 10px;
}
.note-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 8.5px;
    color: #64748b;
    margin-bottom: 14px;
}