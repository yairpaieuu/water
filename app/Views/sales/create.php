<?php
/**
 * Sales – Create
 * Variables: $customers (array), $products (array), $errors (array)
 */
declare(strict_types=1);

$pageTitle = 'New Sales Order';
$errors    = $errors ?? [];
$customers = $customers ?? [];
$products  = $products  ?? [];
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <a href="/orders"
           class="flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">New Sales Order</h1>
            <p class="text-sm text-slate-500 mt-0.5">Fill in the order details and add line items below.</p>
        </div>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="flex items-start gap-3 p-4 mb-6 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div>
        <p class="font-medium mb-1">Please fix the following errors:</p>
        <ul class="list-disc list-inside space-y-0.5">
            <?php foreach ($errors as $msg): ?><li><?= e($msg) ?></li><?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<?php
// Embed product data as JSON for Alpine.js price auto-fill
$productMap = [];
foreach ($products as $p) {
    $productMap[(int)$p['id']] = [
        'name'          => $p['name'] ?? '',
        'selling_price' => (float)($p['selling_price'] ?? 0),
    ];
}
?>

<form method="POST" action="/orders/store"
      x-data="orderForm()"
      x-init="init()"
      @submit.prevent="submitOrder">

    <?= \App\Core\CSRF::field() ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- LEFT: Order details + line items -->
        <div class="xl:col-span-2 space-y-6">

            <!-- Order Details card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-semibold text-slate-700 mb-4">Order Details</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- Customer -->
                    <div class="sm:col-span-2">
                        <label for="customer_id" class="block text-sm font-medium text-slate-700 mb-1">
                            Customer <span class="text-red-500">*</span>
                        </label>
                        <select id="customer_id" name="customer_id" required
                                class="block w-full border <?= isset($errors['customer_id']) ? 'border-red-400' : 'border-slate-300' ?> rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="">— Select Customer —</option>
                            <?php foreach ($customers as $c): ?>
                            <option value="<?= e($c['id']) ?>">
                                <?= e(trim(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? ''))) ?>
                                <?php if (!empty($c['phone'])): ?>(<?= e($c['phone']) ?>)<?php endif; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['customer_id'])): ?>
                        <p class="mt-1 text-xs text-red-600"><?= e($errors['customer_id']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Sale Date -->
                    <div>
                        <label for="sale_date" class="block text-sm font-medium text-slate-700 mb-1">
                            Sale Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="sale_date" name="sale_date" required
                               value="<?= e(date('Y-m-d')) ?>"
                               class="block w-full border <?= isset($errors['sale_date']) ? 'border-red-400' : 'border-slate-300' ?> rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select id="status" name="status"
                                class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="quotation">Quotation</option>
                            <option value="confirmed">Confirmed</option>
                        </select>
                    </div>

                    <!-- Notes -->
                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                        <textarea id="notes" name="notes" rows="2"
                                  placeholder="Optional order notes…"
                                  class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 resize-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- Line Items card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-slate-700">Line Items</h2>
                    <button type="button" @click="addItem()"
                            class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Item
                    </button>
                </div>

                <!-- Items table -->
                <div class="overflow-x-auto -mx-1">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="text-left px-3 py-2 font-semibold text-slate-600 w-2/5">Product</th>
                                <th class="text-center px-3 py-2 font-semibold text-slate-600 w-16">Qty</th>
                                <th class="text-right px-3 py-2 font-semibold text-slate-600 w-28">Unit Price</th>
                                <th class="text-right px-3 py-2 font-semibold text-slate-600 w-24">Discount</th>
                                <th class="text-right px-3 py-2 font-semibold text-slate-600 w-28">Line Total</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, idx) in items" :key="idx">
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="px-3 py-2">
                                        <select :name="'product_id[]'"
                                                x-model="item.product_id"
                                                @change="onProductChange(item)"
                                                class="block w-full border border-slate-300 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                                            <option value="">— Select —</option>
                                            <?php foreach ($products as $p): ?>
                                            <option value="<?= e($p['id']) ?>" data-price="<?= e((string)(float)($p['selling_price'] ?? 0)) ?>">
                                                <?= e($p['name'] ?? '') ?>
                                                <?php if (!empty($p['product_code'])): ?>(<?= e($p['product_code']) ?>)<?php endif; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="'quantity[]'" x-model.number="item.quantity"
                                               @input="calcLine(item)" min="1" step="1"
                                               class="block w-full border border-slate-300 rounded-lg px-2 py-1.5 text-xs text-center focus:outline-none focus:ring-2 focus:ring-sky-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="'unit_price[]'" x-model.number="item.unit_price"
                                               @input="calcLine(item)" min="0" step="0.01"
                                               class="block w-full border border-slate-300 rounded-lg px-2 py-1.5 text-xs text-right focus:outline-none focus:ring-2 focus:ring-sky-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" :name="'item_discount[]'" x-model.number="item.discount"
                                               @input="calcLine(item)" min="0" step="0.01"
                                               placeholder="0.00"
                                               class="block w-full border border-slate-300 rounded-lg px-2 py-1.5 text-xs text-right focus:outline-none focus:ring-2 focus:ring-sky-500">
                                    </td>
                                    <td class="px-3 py-2 text-right font-medium text-slate-800 whitespace-nowrap">
                                        LKR <span x-text="item.line_total.toFixed(2)"></span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" @click="removeItem(idx)"
                                                :disabled="items.length === 1"
                                                class="text-red-400 hover:text-red-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <p x-show="items.length === 0" class="text-sm text-slate-400 text-center py-6">
                    No items added yet. Click "+ Add Item" to begin.
                </p>
            </div>
        </div>

        <!-- RIGHT: Order summary + submit -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sticky top-20">
                <h2 class="text-base font-semibold text-slate-700 mb-4">Order Summary</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span>LKR <span x-text="subtotal.toFixed(2)"></span></span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Order Discount</span>
                        <div class="flex items-center gap-1">
                            <span class="text-xs text-slate-400">LKR</span>
                            <input type="number" name="discount" x-model.number="orderDiscount"
                                   @input="calcTotals()" min="0" step="0.01" placeholder="0.00"
                                   class="w-24 border border-slate-300 rounded px-2 py-1 text-xs text-right focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                    </div>

                    <div class="flex justify-between text-slate-600">
                        <span>Tax (0%)</span>
                        <span>LKR 0.00</span>
                    </div>

                    <div class="border-t border-slate-200 pt-3 flex justify-between font-bold text-slate-800 text-base">
                        <span>Total</span>
                        <span>LKR <span x-text="grandTotal.toFixed(2)"></span></span>
                    </div>
                </div>

                <!-- Hidden totals for form submission -->
                <input type="hidden" name="subtotal" :value="subtotal.toFixed(2)">
                <input type="hidden" name="tax" value="0">
                <input type="hidden" name="total" :value="grandTotal.toFixed(2)">

                <div class="mt-6 space-y-3">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Order
                    </button>
                    <a href="/orders"
                       class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div><!-- /grid -->
