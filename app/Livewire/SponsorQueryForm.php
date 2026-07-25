<?php

namespace App\Livewire;

use App\Mail\SponsorQueryReceived;
use App\Models\SponsorQuery;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class SponsorQueryForm extends Component
{
    public string $name = '';

    public string $company_name = '';

    public string $email = '';

    public string $phone = '';

    public string $details = '';

    public bool $isSubmitted = false;

    public bool $existingFound = false;

    public string $existingQueryStatus = '';

    public string $existingCompanyName = '';

    protected array $rules = [
        'name' => 'required|string|max:255',
        'company_name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:255',
        'details' => 'required|string',
    ];

    public function submit(): void
    {
        $this->validate();

        $cleanEmail = strtolower(trim($this->email));
        $cleanCompany = strtolower(trim($this->company_name));

        // Check if an inquiry already exists for this email or company
        $existingQuery = SponsorQuery::whereRaw('LOWER(email) = ?', [$cleanEmail])
            ->orWhereRaw('LOWER(company_name) = ?', [$cleanCompany])
            ->latest()
            ->first();

        if ($existingQuery) {
            $this->existingFound = true;
            $this->existingQueryStatus = $existingQuery->status;
            $this->existingCompanyName = $existingQuery->company_name;

            return;
        }

        $query = SponsorQuery::create([
            'name' => $this->name,
            'company_name' => $this->company_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'details' => $this->details,
            'status' => 'pending',
        ]);

        try {
            Mail::to($query->email)->send(new SponsorQueryReceived($query));
        } catch (\Throwable $e) {
            // Mail fail-safe log handling
        }

        $this->isSubmitted = true;
    }

    public function resetForm(): void
    {
        $this->existingFound = false;
        $this->isSubmitted = false;
        $this->existingQueryStatus = '';
        $this->existingCompanyName = '';
    }

    public function render()
    {
        return view('livewire.sponsor-query-form');
    }
}
