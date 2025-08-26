<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
  .wrap { width: 100%; }
  .header { margin-bottom: 16px; }
  .totals { margin-top: 16px; }
  table { width: 100%; border-collapse: collapse; }
  th, td { padding: 8px; border-bottom: 1px solid #ddd; text-align: left; }
</style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <h2>Predračun #{{ $order->id }}</h2>
      <p><strong>Ime:</strong> {{ $order->customer_name }}<br>
         <strong>Telefon:</strong> {{ $order->phone }}<br>
         <strong>Adresa:</strong> {{ $order->address_line }}, {{ $order->postal_code }} {{ $order->city }}</p>
      <p><strong>Datum početka:</strong> {{ $order->start_date ?? '—' }}</p>
    </div>

    <table>
      <thead>
        <tr><th>Stavka</th><th>Količina</th><th>Cena</th><th>Ukupno</th></tr>
      </thead>
      <tbody>
        <tr>
          <td>{{ $item->label }}</td>
          <td>{{ $item->qty }}</td>
          <td>{{ number_format($item->unit_price_cents/100,2,',','.') }} {{ $order->currency }}</td>
          <td>{{ number_format($item->total_cents/100,2,',','.') }} {{ $order->currency }}</td>
        </tr>
      </tbody>
    </table>

    <div class="totals">
      <p><strong>Međuzbir:</strong> {{ number_format($order->subtotal_cents/100,2,',','.') }} {{ $order->currency }}</p>
      <p><strong>Dostava:</strong> {{ number_format($order->delivery_fee_cents/100,2,',','.') }} {{ $order->currency }}</p>
      <h3>Za uplatu: {{ number_format($order->total_cents/100,2,',','.') }} {{ $order->currency }}</h3>
    </div>
  </div>
</body>
</html>
