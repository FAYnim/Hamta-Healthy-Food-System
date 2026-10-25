<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function index(): View
    {
        $pengaturan = Pengaturan::firstOrCreate([], [
            'whatsapp' => '+6285812345678',
            'instagram' => '@hamta.sby',
        ]);

        return view('admin.pengaturan', [
            'pengaturan' => $pengaturan,
            'cabang' => Cabang::orderBy('nama_cabang')->get(),
        ]);
    }

    public function updateIdentitas(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'hapus_logo' => ['nullable', 'boolean'],
        ]);

        $pengaturan = Pengaturan::firstOrCreate([], [
            'whatsapp' => '+6285812345678',
            'instagram' => '@hamta.sby',
        ]);
        $logoLama = $pengaturan->logo;
        $logo = $logoLama;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo')->store('pengaturan', 'public');
        } elseif ($request->boolean('hapus_logo')) {
            $logo = null;
        }

        $pengaturan->update(['logo' => $logo]);

        if ($logoLama && $logo !== $logoLama) {
            Storage::disk('public')->delete($logoLama);
        }

        return redirect()->route('admin.pengaturan')
            ->with('success', 'Logo usaha berhasil diperbarui.');
    }

    public function updateKontak(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'instagram' => ['nullable', 'string', 'max:50'],
        ]);

        Pengaturan::firstOrCreate([], [
            'whatsapp' => '+6285812345678',
            'instagram' => '@hamta.sby',
        ])->update([
            'whatsapp' => $data['whatsapp'] ?? null,
            'instagram' => $data['instagram'] ?? null,
        ]);

        return redirect()->route('admin.pengaturan')
            ->with('success', 'Kontak dan media sosial berhasil diperbarui.');
    }

    public function storeCabang(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rulesCabang());

        if (! $request->boolean('layani_pickup') && ! $request->boolean('layani_dinein')) {
            return back()->withInput()->withErrors([
                'layanan' => 'Pilih minimal satu layanan: pickup atau dine-in.',
            ]);
        }

        Cabang::create($this->atributCabang($data, $request));

        return redirect()->route('admin.pengaturan')
            ->with('success', 'Cabang "'.$data['nama_cabang'].'" berhasil ditambahkan.');
    }

    public function updateCabang(Request $request, Cabang $cabang): RedirectResponse
    {
        $data = $request->validate($this->rulesCabang());

        if (! $request->boolean('layani_pickup') && ! $request->boolean('layani_dinein')) {
            return back()->withInput()->withErrors([
                'layanan' => 'Pilih minimal satu layanan: pickup atau dine-in.',
            ]);
        }

        $cabang->update($this->atributCabang($data, $request));

        return redirect()->route('admin.pengaturan')
            ->with('success', 'Cabang "'.$data['nama_cabang'].'" berhasil diperbarui.');
    }

    public function destroyCabang(Cabang $cabang): RedirectResponse
    {
        if ($cabang->pesanan()->exists() || $cabang->produk()->exists()) {
            return redirect()->route('admin.pengaturan')
                ->with('error', 'Cabang "'.$cabang->nama_cabang.'" tidak bisa dihapus karena masih dipakai pesanan atau produk.');
        }

        $namaCabang = $cabang->nama_cabang;
        $cabang->delete();

        return redirect()->route('admin.pengaturan')
            ->with('success', 'Cabang "'.$namaCabang.'" berhasil dihapus.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function atributCabang(array $data, Request $request): array
    {
        return [
            'nama_cabang' => $data['nama_cabang'],
            'alamat' => $data['alamat'],
            'jam_buka' => $data['jam_buka'],
            'jam_tutup' => $data['jam_tutup'],
            'jam_buka_weekend' => $data['jam_buka_weekend'] ?? null,
            'jam_tutup_weekend' => $data['jam_tutup_weekend'] ?? null,
            'layani_pickup' => $request->boolean('layani_pickup'),
            'layani_dinein' => $request->boolean('layani_dinein'),
        ];
    }

    /** @return array<string, array<int, string>> */
    private function rulesCabang(): array
    {
        return [
            'nama_cabang' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string', 'max:255'],
            'jam_buka' => ['required', 'date_format:H:i'],
            'jam_tutup' => ['required', 'date_format:H:i'],
            'jam_buka_weekend' => ['nullable', 'date_format:H:i', 'required_with:jam_tutup_weekend'],
            'jam_tutup_weekend' => ['nullable', 'date_format:H:i', 'required_with:jam_buka_weekend'],
            'layani_pickup' => ['nullable', 'boolean'],
            'layani_dinein' => ['nullable', 'boolean'],
        ];
    }
}
