<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>@yield('title', 'API Documentation')</title>

<style>
    :root{
        --bg:#0f1724; --card:#0b1220; --muted:#9aa4b2; --accent:#3b82f6; --success:#10b981;
        --mono-bg:#0b1220; --mono-fg:#a7f3d0;
    }
    html,body{height:100%;margin:0;font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;}
    body{background:linear-gradient(180deg,#071025 0%, #07172a 100%); color:#e6eef6; -webkit-font-smoothing:antialiased; padding:28px;}
    .container{max-width:1100px;margin:0 auto;}
    .card{background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.04); padding:20px; border-radius:10px; box-shadow:0 6px 20px rgba(2,6,23,0.6);}
    h1,h2,h3{margin:0 0 12px 0;color:#fff}
    p{color:var(--muted); margin:0 0 12px 0;}
    pre{background:#020617; color:#9ff0d6; padding:14px; border-radius:8px; overflow:auto; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, "Roboto Mono", "Courier New", monospace; font-size:13px; line-height:1.45;}
    code{background:rgba(255,255,255,0.02); padding:2px 6px; border-radius:4px; color:#cde9ff;}
    .row{display:flex; gap:16px; align-items:flex-start; flex-wrap:wrap;}
    .col{flex:1; min-width:260px;}
    .btn{display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:8px; border:0; cursor:pointer; font-weight:600;}
    .btn-copy{background:var(--accent); color:#fff;}
    .btn-ghost{background:transparent; color:var(--muted); border:1px solid rgba(255,255,255,0.03);}
    .meta{font-size:13px;color:var(--muted);}
    .small{font-size:13px;color:var(--muted);}
    .endpoint{display:flex; gap:8px; align-items:center; justify-content:space-between;}
    .endpoint .url{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, "Roboto Mono", monospace; background:rgba(255,255,255,0.02); padding:8px; border-radius:6px; color:#bfe9ff; overflow:auto;}
    .panel{padding:12px; border-radius:8px; background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); border:1px solid rgba(255,255,255,0.02);}
    .grid{display:grid; grid-template-columns:1fr 1fr; gap:12px;}
    .flash{position:fixed; right:20px; bottom:20px; background:#041b12; color:var(--mono-fg); padding:10px 14px; border-radius:8px; opacity:0; transform:translateY(10px); transition:all .18s ease;}
    .flash.show{opacity:1; transform:none;}
    .muted{color:var(--muted);}
    .kbd{background:#07172a; border:1px solid rgba(255,255,255,0.03); padding:4px 8px; border-radius:6px; font-family:ui-monospace; font-size:13px;}
</style>

@stack('head')
</head>
<body>
<div class="container">
    <header style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <div>
            <h1>@yield('title', 'API Documentation')</h1>
            <div class="meta">@yield('subtitle','Tally Invoice API — instructions to POST XML from Tally/Postman')</div>
        </div>
        <div style="text-align:right">
            <div class="small muted">Server: <span id="serverHost">{{ request()->getHost() }}</span></div>
            <div class="small muted">API Base: <code class="kbd">{{ url('/api/tally/invoice/{orderNumber}') }}</code></div>
        </div>
    </header>

    <main class="card">
        @yield('content')
    </main>

    <div style="margin-top:18px;text-align:center;color:var(--muted);font-size:13px;">
        Built for internal use • Keep endpoint secure • Use <code>X-Tally-Secret</code> header if required
    </div>
</div>

<div id="flash" class="flash">Copied</div>

<script>
(function(){
    function $(s){return document.querySelector(s)}
    function $all(s){return Array.from(document.querySelectorAll(s))}
    function showFlash(text='Copied'){
        var f = $('#flash'); f.textContent = text; f.classList.add('show'); setTimeout(()=>f.classList.remove('show'),1400);
    }

    document.addEventListener('click', function(e){
        var btn = e.target.closest('[data-copy]');
        if(!btn) return;
        var target = btn.getAttribute('data-copy');
        var text = '';
        if(target.startsWith('#')){
            var el = document.querySelector(target);
            if(!el){ showFlash('Target not found'); return; }
            text = el.innerText.trim();
        } else {
            text = target;
        }
        if(!text){ showFlash('Nothing to copy'); return; }
        if(navigator.clipboard && navigator.clipboard.writeText){
            navigator.clipboard.writeText(text).then(()=>showFlash('Copied'), ()=>{ fallbackCopy(text) });
        } else {
            fallbackCopy(text);
        }
    });

    function fallbackCopy(text){
        var ta = document.createElement('textarea'); ta.value = text; ta.style.position='fixed'; ta.style.left='-9999px';
        document.body.appendChild(ta); ta.select();
        try{ document.execCommand('copy'); showFlash('Copied'); }catch(e){ showFlash('Copy failed'); }
        document.body.removeChild(ta);
    }

    // prettify xml toggle (client-side)
    document.addEventListener('click', function(e){
        var t = e.target;
        if(!t.matches('[data-toggle-pretty]')) return;
        var sel = t.getAttribute('data-target');
        var el = document.querySelector(sel);
        if(!el) return;
        var raw = el.getAttribute('data-raw') || el.innerText;
        // if already prettified (has newlines), revert to minified
        var isPretty = /\>\n\s*\<\w/.test(raw);
        if(isPretty){
            // produce minified
            var min = raw.replace(/\n\s*/g,'').trim();
            el.innerText = min;
            el.setAttribute('data-raw', min);
            t.innerText = 'Prettify';
            return;
        }
        try{
            var parser = new DOMParser();
            var xmlDoc = parser.parseFromString(raw,'application/xml');
            if(xmlDoc.getElementsByTagName('parsererror').length){
                showFlash('Malformed XML — cannot prettify'); return;
            }
            function nodeToString(node, indent){
                indent = indent || 0;
                var pad = '  '.repeat(indent), out='';
                if(node.nodeType === 3){ // text
                    var v = node.nodeValue.trim(); if(!v) return ''; return pad + v + "\\n";
                }
                out += pad + '<' + node.nodeName;
                if(node.attributes && node.attributes.length){
                    for(var i=0;i<node.attributes.length;i++){
                        var a = node.attributes[i]; out += ' ' + a.name + '=\"' + a.value + '\"';
                    }
                }
                out += '>';
                var children = Array.from(node.childNodes).filter(n => !(n.nodeType === 3 && !n.nodeValue.trim()));
                if(children.length === 0){ out += '</' + node.nodeName + '>' + '\\n'; return out; }
                out += '\\n';
                children.forEach(function(c){ out += nodeToString(c, indent+1); });
                out += pad + '</' + node.nodeName + '>' + '\\n';
                return out;
            }
            var out = '';
            Array.from(xmlDoc.childNodes).forEach(function(n){ out += nodeToString(n,0); });
            el.innerText = out.trim();
            el.setAttribute('data-raw', out.trim());
            t.innerText = 'Minify';
        }catch(err){
            showFlash('Prettify failed');
        }
    });

})();
</script>

@stack('scripts')
</body>
</html>
