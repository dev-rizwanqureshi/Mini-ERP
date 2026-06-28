<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use App\Policies\Concerns\ChecksRolePermissions;

class InvoicePolicy
{
    use ChecksRolePermissions;

    public function viewAny(User $user): bool { return $this->allows($user, 'invoices', 'viewAny'); }
    public function view(User $user, Invoice $invoice): bool { return $this->allows($user, 'invoices', 'view'); }
    public function create(User $user): bool { return $this->allows($user, 'invoices', 'create'); }
    public function update(User $user, Invoice $invoice): bool { return $this->allows($user, 'invoices', 'update') && $invoice->isEditable(); }
    public function delete(User $user, Invoice $invoice): bool { return $this->allows($user, 'invoices', 'delete') && $invoice->status->canDelete(); }
    public function send(User $user, Invoice $invoice): bool { return $this->allows($user, 'invoices', 'send') && $invoice->status->canSend(); }
    public function download(User $user, Invoice $invoice): bool { return $this->allows($user, 'invoices', 'download'); }
}
