<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Domain\Plans\{Plan,CalorieOption,Duration,PriceMatrix};
use App\Domain\Delivery\DeliveryZone;
use App\Domain\Orders\{Order,OrderItem};

class OrderWizardController extends Controller
{
    public function index(string $locale)
    {
        $plans = Plan::where('is_active',true)->get()->map(fn($p)=>[
            'id'=>$p->id,
            'slug'=>$p->getTranslation('slug',$locale),
            'name'=>$p->getTranslation('name',$locale),
            'desc'=>$p->getTranslation('description',$locale),
        ]);

        $calories = CalorieOption::all()->map(fn($c)=>[
            'id'=>$c->id,'kcal'=>$c->kcal,'label'=>$c->getTranslation('label',$locale),
        ]);

        $durations = Duration::all()->map(fn($d)=>[
            'id'=>$d->id,'days'=>$d->days,'label'=>$d->getTranslation('label',$locale),
        ]);

        $zones = DeliveryZone::all()->map(fn($z)=>[
            'id'=>$z->id,'name'=>$z->getTranslation('name',$locale),'fee_cents'=>$z->fee_cents
        ]);

        return Inertia::render('OrderWizard', [
            'plans'=>$plans,
            'calories'=>$calories,
            'durations'=>$durations,
            'zones'=>$zones,
            'currency'=>'RSD',
        ]);
    }

    public function price(Request $request, string $locale)
    {
        $data = $request->validate([
            'plan_id'=>'required|exists:plans,id',
            'calorie_option_id'=>'required|exists:calorie_options,id',
            'duration_id'=>'required|exists:durations,id',
            'delivery_zone_id'=>'nullable|exists:delivery_zones,id',
        ]);

        $pm = PriceMatrix::where([
            'plan_id'=>$data['plan_id'],
            'calorie_option_id'=>$data['calorie_option_id'],
            'duration_id'=>$data['duration_id'],
        ])->orderByDesc('active_from')->first();

        if(!$pm){
            return response()->json(['message'=>'No price found'],422);
        }

        $delivery = 0;
        if(!empty($data['delivery_zone_id'])){
            $delivery = DeliveryZone::find($data['delivery_zone_id'])->fee_cents ?? 0;
        }

        $subtotal = $pm->price_cents;
        $total = $subtotal + $delivery;

        return response()->json([
            'subtotal_cents'=>$subtotal,
            'delivery_fee_cents'=>$delivery,
            'total_cents'=>$total,
            'currency'=>$pm->currency,
        ]);
    }

    public function confirm(Request $request, string $locale)
    {
        $data = $request->validate([
            'plan_id'=>'required|exists:plans,id',
            'calorie_option_id'=>'required|exists:calorie_options,id',
            'duration_id'=>'required|exists:durations,id',
            'start_date'=>'nullable|date',
            'delivery_zone_id'=>'nullable|exists:delivery_zones,id',
            'customer_name'=>'required|string|max:120',
            'phone'=>'required|string|max:40',
            'email'=>'nullable|email',
            'address_line'=>'required|string|max:180',
            'city'=>'required|string|max:80',
            'postal_code'=>'required|string|max:16',
        ]);

        // izračun cena
        $pm = PriceMatrix::where([
            'plan_id'=>$data['plan_id'],
            'calorie_option_id'=>$data['calorie_option_id'],
            'duration_id'=>$data['duration_id'],
        ])->orderByDesc('active_from')->firstOrFail();

        $delivery = 0;
        if(!empty($data['delivery_zone_id'])){
            $delivery = DeliveryZone::find($data['delivery_zone_id'])->fee_cents ?? 0;
        }

        $subtotal = $pm->price_cents;
        $total = $subtotal + $delivery;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'status'=>'placed',
                'payment_status'=>null,
                'plan_id'=>$data['plan_id'],
                'calorie_option_id'=>$data['calorie_option_id'],
                'duration_id'=>$data['duration_id'],
                'start_date'=>$data['start_date'] ?? null,
                'customer_name'=>$data['customer_name'],
                'phone'=>$data['phone'],
                'email'=>$data['email'] ?? null,
                'address_line'=>$data['address_line'],
                'city'=>$data['city'],
                'postal_code'=>$data['postal_code'],
                'delivery_zone_id'=>$data['delivery_zone_id'] ?? null,
                'subtotal_cents'=>$subtotal,
                'delivery_fee_cents'=>$delivery,
                'total_cents'=>$total,
                'currency'=>$pm->currency,
            ]);

            // Jednostavna stavka (ceo paket)
            $label = Plan::find($data['plan_id'])->getTranslation('name',$locale) . ' paket';
            $item = $order->items()->create([
                'label'=>$label,
                'qty'=>1,
                'unit_price_cents'=>$subtotal,
                'total_cents'=>$subtotal,
            ]);

            // PDF predračun (proforma)
            $pdfPath = "proforma/order_{$order->id}.pdf";
            $pdf = Pdf::loadView('pdf.proforma', ['order'=>$order, 'item'=>$item, 'locale'=>$locale]);
            \Storage::disk('public')->put($pdfPath, $pdf->output());
            $order->update(['proforma_path'=>$pdfPath]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error'=>$e->getMessage()]);
        }

        return redirect()->to("/{$locale}/narudzbina/{$order->id}/potvrda");
    }

    public function confirmation(string $locale, int $id)
    {
        $order = Order::with(['items','plan','calorie','duration','zone'])->findOrFail($id);
        return Inertia::render('OrderConfirmation', [
            'order'=>[
                'id'=>$order->id,
                'customer_name'=>$order->customer_name,
                'phone'=>$order->phone,
                'email'=>$order->email,
                'address_line'=>$order->address_line,
                'city'=>$order->city,
                'postal_code'=>$order->postal_code,
                'plan'=>$order->plan?->getTranslation('name',$locale),
                'kcal'=>$order->calorie?->kcal,
                'duration_days'=>$order->duration?->days,
                'subtotal_cents'=>$order->subtotal_cents,
                'delivery_fee_cents'=>$order->delivery_fee_cents,
                'total_cents'=>$order->total_cents,
                'currency'=>$order->currency,
                'proforma_url'=> $order->proforma_path ? url('storage/'.$order->proforma_path) : null,
            ],
        ]);
    }
}
