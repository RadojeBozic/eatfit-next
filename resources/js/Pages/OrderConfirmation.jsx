import React from 'react';
import { Head, usePage, Link } from '@inertiajs/react';

export default function OrderConfirmation({ order }) {
  const { locale } = usePage().props;
  const f = (c)=> (c/100).toFixed(2);

  return (
    <div className="max-w-2xl mx-auto p-6">
      <Head title={`Potvrda #${order.id}`} />
      <h1 className="text-2xl font-bold mb-4">✅ Narudžbina #{order.id} primljena</h1>

      <div className="space-y-1">
        <div><strong>Ime:</strong> {order.customer_name}</div>
        <div><strong>Telefon:</strong> {order.phone}</div>
        {order.email && <div><strong>Email:</strong> {order.email}</div>}
        <div><strong>Adresa:</strong> {order.address_line}, {order.postal_code} {order.city}</div>
        <div><strong>Plan:</strong> {order.plan} | <strong>kcal:</strong> {order.kcal} | <strong>Trajanje:</strong> {order.duration_days} dana</div>
      </div>

      <div className="border rounded p-4 my-4">
        <p>Međuzbir: <strong>{f(order.subtotal_cents)} {order.currency}</strong></p>
        <p>Dostava: <strong>{f(order.delivery_fee_cents)} {order.currency}</strong></p>
        <p className="text-lg mt-2">Ukupno: <strong>{f(order.total_cents)} {order.currency}</strong></p>
      </div>

      {order.proforma_url && (
        <a href={order.proforma_url} target="_blank" rel="noreferrer" className="inline-block px-4 py-2 rounded bg-black text-white">
          Preuzmi PDF predračun
        </a>
      )}

      <div className="mt-6">
        <Link href={`/${locale}/`} className="underline">← Nazad na početnu</Link>
      </div>
    </div>
  );
}
