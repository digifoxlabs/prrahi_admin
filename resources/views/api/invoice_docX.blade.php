{{-- resources/views/tally_invoices/api_doc.blade.php --}}
@extends('api.layout')

@section('title','Tally Invoice API Documentation')
@section('subtitle','Secure endpoint to POST Tally XML invoices — clear examples & quick tests')

@section('content')

<div style="display:flex;gap:18px;flex-wrap:wrap">
    <div style="flex:1; min-width:360px;">
        <section class="panel" style="margin-bottom:12px;">
            <h2 style="font-size:16px;margin-bottom:8px">Overview</h2>
            <p class="muted small">
                This endpoint accepts raw Tally XML (ENVELOPE) and stores it in the system linked to an <code>order_number</code>.
                Each order may have at most one Tally invoice. If an invoice is already present for an order, the API returns an error.
            </p>
            <ul style="color:var(--muted);margin:10px 0 0 18px;">
                <li>Method: <strong>POST</strong></li>
                <li>Endpoint: <strong><code>{{ url('/api/tally/invoice/{orderNumber}') }}</code></strong></li>
                <li>Security: <strong>Shared header secret</strong> (X-Tally-Secret)</li>
            </ul>
        </section>

        <section class="panel" style="margin-bottom:12px;">
            <h3 style="font-size:15px;margin-bottom:8px">Authentication (Header)</h3>

            <div class="grid" style="grid-template-columns:1fr 1fr; gap:10px;">
                <div>
                    <p class="small muted"><strong>Required Header</strong></p>
                    <pre id="hdrRequired">Content-Type: text/xml</pre>
                </div>
                <div>
                    <p class="small muted"><strong>Shared Secret (X-Tally-Secret)</strong></p>
                    <pre id="hdrSecret">X-Tally-Secret: prrahi-2025</pre>
                </div>
            </div>

            <div style="margin-top:10px;">
                <button class="btn btn-copy" data-copy="#hdrRequired">Copy Content-Type</button>
                <button class="btn btn-copy" style="margin-left:8px;" data-copy="#hdrSecret">Copy Secret</button>
            </div>

            <p class="muted small" style="margin-top:10px;">
                <strong>Note:</strong> The shared secret must match <code>config('services.tally.secret')</code> on the server.
                Use a secure value in production and rotate periodically.
            </p>
        </section>

        <section class="panel" style="margin-bottom:12px;">
            <h3 style="font-size:15px;margin-bottom:8px">Quick curl Example</h3>

            <pre id="curlExample">
curl -X POST "{{ url('/api/tally/invoice/ORD123') }}" \
  -H "Content-Type: text/xml" \
  -H "X-Tally-Secret: prrahi-2025" \
  --data-binary @invoice.xml
            </pre>

            <div style="margin-top:8px;">
                <button class="btn btn-copy" data-copy="#curlExample">Copy curl</button>
            </div>
        </section>

        <section class="panel">
            <h3 style="font-size:15px;margin-bottom:8px">Postman Quick Steps</h3>
            <ol style="color:var(--muted); margin:0 0 0 18px;">
                <li>Create a <strong>POST</strong> request with URL: <code id="postmanUrl">{{ url('/api/tally/invoice/ORD123') }}</code></li>
                <li>Headers: <code>Content-Type: text/xml</code> and <code>X-Tally-Secret: prrahi-2025</code></li>
                <li>Body → choose <strong>raw</strong> → paste the Tally XML → Send</li>
            </ol>
            <div style="margin-top:8px;">
                <button class="btn btn-copy" data-copy="#postmanUrl">Copy Postman URL</button>
            </div>
        </section>
    </div>

    <div style="flex:1; min-width:360px;">
        <section class="panel" style="margin-bottom:12px;">
            <h3 style="font-size:15px;margin-bottom:8px;">Sample Request Body (Tally XML)</h3>

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                <div class="muted small">Minimal example — replace values as needed</div>
                <div>
                    <button class="btn btn-copy" data-copy="#xmlSample">Copy XML</button>
                    <button class="btn btn-ghost" data-toggle-pretty data-target="#xmlSample" style="margin-left:8px;">Prettify</button>
                </div>
            </div>

            <pre id="xmlSample" data-raw>
