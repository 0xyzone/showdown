<div>
    @if ($isSubmitted)
        <div class="text-center py-8 space-y-4">
            <div class="text-5xl">🎉</div>
            <h4 class="text-2xl font-black text-emerald-400 uppercase">Query Submitted!</h4>
            <p class="text-slate-300 text-sm">Thank you for reaching out. Our tournament management team will contact you within 24 hours.</p>
            <button type="button" onclick="document.getElementById('sponsor-modal').classList.add('hidden')" class="px-8 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold mt-2">Close</button>
        </div>
    @else
        <form wire:submit.prevent="submit" class="space-y-4 text-left">
            <div>
                <label class="block text-xs font-mono-cyber text-emerald-400 uppercase font-bold mb-1">Your Name / Contact Person</label>
                <input type="text" wire:model="name" required placeholder="Alex Mercer" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-emerald-500/30 text-white text-sm focus:outline-none focus:border-emerald-400">
                @error('name') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-mono-cyber text-emerald-400 uppercase font-bold mb-1">Company / Brand Name</label>
                <input type="text" wire:model="company_name" required placeholder="AeroTech Gaming" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-emerald-500/30 text-white text-sm focus:outline-none focus:border-emerald-400">
                @error('company_name') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-mono-cyber text-emerald-400 uppercase font-bold mb-1">Email Address</label>
                    <input type="email" wire:model="email" required placeholder="sponsor@brand.com" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-emerald-500/30 text-white text-sm focus:outline-none focus:border-emerald-400">
                    @error('email') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-mono-cyber text-emerald-400 uppercase font-bold mb-1">Phone Number</label>
                    <input type="tel" wire:model="phone" required placeholder="+977 9800000000" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-emerald-500/30 text-white text-sm focus:outline-none focus:border-emerald-400">
                    @error('phone') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div>
                <label class="block text-xs font-mono-cyber text-emerald-400 uppercase font-bold mb-1">Sponsorship Query Details</label>
                <textarea rows="3" wire:model="details" required placeholder="We are interested in title sponsorship / product placement..." class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-emerald-500/30 text-white text-sm focus:outline-none focus:border-emerald-400"></textarea>
                @error('details') <span class="text-rose-400 text-xs">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="w-full py-3.5 clip-corner bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-sm uppercase tracking-wider shadow-[0_0_25px_rgba(16,185,129,0.5)] transition-all">
                Submit Query
            </button>
        </form>
    @endif
</div>
