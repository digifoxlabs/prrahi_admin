<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tally API Documentation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        pre { white-space: pre-wrap; }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">

<div class="max-w-6xl mx-auto px-6 py-8" x-data="{ tab: 'get' }">

    <!-- HEADER -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tally Integration API</h1>
        <p class="text-gray-600 mt-1">
            Inventory & Order Synchronization APIs for Tally
        </p>
    </div>

    <!-- TABS -->
    <div class="flex gap-4 border-b mb-6">
        <button @click="tab='get'"
                class="pb-2 font-medium"
                :class="tab==='get' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500'">
            Get Pending Invoice Orders
        </button>

        <button @click="tab='post'"
                class="pb-2 font-medium"
                :class="tab==='post' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500'">
            Post Invoice Generated
        </button>

        <button @click="tab='status'"
                class="pb-2 font-medium"
                :class="tab==='status' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500'">
            Get Invoice Status
        </button>
    </div>

    <!-- ================= GET ORDERS ================= -->
    <div x-show="tab==='get'" x-cloak>
        <h2 class="text-xl font-semibold mb-3">1. Fetch Orders Pending Invoice</h2>

        <div class="bg-white rounded-lg p-5 shadow mb-6">
            <p><strong>Method:</strong> GET</p>
            <p><strong>Endpoint:</strong></p>

            <code class="block mt-2 bg-gray-900 text-green-400 p-3 rounded">
                {{ rtrim(config('app.url'), '/') }}/api/tally/orders/pending-invoice
            </code>
        </div>

        <h3 class="font-semibold mb-2">Headers</h3>
        <pre class="bg-gray-900 text-green-400 p-4 rounded mb-6">
Accept: application/json
X-API-KEY: YOUR_TALLY_API_KEY
        </pre>

        <h3 class="font-semibold mb-2">Sample Response</h3>
        <pre class="bg-gray-900 text-gray-200 p-4 rounded overflow-x-auto">
{
  "status": "success",
  "count": 1,
  "data": [
    {
      "distributor": {
        "firm_name": "ABC Distributors",
        "nature_of_firm": "Proprietorship",
        "gst": "18ABCDE1234F1Z5",
        "contact_person": "Ramesh",
        "contact_number": "9876543210"
      },
      "order": {
        "order_number": "ORD-2025-00012",
        "order_date": "2025-01-10",
        "billing_address": "Guwahati, Assam",
        "subtotal": 12000,
        "discount": 500,
        "cgst": 287.5,
        "sgst": 287.5,
        "round_off": -0.5,
        "total_amount": 12075
      },
      "items": [
        {
          "product_name": "Detergent Powder - Lemon (DP-001)",
          "rate": 120,
          "base_unit": "Dozen",
          "quantity": 5,
          "discount_percent": 5,
          "total": 600
        }
      ]
    }
  ]
}
        </pre>
    </div>

    <!-- ================= POST INVOICE ================= -->
    <div x-show="tab==='post'" x-cloak>
        <h2 class="text-xl font-semibold mb-3">2. Post Invoice Generated</h2>

        <div class="bg-white rounded-lg p-5 shadow mb-6">
            <p><strong>Method:</strong> POST</p>
            <p><strong>Endpoint:</strong></p>

            <code class="block mt-2 bg-gray-900 text-green-400 p-3 rounded">
                {{ rtrim(config('app.url'), '/') }}/api/tally/orders/invoice-generated
            </code>
        </div>

        <h3 class="font-semibold mb-2">Headers</h3>
        <pre class="bg-gray-900 text-green-400 p-4 rounded mb-4">
Content-Type: application/json
Accept: application/json
X-API-KEY: YOUR_TALLY_API_KEY
        </pre>

        <h3 class="font-semibold mb-2">Request Body</h3>
        <pre class="bg-gray-900 text-gray-200 p-4 rounded mb-4">
{
  "order_number": "ORD-2025-00012",
  "invoice_no": "INV-4599",
  "invoice_date": "2025-01-10"
}
        </pre>

        <h3 class="font-semibold mb-2">Response</h3>
        <pre class="bg-gray-900 text-gray-200 p-4 rounded">
{
  "status": "success",
  "message": "Invoice details saved successfully"
}
        </pre>
    </div>

    <!-- ================= GET STATUS ================= -->
    <div x-show="tab==='status'" x-cloak>
        <h2 class="text-xl font-semibold mb-3">3. Get Invoice Status</h2>

        <div class="bg-white rounded-lg p-5 shadow mb-6">
            <p><strong>Method:</strong> GET</p>
            <p><strong>Endpoint:</strong></p>

            <code class="block mt-2 bg-gray-900 text-green-400 p-3 rounded">
                {{ rtrim(config('app.url'), '/') }}/api/tally/orders/invoice-status?order_number=ORD-2025-00012
            </code>
        </div>

        <h3 class="font-semibold mb-2">Sample Response</h3>
        <pre class="bg-gray-900 text-gray-200 p-4 rounded">
{
  "status": "success",
  "order": {
    "order_number": "ORD-2025-00012",
    "order_status": "confirmed",
    "invoice_status": "generated",
    "bill_generated": true,
    "invoice": {
      "invoice_no": "INV-4599",
      "invoice_date": "2025-01-10"
    }
  }
}
        </pre>
    </div>

</div>

</body>
</html>
