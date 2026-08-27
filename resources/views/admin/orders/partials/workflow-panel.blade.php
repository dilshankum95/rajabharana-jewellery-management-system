@php
    $productionSteps = \App\Enums\ProductionStatus::orderedSteps();
    $currentProduction = $order->production_status;
@endphp

<section class="jewel-card overflow-hidden mb-6">
    <div class="jewel-card-header border-b border-jewel-gold/10 bg-gradient-to-r from-jewel-cream/80 to-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="jewel-section-title">Production Workflow</h2>
                <p class="text-sm text-slate-500 mt-0.5">Track progress, assign technicians, and manage production</p>
            </div>
            @if($order->status === \App\Enums\OrderStatus::Accepted)
                <div class="flex flex-wrap items-center gap-2">
                    <x-task-status-badge :status="$order->task_status" />
                    <x-production-status-badge :status="$order->production_status" />
                </div>
            @else
                <x-order-status-badge :status="$order->status" />
            @endif
        </div>
    </div>

    <div class="jewel-card-body space-y-6">
        {{-- Status overview --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Order</p>
                <div class="mt-2"><x-order-status-badge :status="$order->status" /></div>
            </div>
            @if($order->status === \App\Enums\OrderStatus::Accepted)
                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                    <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Technician</p>
                    <p class="mt-2 text-sm font-medium text-jewel-dark">
                        {{ $order->assignedTechnician?->name ?? 'Not assigned' }}
                    </p>
                    @if($order->assigned_at)
                        <p class="text-xs text-slate-400 mt-0.5">Since {{ $order->assigned_at->format('M d, Y') }}</p>
                    @endif
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                    <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Task</p>
                    <div class="mt-2"><x-task-status-badge :status="$order->task_status" /></div>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                    <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Production</p>
                    <div class="mt-2"><x-production-status-badge :status="$order->production_status" /></div>
                </div>
            @endif
        </div>

        @if($order->status === \App\Enums\OrderStatus::Accepted)
            {{-- Production stepper --}}
            <div>
                <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold mb-3">Production pipeline</p>
                <ol class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-0">
                    @foreach($productionSteps as $index => $step)
                        @php
                            $currentIndex = $currentProduction !== null
                                ? array_search($currentProduction, $productionSteps, true)
                                : false;
                            $isCurrent = $currentProduction === $step;
                            $isComplete = $currentIndex !== false && $index < $currentIndex;
                        @endphp
                        <li class="flex sm:flex-1 items-center gap-2 sm:gap-0">
                            <div @class([
                                'flex items-center gap-2 sm:flex-col sm:text-center sm:flex-1 px-3 py-2 rounded-lg sm:rounded-none sm:px-2',
                                'bg-emerald-50 ring-1 ring-emerald-200' => $isCurrent,
                                'opacity-100' => $isComplete,
                                'opacity-50' => ! $isComplete && ! $isCurrent,
                            ])>
                                <span @class([
                                    'flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                                    'bg-emerald-600 text-white' => $isCurrent,
                                    'bg-emerald-100 text-emerald-700' => $isComplete && ! $isCurrent,
                                    'bg-slate-100 text-slate-400' => ! $isComplete,
                                ])>{{ $index + 1 }}</span>
                                <span class="text-xs sm:text-[11px] font-medium text-slate-700 sm:mt-1.5 leading-tight">{{ $step->label() }}</span>
                            </div>
                            @if(! $loop->last)
                                <div class="hidden sm:block h-px flex-1 bg-slate-200 mx-1"></div>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>

            @if(auth()->user()->isAdmin())
                <div class="grid lg:grid-cols-2 gap-6 pt-2 border-t border-slate-100">
                    {{-- Workshop assignment --}}
                    <div>
                        <h3 class="text-sm font-semibold text-jewel-dark mb-3">Workshop Assignment</h3>
                        @if($order->isAssignableToTechnician() || $order->assignedTechnician)
                            @if($technicians->isEmpty())
                                <x-alert type="warning">No technician accounts exist. Create one under Staff Accounts first.</x-alert>
                            @else
                                <form method="POST" action="{{ route('admin.orders.assign-technician', $order) }}" class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label for="assigned_technician_id" class="jewel-label">Assign technician</label>
                                        <select id="assigned_technician_id" name="assigned_technician_id" class="jewel-input mt-1.5">
                                            <option value="">— Unassigned —</option>
                                            @foreach($technicians as $technician)
                                                <option value="{{ $technician->id }}" @selected(old('assigned_technician_id', $order->assigned_technician_id) == $technician->id)>
                                                    {{ $technician->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('assigned_technician_id')" class="mt-1" />
                                    </div>
                                    <button type="submit" class="jewel-btn-outline w-full sm:w-auto px-6">Save Assignment</button>
                                </form>
                            @endif
                        @else
                            <p class="text-sm text-slate-500">Accept this order before assigning a technician.</p>
                        @endif
                    </div>

                    {{-- Update production --}}
                    <div>
                        <h3 class="text-sm font-semibold text-jewel-dark mb-3">Update Production</h3>
                        @if($order->adminCanUpdateProduction())
                            <form method="POST" action="{{ route('admin.orders.update-production', $order) }}" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="production_status" class="jewel-label">Production status</label>
                                    <select id="production_status" name="production_status" required class="jewel-input mt-1.5">
                                        @foreach($adminProductionOptions as $value => $label)
                                            <option value="{{ $value }}" @selected(old('production_status', $order->production_status?->value) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-400">Administrators can move forward or backward to any production step.</p>
                                    <x-input-error :messages="$errors->get('production_status')" class="mt-1" />
                                </div>
                                <div>
                                    <label for="production_note" class="jewel-label">Note (optional)</label>
                                    <textarea id="production_note" name="note" rows="2" maxlength="2000"
                                        placeholder="Reason for change or progress update..."
                                        class="jewel-input mt-1.5">{{ old('note') }}</textarea>
                                    <x-input-error :messages="$errors->get('note')" class="mt-1" />
                                </div>
                                <button type="submit" class="jewel-btn w-full sm:w-auto px-6">Update Production</button>
                            </form>
                        @else
                            <p class="text-sm text-slate-500">Production can be updated after the assigned technician accepts the task.</p>
                        @endif
                    </div>
                </div>
            @endif
        @elseif($order->status === \App\Enums\OrderStatus::Pending)
            <x-alert type="info">This order is awaiting review. Accept or reject it using the manage form on the right.</x-alert>
        @elseif($order->status === \App\Enums\OrderStatus::Rejected)
            <x-alert type="warning">This order was rejected. No production workflow applies.</x-alert>
        @endif

        {{-- Production log --}}
        @if($order->productionLogs->isNotEmpty())
            <div class="pt-2 border-t border-slate-100">
                <h3 class="text-sm font-semibold text-jewel-dark mb-3">Recent Activity</h3>
                <ol class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-48 overflow-y-auto">
                    @foreach($order->productionLogs->take(12) as $log)
                        <li class="rounded-lg border border-slate-100 bg-slate-50/50 p-3 text-sm">
                            <p class="text-xs text-slate-400">{{ $log->created_at->format('M d, h:i A') }}</p>
                            <p class="mt-1 font-medium text-slate-700 leading-snug">
                                @if($log->from_status && $log->to_status && $log->from_status !== $log->to_status)
                                    {{ $log->from_status->label() }} → {{ $log->to_status->label() }}
                                @elseif($log->note)
                                    Update
                                @else
                                    Activity logged
                                @endif
                            </p>
                            @if($log->note)
                                <p class="mt-1 text-slate-500 text-xs line-clamp-2">{{ $log->note }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif
    </div>
</section>
