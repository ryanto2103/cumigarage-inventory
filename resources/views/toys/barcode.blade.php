<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Print Barcode — {{ $toy->name }}</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.6/JsBarcode.all.min.js"></script>
<style>
    body { font-family: 'Courier New', monospace; background: white; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
    .barcode-card { border: 2px solid #000; border-radius: 8px; padding: 24px 32px; text-align: center; display: inline-block; }
    .toy-name { font-size: 1rem; font-weight: bold; margin-bottom: 8px; max-width: 240px; word-break: break-word; }
    .toy-sku  { font-size: 0.75rem; color: #555; margin-bottom: 12px; }
    .toy-price { font-size: 0.9rem; font-weight: bold; margin-top: 10px; }
    .btn-print { margin-top: 24px; padding: 10px 24px; background: black; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 1rem; }
    @media print { .btn-print { display: none; } }
</style>
</head>
<body>
<div class="barcode-card">
    <div class="toy-name">{{ $toy->name }}</div>
    <div class="toy-sku">SKU: {{ $toy->sku }}</div>
    <svg id="barcode"></svg>
    <div style="font-size:0.75rem;color:#333;margin-top:4px">{{ $toy->barcode }}</div>
    <div class="toy-price">Rp {{ number_format($toy->sell_price, 0, ',', '.') }}</div>
</div>
<button class="btn-print" onclick="window.print()">🖨️ Print</button>
<script>
JsBarcode('#barcode', '{{ $toy->barcode }}', {
    format: 'CODE128',
    width: 2.5,
    height: 60,
    displayValue: false,
    background: '#ffffff',
    lineColor: '#000000'
});
</script>
</body>
</html>
