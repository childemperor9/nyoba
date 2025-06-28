<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coa; // Model for Master COA
use App\Models\CoaConfiguration; // Model for COA Configuration
use Illuminate\Validation\Rule; // For unique validation on update
use Illuminate\Support\Facades\Log; // Important for error logging
use Illuminate\Support\Facades\DB; // For database transactions

class CoaController extends Controller
{
    // --- Methods for Master COA ---
    public function index()
    {
        try {
            $allCoas = Coa::orderBy('acc_num', 'asc')->get();
        } catch (\Exception $e) {
            Log::error("Error fetching COAs: " . $e->getMessage());
            return view('coa.index')->withErrors('Terjadi kesalahan saat mengambil data COA: ' . $e->getMessage());
        }
        return view('coa.index', compact('allCoas'));
    }

    public function create()
    {
        return view('coa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'acc_num' => 'required|string|unique:coa,acc_num|max:255', // Changed 'coas' to 'coa'
            'acc_name' => 'required|string|max:255',
        ]);

        Coa::create([
            'acc_num' => $request->acc_num,
            'acc_name' => $request->acc_name,
        ]);

        return redirect()->route('coa.index')->with('success', 'Data COA berhasil ditambahkan!');
    }

    public function edit(Coa $coa)
    {
        return view('coa.edit', compact('coa'));
    }

    public function update(Request $request, Coa $coa)
    {
        $request->validate([
            'acc_num' => [
                'required',
                'string',
                'max:255',
                Rule::unique('coa')->ignore($coa->id), // Changed 'coas' to 'coa'
            ],
            'acc_name' => 'required|string|max:255',
        ]);

        $coa->update([
            'acc_num' => $request->acc_num,
            'acc_name' => $request->acc_name,
        ]);

        return redirect()->route('coa.index')->with('success', 'Data COA berhasil diperbarui!');
    }

    public function destroy(Coa $coa)
    {
        try {
            $coa->delete();
            return redirect()->route('coa.index')->with('success', 'Data COA berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error("Error deleting COA: " . $e->getMessage());
            return redirect()->route('coa.index')->with('error', 'Gagal menghapus data COA: ' . $e->getMessage());
        }
    }

    // --- Methods for COA Configuration ---

    /**
     * Display a listing of the COA configurations.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function konfigurasiCoaIndex(Request $request)
    {
        try {
            $search = $request->query('search');
            $perPage = $request->query('per_page', 5);

            $configurationsQuery = CoaConfiguration::query();

            if ($search) {
                $configurationsQuery->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('description', 'like', '%' . $search . '%');
            }

            $configurations = $configurationsQuery->paginate($perPage);

        } catch (\Exception $e) {
            Log::error("Error fetching COA configurations: " . $e->getMessage());
            return view('konfigurasi.konfigurasicoa')->withErrors('Terjadi kesalahan saat mengambil data konfigurasi COA: ' . $e->getMessage());
        }

        return view('konfigurasi.konfigurasicoa', compact('configurations'));
    }

    /**
     * Show the form for creating a new COA configuration.
     * @return \Illuminate\View\View
     */
    public function konfigurasiCoaCreate()
    {
        // Get all COA accounts to populate the 'Akun' dropdown in the detail table
        $allCoas = Coa::orderBy('acc_num', 'asc')->get();
        return view('konfigurasi.create', compact('allCoas'));
    }

    /**
     * Store a newly created COA configuration in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function konfigurasiCoaStore(Request $request)
    {
        // Clone the request data to manipulate details without affecting original validation
        $requestData = $request->except(['details']);
        $detailsData = $request->input('details', []);

        // Validate the main configuration data
        $request->validate([
            'name' => 'required|string|unique:coa_configurations,name|max:255',
            'transaction_type' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'details' => 'array',
            'details.*.coa_id' => 'required|exists:coa,id',
            'details.*.percentage' => 'required|numeric|min:0|max:100',
            'details.*.type' => 'required|in:Debit,Kredit',
            'details.*.nominal' => 'required|string',
        ]);

        // Clean and prepare nominals for storage
        $cleanedDetails = [];
        foreach ($detailsData as $detail) {
            $nominal = str_replace('.', '', $detail['nominal']);
            $nominal = str_replace(',', '.', $nominal);
            $detail['nominal'] = (float)$nominal;
            $cleanedDetails[] = $detail;
        }

        DB::beginTransaction();

        try {
            $coaConfiguration = CoaConfiguration::create([
                'name' => $requestData['name'],
                'transaction_type' => $requestData['transaction_type'] ?? null,
                'description' => $requestData['description'] ?? null,
                'details_data' => $cleanedDetails,
            ]);

            DB::commit();
            return redirect()->route('konfigurasi.coa.index')->with('success', 'Konfigurasi COA berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error storing COA configuration: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan Konfigurasi COA: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified COA configuration.
     * @param  \App\Models\CoaConfiguration  $coaConfiguration
     * @return \Illuminate\View\View
     */
    public function konfigurasiCoaEdit(CoaConfiguration $coaConfiguration)
    {
        // Get all COA accounts to populate the 'Akun' dropdown in the detail table
        $allCoas = Coa::orderBy('acc_num', 'asc')->get();

        $details = $coaConfiguration->details_data ?? [];

        return view('konfigurasi.edit', compact('coaConfiguration', 'allCoas', 'details'));
    }

    /**
     * Update the specified COA configuration in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CoaConfiguration  $coaConfiguration
     * @return \Illuminate\Http\RedirectResponse
     */
    public function konfigurasiCoaUpdate(Request $request, CoaConfiguration $coaConfiguration)
    {
        // Clone the request data to manipulate details without affecting original validation
        $requestData = $request->except(['details']);
        $detailsData = $request->input('details', []);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('coa_configurations')->ignore($coaConfiguration->id),
            ],
            'transaction_type' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'details' => 'array',
            'details.*.coa_id' => 'required|exists:coa,id',
            'details.*.percentage' => 'required|numeric|min:0|max:100',
            'details.*.type' => 'required|in:Debit,Kredit',
            'details.*.nominal' => 'required|string',
        ]);

        // Clean and prepare nominals for storage
        $cleanedDetails = [];
        foreach ($detailsData as $detail) {
            $nominal = str_replace('.', '', $detail['nominal']);
            $nominal = str_replace(',', '.', $nominal);
            $detail['nominal'] = (float)$nominal;
            $cleanedDetails[] = $detail;
        }

        DB::beginTransaction();

        try {
            // Update the main COA Configuration record
            $coaConfiguration->update([
                'name' => $requestData['name'],
                'transaction_type' => $requestData['transaction_type'] ?? null,
                'description' => $requestData['description'] ?? null,
                'details_data' => $cleanedDetails,
            ]);

            DB::commit();

            // Determine redirect based on which button was clicked
            $redirectRoute = 'konfigurasi.coa.edit';
            $params = ['coaConfiguration' => $coaConfiguration->id];
            if ($request->has('save_and_back')) {
                $redirectRoute = 'konfigurasi.coa.index';
                $params = [];
            }

            return redirect()->route($redirectRoute, $params)->with('success', 'Konfigurasi COA berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating COA configuration: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui Konfigurasi COA: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified COA configuration from storage.
     * @param  \App\Models\CoaConfiguration  $coaConfiguration
     * @return \Illuminate\Http\RedirectResponse
     */
    public function konfigurasiCoaDestroy(CoaConfiguration $coaConfiguration)
    {
        try {
            $coaConfiguration->delete();
            return redirect()->route('konfigurasi.coa.index')->with('success', 'Konfigurasi COA berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error("Error deleting COA configuration: " . $e->getMessage());
            return redirect()->route('konfigurasi.coa.index')->with('error', 'Gagal menghapus konfigurasi COA: ' . $e->getMessage());
        }
    }
}