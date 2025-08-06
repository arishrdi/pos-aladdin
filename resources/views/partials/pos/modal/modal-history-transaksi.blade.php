<div
  id="transactionModal"
  class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden"
>
  <div class="bg-white rounded-lg w-full max-w-lg mx-4 overflow-hidden shadow-lg">
    {{-- Header --}}
    <div class="flex justify-between items-center px-6 py-4 border-b">
      <h3 class="text-xl font-semibold">Detail Transaksi</h3>
      <button
        type="button"
        onclick="tutupModal('transactionModal')"
        class="text-gray-500 hover:text-gray-700 text-2xl leading-none"
      >&times;</button>
    </div>

    {{-- Body --}}
    <div class="p-6 space-y-6">
      {{-- Invoice + Tanggal & Status --}}
      <div class="space-y-4">
        <div>
          <span class="font-medium">Invoice:</span>
          <span id="modalInvoice" class="font-mono">-</span>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <div class="text-sm text-gray-500">Tanggal</div>
            <div id="modalDate" class="font-medium">-</div>
          </div>
          <div>
            <div class="text-sm text-gray-500">Status</div>
            <div id="modalStatus" class="inline-block px-3 py-1 rounded-full text-xs font-medium">-</div>
          </div>
        </div>
      </div>

      {{-- Tabel Produk --}}
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-3 py-2">Produk</th>
              <th class="px-3 py-2">Qty</th>
              <th class="px-3 py-2">Harga</th>
              <th class="px-3 py-2">Total</th>
            </tr>
          </thead>
          <tbody id="modalItems" class="divide-y"></tbody>
        </table>
      </div>

      {{-- Ringkasan Harga --}}
      <div class="space-y-2">
        <div class="flex justify-between text-sm">
          <div class="font-medium">Subtotal</div>
          <div id="modalSubtotal" class="font-medium">Rp 0</div>
        </div>
        <div class="flex justify-between text-sm">
          <div class="font-medium">Pajak</div>
          <div id="modalTax" class="font-medium">Rp 0</div>
        </div>
        <div class="flex justify-between text-sm">
          <div class="font-medium">Diskon</div>
          <div id="modalDiscount" class="font-medium">Rp 0</div>
        </div>
        <div class="flex justify-between text-base font-semibold border-t pt-2">
          <div>Total</div>
          <div id="modalTotal">Rp 0</div>
        </div>
      </div>

      {{-- Bayar & Kembalian --}}
      <div class="flex justify-between text-sm">
        <div class="font-medium">Total Bayar</div>
        <div id="modalTotalPaid" class="font-medium text-green-600">-</div>
      </div>
      <div class="flex justify-between text-sm">
        <div class="font-medium">Kembalian</div>
        <div id="modalChange" class="font-medium text-green-600">-</div>
      </div>
    </div>
  </div>
</div>