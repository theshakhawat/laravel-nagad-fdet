<?php

namespace Theshakhawat\NagadFdet\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallNagadPackage extends Command
{
    protected $signature = 'nagad:install';
    protected $description = 'Install Nagad Payment Gateway package: config, .env, controller, views, and routes';

    public function handle()
    {
        $this->info('🚀 Installing Nagad Payment Gateway Package...');

        // ১. কনফিগ ফাইল পাবলিশ করা
        $this->call('vendor:publish', ['--tag' => 'nagad-config', '--force' => true]);

        // ২. .env ফাইলে পরিবেশ ভেরিয়েবল যোগ করা
        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $envContent = File::get($envPath);
            if (!str_contains($envContent, 'NAGAD_API_KEY')) {
                $envStub = "\n# Nagad Payment Gateway (FDET)\nNAGAD_API_KEY=\"\"\nNAGAD_BASE_URL=\"https://nagad.banglaqr.online\"\n";
                File::append($envPath, $envStub);
                $this->info('✓ NAGAD_API_KEY & NAGAD_BASE_URL added to your .env file');
            } else {
                $this->warn('! NAGAD_API_KEY already exists in .env');
            }
        }

        // ৩. ডেমো চেকআউট ভিউ resources/views/nagad/pay.blade.php এ কপি করা
        $targetViewDir = resource_path('views/nagad');
        if (!File::isDirectory($targetViewDir)) {
            File::makeDirectory($targetViewDir, 0755, true);
        }
        $sourceView = __DIR__ . '/../../resources/views/pay.blade.php';
        $targetView = resource_path('views/nagad/pay.blade.php');

        if (!File::exists($targetView)) {
            File::copy($sourceView, $targetView);
            $this->info('✓ Demo checkout view copied to resources/views/nagad/pay.blade.php');
        } else {
            $this->warn('! pay.blade.php already exists in resources/views/nagad/');
        }

        // ৪. কন্ট্রোলার ফাইল ইউজারের app/Http/Controllers এ কপি করা
        $targetController = app_path('Http/Controllers/NagadPaymentController.php');
        $sourceController = __DIR__ . '/../../Http/Controllers/NagadPaymentController.php';

        if (!File::exists($targetController)) {
            $content = File::get($sourceController);
            // Namespace পরিবর্তন করে App\Http\Controllers করা
            $content = str_replace(
                'namespace Theshakhawat\NagadFdet\Http\Controllers;',
                'namespace App\Http\Controllers;',
                $content
            );
            File::put($targetController, $content);
            $this->info('✓ NagadPaymentController published to app/Http/Controllers/');
        } else {
            $this->warn('! NagadPaymentController already exists in app/Http/Controllers/');
        }

        // ৫. routes/web.php এ স্বয়ংক্রিয়ভাবে রাউট যুক্ত করা
        $webRoutesPath = base_path('routes/web.php');

        if (File::exists($webRoutesPath)) {
            $routesContent = File::get($webRoutesPath);

            $nagadRoutes = <<<PHP


// --- Nagad Payment Gateway Routes ---
use App\Http\Controllers\NagadPaymentController;

Route::get('/nagad/checkout', [NagadPaymentController::class, 'checkout'])->name('nagad.payment.checkout');
Route::post('/nagad/payment/initiate', [NagadPaymentController::class, 'initiatePayment'])->name('nagad.payment.initiate');
Route::get('/nagad/payment/callback', [NagadPaymentController::class, 'handleCallback'])->name('nagad.payment.callback');
Route::get('/nagad/payment/verify/{paymentRefId?}', [NagadPaymentController::class, 'verifyPayment'])->name('nagad.payment.verify');
PHP;

            if (!str_contains($routesContent, 'nagad.payment.initiate')) {
                File::append($webRoutesPath, $nagadRoutes);
                $this->info('✓ Nagad routes successfully appended to routes/web.php');
            } else {
                $this->warn('! Nagad routes already exist in routes/web.php');
            }
        }

        $this->newLine();
        $this->info('🎉 Nagad package installed successfully!');
        $this->comment('👉 Check your .env to add your NAGAD_API_KEY');
        $this->comment('👉 Visit http://your-domain.test/nagad/checkout to test payment');
    }
}
