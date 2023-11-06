<?php

namespace AbdelrahmanMedhat\Cashier\Contracts;

use AbdelrahmanMedhat\Cashier\Invoice;

interface InvoiceRenderer
{
    /**
     * Render the invoice as a PDF and return the raw bytes.
     *
     * @param  \AbdelrahmanMedhat\Cashier\Invoice  $invoice
     * @param  array  $data
     * @param  array  $options
     * @return string
     */
    public function render(Invoice $invoice, array $data = [], array $options = []): string;
}
