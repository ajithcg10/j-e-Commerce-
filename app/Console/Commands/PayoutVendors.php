<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Payout;
use App\Models\Vendor;
use App\OrderStatusEnum;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class PayoutVendors extends Command
{
    
    protected $signature = 'payout:vendors';

   
    protected $description = 'Perform vendors payouts ';

    public function handle()
    {
        $this->info('Starting monthly payout process for vendors...');

        $vendors = Vendor::eligibleForPayout()->get();

        foreach ($vendors as $vendor) {
            $this->processPayout($vendor);

        }
        $this->info('Monthly payout process completed successfully' ) ;
        
        return Command::SUCCESS;
       
    }

    protected function processPayout(Vendor $vendor)
    {
        
        $this->info("Payout processed for vendor: [ID: {$vendor->user_id}, Name: {$vendor->store_name}]");

        try {
            DB::beginTransaction();
            $startingFrom = Payout::where('vendor_id', $vendor->user_id)
                ->orderBY('until', 'desc')->value('until');

            $startingFrom = $startingFrom ?: Carbon::make('1970-01-01');
            $until = Carbon::now()->subMonthNoOverflow()->startOfMonth();
            $vendorSubTotal = Order::query()->where('user_id', $vendor->user_id)
                ->where('status', OrderStatusEnum::Paid->value)->whereBetween('created_at', [$startingFrom, $until])
                ->sum('vendor_sub_total');

            if($vendorSubTotal ) {
                $this->info('Payout made with amount: ' . $vendorSubTotal);
                Payout::create([
                    'vendor_id' => $vendor->user_id,
                    'amount' => $vendorSubTotal,
                    'starting_from' => $startingFrom,
                    'until' => $until,
                ]);
                $vendor->user->transfer((int)($vendorSubTotal * 100), config('app.currency'));
            }
            else{
                $this->info('Nothing to process');
            }
        }
        catch (\Exception $e) {
            $this->error("Failed to process payout for vendor: [ID: {$vendor->user_id}, Name: {$vendor->store_name}]. Error: {$e->getMessage()}");
        }

    }
}