&lt;ENVELOPE&gt;
  &lt;HEADER&gt;
    &lt;TALLYREQUEST&gt;Import Data&lt;/TALLYREQUEST&gt;
  &lt;/HEADER&gt;
  &lt;BODY&gt;
    &lt;IMPORTDATA&gt;
      &lt;REQUESTDESC&gt;
        &lt;REPORTNAME&gt;Vouchers&lt;/REPORTNAME&gt;
      &lt;/REQUESTDESC&gt;
      &lt;REQUESTDATA&gt;
        &lt;TALLYMESSAGE&gt;
          &lt;VOUCHER VCHTYPE="Sales" ACTION="Create"&gt;
            &lt;DATE&gt;20250905&lt;/DATE&gt;
            &lt;PARTYNAME&gt;ABC Distributor&lt;/PARTYNAME&gt;
            &lt;VOUCHERNUMBER&gt;INV-1001&lt;/VOUCHERNUMBER&gt;
            &lt;NARRATION&gt;Invoice linked to Order ORD123&lt;/NARRATION&gt;
            &lt;ALLLEDGERENTRIES.LIST&gt;
              &lt;LEDGERNAME&gt;Sales Account&lt;/LEDGERNAME&gt;
              &lt;AMOUNT&gt;-1000.00&lt;/AMOUNT&gt;
            &lt;/ALLLEDGERENTRIES.LIST&gt;
            &lt;ALLLEDGERENTRIES.LIST&gt;
              &lt;LEDGERNAME&gt;ABC Distributor&lt;/LEDGERNAME&gt;
              &lt;AMOUNT&gt;1000.00&lt;/AMOUNT&gt;
            &lt;/ALLLEDGERENTRIES.LIST&gt;
          &lt;/VOUCHER&gt;
        &lt;/TALLYMESSAGE&gt;
      &lt;/REQUESTDATA&gt;
    &lt;/IMPORTDATA&gt;
  &lt;/BODY&gt;
&lt;/ENVELOPE&gt;
            </pre>
        </section>

        <section class="panel" style="margin-top:12px;">
            <h3 style="font-size:15px;margin-bottom:8px;">Responses</h3>

            <p class="muted small">Successful response (HTTP 200):</p>
            <pre>
&lt;RESPONSE&gt;
  &lt;STATUS&gt;1&lt;/STATUS&gt;
  &lt;MESSAGE&gt;Invoice saved with ID 5 for Order ORD123&lt;/MESSAGE&gt;
&lt;/RESPONSE&gt;
            </pre>

            <p class="muted small">Common error responses:</p>
            <ul style="color:var(--muted); margin:8px 0 0 18px;">
                <li><code>401 Unauthorized</code> — Missing or invalid <code>X-Tally-Secret</code></li>
                <li><code>404 Not Found</code> — Provided <code>order_number</code> does not exist</li>
                <li><code>400 / 422</code> — Empty body or malformed XML</li>
                <li><code>409 / 400</code> — Invoice already exists for order (duplicate)</li>
            </ul>
        </section>
    </div>
</div>

<div style="margin-top:14px" class="panel">
    <h3 style="font-size:15px;margin-bottom:8px">Best Practices & Security</h3>
    <ul style="color:var(--muted);margin:0 0 0 18px;">
        <li>Use HTTPS for all requests to protect the shared secret in transit.</li>
        <li>Do not hardcode the shared secret in distributed TDL files — keep it configurable in Tally's environment or secure store.</li>
        <li>Rotate the secret periodically and update both Tally and server configuration.</li>
        <li>Log incoming requests (headers + IP) for audit and troubleshooting, but never log the secret in plain text.</li>
        <li>Perform request size limits and XML validation to avoid resource exhaustion or XML-based attacks.</li>
    </ul>
</div>

<div style="margin-top:12px" class="panel">
    <h3 style="font-size:15px;margin-bottom:8px">Troubleshooting</h3>
    <ol style="color:var(--muted); margin:0 0 0 18px;">
        <li>Confirm endpoint URL and that <code>{orderNumber}</code> exists in your orders table.</li>
        <li>Confirm header <code>X-Tally-Secret: prrahi-2025</code> is being sent (case-sensitive).</li>
        <li>Test locally with curl/Postman before integrating with Tally.</li>
        <li>Check Laravel logs (<code>storage/logs/laravel.log</code>) for server-side errors & stack trace.</li>
    </ol>
</div>

@endsection
