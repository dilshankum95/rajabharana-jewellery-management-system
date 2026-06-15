@props(['staffUser' => null, 'roles'])

<div class="grid sm:grid-cols-2 gap-5">
    <div class="sm:col-span-2">
        <label for="role" class="jewel-label">Role *</label>
        <select id="role" name="role" required class="jewel-input mt-1.5">
            <option value="">Select role</option>
            @foreach($roles as $role)
                <option value="{{ $role->value }}" @selected(old('role', $staffUser?->role?->value) === $role->value)>
                    {{ $role->label() }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('role')" class="mt-2" />
        <div class="mt-3 space-y-2">
            @foreach($roles as $role)
                <p class="text-xs text-slate-500"><span class="font-semibold text-slate-700">{{ $role->label() }}:</span> {{ $role->description() }}</p>
            @endforeach
        </div>
    </div>

    <div>
        <label for="name" class="jewel-label">Full Name *</label>
        <input id="name" name="name" type="text" required minlength="2" maxlength="255"
            value="{{ old('name', $staffUser?->name) }}"
            class="jewel-input mt-1.5">
        <x-input-error :messages="$errors->get('name')" />
    </div>

    <div>
        <label for="email" class="jewel-label">Email *</label>
        <input id="email" name="email" type="email" required maxlength="255"
            value="{{ old('email', $staffUser?->email) }}"
            class="jewel-input mt-1.5">
        <x-input-error :messages="$errors->get('email')" />
    </div>

    <div class="sm:col-span-2">
        <label for="phone" class="jewel-label">Phone (optional)</label>
        <input id="phone" name="phone" type="tel" maxlength="25" minlength="7"
            pattern="[\+]?[0-9\s\-().]{7,25}"
            value="{{ old('phone', $staffUser?->phone) }}"
            class="jewel-input mt-1.5">
        <x-input-error :messages="$errors->get('phone')" />
    </div>

    <div class="{{ $staffUser ? 'sm:col-span-2' : '' }}">
        <label for="password" class="jewel-label">Password {{ $staffUser ? '(leave blank to keep current)' : '*' }}</label>
        <input id="password" name="password" type="password" {{ $staffUser ? '' : 'required' }}
            minlength="8" maxlength="255" autocomplete="new-password"
            class="jewel-input mt-1.5">
        @if(! $staffUser)
            <x-password-requirements />
        @endif
        <x-input-error :messages="$errors->get('password')" />
    </div>

    @if($staffUser)
        <div>
            <label for="password_confirmation" class="jewel-label">Confirm New Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password"
                minlength="8" maxlength="255" autocomplete="new-password"
                class="jewel-input mt-1.5">
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>
    @else
        <div>
            <label for="password_confirmation" class="jewel-label">Confirm Password *</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                minlength="8" maxlength="255" autocomplete="new-password"
                class="jewel-input mt-1.5">
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>
    @endif
</div>
