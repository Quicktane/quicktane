<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CreateOrderTest extends DuskTestCase
{
    public function test_full_checkout_flow_with_saved_address(): void
    {
        $this->browse(function (Browser $browser): void {
            // 1. Login
            $browser->visit('/login')
                ->waitFor('#email', 10)
                ->type('#email', 'john.doe@example.com')
                ->type('#password', 'password123')
                ->press('Sign In')
                ->waitForLocation('/', 10)
                ->pause(1000);

            // 2. Navigate to a product and add to cart
            $browser->visit('/products/classic-cotton-t-shirt')
                ->waitForText('Classic Cotton T-Shirt', 10)
                ->waitForText('Add to Cart', 5);

            $browser->script("document.querySelectorAll('button').forEach(b => { if (b.textContent.includes('Add to Cart')) b.click(); })");
            $browser->pause(5000) // wait for API response + toast to disappear
                ->screenshot('01-added-to-cart');

            // 3. Go to cart via JS navigation (avoids toast overlay blocking click)
            $browser->script("document.querySelector('a[href=\"/cart\"]').click()");
            $browser->waitForText('Shopping Cart', 10)
                ->waitForText('Classic Cotton T-Shirt', 10)
                ->screenshot('02-cart');

            // 4. Click Proceed to Checkout (client-side navigation, preserves cart context)
            $browser->script("document.querySelectorAll('button').forEach(b => { if (b.textContent.includes('Proceed to Checkout')) b.click(); })");
            $browser->pause(4000)
                ->screenshot('03-checkout-loaded');

            // 5. Checkout: select saved address
            $browser->waitForText('Shipping Address', 15)
                ->waitForText('123 Main Street', 10)
                ->screenshot('04-shipping-address');

            $browser->script("document.querySelectorAll('button').forEach(b => { if (b.textContent.includes('123 Main Street')) b.click(); })");
            $browser->pause(5000);

            // 6. Checkout: select shipping method
            $browser->waitForText('Shipping Method', 15)
                ->screenshot('05-shipping-method')
                ->pause(1000);

            $browser->script("
                const methods = document.querySelectorAll('button[class*=\"justify-between\"]');
                if (methods.length > 0) methods[0].click();
            ");
            $browser->pause(4000);

            // 7. Checkout: select payment method
            $browser->waitForText('Payment Method', 15)
                ->screenshot('06-payment-method')
                ->pause(1000);

            $browser->script("
                const payments = document.querySelectorAll('button[class*=\"items-start\"]');
                if (payments.length > 0) payments[0].click();
            ");
            $browser->pause(4000);

            // 8. Review and place order
            $browser->waitForText('Order Review', 15)
                ->screenshot('07-review')
                ->pause(2000);

            $browser->script("document.querySelectorAll('button').forEach(b => { if (b.textContent.includes('Place Order')) b.click(); })");
            $browser->pause(10000);

            // 9. Verify order confirmation
            $browser->screenshot('08-result');

            $browser->waitForText('Order Confirmed!', 20)
                ->assertSee('Thank you for your purchase')
                ->screenshot('09-order-confirmed');
        });
    }
}
