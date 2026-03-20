<?php
declare(strict_types=1);

namespace App\Models;

class Lead extends BaseModel
{
    protected string $table = 'leads';

    /**
     * Find a lead by its unique lead code.
     */
    public function findByCode(string $code): array|false
    {
        return $this->db->fetch(
            "SELECT * FROM `{$this->table}` WHERE `lead_code` = ? LIMIT 1",
            [$code]
        );
    }

    /**
     * Return a lead joined with branch and assigned user details.
     */
    public function getWithDetails(int $id): array|false
    {
        return $this->db->fetch(
            "SELECT l.*,
                    b.`name`       AS branch_name,
                    u.`name`       AS assigned_to_name,
                    c.`name`       AS created_by_name
               FROM `{$this->table}` l
          LEFT JOIN `branches` b ON b.`id` = l.`branch_id`
          LEFT JOIN `users`    u ON u.`id` = l.`assigned_to`
          LEFT JOIN `users`    c ON c.`id` = l.`created_by`
              WHERE l.`id` = ?
              LIMIT 1",
            [$id]
        );
    }

    /**
     * Convert a lead to a customer record.
     * Sets lead status to 'won' and creates a new customer row.
     *
     * @param  array<string, mixed> $data  Customer fields to override/supplement.
     * @return int|false  The new customer ID, or false on failure.
     */
    public function convertToCustomer(int $leadId, array $data): int|false
    {
        $lead = $this->find($leadId);
        if ($lead === false) {
            return false;
        }

        $customerModel = new Customer();

        $customerData = array_merge([
            'customer_code' => $customerModel->generateCode(),
            'first_name'    => $lead['first_name'],
            'last_name'     => $lead['last_name'],
            'email'         => $lead['email'],
            'phone'         => $lead['phone'],
            'address'       => $lead['address'],
            'city'          => $lead['city'],
            'branch_id'     => $lead['branch_id'],
            'source'        => 'lead_conversion',
            'status'        => 'active',
        ], $data);

        $this->db->beginTransaction();
        try {
            $customerId = $customerModel->create($customerData);
            if ($customerId === false) {
                $this->db->rollback();
                return false;
            }

            $this->update($leadId, ['status' => 'won']);

            $this->db->commit();
            return $customerId;
        } catch (\Throwable $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Generate the next LEAD-XXXX code.
     */
    public function generateCode(): string
    {
        $row = $this->db->fetch(
            "SELECT `lead_code` FROM `{$this->table}` ORDER BY `id` DESC LIMIT 1"
        );

        $next = 1;
        if ($row !== false) {
            $parts = explode('-', $row['lead_code']);
            $next  = (int) end($parts) + 1;
        }

        return 'LEAD-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