</form>

<script>
const PRODUCT_MAP = <?= json_encode($productMap, JSON_HEX_TAG | JSON_HEX_AMP) ?>;

function orderForm() {
    return {
        items: [],
        orderDiscount: 0,
        subtotal: 0,
        grandTotal: 0,

        init() {
            this.addItem();
        },

        addItem() {
            this.items.push({
                product_id: '',
                quantity: 1,
                unit_price: 0,
                discount: 0,
                line_total: 0,
            });
        },

        removeItem(idx) {
            if (this.items.length > 1) {
                this.items.splice(idx, 1);
                this.calcTotals();
            }
        },

        onProductChange(item) {
            const id = parseInt(item.product_id);
            if (id && PRODUCT_MAP[id]) {
                item.unit_price = PRODUCT_MAP[id].selling_price;
            } else {
                item.unit_price = 0;
            }
            this.calcLine(item);
        },

        calcLine(item) {
            const qty   = parseFloat(item.quantity)   || 0;
            const price = parseFloat(item.unit_price)  || 0;
            const disc  = parseFloat(item.discount)    || 0;
            item.line_total = Math.max(0, qty * price - disc);
            this.calcTotals();
        },

        calcTotals() {
            this.subtotal  = this.items.reduce((sum, i) => sum + (parseFloat(i.line_total) || 0), 0);
            const disc     = parseFloat(this.orderDiscount) || 0;
            this.grandTotal = Math.max(0, this.subtotal - disc);
        },

        submitOrder() {
            if (this.items.length === 0 || !this.items.some(i => i.product_id)) {
                Swal.fire({ icon: 'warning', title: 'No Items', text: 'Please add at least one product.' });
                return;
            }
            this.$el.submit();
        },
    };
}
</script>
