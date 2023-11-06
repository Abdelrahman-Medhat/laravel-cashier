<?php

namespace AbdelrahmanMedhat\Cashier;

use AbdelrahmanMedhat\Cashier\Concerns\HandlesTaxes;
use AbdelrahmanMedhat\Cashier\Concerns\ManagesCustomer;
use AbdelrahmanMedhat\Cashier\Concerns\ManagesInvoices;
use AbdelrahmanMedhat\Cashier\Concerns\ManagesPaymentMethods;
use AbdelrahmanMedhat\Cashier\Concerns\ManagesSubscriptions;
use AbdelrahmanMedhat\Cashier\Concerns\PerformsCharges;

trait Billable
{
    use HandlesTaxes;
    use ManagesCustomer;
    use ManagesInvoices;
    use ManagesPaymentMethods;
    use ManagesSubscriptions;
    use PerformsCharges;
}
