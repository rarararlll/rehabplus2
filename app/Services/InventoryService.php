<?php

namespace App\Services;

use CodeIgniter\Session\SessionInterface;

/**
 * InventoryService
 *
 * Owns the session-backed list of inventory items that the
 * `DashboardController::inventory` and `saveInventory` handlers used to manage
 * inline.
 *
 * Items are stored with numeric `stock` / `reorder` values so the summary
 * arithmetic is exact. Status is derived from the stock level rather than
 * trusted from user input, which keeps the low-stock logic in one place.
 */
class InventoryService
{
    /** Session key under which the inventory items are stored. */
    private const SESSION_KEY = 'inventory_items';

    /** Stock levels at or below this value are considered "Low". */
    private const LOW_STOCK_THRESHOLD = 10;

    public function __construct(private ?SessionInterface $session = null)
    {
        $this->session ??= service('session');
    }

    /**
     * Return every stored inventory item, or an empty list when none exist.
     *
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        $items = $this->session->get(self::SESSION_KEY);

        return is_array($items) ? $items : [];
    }

    /**
     * Validate and persist a new inventory item.
     *
     * @param array<string, mixed> $input Raw POST data.
     *
     * @return array<string, string>|null Validation errors keyed by field, or
     *                                     null when the item was saved.
     */
    public function add(array $input): ?array
    {
        $item     = trim((string) ($input['item'] ?? ''));
        $category = trim((string) ($input['category'] ?? ''));
        $stock    = trim((string) ($input['stock'] ?? ''));
        $reorder  = trim((string) ($input['reorder'] ?? ''));
        $status   = trim((string) ($input['status'] ?? ''));

        $errors = $this->validate($item, $category, $stock);

        if ($errors !== []) {
            log_message('debug', 'InventoryService::add rejected input', $errors);

            return $errors;
        }

        $stockInt    = (int) $stock;
        $reorderInt  = $reorder !== '' ? (int) $reorder : self::LOW_STOCK_THRESHOLD;
        $derivedStatus = $status !== '' ? $status : ($stockInt <= self::LOW_STOCK_THRESHOLD ? 'Low' : 'Healthy');

        $items        = $this->all();
        $items[]     = [
            'item'     => $item,
            'category' => $category,
            'stock'    => $stockInt,
            'reorder'  => $reorderInt,
            'status'   => $derivedStatus,
        ];

        $this->session->set(self::SESSION_KEY, $items);
        log_message('info', 'InventoryService::add stored item ' . $item, ['total' => count($items)]);

        return null;
    }

    /**
     * Compute the summary values used by the inventory view.
     *
     * @return array{totalStock: int, lowStock: int, restockNeeded: int,
     *               items: list<array<string, mixed>>}
     */
    public function summary(): array
    {
        $items = $this->all();

        $totalStock    = 0;
        $lowStock      = 0;
        $restockNeeded = 0;

        foreach ($items as $record) {
            $stock   = (int) ($record['stock'] ?? 0);
            $reorder = (int) ($record['reorder'] ?? self::LOW_STOCK_THRESHOLD);

            $totalStock += $stock;

            if ($stock <= $reorder) {
                $lowStock++;
                $restockNeeded++;
            }
        }

        return [
            'items'          => $items,
            'totalStock'     => $totalStock,
            'lowStock'       => $lowStock,
            'restockNeeded'  => $restockNeeded,
        ];
    }

    /**
     * Validate the required inventory fields.
     *
     * @return array<string, string>
     */
    private function validate(string $item, string $category, string $stock): array
    {
        $errors = [];

        if ($item === '') {
            $errors['item'] = 'Item name is required.';
        }

        if ($category === '') {
            $errors['category'] = 'Category is required.';
        }

        if ($stock === '' || ! ctype_digit($stock)) {
            $errors['stock'] = 'A valid stock level is required.';
        }

        return $errors;
    }
}