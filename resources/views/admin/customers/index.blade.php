<x-admin-layout>
    <x-slot name="header">
        <div>
            <h1 class="font-display text-xl font-semibold text-jewel-dark">Customers</h1>
            <p class="text-sm text-gray-500">{{ $customers->total() }} registered customers</p>
        </div>
    </x-slot>

    <form method="GET" class="mb-6">
        <div class="flex gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                placeholder="Search name, email or phone..."
                class="jewel-input flex-1 max-w-md">
            <button type="submit" class="jewel-btn px-6">Search</button>
        </div>
    </form>

    <div class="bg-white rounded-xl border border-jewel-gold/10 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-jewel-gold/10">
                <thead class="bg-jewel-cream/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Joined</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-jewel-gold/10">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-jewel-cream/30 transition">
                            <td class="px-6 py-4">
                                <p class="font-medium text-jewel-dark">{{ $customer->name }}</p>
                                <p class="text-sm text-gray-400">{{ $customer->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->phone ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->city ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-jewel-gold/15 text-jewel-gold-dark">
                                    {{ $customer->orders_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="text-sm font-medium text-jewel-gold-dark hover:text-jewel-gold">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">No customers found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-jewel-gold/10">{{ $customers->links() }}</div>
        @endif
    </div>
</x-admin-layout>
