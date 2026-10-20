<?php

namespace Tests\Support;

use Stripe\ApiRequestor;

trait InteractsWithFakeStripe
{
    protected FakeStripeHttpClient $fakeStripe;

    protected function fakeStripeHttp(): FakeStripeHttpClient
    {
        config(['services.stripe.secretKey' => 'sk_test_fake']);
        $this->fakeStripe = new FakeStripeHttpClient();
        ApiRequestor::setHttpClient($this->fakeStripe);

        return $this->fakeStripe;
    }

    protected function tearDownFakeStripe(): void
    {
        ApiRequestor::setHttpClient(null);
    }
}
