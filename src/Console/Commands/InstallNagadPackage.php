<?php

namespace Theshakhawat\NagadFdet\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallNagadPackage extends Command
{
    protected $signature = 'nagad:install';
    protected $description = 'Install Nagad Payment Gateway package and append routes to routes/web.php';

    public function handle()
    {
        $this->info('Installing Nagad Payment Gateway Package...');

        // ১. কনফিগ ফাইল পাবলিশ করা
        $this->call('vendor:publish', ['--tag' => 'nagad-config', '--force' => true]);

        // ২. কন্ট্রোলার ফাইল ইউজারের app/Http/Controllers এ কপি করা
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

        // ৩. routes/web.php এ স্বয়ংক্রিয়ভাবে রাউট যুক্ত করা
        $webRoutesPath = base_path('routes/web.php');

        if (File::exists($webRoutesPath)) {
            $routesContent = File::get($webRoutesPath);

            $nagadRoutes = <<<PHP


// --- Nagad Payment Gateway Routes ---
use App\Http\Controllers\NagadPaymentController;

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

        $this->info('🎉 Nagad package installed successfully! Check your routes/web.php.');
    }
}
