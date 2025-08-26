import React, { useMemo, useState } from 'react';
import { Head, usePage } from '@inertiajs/react';

export default function OrderWizard({ plans, calories, durations, zones, currency }) {
  const { locale } = usePage().props;

  const [step, setStep] = useState(1);
  const [form, setForm] = useState({
    plan_id: '', calorie_option_id: '', duration_id: '',
    start_date: '', delivery_zone_id: '',
    customer_name: '', phone: '', email: '',
    address_line: '', city: '', postal_code: '',
  });

  const [quote, setQuote] = useState(null);
  const change = (e) => setForm({ ...form, [e.target.name]: e.target.value });

  const canNext1 = form.plan_id && form.calorie_option_id && form.duration_id;
  const canNext2 = true; // start date optional
  const canNext3 = form.delivery_zone_id !== '';
  const canNext4 = form.customer_name && form.phone && form.address_line && form.city && form.postal_code;

  const rup = (cents)=> (cents/100).toFixed(2);

  const fetchPrice = async () => {
    const res = await fetch(`/${locale}/naruci/price`, {
      method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
      body: JSON.stringify({
        plan_id: form.plan_id,
        calorie_option_id: form.calorie_option_id,
        duration_id: form.duration_id,
        delivery_zone_id: form.delivery_zone_id || null,
      })
    });
    const data = await res.json();
    if(res.ok) setQuote(data); else alert(data.message||'Greška u obračunu');
  };

  const confirmOrder = async () => {
    const res = await fetch(`/${locale}/naruci/confirm`, {
      method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
      body: JSON.stringify(form)
    });
    if (res.redirected) {
      window.location.href = res.url; return;
    }
    const data = await res.text();
    alert('Greška pri potvrdi narudžbine: ' + data);
  };

  return (
    <div className="max-w-3xl mx-auto p-6">
      <Head title="Naruči" />
      <h1 className="text-3xl font-bold mb-6">🛒 Naruči (korak {step}/5)</h1>

      {/* Step 1 */}
      {step===1 && (
        <div className="space-y-4">
          <div>
            <label className="block font-semibold mb-1">Plan</label>
            <select name="plan_id" value={form.plan_id} onChange={change} className="w-full border p-2 rounded">
              <option value="">— izaberi —</option>
              {plans.map(p=> <option key={p.id} value={p.id}>{p.name}</option>)}
            </select>
          </div>

          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block font-semibold mb-1">Kalorije</label>
              <select name="calorie_option_id" value={form.calorie_option_id} onChange={change} className="w-full border p-2 rounded">
                <option value="">— izaberi —</option>
                {calories.map(c=> <option key={c.id} value={c.id}>{c.label || `${c.kcal} kcal`}</option>)}
              </select>
            </div>
            <div>
              <label className="block font-semibold mb-1">Trajanje</label>
              <select name="duration_id" value={form.duration_id} onChange={change} className="w-full border p-2 rounded">
                <option value="">— izaberi —</option>
                {durations.map(d=> <option key={d.id} value={d.id}>{d.label || `${d.days} dana`}</option>)}
              </select>
            </div>
          </div>

          <div className="flex justify-between mt-4">
            <div />
            <button disabled={!canNext1} onClick={()=>setStep(2)} className={`px-4 py-2 rounded ${canNext1?'bg-black text-white':'bg-gray-300'}`}>Dalje</button>
          </div>
        </div>
      )}

      {/* Step 2 */}
      {step===2 && (
        <div className="space-y-4">
          <div>
            <label className="block font-semibold mb-1">Datum početka</label>
            <input type="date" name="start_date" value={form.start_date||''} onChange={change} className="w-full border p-2 rounded"/>
          </div>
          <div className="flex justify-between mt-4">
            <button onClick={()=>setStep(1)} className="px-4 py-2 rounded border">Nazad</button>
            <button disabled={!canNext2} onClick={()=>setStep(3)} className={`px-4 py-2 rounded ${canNext2?'bg-black text-white':'bg-gray-300'}`}>Dalje</button>
          </div>
        </div>
      )}

      {/* Step 3 */}
      {step===3 && (
        <div className="space-y-4">
          <div>
            <label className="block font-semibold mb-1">Zona dostave</label>
            <select name="delivery_zone_id" value={form.delivery_zone_id} onChange={change} className="w-full border p-2 rounded">
              <option value="">— izaberi —</option>
              {zones.map(z=> <option key={z.id} value={z.id}>{z.name}</option>)}
            </select>
          </div>
          <div className="flex justify-between mt-4">
            <button onClick={()=>setStep(2)} className="px-4 py-2 rounded border">Nazad</button>
            <button disabled={!canNext3} onClick={()=>setStep(4)} className={`px-4 py-2 rounded ${canNext3?'bg-black text-white':'bg-gray-300'}`}>Dalje</button>
          </div>
        </div>
      )}

      {/* Step 4 */}
      {step===4 && (
        <div className="space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block font-semibold mb-1">Ime i prezime</label>
              <input name="customer_name" value={form.customer_name} onChange={change} className="w-full border p-2 rounded"/>
            </div>
            <div>
              <label className="block font-semibold mb-1">Telefon</label>
              <input name="phone" value={form.phone} onChange={change} className="w-full border p-2 rounded"/>
            </div>
          </div>
          <div>
            <label className="block font-semibold mb-1">Email (opciono)</label>
            <input name="email" value={form.email||''} onChange={change} className="w-full border p-2 rounded"/>
          </div>
          <div>
            <label className="block font-semibold mb-1">Adresa</label>
            <input name="address_line" value={form.address_line} onChange={change} className="w-full border p-2 rounded"/>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block font-semibold mb-1">Grad</label>
              <input name="city" value={form.city} onChange={change} className="w-full border p-2 rounded"/>
            </div>
            <div>
              <label className="block font-semibold mb-1">Poštanski broj</label>
              <input name="postal_code" value={form.postal_code} onChange={change} className="w-full border p-2 rounded"/>
            </div>
          </div>

          <div className="flex justify-between mt-4">
            <button onClick={()=>setStep(3)} className="px-4 py-2 rounded border">Nazad</button>
            <button disabled={!canNext4} onClick={async()=>{ await fetchPrice(); setStep(5); }} className={`px-4 py-2 rounded ${canNext4?'bg-black text-white':'bg-gray-300'}`}>Prikaži cenu</button>
          </div>
        </div>
      )}

      {/* Step 5 */}
      {step===5 && (
        <div className="space-y-4">
          <h2 className="text-xl font-semibold">Pregled i potvrda</h2>
          {quote ? (
            <div className="border rounded p-4">
              <p>Međuzbir: <strong>{rup(quote.subtotal_cents)} {quote.currency}</strong></p>
              <p>Dostava: <strong>{rup(quote.delivery_fee_cents)} {quote.currency}</strong></p>
              <p className="text-lg mt-2">Ukupno: <strong>{rup(quote.total_cents)} {quote.currency}</strong></p>
            </div>
          ) : <p>Računam...</p>}

          <div className="flex justify-between mt-4">
            <button onClick={()=>setStep(4)} className="px-4 py-2 rounded border">Nazad</button>
            <button onClick={confirmOrder} className="px-4 py-2 rounded bg-emerald-600 text-white">Potvrdi narudžbinu</button>
          </div>
        </div>
      )}
    </div>
  );
}
