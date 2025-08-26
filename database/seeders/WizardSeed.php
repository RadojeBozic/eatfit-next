<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Plans\{Plan,CalorieOption,Duration,PriceMatrix};
use App\Domain\Delivery\DeliveryZone;

class WizardSeed extends Seeder
{
    public function run(): void
    {
        // Plans
        $plans = [
            ['slug'=>['sr-Latn-RS'=>'balans','sr-Cyrl-RS'=>'баланс','en'=>'balance'],
             'name'=>['sr-Latn-RS'=>'Balans','sr-Cyrl-RS'=>'Баланс','en'=>'Balance'],
             'description'=>['sr-Latn-RS'=>'Uravnotežen plan','sr-Cyrl-RS'=>'Уравнотежен план','en'=>'Balanced plan']],
            ['slug'=>['sr-Latn-RS'=>'keto','sr-Cyrl-RS'=>'кето','en'=>'keto'],
             'name'=>['sr-Latn-RS'=>'Keto','sr-Cyrl-RS'=>'Кето','en'=>'Keto'],
             'description'=>['sr-Latn-RS'=>'Nizak unos UH','sr-Cyrl-RS'=>'Низак унос УХ','en'=>'Low carb']],
            ['slug'=>['sr-Latn-RS'=>'high-protein','sr-Cyrl-RS'=>'хај-протеин','en'=>'high-protein'],
             'name'=>['sr-Latn-RS'=>'High Protein','sr-Cyrl-RS'=>'Хај Протеин','en'=>'High Protein'],
             'description'=>['sr-Latn-RS'=>'Više proteina','sr-Cyrl-RS'=>'Више протеина','en'=>'Higher protein']],
        ];
        foreach ($plans as $p) Plan::create($p);

        // Calories
        foreach ([1500,1800,2200] as $k) {
            CalorieOption::create(['kcal'=>$k,'label'=>[
                'sr-Latn-RS'=>"$k kcal",'sr-Cyrl-RS'=>"$k ккал",'en'=>"$k kcal"
            ]]);
        }

        // Durations
        foreach ([[1,'1 dan','1 дан','1 day'],[5,'5 dana','5 дана','5 days'],[6,'6 dana','6 дана','6 days']] as [$d,$lat,$cyr,$en]) {
            Duration::create(['days'=>$d,'label'=>[
                'sr-Latn-RS'=>$lat,'sr-Cyrl-RS'=>$cyr,'en'=>$en
            ]]);
        }

        // Price matrix (demo)
        foreach (Plan::all() as $plan) {
            foreach (CalorieOption::all() as $cal) {
                foreach (Duration::all() as $dur) {
                    PriceMatrix::create([
                        'plan_id'=>$plan->id,
                        'calorie_option_id'=>$cal->id,
                        'duration_id'=>$dur->id,
                        'price_cents'=> (int)(3500 * $dur->days), // demo
                        'currency'=>'RSD',
                        'active_from'=>now()->toDateString(),
                    ]);
                }
            }
        }

        // Delivery zones (demo)
        DeliveryZone::create(['name'=>['sr-Latn-RS'=>'Beograd','sr-Cyrl-RS'=>'Београд','en'=>'Belgrade'],'fee_cents'=>0]);
        DeliveryZone::create(['name'=>['sr-Latn-RS'=>'Novi Sad','sr-Cyrl-RS'=>'Нови Сад','en'=>'Novi Sad'],'fee_cents'=>20000]);
    }
}
