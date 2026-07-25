<div>
    @if ($existingFound)
        <div class="text-center py-6 space-y-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-3xl mx-auto text-amber-400">
                ⚠️
            </div>
            <h4 class="text-xl font-black text-amber-400 uppercase">Existing Inquiry Found!</h4>
            
            <div class="p-4 rounded-xl bg-slate-900/90 border border-amber-500/30 text-left text-sm space-y-2">
                <div class="flex justify-between items-center border-b border-amber-500/20 pb-2">
                    <span class="text-slate-400 text-xs font-mono-cyber">COMPANY / BRAND:</span>
                    <span class="font-extrabold text-white">{{ $existingCompanyName }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 text-xs font-mono-cyber">CURRENT STATUS:</span>
                    <span class="px-2.5 py-0.5 rounded text-xs font-mono-cyber font-extrabold uppercase
                        @if($existingQueryStatus === 'pending') bg-amber-500/20 text-amber-300 border border-amber-500/40
                        @elseif($existingQueryStatus === 'contacted') bg-cyan-500/20 text-cyan-300 border border-cyan-500/40
                        @elseif($existingQueryStatus === 'converted') bg-emerald-500/20 text-emerald-300 border border-emerald-500/40
                        @else bg-slate-800 text-slate-400 @endif">
                        {{ $existingQueryStatus }}
                    </span>
                </div>
            </div>

            <p class="text-slate-300 text-xs leading-relaxed px-2">
                @if($existingQueryStatus === 'pending')
                    An inquiry for <strong>{{ $existingCompanyName }}</strong> has already been submitted and is currently under review by our team. Duplicate submissions are not allowed.
                @elseif($existingQueryStatus === 'contacted')
                    Our tournament organizing committee has already initiated contact regarding this inquiry. Please check your inbox for our follow-up email!
                @elseif($existingQueryStatus === 'converted')
                    <strong>{{ $existingCompanyName }}</strong> has already been officially approved as a Sponsor/Partner for Outlaw Showdown 2026!
                @else
                    A previous inquiry for <strong>{{ $existingCompanyName }}</strong> has been processed. If you need to submit a new inquiry, please use a different company name or email address.
                @endif
            </p>

            <div class="flex items-center justify-center gap-3 pt-2">
                <button type="button" wire:click="resetForm" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all">
                    Edit Query Details
                </button>
                <button type="button" onclick="document.getElementById('sponsor-modal').classList.add('hidden')" class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-black uppercase tracking-wider transition-all">
                    Close
                </button>
            </div>
        </div>
    @elseif ($isSubmitted)
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
